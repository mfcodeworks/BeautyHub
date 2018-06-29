<?php
	$PeepSoProfile=PeepSoProfile::get_instance();
    $PeepSoUser = $PeepSoProfile->user;

	if (FALSE === $PeepSoUser->has_cover()) {
		$reposition_style = 'display:none;';
		$cover_class = 'default';
	} else {
		$reposition_style = '';
		$cover_class = 'has-cover';
	}

	$is_profile_segment = isset($current) ? TRUE : FALSE;
?>
<div class="ps-focus js-focus <?php if($is_profile_segment && 0 == PeepSo::get_option('always_full_cover', 0)) { echo 'ps-focus-mini'; } ?> ps-js-focus ps-js-focus--<?php echo $PeepSoUser->get_id() ?>">
	<div class="ps-focus-cover js-focus-cover ">
		<div class="ps-focus-image">
			<img id="<?php echo $PeepSoUser->get_id(); ?>"
				data-cover-context="profile"
				class="focusbox-image cover-image <?php echo $cover_class; ?>"
				src="<?php echo $PeepSoUser->get_cover(); ?>"
				alt="<?php echo $PeepSoUser->get_fullname(); ?> cover photo"
				style="<?php echo $PeepSoUser->get_cover_position(); ?>"
			/>
		</div>

		<div class="ps-focus-image-mobile" style="background:url(<?php echo $PeepSoUser->get_cover(); ?>) no-repeat center center;">
		</div>

		<div class="js-focus-gradient" data-cover-context="profile" data-cover-type="cover"></div>

		<?php if ($PeepSoProfile->can_edit() && (!$is_profile_segment || 1 == PeepSo::get_option('always_full_cover', 0))) { ?>

		<?php wp_nonce_field('profile-photo', '_photononce'); ?>
		<!-- Cover options dropdown -->
		<div class="ps-focus-options ps-dropdown ps-dropdown-focus ps-js-dropdown">
			<a href="javascript:" class="ps-dropdown__toggle ps-js-dropdown-toggle">
				<span class="ps-icon-camera"></span>
			</a>
			<div class="ps-dropdown__menu ps-js-dropdown-menu">
				<a href="javascript:" class="ps-reposition-cover" id="profile-reposition-cover" style="<?php echo $reposition_style; ?>"
						data-cover-context="profile" onclick="profile.reposition_cover();">
					<i class="ps-icon-move"></i>
					<?php _e('Reposition', 'peepso-core'); ?>
				</a>
				<a href="javascript:" data-cover-context="profile" onclick="profile.show_cover_dialog();">
					<i class="ps-icon-cog"></i>
					<?php _e('Modify', 'peepso-core'); ?>
				</a>
			</div>
		</div>
		<!-- Reposition cover - buttons -->
		<div class="ps-focus-change js-focus-change-cover" data-cover-type="cover">
			<div class="reposition-cover-actions" style="display: none;">
				<a href="javascript:void(0)" class="ps-btn" onclick="profile.cancel_reposition_cover();"><?php _e('Cancel', 'peepso-core'); ?></a>
				<a href="javascript:void(0)" class="ps-btn ps-btn-primary" onclick="profile.save_reposition_cover();"><?php _e('Save', 'peepso-core'); ?></a>
			</div>
			<div class="ps-reposition-loading" style="display: none;">
				<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>">
				<div> </div>
			</div>
		</div>
		<?php } ?>

		<!-- Focus Title , Avatar, Add as friend button -->
		<div class="ps-focus-header js-focus-content">
			<div class="ps-avatar-focus js-focus-avatar ps-js-focus-avatar">
				<img src="<?php echo $PeepSoUser->get_avatar('full'); ?>" alt="<?php echo $PeepSoUser->get_fullname(); ?> avatar">
				<?php if ((1 != PeepSo::get_option('avatars_wordpress_only', 0)) && $PeepSoProfile->can_edit()) { ?>
					<?php wp_nonce_field('profile-photo', '_photononce'); ?>

					<span class="ps-avatar-change js-focus-avatar-option">
						<a href="#" class="ps-js-focus-avatar-button">
							<i class="ps-icon-camera"></i>
						</a>
					</span>
				<?php } ?>
				<!-- Online status -->
				<?php if($PeepSoUser->get_online_status()) { ?><?php PeepSoTemplate::exec_template('profile', 'online', array('PeepSoUser'=>$PeepSoUser, 'class'=>'ps-user__status--focus')); ?><?php } ?>
			</div>
			<div class="ps-focus-title">
				<span>
				<!-- Online status -->
				<?php if($PeepSoUser->get_online_status()) { ?><?php PeepSoTemplate::exec_template('profile', 'online', array('PeepSoUser'=>$PeepSoUser)); ?><?php } ?>
					<?php
					//[peepso]_[action]_[WHICH_PLUGIN]_[WHERE]_[WHAT]_[BEFORE/AFTER]
						do_action('peepso_action_render_user_name_before', $PeepSoUser->get_id());

						echo $PeepSoUser->get_fullname();

						//[peepso]_[action]_[WHICH_PLUGIN]_[WHERE]_[WHAT]_[BEFORE/AFTER]
						do_action('peepso_action_render_user_name_after', $PeepSoUser->get_id());
					?>
				</span>
				<br/>
				<?php
				if(!$is_profile_segment || 1 == PeepSo::get_option('always_full_cover', 0)) {
					do_action('peepso_profile_cover_full_after_name', $PeepSoUser->get_id());
				}
				?>
			</div>
			<div class="ps-focus-actions">
				<?php $PeepSoProfile->profile_actions(); ?>
			</div>
		</div>
	</div><!-- .js-focus-cover -->

	<?php
	if(!$is_profile_segment)
	{
		$current='stream';
	}
	?>

	<!-- Profile actions - mobile -->
	<div class="ps-focus-actions-mobile"><?php $PeepSoProfile->profile_actions(); ?></div>

	<div class="ps-focus__footer">
		<div class="ps-focus__menu profile-interactions ps-js-focus-links">
			<?php echo $PeepSoProfile->profile_navigation(array('current'=>$current)); ?>
		</div>

		<div class="ps-focus__interactions profile-interactions profile-social ps-js-focus-interactions">
			<?php $PeepSoProfile->interactions(); ?>
		</div>
	</div>

	<div class="js-focus-actions">
	</div><!-- .js-focus-actions -->
</div><!-- .js-focus -->
