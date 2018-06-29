<?php

    $recaptchaEnabled = PeepSo::get_option('site_registration_recaptcha_enable', 0);
    $recaptchaClass = $recaptchaEnabled ? ' ps-js-recaptcha' : '';

?><div class="peepso">
    <section id="mainbody" class="ps-page">
        <section id="component" role="article" class="ps-clearfix">
            <div id="peepso" class="on-socialize ltr cRegister">
            	<?php
            	if(isset($error) && !in_array($error->get_error_code(), array('bad_form', 'expired_key', 'invalid_key'))) {
				?>
                <h4><?php _e('Pick a New Password', 'peepso-core'); ?></h4>
                <?php } ?>

                <div class="ps-register-recover">

                    <?php
                    if (isset($error) && !empty($error)) {
                        PeepSoGeneral::get_instance()->show_error($error);
                    }

                    if(isset($error) && !in_array($error->get_error_code(), array('bad_form', 'expired_key', 'invalid_key'))) {
                    ?>
                    <form id="recoverpasswordform" name="recoverpasswordform" action="<?php PeepSo::get_page('recover'); ?>?submit" method="post" class="ps-form">
                    	<input type="hidden" id="user_login" name="rp_login" value="<?php echo esc_attr( $attributes['login'] ); ?>" autocomplete="off" />
        				<input type="hidden" name="rp_key" value="<?php echo esc_attr( $attributes['key'] ); ?>" />
                        <input type="hidden" name="task" value="-reset-password" />
                        <input type="hidden" name="-form-id" value="<?php echo wp_create_nonce('peepso-reset-password-form'); ?>" />
                        <div class="ps-form-row">
                            <div class="ps-form-group">
                                <label for="email" class="ps-form-label"><?php _e('New Password:', 'peepso-core'); ?>
                                    <span class="required-sign">&nbsp;*<span></span></span>
                                </label>
                                <div class="ps-form-field">
                                    <input class="ps-input" type="password" name="pass1" placeholder="<?php _e('New Password', 'peepso-core'); ?>" />
                                    <p class="ps-form__label-desc lbl-descript"><?php _e('Enter your desired password', 'peepso-core'); ?></p>
                                    <ul class="ps-form-error" style="display:none"></ul>
                                </div>
                            </div>

                            <div class="ps-form-group">
                                <label for="email" class="ps-form-label"><?php _e('Repeat new password:', 'peepso-core'); ?>
                                    <span class="required-sign">&nbsp;*<span></span></span>
                                </label>
                                <div class="ps-form-field">
                                    <input class="ps-input" type="password" name="pass2" placeholder="<?php _e('Repeat new password', 'peepso-core'); ?>" />
                                    <p class="ps-form__label-desc lbl-descript"><?php _e('Please re-enter your password', 'peepso-core'); ?></p>
                                    <ul class="ps-form-error" style="display:none"></ul>
                                </div>
                            </div>

                            <div class="ps-form-group submitel">
                                <button type="submit" name="submit-recover"
                                    class="ps-btn ps-btn-primary<?php echo $recaptchaClass; ?>">
                                    <?php _e('Submit', 'peepso-core'); ?>
                                    <img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt=""
                                        style="display:none" />
                                </button>
                            </div>
                        </div>
                    </form>
                    <div class="ps-gap"></div>
                    <p class="description"><?php echo wp_get_password_hint(); ?></p>
                    <?php
                    }
                    ?>

                    <div class="ps-gap"></div>
                    <a href="<?php echo get_bloginfo('wpurl'); ?>"><?php _e('Back to Home', 'peepso-core'); ?></a>
                </div>
            </div><!--end peepso-->
        </section><!--end component-->
    </section><!--end mainbody-->
</div><!--end row-->
