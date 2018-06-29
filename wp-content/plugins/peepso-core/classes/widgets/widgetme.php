<?php


class PeepSoWidgetMe extends WP_Widget
{

    /**
     * Set up the widget name etc
     */
    public function __construct($id = null, $name = null, $args= null) {
        if(!$id)    $id     = 'PeepSoWidgetMe';
        if(!$name)  $name   = __('PeepSo Profile', 'peepso-core');
        if(!$args)  $args   = array( 'description' => __('PeepSo Profile Widget', 'peepso-core'), );

        parent::__construct(
            $id, // Base ID
            $name, // Name
            $args // Args
        );
    }

    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {

        $instance['user_id']    = get_current_user_id();
        $instance['user']       = PeepSoUser::get_instance($instance['user_id']);

        // Disable the widget for guests if
        if(isset($instance['guest_behavior']) && 'hide' === $instance['guest_behavior'] && !$instance['user_id'])
        {
            return FALSE;
        }

        // List of links to be displayed
        $links = apply_filters('peepso_navigation_profile', array('_user_id'=>get_current_user_id()));

        $community_links = apply_filters('peepso_navigation', array());
        unset($community_links['profile']);

        $instance['links'] = $links;
        $instance['community_links'] = $community_links;


        if(!array_key_exists('template', $instance) || !strlen($instance['template']))
        {
            $instance['template'] = 'me.tpl';
        }

        $instance['toolbar'] = '';
        if(isset($instance['show_notifications']) && 1 === intval($instance['show_notifications'])) {
                $instance['toolbar'] = $this->toolbar();
        }


        PeepSoTemplate::exec_template( 'widgets', $instance['template'], array( 'args'=>$args, 'instance' => $instance ) );

        // Included in peepso bundle.
        wp_enqueue_script('peepso-widget-me', FALSE, array('peepso-bundle', 'peepso-notification'),
            PeepSo::PLUGIN_VERSION, TRUE);
    }

    // Displays the frontend navbar
    public function toolbar()
    {
        $note = PeepSoNotifications::get_instance();
        $unread_notes = $note->get_unread_count_for_user();

        $toolbar = array(
            'notifications' => array(
                'href' => PeepSo::get_page('notifications'),
                'icon' => 'globe',
                'class' => 'dropdown-notification ps-js-notifications',
                'title' => __('Pending Notifications', 'peepso-core'),
                'count' => $unread_notes,
                'order' => 100
            ),
        );

        $toolbar = PeepSoGeneral::get_instance()->get_navigation('notifications');

        ob_start();
        ?>

        <div class="ps-widget--profile__notifications">

        <?php foreach ($toolbar as $item => $data) { ?>
            <span class="<?php echo $data['class'];?>">
              <a href="<?php echo $data['href'];?>" title="<?php echo esc_attr($data['label']);?>">
                <div class="ps-bubble__wrapper">
                    <i class="<?php echo $data['icon'];?>"></i>
                        <span class="js-counter ps-bubble ps-bubble--widget ps-js-counter" <?php echo ($data['count'] > 0) ? '' : ' style="display:none"';?>>
                            <?php echo ($data['count'] > 0) ? $data['count'] : '';?>
                        </span>
                </div>
              </a>
            </span>
        <?php } ?>

        </div>

        <?php
        $html = str_replace(PHP_EOL,'',ob_get_clean());

        return $html;
    }

    /**
     * Outputs the admin options form
     *
     * @param array $instance The widget options
     */
    public function form( $instance ) {

        $instance['fields'] = array(
            // general
            'section_general' => FALSE,
            'limit'     => FALSE,
            'title'     => TRUE,

            // peepso
            'integrated'   => FALSE,
            'position'  => FALSE,
            'ordering'  => FALSE,
            'hideempty' => FALSE,

        );

        ob_start();

        $settings =  apply_filters('peepso_widget_form', array('html'=>'', 'that'=>$this,'instance'=>$instance));

        $guest_behavior         = !empty($instance['guest_behavior']) ? $instance['guest_behavior'] : 'login';
        $show_notifications     = isset($instance['show_notifications']) ? $instance['show_notifications'] : 1;
        $show_community_links   = isset($instance['show_community_links']) ? $instance['show_community_links'] : 0;
        $show_cover             = isset($instance['show_cover']) ? $instance['show_cover'] : 0;

        ?>
        <p>
            <label for="<?php echo $this->get_field_id('guest_behavior'); ?>">
                <?php _e('Guest view', 'peepso-core'); ?>
                <select class="widefat" id="<?php echo $this->get_field_id('guest_behavior'); ?>"
                        name="<?php echo $this->get_field_name('guest_behavior'); ?>">
                    <option value="login"><?php _e('Log-in form', 'peepso-core'); ?></option>
                    <option value="hide" <?php if('hide' === $guest_behavior) echo ' selected="selected" ';?>><?php _e('Hide', 'peepso-core'); ?></option>
                </select>

            </label>
        </p>
        <p>
            <input name="<?php echo $this->get_field_name('show_notifications'); ?>" class="ace ace-switch ace-switch-2"
                   id="<?php echo $this->get_field_id('show_notifications'); ?>" type="checkbox" value="1"
                <?php if(1 === $show_notifications) echo ' checked="" ';?>>
            <label class="lbl" for="<?php echo $this->get_field_id('show_notifications'); ?>">
                <?php _e('Show notifications', 'peepso-core'); ?>
            </label>
        </p>
        <p>
            <input name="<?php echo $this->get_field_name('show_community_links'); ?>" class="ace ace-switch ace-switch-2"
                   id="<?php echo $this->get_field_id('show_community_links'); ?>" type="checkbox" value="1"
                <?php if(1 === $show_community_links) echo ' checked="" ';?>>
            <label class="lbl" for="<?php echo $this->get_field_id('show_community_links'); ?>">
                <?php _e('Show community links', 'peepso-core'); ?>
            </label>
        </p>

        <p>
            <input name="<?php echo $this->get_field_name('show_cover'); ?>" class="ace ace-switch ace-switch-2"
                   id="<?php echo $this->get_field_id('show_community_links'); ?>" type="checkbox" value="1"
                <?php if(1 === $show_cover) echo ' checked="" ';?>>
            <label class="lbl" for="<?php echo $this->get_field_id('show_cover'); ?>">
                <?php _e('Show cover', 'peepso-core'); ?>
            </label>
        </p>
        <?php
        $settings['html']  .= ob_get_clean();

        echo $settings['html'];
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['guest_behavior']         = $new_instance['guest_behavior'];
        $instance['show_notifications']     = (int) $new_instance['show_notifications'];
        $instance['show_community_links']   = (int) $new_instance['show_community_links'];
        $instance['show_cover']             = (int) $new_instance['show_cover'];
        $instance['title']                  = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

        return $instance;
    }
}

// EOF
