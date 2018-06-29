<?php

class PeepSoConfigSections extends PeepSoConfigSectionAbstract
{
	const SITE_ALERTS_SECTION = 'site_alerts_';

	public function register_config_groups()
	{
		$this->set_context('left');
		$this->_group_activity();
        $this->_group_login();
        $this->_group_usernames();
        $this->_group_registration();

		$this->set_context('right');

		// Don't show licenses box on our demo site
		if(!isset($_SERVER['HTTP_HOST']) || 'demo.peepso.com' != $_SERVER['HTTP_HOST'] ) {
            $this->_group_license();
        }
        $this->_group_integrations();

    }

    private function _group_login()
    {

        # Always remember me
        $this->args('default', 0);

        $this->set_field(
            'site_frontpage_rememberme_default',
            __('Always check "remember me" by default', 'peepso-core'),
            'yesno_switch'
        );

        // # Redirect Successful Logins
        $args = array(
            'sort_order' => 'asc',
            'sort_column' => 'post_title',
            'hierarchical' => 1,
            'exclude' => '',
            'include' => '',
            'meta_key' => '',
            'meta_value' => '',
            'authors' => '',
            'child_of' => 0,
            'parent' => -1,
            'exclude_tree' => '',
            'number' => '',
            'offset' => 0,
            'post_type' => 'page',
            'post_status' => 'publish'
        );
        $pages = get_pages($args);
        $options = array(
            -1 => __('Home page', 'peepso-core') . ': ' . home_url('/'),
            0 => __('No redirect', 'peepso-core'),
        );

        $pageredirect = PeepSo::get_option('site_frontpage_redirectlogin');
        $settings = PeepSoConfigSettings::get_instance();
        foreach ($pages as $page) {
            // handling selected old value (activity/profile)
            if($page->post_name == $pageredirect) {
                //$this->args('default', $page->ID);
                // update option to selected ID
                $settings->set_option('site_frontpage_redirectlogin', $page->ID);
            }

            $options[$page->ID] = __('Page:','peepso-core') . ' ' . ($page->post_parent > 0 ? '&nbsp;&nbsp;' : '') . $page->post_title;
        }

        $this->args('options', $options);

        $this->set_field(
            'site_frontpage_redirectlogin',
            __('Log-in redirect', 'peepso-core'),
            'select'
        );


        // # Redirect Logout

        $args = array(
            'sort_order' => 'asc',
            'sort_column' => 'post_title',
            'hierarchical' => 1,
            'exclude' => '',
            'include' => '',
            'meta_key' => '',
            'meta_value' => '',
            'authors' => '',
            'child_of' => 0,
            'parent' => -1,
            'exclude_tree' => '',
            'number' => '',
            'offset' => 0,
            'post_type' => 'page',
            'post_status' => 'publish'
        );
        $pages = get_pages($args);
        $options = array(
            -1 => __('Home page', 'peepso-core') . ': ' . home_url('/'),
            0 => __('No redirect', 'peepso-core'),
        );

        $pageredirect = PeepSo::get_option('logout_redirect');
        $settings = PeepSoConfigSettings::get_instance();
        foreach ($pages as $page) {
            // handling selected old value (activity/profile)
            if($page->post_name == $pageredirect) {
                //$this->args('default', $page->ID);
                // update option to selected ID
                $settings->set_option('logout_redirect', $page->ID);
            }

            $options[$page->ID] = __('Page:','peepso-core') . ' ' . ($page->post_parent > 0 ? '&nbsp;&nbsp;' : '') . $page->post_title;
        }

        $this->args('options', $options);

        $this->set_field(
            'logout_redirect',
            __('Log-out redirect', 'peepso-core'),
            'select'
        );

        $this->set_group(
            'login_logout',
            __('Login & Logout', 'peepso-core')
        );
    }

    private function _group_usernames() {
        // Allow Username changes
        $this->set_field(
            'system_allow_username_changes',
            __('Allow username changes', 'peepso-core'),
            'yesno_switch'
        );

        // Allow Username changes
        $this->args('descript', __('Some plugins (like WooCommerce, EDD and Learndash) create user accounts where the usename is an e-mail address. PeepSo uses the usernames to build profile URLs, which can lead to accidental e-mail exposure through site URLs. Enabling this feature will cause PeepSo to step in during third party user registration and automatically generate a safe username for the new user.','peepso-core'));
        $this->set_field(
            'thirdparty_username_cleanup',
            __('Clean up third party registrations', 'peepso-core'),
            'yesno_switch'
        );

        $this->set_group(
            'usernames',
            __('Usernames', 'peepso-core')
        );
    }

	private function _group_registration()
	{
		/** GENERAL **/

        // disabled registration
        $this->args('descript',__('Enabled: registration through PeepSo becomes impossible and is not shown anywhere in the front-end. Use only if your site is a closed community or registrations are coming in through another plugin.','peepso-core'));
        $this->args('default', 0);
        $this->set_field(
                'site_registration_disabled',
                __('Disable registration', 'peepso-core'),
                'yesno_switch'
        );

		// Enable Account Verification
        $this->args('descript', __('Enabled: users register, confirm their e-mail and must be accepted by an Admin. Users are notified by email when they\'re approved.<br/>Disabled: users register, confirm their email address and can immediately participate in your community.', 'peepso-core'));
		$this->set_field(
			'site_registration_enableverification',
			__('Admin Account Verification', 'peepso-core'),
			'yesno_switch'
		);

		// # Force profile completion
        $this->args('descript',__('Enabled: users have to fill in all required fields before being able to participate in the community.', 'peepso-core'));
		$this->set_field(
			'force_required_profile_fields',
			__('Force Profile Completion', 'peepso-core'),
			'yesno_switch'
		);


        // # Redirect After activations

        $args = array(
            'sort_order' => 'asc',
            'sort_column' => 'post_title',
            'hierarchical' => 1,
            'exclude' => '',
            'include' => '',
            'meta_key' => '',
            'meta_value' => '',
            'authors' => '',
            'child_of' => 0,
            'parent' => -1,
            'exclude_tree' => '',
            'number' => '',
            'offset' => 0,
            'post_type' => 'page',
            'post_status' => 'publish'
        );
        $pages = get_pages($args);
        $options = array(
            -1 => __('Home page', 'peepso-core') . ': ' . home_url('/'),
        );

        $pageredirect = PeepSo::get_option('site_activation_redirect');
        $settings = PeepSoConfigSettings::get_instance();
        foreach ($pages as $page) {
            // handling selected old value (activity/profile)
            if($page->post_name == $pageredirect) {
                //$this->args('default', $page->ID);
                // update option to selected ID
                $settings->set_option('site_activation_redirect', $page->ID);
            }

            $options[$page->ID] = __('Page:','peepso-core') . ' ' . ($page->post_parent > 0 ? '&nbsp;&nbsp;' : '') . $page->post_title;
        }

        $this->args('options', $options);

        $this->set_field(
            'site_activation_redirect',
            __('Activation redirect', 'peepso-core'),
            'select'
        );


		// Enable Secure Mode For Registration
        $this->args('descript',__('Requires a valid SSL certificate.<br/>Enabling this option without a valid certificate might break your site.', 'peepso-core'));
		$this->set_field(
				'site_registration_enable_ssl',
				__('Force SSL on Registration Page', 'peepso-core'),
				'yesno_switch'
		);



		/** RECAPTCHA **/
		// # Separator Recaptcha
		$this->set_field(
			'separator_recaptcha',
			__('ReCaptcha', 'peepso-core'),
			'separator'
		);

		// # Enable ReCaptcha
		$this->set_field(
			'site_registration_recaptcha_enable',
			__('Enable ReCaptcha', 'peepso-core'),
			'yesno_switch'
		);

		// # ReCaptcha Site Key
		$this->set_field(
			'site_registration_recaptcha_sitekey',
			__('Site Key', 'peepso-core'),
			'text'
		);

		// # ReCaptcha Secret Key
        $this->args('descript',
            __('Get INVISIBLE ReCaptcha keys <a href="https://www.google.com/recaptcha/" target="_blank">here</a></strong>.<br/>Having issues or questions? Please refer to the <a href="http://peep.so/recaptcha" target="_blank">documentation</a> or contact PeepSo Support.', 'peepso-core'));
		$this->set_field(
			'site_registration_recaptcha_secretkey',
			__('Secret Key', 'peepso-core'),
			'text'
		);


		/** T&C **/

		// # Separator Terms & Conditions
		$this->set_field(
			'separator_terms',
			__('Terms & Conditions', 'peepso-core'),
			'separator'
		);

        // # Enable Terms & Conditions
        $this->set_field(
            'site_registration_enableterms',
            __('Enable Terms &amp; Conditions', 'peepso-core'),
            'yesno_switch'
        );

        // # Terms & Conditions Text
        $this->args('raw', TRUE);

        $this->set_field(
            'site_registration_terms',
            __('Terms &amp; Conditions', 'peepso-core'),
            'textarea'
        );

        /** Privacy Policy **/

        // # Separator Terms & Conditions
        $this->set_field(
            'separator_privacy',
            __('Privacy Policy', 'peepso-core'),
            'separator'
        );

        // # Enable Privacy
        $this->set_field(
            'site_registration_enableprivacy',
            __('Enable Privacy Policy', 'peepso-core'),
            'yesno_switch'
        );

        // # Privacy Text
        $this->args('raw', TRUE);

        $this->set_field(
            'site_registration_privacy',
            __('Privacy Policy', 'peepso-core'),
            'textarea'
        );

		// Build Group

		#$this->args('summary', $summary);

		$this->set_group(
			'registration',
			__('Registration', 'peepso-core')
		);
	}

    private function _group_integrations()
    {
        /** WORDPRESS SOCIAL INVITES */

        // # Separator WSI
        $this->set_field(
            'separator_wsi',
            __('WordPress Social Invites', 'peepso-core'),
            'separator'
        );

        $wsi = ' <a href="http://peep.so/wsi" target="_blank">Wordpress Social Invites</a> ';

        // # message WSI
        $this->set_field(
            'message_wsi',
            sprintf(__('Requires %s to be installed and properly configured.', 'peepso-core'), $wsi),
            'message'
        );

        if (class_exists('Wsi_Public')) {
            // # Enable WSI
            $this->set_field(
                'wsi_enable_members',
                __('Show WSI on Members Page', 'peepso-core'),
                'yesno_switch'
            );
        } else {
            $this->set_field(
                'message_wsi_missing',
                sprintf(__('%s not found! Please install the plugin to see the configuration setting.', 'peepso-core'), $wsi),
                'message'
            );
        }

        /** WOOCOMMERCE SOCIAL LOGIN */

        // # Separator WSL
        $this->set_field(
            'separator_wsl',
            __('WooCommerce Social Login', 'peepso-core'),
            'separator'
        );

        $woosl = ' <a href="https://codecanyon.net/item/woocommerce-social-login-wordpress-plugin/8495883?ref=peepsowp" target="_blank">WooCommerce Social Login</a> ';

        // # message WSL
        $this->set_field(
            'message_wsl',
            sprintf(__('Requires %s to be installed and properly configured.', 'peepso-core'), $woosl),
            'message'
        );

        if (class_exists('WC_Social_Login') || (is_plugin_active('woo-social-login/woo-social-login.php')) || defined('WOO_SLG_VERSION')) {
            // # Enable WSL
            $configsettings = ' <a href="' . admin_url('admin.php?page=woo-social-login') . '">' . __('WooCommerce Social Login configuration', 'peepso-core') . '</a> ';
            $this->set_field(
                'message_wsl_config',
                sprintf(__('You may go to %s.', 'peepso-core'), $configsettings),
                'message'
            );
        } else {
            $this->set_field(
                'message_woosl_missing',
                sprintf(__('%s not found! Please install the plugin to see the configuration setting.', 'peepso-core'), $woosl),
                'message'
            );
        }

        $this->set_group(
            'integrations',
            __('Core Integrations', 'peepso-core'),
            __('Settings for built-in integrations between PeepSo and third party plugins.', 'peepso-core')
        );
    }

	private function _group_activity()
	{
		// # Separator Callout
		$this->set_field(
				'separator_general',
				__('General', 'peepso-core'),
				'separator'
		);

		$stream_id_list = apply_filters('peepso_stream_id_list', array());

        if(count($stream_id_list) > 1) {

            $desc = array();
            $options = array();

            foreach($stream_id_list as $stream_id=>$stream_meta) {
                $options[$stream_id] = $stream_meta['label'];
                $desc[]= $stream_meta['label'] . ': '. $stream_meta['desc'];
            }

            $this->args('descript', implode('<br/>', $desc));

            $this->args('options', $options);
            $this->set_field(
                'stream_id_default',
                __('Default stream filter', 'peepso-core'),
                'select'
            );
        }

        // # Default privacy
        $privacy = PeepSoPrivacy::get_instance();
        $privacy_settings = apply_filters('peepso_privacy_access_levels', $privacy->get_access_settings());

        $options = array();

        foreach($privacy_settings as $key => $value) {
            $options[$key] = $value['label'];
        }

        $this->args('options', $options);
        $this->args('descript',__('Defines the default starting privacy level for new posts. Users can change it, and the postbox will always remember their last choice.','peepso-core'));

        $this->set_field(
            'activity_privacy_default',
            __('Default post privacy', 'peepso-core'),
            'select'
        );

		// # Maximum size of Post
		$this->args('validation', array('required', 'numeric','minval:500'));
		$this->args('int', TRUE);

		$this->set_field(
			'site_status_limit',
			__('Maximum size of Post', 'peepso-core'),
			'text'
		);

		// # Open Links In New Tab
		$this->set_field(
				'site_activity_open_links_in_new_tab',
				__('Open links in new tab', 'peepso-core'),
				'yesno_switch'
		);

		// # Hide Activity Stream From Guests
		$this->set_field(
				'site_activity_hide_stream_from_guest',
				__('Hide Activity Stream from Non-logged in Users', 'peepso-core'),
				'yesno_switch'
		);

		// # Enable Repost
		$this->set_field(
			'site_repost_enable',
			__('Enable Repost', 'peepso-core'),
			'yesno_switch'
		);

		// # Enable Repost
		$this->set_field(
			'moods_enable',
			__('Enable Moods', 'peepso-core'),
			'yesno_switch'
		);

		// # Enable Repost
		$this->set_field(
			'tags_enable',
			__('Enable Tags', 'peepso-core'),
			'yesno_switch'
		);

		// # Enable Repost
		$this->set_field(
			'location_enable',
			__('Enable Location', 'peepso-core'),
			'yesno_switch'
		);

		$stream_config = apply_filters('peepso_activity_stream_config', array());

		if(count($stream_config) > 0 ) {

			foreach ($stream_config as $option) {
				if(isset($option['descript'])) {
					$this->args('descript', $option['descript']);
				}
				if(isset($option['int'])) {
					$this->args('int', $option['int']);
				}
				if(isset($option['default'])) {
					$this->args('default', $option['default']);
				}

				$this->set_field($option['name'], $option['label'], $option['type']);
			}
		}

		// # Separator Comments
		$this->set_field(
				'separator_comments',
				__('Comments', 'peepso-core'),
				'separator'
		);

		// # Number Of Comments To Display
		$this->args('validation', array('required', 'numeric'));

		$this->set_field(
			'site_activity_comments',
			__('Number of Comments to display', 'peepso-core'),
			'text'
		);

		// Show comments in batches
		$this->args('validation', array('required', 'numeric'));
		$this->args('int', TRUE);
		$this->set_field(
			'activity_comments_batch',
			__('Show X more comments', 'peepso-core'),
			'text'
		);

		/* READMORE */

		// # Separator Readmore
		$this->set_field(
				'separator_readmore',
				__('Read more', 'peepso-core'),
				'separator'
		);

		// # Show Read More After N Characters
		$this->args('default', 1000);
		$this->args('validation', array('required', 'numeric'));

		$this->set_field(
			'site_activity_readmore',
			__("Show 'read more' after: [n] characters", 'peepso-core'),
			'text'
		);


		// # Redirect To Single Post View
		$this->args('default', 2000);
		$this->args('validation', array('required', 'numeric'));

		$this->set_field(
			'site_activity_readmore_single',
			__('Redirect to single post view when post is longer than: [n] characters', 'peepso-core'),
			'text'
		);

		// # Separator Profile
		$this->set_field(
				'separator_profile',
				__('Profile Posts', 'peepso-core'),
				'separator'
		);

		// # Who can post on "my profile" page
		$privacy = PeepSoPrivacy::get_instance();
		$privacy_settings = apply_filters('peepso_privacy_access_levels', $privacy->get_access_settings());

		$options = array();

		foreach($privacy_settings as $key => $value) {
			$options[$key] = $value['label'];
		}

		// Remove site guests & rename "only me"
		unset($options[PeepSo::ACCESS_PUBLIC]);
		$options[PeepSo::ACCESS_PRIVATE] .= __(' (profile owner)', 'peepso-core');

		$this->args('options', $options);

		$this->set_field(
				'site_profile_posts',
				__('Who can post on "my profile" page', 'peepso-core'),
				'select'
		);

		$this->args('default', 1);
		$this->set_field(
				'site_profile_posts_override',
				__('Let users override this setting', 'peepso-core'),
				'yesno_switch'
		);

        // # Separator Reporting
        $this->set_field(
            'separator_reporting',
            __('Reporting', 'peepso-core'),
            'separator'
        );
        // # Enable Reporting
        $this->args('children',array('site_reporting_types'));
        $this->set_field(
            'site_reporting_enable',
            __('Enable Reporting', 'peepso-core'),
            'yesno_switch'
        );

        // # Predefined  Text
        $this->args('raw', TRUE);
        $this->args('multiple', TRUE);

        $this->set_field(
            'site_reporting_types',
            __('Predefined Text (Separated by a New Line)', 'peepso-core'),
            'textarea'
        );

		// Build Group
		$this->set_group(
			'activity',
			__('Activity', 'peepso-core')
		);
	}

	private function _group_license()
	{
        $this->set_field(
            'bundle',
            __('I have an Ultimate Bundle license', 'peepso-core'),
            'yesno_switch'
        );

        $this->set_field(
            'bundle_license',
            __('Ultimate Bundle License Key','peepso-core'),
            'text'
        );

        if(isset($_GET['debug'])) {
            delete_transient('peepso_config_licenses_bundle');
        }

        $bundle = get_transient('peepso_config_licenses_bundle');

        if (!strlen($bundle)) {
            $url = PeepSoAdmin::PEEPSO_URL . '/peepsotools-integration-json/peepso_config_licenses_bundle.html';

            // Attempt contact with PeepSo.com without sslverify
            $resp = wp_remote_get(add_query_arg(array(), $url), array('timeout' => 10, 'sslverify' => FALSE));

            // In some cases sslverify is needed
            if (is_wp_error($resp)) {
                $resp = wp_remote_get(add_query_arg(array(), $url), array('timeout' => 10, 'sslverify' => TRUE));
            }

            if (is_wp_error($resp)) {

            } else {
                $bundle = $resp['body'];
                set_transient('peepso_config_licenses_bundle', $bundle, 3600 * 24);
            }
        }

        $this->set_field(
            'bundle_message',
            $bundle,
            'message'
        );

		// Get all licensed PeepSo products
		$products = apply_filters('peepso_license_config', array());

		if (count($products)) {

            $new_products = array();
            foreach ($products as $prod) {

                $key = $prod['plugin_name'];

                if (strstr($prod['plugin_name'], ':')) {
                    $name = explode(':', $prod['plugin_name']);
                    $prod['cat'] = $name[0];
                    $prod['plugin_name'] = $name[1];
                }

                $new_products[$key] = $prod;
            }

            ksort($new_products);

            // Loop through the list and build fields
            $prev_cat = NULL;
            foreach ($new_products as $prod) {

                if (isset($prod['cat']) && $prev_cat != $prod['cat']) {
                    $this->set_field(
                        'cat_' . $prod['cat'],
                        $prod['cat'],
                        'separator'
                    );

                    $prev_cat = $prod['cat'];
                }
                // label contains some extra HTML for  license checking AJAX to hook into
                $label = $prod['plugin_name'];
                $label .= ' <small style=color:#cccccc>';
                $label .= $prod['plugin_version'] . '</small>';
                $label .= ' <span class="license_status_check" id="' . $prod['plugin_slug'] . '" data-plugin-name="' . $prod['plugin_edd'] . '"><img src="images/loading.gif"></span>';

                $this->set_field(
                    'site_license_' . $prod['plugin_slug'],
                    $label,
                    'text'
                );
            }
        }

		// Build Group
		$this->set_group(
			'license',
			__('License Key Configuration', 'peepso-core'),
			__('This is where you configure the license keys for each PeepSo add-on. You can find your license numbers on <a target="_blank" href="http://peepso.com/my-account/">My Orders</a> page. Please copy them here and click SAVE at the bottom of this page.', 'peepso-core')
            .' '  . sprintf(__('We are detecting %s as your  install URL. Please make sure your "supported domain" is configured properly.','peepso-core'), str_ireplace(array('http://','https://'), '', home_url()))
            . '<br><br><b>'
            .__('If some licenses are not validating, please make sure to click the SAVE button.','peepso-core')
            .' </b><br/>'
            .__('If that does not help, please contact Support.','peepso-core')

		);
	}
}

// EOF
