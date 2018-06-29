<?php

class PeepSoAdminRequestData
{
	/** 
	 * DIsplays the table for managing the Request data queue.
	 */
	public static function administration()
	{
		if (isset($_GET['action']) && 'process-request-data' === $_GET['action']
			&& check_admin_referer('process-request-data-nonce')) {
			PeepSoGdpr::process_export_data();
			PeepSoGdpr::process_cleanup_data();
			PeepSo::redirect(admin_url('admin.php?page=peepso-gdpr-request-data'));
		}

		$oPeepSoListTable = new PeepSoGdprListTable();
		$oPeepSoListTable->prepare_items();

		#echo "<div id='peepso' class='wrap'>";
		PeepSoAdmin::admin_header(__('Request Data Queue (BETA)', 'peepso-core'));

		echo '<form id="form-request-data" method="post">';
		wp_nonce_field('bulk-action', 'request-data-nonce');
		$oPeepSoListTable->display();
		echo '</form>';
		echo '<p>';
		_e('Ready : Request data process is ready to execute by cron.', 'peepso-core');
		echo "<br>";
		_e('Processing: Process generating user data is inprogress.', 'peepso-core');
		echo "<br>";
		_e('Success : Process generating user data is complete and ready to download by user.', 'peepso-core');
		echo "<br>";
		_e('Retry : Generate user data is failed and system retry the process.', 'peepso-core');
		echo "<br>";
		_e('Failed: Process generating user data is failed.', 'peepso-core');
		echo '</p>';
		#echo "</div>";
	}
}