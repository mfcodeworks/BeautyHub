<?php
$PeepSoForm = PeepSoForm::get_instance();
$PeepSoRegister = PeepSoRegister::get_instance();
?>
<div class="peepso">
	<section id="mainbody" class="ps-page ps-page--register">

		<section id="component" role="article" class="ps-clearfix">
			<div class="ps-page-register cRegister">
				<?php do_action('peepso_before_registration_form');?>

				<div class="ps-register-form cprofile-edit">
					<?php if (!empty($error)) { ?>
						<div class="ps-alert ps-alert-danger"><?php _e('Error: ', 'peepso-core'); echo $error; ?></div>
					<?php } ?>
					<?php $PeepSoForm->render($PeepSoRegister->register_form()); ?>
				</div>
			</div><!--end cRegister-->
		</section><!--end component-->

		<?php do_action('peepso_after_registration_form'); ?>

	</section><!--end mainbody-->
</div><!--end row-->

<script>

// show terms and condition dialog
function show_terms() {
    var inst = pswindow.show('<?php _e('Terms and Conditions', 'peepso-core'); ?>', peepsoregister.terms ),
        elem = inst.$container.find('.ps-dialog');

    elem.addClass('ps-dialog-full');
    ps_observer.add_filter('pswindow_close', function() {
        elem.removeClass('ps-dialog-full');
    }, 10, 1 );
}

function show_privacy() {
    var inst = pswindow.show('<?php _e('Privacy Policy', 'peepso-core'); ?>', peepsoregister.privacy ),
        elem = inst.$container.find('.ps-dialog');

    elem.addClass('ps-dialog-full');
    ps_observer.add_filter('pswindow_close', function() {
        elem.removeClass('ps-dialog-full');
    }, 10, 1 );
}

</script>
