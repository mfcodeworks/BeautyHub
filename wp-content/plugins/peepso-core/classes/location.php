<?php

class PeepSoLocation
{
    private static $_instance = null;

    const SHORTCODE_TAG = 'peepso_geo';
    
    public $is_enabled = FALSE;

    /**
     * Initialize all variables, filters and actions
     */
    private function __construct()
    {
		$this->is_enabled = PeepSo::get_option('location_enable', 0) == 1 ? TRUE : FALSE;
        add_action('peepso_init', array(&$this, 'init'));
        add_filter('peepso_admin_profile_field_types', array(&$this, 'admin_profile_field_types'));
    }

    /**
     * Retrieve singleton class instance
     * @return instance reference to plugin
     */
    public static function get_instance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return (self::$_instance);
    }

    /*
     * Callback for 'peepso_init' action; initialize the PeepSoLocation plugin
     */
    public function init()
    {
        if (is_admin()) {
            if (!strlen(PeepSo::get_option('location_gmap_api_key')) && !isset($_REQUEST['location_gmap_api_key']) && $this->is_enabled) {
                add_action('admin_notices', array(&$this, 'api_key_missing_notice'));
            }
        } else {
            add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));

            if ($this->is_enabled) {
                // PeepSo postbox
                add_filter('peepso_postbox_interactions', array(&$this, 'postbox_interactions'), 30, 1);
                add_filter('peepso_activity_allow_empty_content', array(&$this, 'activity_allow_empty_content'), 10, 1);

                // Attach post extras
                add_action('wp_insert_post', array(&$this, 'insert_post'), 30, 2);
                add_action('peepso_activity_after_save_post', array(&$this, 'insert_post'), 30, 2);

                // Clean up all legacy information from old posts
                add_filter('peepso_activity_content', array(&$this, 'filter_remove_legacy'), 20, 1);
                add_filter('peepso_remove_shortcodes', array(&$this, 'filter_remove_legacy'), 30, 1);

                // create album extra fields
                add_filter('peepso_photo_album_extra_fields', array(&$this, 'photo_album_extra_fields'), 10, 1);
                add_filter('peepso_photo_album_show_extra_fields', array(&$this, 'photo_album_show_extra_fields'), 10, 3);
                add_filter('peepso_photo_album_update_location', array(&$this, 'photo_album_update_location'), 10);

                add_filter('peepso_activity_post_edit', array(&$this, 'filter_post_edit'), 10, 1);
            }

            // Print post extras
            add_filter('peepso_post_extras', array(&$this, 'filter_post_extras'), 20, 1);
        }

        ## Query modifiers
        // modify limit
        add_filter('peepso_profile_fields_query_limit', array(&$this, 'filter_profile_fields_query_limit'));
    }

    # # # # # # # # # # User Front End # # # # # # # # # #

    /**
     * POSTBOX - add the Location button
     * @param  array $interactions An array of interactions available.
     * @return array $interactions
     */
    public function postbox_interactions($interactions = array())
    {
        wp_enqueue_script('peepsolocation-js');
        wp_enqueue_style('locso');

        $interactions['location'] = array(
            'label' => __('Location', 'peepso-core'),
            'id' => 'location-tab',
            'class' => 'ps-postbox__menu-item',
            'icon' => 'map-marker',
            'click' => 'return;',
            'title' => __('Set a Location for your post', 'peepso-core'),
            'extra' => PeepSoTemplate::exec_template('location', 'interaction', null, true),
        );

        return ($interactions);
    }

    public function filter_profile_fields_query_limit($limit)
    {
        $limit = $limit + 1;

        return $limit;
    }

    /**
     * EP add field types
     * @param array $fieldtypes An array of field types
     * @return array modified $fieldtypes
     */
    public function admin_profile_field_types($fieldtypes)
    {
        $fieldtypes[] = 'location';

        return $fieldtypes;
    }

    /**
     * PHOTO ALBUM - add the Location field
     * @param  array $fields An array of interactions available.
     * @return array $fields
     */
    public function photo_album_extra_fields($fields = array())
    {
        wp_enqueue_script('peepsolocation-js');
        wp_enqueue_style('locso');

        $fields['location'] = array(
            'label' => __('Location', 'peepso-core'),
            'field' => '<input type="text" name="album_location" class="ps-input ps-js-location" value="" />',
            'isfull' => true,
            'extra' => PeepSoTemplate::exec_template('location', 'photo_album_extra_fields', null, true),
        );

        return ($fields);
    }

    /**
     * PHOTO ALBUM - display the Location field
     * @param  array $fields An array of interactions available.
     * @return array $fields
     */
    public function photo_album_show_extra_fields($extras, $post_id, $can_edit)
    {
        $loc = get_post_meta($post_id, 'peepso_location', true);

        if ($can_edit || $loc) {
            $data = array(
                'post_id' => $post_id,
                'can_edit' => $can_edit,
                'loc' => $loc,
            );
            $extras = PeepSoTemplate::exec_template('location', 'photo_album_show_extra_fields', $data, true);
        }

        return $extras;
    }

    /**
     * PHOTO ALBUM - update metadata
     * @param  int $post_id The post ID to add the metadata in.
     * @param  object $post The WP_Post object.
     */
    public function photo_album_update_location($save = array())
    {
        $input = new PeepSoInput();

        $owner = $input->val('user_id');
        $post_id = $input->val('post_id');
        $location = $input->val('location', null);

        if (false === wp_verify_nonce($input->val('_wpnonce'), 'set-album-location')) {
            $save['success'] = false;
            $save['error'] = __('Request could not be verified.', 'peepso-core');
        } else {
            $the_post = get_post($post_id);
            if (get_current_user_id() === intval($the_post->post_author)) {

                if (false === is_null($location)) {
                    update_post_meta($post_id, 'peepso_location', $location);

                    $save['success'] = true;
                    $save['msg'] = __('Photo album location saved.', 'peepso-core');
                } else {
                    $save['success'] = false;
                    $save['msg'] = __('Missing field location.', 'peepso-core');
                }
            } else {
                $save['success'] = false;
                $save['msg'] = __('You are not authorized to change this album location.', 'peepso-core');
            }
        }

        return $save;
    }

    /**
     * POSTBOX - set a flag allowing the post content to be empty
     * @param string $allowed
     * @return boolean
     */
    public function activity_allow_empty_content($allowed)
    {
        $input = new PeepSoInput();
        $location = $input->val('location');
        if (!empty($location)) {
            $allowed = true;
        }
        return ($allowed);
    }

    /**
     * POST CREATION - build metadata
     * @param  int $post_id The post ID to add the metadata in.
     * @param  object $post The WP_Post object.
     */
    public function insert_post($post_id)
    {
        $input = new PeepSoInput();
        $location = $input->val('location', null);

        if (false === is_null($location)) {
            update_post_meta($post_id, 'peepso_location', $location);
        } else {
            delete_post_meta($post_id, 'peepso_location');
        }
    }

    /**
     * POST RENDERING - add location information to post extras array
     * @return array
     */
    public function filter_post_extras($extras = array())
    {
        global $post;
        $loc = get_post_meta($post->ID, 'peepso_location', true);

        if ($loc) {
            ob_start();
            ?>
			<span>
			<a
				href="javascript:" title="<?php echo esc_attr($loc['name']); ?>"
				onclick="pslocation.show_map(<?php echo $loc['latitude']; ?>, <?php echo $loc['longitude']; ?>, '<?php echo esc_attr($loc['name']); ?>');">
				<i class="ps-icon-map-marker"></i><?php echo $loc['name']; ?>
			</a>
			</span>
			<?php
			$extras[] = ob_get_clean();
        }

        return $extras;
    }

    /**
     * POST RENDERING - clean old location information and shortcodes
     * @return string
     */
    public function filter_remove_legacy($content)
    {
        // Clean up old info attached to the post
        $regex = '/(<span>&mdash;)[\s\S]+(<\/span>)/';
        $content = preg_replace($regex, '', $content);

        // Since 1.6.1 we don't use shortcodes
        $content = preg_replace('/\[peepso_geo(?:.*?)\][\s\S]*\[\/peepso_geo]/', '', $content);

        $content = trim($content);

        return $content;
    }

    /**
     * Enqueue the assets
     */
    public function enqueue_scripts()
    {
        global $wp_query;
        $api_key = PeepSo::get_option('location_gmap_api_key');

        wp_localize_script('peepso', 'peepsogeolocationdata',
            array(
                'api_key' => $api_key,
                'template_selector' => PeepSoTemplate::exec_template('location', 'selector', array(), true),
                'template_postbox' => PeepSoTemplate::exec_template('location', 'postbox', array(), true),
            )
        );

        wp_enqueue_script('peepsolocation-js', PeepSo::get_asset('js/location/bundle.min.js'),
            array('peepso', 'jquery-ui-position', 'peepso-lightbox'), PeepSo::PLUGIN_VERSION, true);
    }

    # # # # # # # # # # Utilities: Activation, Licensing, PeepSo detection and compatibility  # # # # # # # # # #

	public function api_key_missing_notice()
    {?>
		<div class="error">
			<strong>
                <?php echo __('PeepSo Location requires a Google Maps API key.', 'peepso-core'); ?>
                <a href="admin.php?page=peepso_config&tab=location"><?php echo __('Click here to configure it', 'peepso-core'); ?></a>.


			</strong>
		</div>
		<?php
    }

    public function filter_post_edit($data = array())
    {
        $input = new PeepSoInput();
        $post_id = $input->int('postid');

        $location = get_post_meta($post_id, 'peepso_location', true);
        if (!empty($location)) {
            $data['location'] = $location;
        }

        return $data;
    }
}

// EOF
