<?php

/****** ENABLED & VALIDATION ******/

$PeepSoInput = new PeepSoInput();
$url = $PeepSoInput->val('url');

// if the feature is disabled or the URL passed is invalid
if(0==PeepSo::get_option('external_link_warning',0) || !filter_var($url, FILTER_VALIDATE_URL)) {
    PeepSo::redirect(PeepSo::get_page('activity'));
}


/***** WHITELIST ******/

// if the URL is whitelisted
$parse = parse_url($url);
$host=trim(str_replace('www.','',strtolower($parse['host'])),' /');

$whitelist = explode("\n", PeepSo::get_option('external_link_whitelist', ''));

if(!is_array($whitelist)) {
    $whitelist = array();
}

// whitelist self
$parse = parse_url(get_site_url());
$self_host=str_replace('www.','',strtolower($parse['host']));
$whitelist[]=$self_host;

$allowed = array();
foreach($whitelist as $whitelist_item) {
    $whitelist_item = trim($whitelist_item);
    #$whitelist_item = trim($whitelist_item,'/');

    if(strlen($whitelist_item)) {
        $allowed[]=$whitelist_item;
    }
}

if(in_array($host, $allowed)) {
    PeepSo::redirect($url);
}

/****** BACK LINK ***/
$back ="javascript:window.history.back();";
if(PeepSo::get_option('site_activity_open_links_in_new_tab')) {
    $back = PeepSo::get_page('activity');
}
/****** RENDER ******/

$url_link = "<a href=\"$url\">$url</a>";

?>

<div class="ps-redirect__box">
	<div class="ps-redirect__box-body">
		<p><?php echo sprintf(__('The link you just clicked redirects to: <span class="ps-redirect__link">%s</span>', 'peepso-core'), $url_link); ?></p>
		<hr>
		<p><?php echo __('Do you want to continue?', 'peepso-core'); ?></p>
	</div>
	<div class="ps-redirect__box-actions">
		<a role="button" class="ps-btn" href="<?php echo $back;?>"><?php echo __('No, take me back', 'peepso-core'); ?></a>
		<a role="button" class="ps-btn ps-btn-primary" href="<?php echo $url; ?>"
			data-no-hijack="1"><?php echo __('Yes, take me there', 'peepso-core'); ?></a>
	</div>
</div>
