<?php
$PeepSoActivity = PeepSoActivity::get_instance();
?>
<div class="ps-stream-repost">
	<div class="ps-stream-attachment">
		<div class="ps-stream-quote"><?php $PeepSoActivity->content(); ?></div>
		<div class="cstream-attachments ps-clearfix">
			<?php $PeepSoActivity->post_attachment(); ?>
		</div>
	</div>
</div>