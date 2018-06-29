<?php
$PeepSoProfile = PeepSoProfile::get_instance();
$PeepSoUser = $PeepSoProfile->user;

    foreach ($links as $id=>$link) {
            ?><a class="ps-focus__menu-item <?php if ($current == $id) { echo ' current '; } ?>" href="<?php echo $PeepSoUser->get_profileurl() . $link['href'];?>">
                    <i class="<?php echo $link['icon'];?>"></i>
                    <span><?php echo $link['label'];?></span>
            </a><?php
    }

?>
<a href="javascript:" class="ps-focus__menu-item ps-js-focus-link-more" style="display:none">
    <i class="ps-icon-caret-down"></i>
    <span>
        <span><?php echo __('More', 'peepso-core'); ?></span>
        <span class="ps-icon-caret-down"></span>
    </span>
</a>
<div class="ps-focus__menu-more">
    <div class="ps-dropdown__menu ps-js-focus-link-dropdown" style="left:auto; right:0"></div>
</div>
