<?php

class PeepSoError
{
	public function __construct($err_msg, $err_type='error', $err_extra='core', $override_file='', $override_line='')
	{
        if (!PeepSo::get_option('system_enable_logging')) {
            return (FALSE);
        }
        
        $trace = debug_backtrace();
		$caller = $trace[1];

		// Caller function
		$err_func = $caller['function'];
		if (!empty($caller['class'])) {
			$type = '->';
			if (isset($caller['type']) && !empty($caller['type']))
				$type = $caller['type'];
			$err_func = $caller['class'] . $type . $err_func;
		}

		// Caller file
		if(!array_key_exists('file', $caller)) {
			$caller['line']="n/a";
			$caller['file']='hook';
		}

		$code_file = str_replace('\\', '/', $caller['file']);
		$err_file = str_replace('\\', '/', plugin_dir_path(dirname(dirname(__FILE__)))); //), '', $code_file);
		$err_file = str_replace($err_file, '', $code_file);
		$line = $caller['line'];

		$err_file ="$err_file:$line";

		if( strlen($override_file) && strlen($override_line)) {;
			$err_file.= " ($override_file:$override_line)";
		}

        $message =  ""
            . date('H:i:s D Y-m-d') . " - ". "$err_msg\n"
            . " $err_type $err_extra \n"
            . " User " . get_current_user_id() . " @ " . PeepSo::get_ip_address() . "\n"
            . " $err_func @ $err_file\n"

            ."\n";

        $peepso_dir = PeepSo::get_option('site_peepso_dir', WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'peepso');

        error_log ( $message, 3, $peepso_dir.'/peepso.log');
	}
}

// EOF