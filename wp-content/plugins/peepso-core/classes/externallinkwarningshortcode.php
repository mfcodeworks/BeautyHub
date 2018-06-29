<?php

class PeepSoExternalLinkWarningShortcode
{
	public function __construct()
	{
		add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));
	}

	/**
	 * Enqueues the scripts used in this shortcode only.
	 */
	public function enqueue_scripts()
	{

	}

	/**
	 * Displays the member search page.
	 */
	public function do_shortcode()
	{
	    PeepSo::reset_query();
		PeepSo::set_current_shortcode('peepso_external_link_warning');

		ob_start();
		echo PeepSoTemplate::get_before_markup();
        PeepSoTemplate::exec_template('general', 'external-link-warning');
		echo PeepSoTemplate::get_after_markup();

		return ob_get_clean();
	}
}

// EOF
