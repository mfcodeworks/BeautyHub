<?php
$PeepSoPrivacy	= PeepSoPrivacy::get_instance();
?>
<div id="repost-dialog">
	<div class="dialog-title">
		<?php _e('Share This Post', 'peepso-core'); ?>
	</div>
	<div class="dialog-content">
		<div class="ps-postbox-input ps-inputbox">
			<textarea id="share-post-box" class="ps-textarea" placeholder="<?php _e('Say what is on your mind...', 'peepso-core'); ?>"></textarea>
		</div>
		<div class="ps-gap"></div>
		<div class="ps-share-status-preview ps-clearfix">
			<div class="ps-dropdown ps-privacy-dropdown ps-stream-privacy ps-js-dropdown ps-js-dropdown--privacy">
				<button class="ps-btn ps-btn-small ps-dropdown__toggle ps-js-dropdown-toggle" data-value="">
					<span class="dropdown-value"><i class="ps-icon-globe"></i></span>
					<span>&#9662;</span>
				</button>
				<input type="hidden" id="repost_acc" name="repost_acc" value="<?php echo PeepSo::ACCESS_PUBLIC;?>" />
				<?php echo $PeepSoPrivacy->render_dropdown(); ?>
			</div>
			<input type="hidden" id="postbox-post-id" name="post_id" value="{post-id}" />
			<div class="ps-gap"></div>
	      	<div class="ps-share-status-inner ps-text--muted">
    	        <span class="ps-share-status-content">
    	        	{post-content}
    	        </span>
	      	</div>
	    </div>
	</div>
	<div class="dialog-action">
		<button type="button" name="rep_cacel" class="ps-btn ps-btn-small ps-button-cancel" onclick="pswindow.hide(); return false;"><?php _e('Cancel', 'peepso-core'); ?></button>
		<button type="button" name="rep_submit" class="ps-btn ps-btn-small ps-button-action" onclick="activity.submit_repost(); return false;"><?php _e('Share', 'peepso-core'); ?></button>
	</div>
</div>
