<?php
$PeepSoUser = PeepSoUser::get_instance(PeepSoProfileShortcode::get_instance()->get_view_user_id());
if( get_current_user_id() == $PeepSoUser->get_id()) {

    $current_tab = isset($current_tab) ? $current_tab : 'about';
    ?>
    <div class="ps-tabs__wrapper">
        <div class="ps-tabs ps-tabs--arrows">
            <?php
                foreach($tabs as $key => $tab){
                    ?>
            <div class="ps-tabs__item <?php if ($key == $current_tab) echo "current"; ?>"><a
                        href="<?php echo $tab['link']; ?>"><?php echo $tab['label']; ?></a>
            </div>
                    <?php
                }
            ?>
        </div>
    </div>

    <?php
}