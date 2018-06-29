<?php
$user = PeepSoUser::get_instance(PeepSoProfileShortcode::get_instance()->get_view_user_id());

$can_edit = FALSE;
if($user->get_id() == get_current_user_id() || current_user_can('edit_users')) {
    $can_edit = TRUE;
}

if(!$can_edit) {
    PeepSo::redirect(PeepSo::get_page('activity'));
} else {

    $PeepSoProfile = PeepSoProfile::get_instance();
    ?>

    <div class="peepso ps-page-profile">
        <?php PeepSoTemplate::exec_template('general', 'navbar'); ?>

        <?php PeepSoTemplate::exec_template('profile', 'focus', array('current'=>'about')); ?>

        <section id="mainbody" class="ps-page-unstyled">
            <section id="component" role="article" class="ps-clearfix">


                <?php if($can_edit) { PeepSoTemplate::exec_template('profile', 'profile-about-tabs', array('tabs' => $tabs, 'current_tab'=>'account'));} ?>

                <div class="ps-list--column cfield-list creset-list ps-js-profile-list">

                    <div class="ps-form-container">
                        <?php if (strlen($PeepSoProfile->edit_form_message())) { ?>
                            <div class="ps-alert ps-alert-success">
                                <?php echo $PeepSoProfile->edit_form_message(); ?>
                            </div>
                        <?php } ?>
                        <div class="ps-form-legend">
                            <?php _e('Basic Information', 'peepso-core'); ?>
                        </div>
                        <?php $PeepSoProfile->edit_form(); ?>
                        <div class="ps-form-group">
                            <label for=""></label>
                            <span class="ps-form-helper"><?php _e('Fields marked with an asterisk (<span class="required-sign">*</span>) are required.', 'peepso-core'); ?></span>
                        </div>
                    </div>
                    <?php if(PeepSo::get_option('site_registration_allowdelete', 0)) { ?>
                    <hr>
                    <div class="ps-form-container">
                        <div class="ps-form-legend">
                            <?php _e('Profile Deletion', 'peepso-core'); ?>
                            
                        </div>
                        <p><?php _e('Deleting your account will disable your profile and remove your name and photo from most things you\'ve shared. Some information may still be visible to others, such as your name in their friends list and messages you sent.', 'peepso-core'); ?></p>
                        <?php $PeepSoProfile->delete_form(); ?>
                    </div> <!-- .clayout -->
                    <?php } ?>
                    <?php if(PeepSo::get_option('gdpr_enable', 1)) { ?>
                    <hr>
                    <div class="ps-form-container">
                        <div class="ps-form-legend">
                            <?php _e('Export and Download A Copy Of Your Community Information (BETA)', 'peepso-core'); ?>
                        </div>
                        <?php $PeepSoProfile->request_data_form(); ?>
                    </div> <!-- .clayout -->
                    <?php } ?>
                </div>
            </section><!--end component-->
        </section><!--end mainbody-->
    </div><!--end row-->
<?php }