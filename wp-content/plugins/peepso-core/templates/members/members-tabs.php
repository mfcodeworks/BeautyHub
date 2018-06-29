<?php if(get_current_user_id()) { ?>

<div class="ps-tabs__wrapper">
    <div class="ps-tabs ps-tabs--arrows">
        <div class="ps-tabs__item <?php if (!isset($tab)) echo "current"; ?>"><a
                    href="<?php echo PeepSo::get_page('members'); ?>"><?php _e('Members', 'peepso-core'); ?></a>
        </div>
        <div class="ps-tabs__item <?php if (isset($tab) && 'blocked' == $tab) echo "current"; ?>"><a
                    href="<?php echo PeepSo::get_page('members').'blocked/'; ?>"><?php _e('Blocked', 'peepso-core'); ?></a>
        </div>
    </div>
</div>

<?php }