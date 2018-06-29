<?php

class PeepSoConfigSectionAdvanced extends PeepSoConfigSectionAbstract
{
	public static $css_overrides = array(
		'appearance-avatars-circle',
	);

	// Builds the groups array
	public function register_config_groups()
	{
		$this->context='full';

        if( !isset($_GET['filesystem']) ) {
            $this->_group_emails();
        }

		$this->_group_filesystem();

		if( !isset($_GET['filesystem']) ) {
            $this->_group_uninstall();
            $this->_group_profiles();

			$this->context = 'left';
            $this->_group_gdpr();
            $this->_group_opengraph();
            $this->_group_debug();

			$this->context = 'right';
            $this->_group_performance();
            $this->_group_ajax();
            $this->_group_storage();
            $this->_group_security();
		}

		# @todo #257 $this->config_groups[] = $this->_group_opengraph();
	}

    private function _group_gdpr() {
        $section = 'gdpr_';


        $message = __('The EU General Data Protection Regulation (GDPR, or EUGDPR for short) is a regulation in European Union law on data protection and privacy for all individuals within the European Union. All businesses and websites processing personal information of EU citizens must abide by this law, including the right to be forgotten (data deletion), the right to full data download (export) etc. You can read more about it ', 'peepso-core');
        $message .= '<a href="http://peep.so/gdpr" target="_blank">';
        $message .= __('here', 'peepso');
        $message .= '</a>';

        $this->set_field(
                $section, $message, 'message'
        );

        $this->set_field(
            $section . 'enable',
            __('Enable GDPR Compliance', 'peepso-core'),
            'yesno_switch'
        );

        $args = array(
            'descript' => sprintf(__("It's advised to switch this setting on and setup a server-side cron job. You can use this command: wget %s It can easily run every five minutes.", 'peepso-core'), get_bloginfo('url') . '/?peepso_gdpr_export_data_event'),
            'int' => TRUE,
            'default' => 0,
            'field_wrapper_class' => 'controls col-sm-8',
            'field_label_class' => 'control-label col-sm-4',
        );
        $this->args = $args;
        $this->set_field(
                'gdpr_external_cron', __('External Export Cron Job', 'peepso-core'), 'yesno_switch'
        );

        // # Full HTML
        // # Move to stage 2
        // $this->args('raw', TRUE);
        // $this->args('validation', array('custom'));
        // $this->args('validation_options',
        //     array(
        //     'error_message' => __('Missing variable {data_contents} or {data_title} or {data_name} or {data_sidebar}', 'peepso-core'),
        //     'function' => array($this, 'check_gdpr_template_layout')
        //     )
        // );

        // $this->set_field(
        //     $section . 'personal_data_template_html',
        //     __('Override entire HTML Template', 'peepso-core'),
        //     'textarea'
        // );

        // Build Group
        $this->set_group(
                'gdpr', __('GDPR Compliance (BETA)', 'peepso-core')
        );
    }

	private function _group_profiles() {

	    $tabs = apply_filters('peepso_navigation_profile', array());
	    $tablist='';
	    foreach($tabs as $id=>$tab) {
	        if(in_array($tab,array('stream','about'))) {
                $tablist.="<br>$id";
	            continue;
            }

            $tablist.="<br><strong>$id</strong>";
        }

        $this->args('raw', TRUE);
        $this->args('descript', sprintf(__('One tab name per line. "Stream" and "About" will always be first. Current order: %s', 'peepso-core'), $tablist));

        $this->set_field(
            'profile_tabs_order',
            __('Profile tabs order (beta)', 'peepso-core'),
            'textarea'
        );


        $this->set_group(
            'profiles',
            __('Profiles', 'peepso-core')
        );
    }

	private function _group_filesystem()
	{

		// Message Filesystem
		$this->set_field(
			'system_filesystem_warning',
			__('This setting is to be changed upon very first PeepSo activation or in case of site migration. If changed in any other case it will result in missing content including user avatars, covers, photos etc. (error 404).', 'peepso-core'),
			'warning'
		);

		// Message Filesystem
		$this->set_field(
			'system_filesystem_description',
			__('PeepSo allows users to upload images that are stored on your server. Enter a location where these files are to be stored.<br/>This must be a directory that is writable by your web server and and is accessible via the web. If the directory specified does not exist, it will be created.', 'peepso-core'),
			'message'
		);

		$this->args('class','col-xs-12');
		$this->args('field_wrapper_class','controls col-sm-10');
		$this->args('field_label_class', 'control-label col-sm-2');
		$this->args('default', WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'peepso');

		$this->args('validation', array('required', 'custom'));
		$this->args('validation_options',
			array(
			'error_message' => __('Can not write to directory', 'peepso-core'),
			'function' => array($this, 'check_wp_filesystem')
			)
		);
		// Uploads
		$this->set_field(
			'site_peepso_dir',
			__('Uploads Directory', 'peepso-core'),
			'text'
		);

		$this->set_group(
			'filesystem',
			__('File System', 'peepso-core')
		);
	}

	private function _group_debug()
	{
		// Logging
        $this->args('descript', __('Enabled: various debug information is written to a log file.','peepso-core').'<br/>'.__('This can impact website speed and should ONLY be enabled when someone is debugging PeepSo.', 'peepso-core'));
		$this->set_field(
			'system_enable_logging',
			__('Enable Logging', 'peepso-core'),
			'yesno_switch'
		);

        // FSTVL
        $this->args('descript', __('Strict Version Lock makes sure that it\'s impossible to upgrade PeepSo before all of the child plugins have been updated.','peepso-core').'<br/>'.__('Please DO NOT enable this unless you are having issues with updating PeepSo.', 'peepso-core'));
        $this->set_field(
            'override_fstvl',
            __('Override Strict Version Lock', 'peepso-core'),
            'yesno_switch'
        );

		$this->set_group(
			'advanced_debug',
			__('Maintenance & debugging', 'peepso-core')
		);
	}



    private function _group_performance()
    {
        // Infinite load
        $this->args('descript', __('Disables infinite loading of activities, members lists etc until the users clicks the "load more" button.', 'peepso-core'));
        $this->set_field(
            'loadmore_enable',
            __('Enable "load more:" button', 'peepso-core'),
            'yesno_switch'
        );

        // Avatar size
        $this->args('default', 0);

        $options=array();
        for($i = 0; $i<=50; $i+=10){
            $options[$i]= sprintf(__('Every %d posts', 'peepso-core'), $i);
            if($i == 0) {
                $options[$i] = __('No', 'peepso-core');
            }
        }

        $this->args('options', $options);

        $this->args('descript', __('By default all posts load in an "infinite scroll".','peepso-core').'<br>'.__('You can choose to have a specified batch of posts to loade before showing the "load button" again.','peepso_core'));
        $this->set_field(
            'loadmore_repeat',
            __('Repeat "load more" button?', 'peepso-core'),
            'select'
        );


        // # Disable Maintenance
        $this->args('descript', __('This should be only enabled if you are planning to use an external cron job to process the PeepSo Maintenance scripts.<br/>External cron job is recommended for bigger communities.<br/>Please refer to <a href="http://peep.so/maintenance/" target="_blank">the documentation</a>.', 'peepso-core'));
        $this->set_field(
            'disable_maintenance',
            __('External Maintenance Cron Job', 'peepso-core'),
            'yesno_switch'
        );

        // Build Group
        $this->set_group(
            'performance',
            __('Performance', 'peepso-core')
        );
    }

    private function _group_ajax() {

        // DELAY MIN
        $this->args('descript', __('minutes:seconds - how often the calls are allowed to run if there is a related site activity', 'peepso-core'));
        $this->args('default', 30000);
        $options=array();

        // 00:01, 00:02, 00:03, 00:04
        for($i = 1000; $i<=4000; $i+=1000){
            $options[$i] = $i;
        }
        // 00:05, 00:10, 00:15 ... 00:55
        for($i = 5000; $i<=55000; $i+=5000){
            $options[$i] = $i;
        }

        // 01:00, 01:15, 01:30 ... 05:00
        for($i = 60000; $i<=300000; $i+=15000){
            $options[$i]= $i;
        }

        // Format
        foreach($options as $i) {
            $options[$i] = gmdate("i:s", $i/1000);
        }

        // Default
        $options_min = $options;
        $options_min[5000] = $options_min[5000] . ' ('.__('default', 'peepso-core').')';

        $this->args('options', $options_min);

        $this->set_field(
            'notification_ajax_delay_min',
            __('Active', 'peepso-core'),
            'select'
        );

        // DELAY MAX
        $this->args('descript', __('minutes:seconds - how often the calls should be made, if the related site activity is idle', 'peepso-core'));
        $this->args('default', 5000);
        $options_max=$options;
        $options_max[30000] = $options_max[30000] . ' ('.__('default', 'peepso-core').')';
        unset($options_max[1000]);
        unset($options_max[2000]);
        unset($options_max[3000]);
        unset($options_max[4000]);
        unset($options_max[5000]);


        $this->args('options', $options_max);

        $this->set_field(
            'notification_ajax_delay',
            __('Idle', 'peepso-core'),
            'select'
        );

        // DELAY MULTI
        $this->args('descript', __('If there is no related site activity, how quickly should the intensity shift from minimum/active to maximum/idle', 'peepso-core'));
        $this->args('default', '2.0');
        $options=array(
            '1.5'   => '1.5 x',
            '2.0'   => '2.0 x'. ' ('.__('default', 'peepso-core').')',
            '2.5'   => '2.5 x',
            '3.0'   => '3.0 x',
            '3.5'   => '3.5 x',
            '4.0'   => '4.0 x',
            '4.5'   => '4.5 x',
            '5.0'   => '5.0 x',
        );

        $this->args('options', $options);

        $this->set_field(
            'notification_ajax_delay_multiplier',
            __('Multiplier', 'peepso-core'),
            'select'
        );
        // Build Group
        $this->set_group(
            'ajax',
            __('AJAX Call Intensity', 'peepso-core'),
            __('PeepSo and all its plugins  run various background (AJAX) calls for each user that is logged in.','peepso-core')
            .'<br>'.__('By adjusting the settings below you control how "instant" experience your users are having.','peepso_core')
            .'<br>'.__('<strong>Lower values mean more robust notifications, but also <u>higher server load.</u></strong>','peepso-core')
            .'<br>'.__('<strong><u>Values lower than defaults are not recommended.</u></strong>','peepso-core')
        );

    }
    private function _group_storage()
    {
        // Avatar size
        $this->args('default', 100);

        $options=array();
        for($i = 100; $i<=500; $i+=50){
            $options[$i]= sprintf(__('%d pixels', 'peepso-core'), $i);
            if($i == 100) {
                $options[$i] .= ' ('.__('default', 'peepso-core').')';
            }
        }

        $this->args('options', $options);

        $this->args('descript', __('Bigger images use more storage, but will look better - especially on high resolution screens.','peepso_core'));
        $this->set_field(
            'avatar_size',
            __('Avatar size', 'peepso-core'),
            'select'
        );

        // Avatar quality
        $this->args('default', 75);

        $options=array();
        for($i = 50; $i<=100; $i+=5){
            $options[$i]= sprintf(__('%d%%', 'peepso-core'), $i);
            if($i == 75) {
                $options[$i] .= ' ('.__('default', 'peepso-core').')';
            }
        }

        $this->args('options', $options);

        $this->args('descript', __('Higher quality will use more storage, but the images will look better','peepso_core'));
        $this->set_field(
            'avatar_quality',
            __('Avatar quality', 'peepso-core'),
            'select'
        );

        // Build Group
        $this->set_group(
            'storage',
            __('Storage', 'peepso-core'),
            __('These settings control the dimensions and compression levels, and will only be applied to newly uploaded images.', 'peepso-core')
        );
    }

    private function _group_security()
    {
        // non-SSL embeds
        $this->args('descript', __('Enables non-SSL (http://) link fetching. This can lead to "insecure content" warnings if your site is using SSL','peepso-core'));
        $this->set_field(
            'allow_non_ssl_embed',
            __('Allow non-SSL embeds', 'peepso-core'),
            'yesno_switch'
        );

        // external link warning
        $this->args('descript', __('When enabled, users will be shown a warning page when clicking an external link inside any PeepSo page. The warning page is the one containing peepso_external_link_warning shortcode.','peepso-core'));
        $this->set_field(
            'external_link_warning',
            __('Enable "external link warning" page', 'peepso-core'),
            'yesno_switch'
        );

        // external link whitelist
        $this->args('raw', TRUE);
        $this->args('descript', __('Domains that do not require a warning page, without "www" or "http(s). One domain name per line. Your website is whitelisted by default. ','peepso-core').'<br/>'.__('Example domains:','peepso-core').'<br/>google.com<br/>yahoo.com');

        $this->set_field(
            'external_link_whitelist',
            __('Domain whitelist', 'peepso-core'),
            'textarea'
        );

        // Build Group
        $this->set_group(
            'ajax',
            __('Security', 'peepso-core')
        );
    }

	private function _group_emails()
	{
		// # Email Sender
		$this->args('validation', array('required','validate'));
		$this->args('data', array(
			'rule-min-length' => 1,
			'rule-max-length' => 64,
			'rule-message'    => __('Should be between 1 and 64 characters long.', 'peepso-core')
		));


		$this->set_field(
			'site_emails_sender',
			__('Email sender', 'peepso-core'),
			'text'
		);

		// # Admin Email
		$this->args('validation', array('required','validate'));
		$this->args('data', array(
			'rule-type'    => 'email',
			'rule-message' => __('Email format is invalid.', 'peepso-core')
		));
		$this->set_field(
			'site_emails_admin_email',
			__('Admin Email', 'peepso-core'),
			'text'
		);


        // # Disable MailQueue
        $this->args('descript', __('This should be only enabled if you are planning to use an external cron job to process the PeepSo mail queue.<br/>External cron job is recommended for bigger communities.<br/>Please refer to <a href="http://peep.so/mailqueue/" target="_blank">the documentation</a>.', 'peepso-core'));
        $this->set_field(
            'disable_mailqueue',
            __('External Mail Queue Cron Job', 'peepso-core'),
            'yesno_switch'
        );

        // # Don't subscribe new members to emails
        $this->args('descript', __('All new members will have their e-mail notifications disabled by default', 'peepso-core'));
        $this->set_field(
            'new_member_disable_all_email_notifications',
            __('Don\'t subscribe new members to any e-mail notifications', 'peepso-core'),
            'yesno_switch'
        );

        $this->set_field(
            'emails_override_full_separator',
            __('Customize entire e-mail layout', 'peepso-core'),
            'separator'
        );

        $this->set_field(
            'emails_override_full_msg',
            __('Text, HTML and inline CSS only (no PHP or shortcodes). Leave empty for the default layout.','peepso-core')
            . '<br/><br/>'
            . sprintf(__('<a href="%s" target="_blank">Click here</a> after saving to test your changes.','peepso-core'), admin_url('admin-ajax.php?action=peepso_preview_email'))
            .'<br/><br/>'.
            __('Available variables: <br/>{email_contents} - e-mail contents <font color="red">*</font><br/>{unsubscribeurl} - URL of the user notification preferences <font color="red">*</font><br/>{currentuserfullname} - full name of the recicpient<br>{useremail} - e-mail of the recipient<br/>{sitename} - the name of your site<br/>{siteurl} - the URL of your site<br/><br/><font color="red">*</font>) is required', 'peepso-core'),
            'message'
        );

        // # Full HTML
        $this->args('raw', TRUE);
        $this->args('validation', array('custom'));
        $this->args('validation_options',
            array(
            'error_message' => __('Missing variable {emails_contents} or {unsubscribeurl}', 'peepso-core'),
            'function' => array($this, 'check_emails_layout')
            )
        );

        $this->set_field(
            'emails_override_entire_html',
            __('Override entire HTML', 'peepso-core'),
            'textarea'
        );

		// Build Group
		$this->set_group(
			'emails',
			__('Emails', 'peepso-core')
		);
	}

	private function _group_uninstall()
	{
		// # Delete Posts and Comments
		$this->args('field_wrapper_class', 'controls col-sm-8 danger');

		$this->set_field(
			'delete_post_data',
			__('Delete Post and Comment data', 'peepso-core'),
			'yesno_switch'
		);

		// # Delete All Data And Settings
		$this->args('field_wrapper_class', 'controls col-sm-8 danger');

		$this->set_field(
			'delete_on_deactivate',
			__('Delete all data and settings', 'peepso-core'),
			'yesno_switch'
		);

		// Build Group
		$summary= __('When set to "YES", all <em>PeepSo</em> data will be deleted upon plugin Uninstall (but not Deactivation).<br/>Once deleted, <u>all data is lost</u> and cannot be recovered.', 'peepso-core');
		$this->args('summary', $summary);

		$this->set_group(
			'peepso_uninstall',
			__('PeepSo Uninstall', 'peepso-core'),
			__('Control behavior of PeepSo when uninstalling / deactivating', 'peepso-core')
		);
	}

	private function _group_opengraph()
	{
		$this->set_field(
			'opengraph_enable',
			__('Enable Open Graph', 'peepso-core'),
			'yesno_switch'
		);

		// Open Graph Title
		$this->set_field(
			'opengraph_title',
			__('Title (og:title)', 'peepso-core'),
			'text'
		);

		// Open Graph Title
		$this->set_field(
			'opengraph_description',
			__('Description (og:description)', 'peepso-core'),
			'textarea'
		);

		// Open Graph Image
		$this->set_field(
			'opengraph_image',
			__('Image (og:image)', 'peepso-core'),
			'text'
		);

        // Disable "?" in Profile / Group / Activity URLs
        $this->args('descript', __('This feature is currently in BETA and should be considered experimental. It will remove "?" from certain PeepSo URLs, such as "profile/?username/about".', 'peepso-core'));
        $this->set_field(
            'disable_questionmark_urls',
            __('Enable SEO Friendly links', 'peepso-core'),
            'yesno_switch'
        );

        $frontpage = get_post(get_option('page_on_front'));

        if (1 == PeepSo::get_option('disable_questionmark_urls', 0) && 'page' == get_option( 'show_on_front' ) && has_shortcode($frontpage->post_content, 'peepso_activity')) {
            $this->set_field(
                'activity_homepage_warning',
                __('You are currently using [peepso_activity] as your home page. Because of that, single activity URLs will have to contain "?" no matter what the above setting is.', 'peepso-core'),
                'message'
            );
        }


        // PeepSo::reset_query()
        $this->args('descript', __('This advanced feature causes PeepSo pages to override the global WP_Query for better SEO.','peepso-core').'<br>'.__('This can interfere with SEO plugins, so use with caution.', 'peepso-core'));
        $this->set_field(
            'force_reset_query',
            __('PeepSo can reset WP_Query', 'peepso-core'),
            'yesno_switch'
        );


		$this->set_group(
			'opengraph',
			__('SEO & Open Graph', 'peepso-core'),
			__("The Open Graph protocol enables sites shared for example to Facebook carry information that render shared URLs in a great way. Having a photo, title and description. You can learn more about it in our documentation. Just search for 'Open Graph'.", 'peepso-core')
		);
	}


	/**
	 * Checks if the directory has been created, if not use WP_Filesystem to create the directories.
	 * @param  string $value The peepso upload directory
	 * @return boolean
	 */
	public function check_wp_filesystem($value)
	{
		$form_fields = array('site_peepso_dir');
		$url = wp_nonce_url('admin.php?page=peepso_config&tab=advanced', 'peepso-config-nonce', 'peepso-config-nonce');

		if (FALSE === ($creds = request_filesystem_credentials($url, '', false, false, $form_fields))) {
			return FALSE;
		}

		// now we have some credentials, try to get the wp_filesystem running
		if (!WP_Filesystem($creds)) {
			// our credentials were no good, ask the user for them again
			request_filesystem_credentials($url, '', true, false, $form_fields);
			return FALSE;
		}

		global $wp_filesystem;

		if (!$wp_filesystem->is_dir($value) || !$wp_filesystem->is_dir($value . DIRECTORY_SEPARATOR . 'users')) {
			$wp_filesystem->mkdir($value);
			$wp_filesystem->mkdir($value . DIRECTORY_SEPARATOR . 'users');
			return TRUE;
		}

		return $wp_filesystem->is_writable($value);
	}

    public function check_emails_layout($value) 
    {
        if (!empty($value)) {
            if (strpos($value, 'email_contents') === false || strpos($value, 'unsubscribeurl') === false) {
                return FALSE;
            }
        }

        return TRUE;
    }



    public function check_gdpr_template_layout($value) 
    {
        if (!empty($value)) {
            if (strpos($value, 'data_contents') === false || strpos($value, 'data_sidebar') === false || strpos($value, 'data_name') === false || strpos($value, 'data_title') === false) {
                return FALSE;
            }
        }

        return TRUE;
    }

}