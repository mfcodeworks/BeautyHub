<div class="ps-stream__filters">
<?php

/** STREAM ID **/

$stream_id = $user_stream_filters['stream_id'];
if(count($stream_id_list)) {
    $selected = $stream_id_list[$stream_id];

    ?>
    <input type="hidden" id="peepso_stream_id" value="<?php echo $stream_id; ?>"/>
    <?php if (count($stream_id_list) > 1) { ?>

    <span class="ps-dropdown ps-dropdown--stream-filter ps-js-dropdown ps-js-activitystream-filter" data-id="peepso_stream_id">
      <button class="ps-btn ps-btn--small ps-js-dropdown-toggle" aria-haspopup="true">
        <i class="ps-icon-<?php echo $selected['icon']; ?>"></i>
        <span><?php echo $selected['label']; ?></span>
      </button>
      <div role="menu" class="ps-dropdown__menu ps-js-dropdown-menu">
        <?php foreach ($stream_id_list as $key => $value) { ?>
        <a role="menuitem" class="ps-dropdown__group" data-option-value="<?php echo $key; ?>">
          <div class="ps-checkbox ps-dropdown__group-title">
            <input type="radio" name="peepso_stream_id" id="peepso_stream_id_opt_<?php echo $key ?>"
              value="<?php echo $key ?>" <?php if ($key == $stream_id) echo "checked"; ?> />
            <label for="peepso_stream_id_opt_<?php echo $key ?>">
              <span><?php echo $value['label']; ?></span>
            </label>
            <i class="ps-icon-<?php echo $value['icon']; ?>"></i>
          </div>
          <div class="ps-dropdown__group-desc"><?php echo $value['desc']; ?></div>
        </a>
        <?php } ?>
        <div class="ps-dropdown__actions">
          <button class="ps-btn ps-btn--small ps-js-cancel"><?php _e('Cancel', 'peepso-core'); ?></button>
          <button class="ps-btn ps-btn--small ps-btn-primary ps-js-apply" ><?php _e('Apply', 'peepso-core'); ?></button>
        </div>
      </div>
    </span>

    <?php } ?>
<?php } ?>

<?php

/** HIDE MY POSTS **/

$show_my_posts_list = array(
	'1' => array('label' => __('Show my posts', 'peepso-core')),
	'0' => array('label' => __('Hide my posts', 'peepso-core')),
);

$show_my_posts = $user_stream_filters['show_my_posts'];
$selected = $show_my_posts_list[$show_my_posts];

?>

<input type="hidden" id="peepso_stream_filter_show_my_posts" value="<?php echo $show_my_posts; ?>" />
<span class="ps-dropdown ps-dropdown--stream-filter ps-js-dropdown ps-js-activitystream-filter" data-id="peepso_stream_filter_show_my_posts">
	<button class="ps-btn ps-btn--small ps-js-dropdown-toggle" aria-haspopup="true">
		<span><?php echo $show_my_posts ? __('Show my posts', 'peepso-core') : __('Hide my posts', 'peepso-core'); ?></span>
	</button>
	<div role="menu" class="ps-dropdown__menu ps-js-dropdown-menu">
		<?php foreach ($show_my_posts_list as $key => $value) { ?>
		<a role="menuitem" class="ps-dropdown__group" data-option-value="<?php echo $key; ?>">
      <div class="ps-checkbox ps-dropdown__group-title">
        <input type="radio" name="peepso_stream_filter_show_my_posts" id="peepso_stream_filter_show_my_posts_opt_<?php echo $key ?>"
          value="<?php echo $key ?>" <?php if($key == $show_my_posts) echo "checked"; ?> />
  			<label for="peepso_stream_filter_show_my_posts_opt_<?php echo $key ?>">
          <span><?php echo $value['label']; ?></span>
  			</label>
      </div>
		</a>
		<?php } ?>
		<div class="ps-dropdown__actions">
			<button class="ps-btn ps-btn--small ps-js-cancel"><?php _e('Cancel', 'peepso-core'); ?></button>
			<button class="ps-btn ps-btn--small ps-btn-primary ps-js-apply"><?php _e('Apply', 'peepso-core'); ?></button>
		</div>
	</div>
</span>

<?php

/** SEARCH POSTS **/
$search = FALSE;
$PeepSoUrlSegments = PeepSoUrlSegments::get_instance();

if('search' == $PeepSoUrlSegments->get(1)) {
    $search = $PeepSoUrlSegments->get(2);
}

?>
<input type="hidden" id="peepso_search" value="<?php echo $show_my_posts; ?>" />
<span class="ps-dropdown ps-dropdown--stream-filter ps-js-dropdown ps-js-activitystream-filter" data-id="peepso_search">
	<a class="ps-btn ps-btn--small ps-js-dropdown-toggle" aria-haspopup="true" aria-label="<?php echo __('Search', 'peepso-core'); ?>">
		<i class="ps-icon-search"></i>
		<span data-empty="<?php //echo __('Search', 'peepso-core'); ?>"
			data-keyword="<?php echo __('Search: ', 'peepso-core'); ?>"></span>
	</a>
	<div role="menu" class="ps-dropdown__menu ps-js-dropdown-menu">
		<div class="ps-dropdown__actions">
			<input type="text" id="ps-activitystream-search" class="ps-input ps-input--small ps-full"
				placeholder="<?php echo __('Type to search', 'peepso-core'); ?>" value="<?php echo $search;?>" />
		</div>

		<a role="menuitem" class="ps-dropdown__group" data-option-value="exact">
			<div class="ps-checkbox ps-dropdown__group-title">
				<input type="radio" name="peepso_search" id="peepso_search_opt_exact" value="exact" checked />
				<label for="peepso_search_opt_exact">
					<span><?php echo __('Exact phrase', 'peepso-core'); ?></span>
				</label>
			</div>
		</a>
		<a role="menuitem" class="ps-dropdown__group" data-option-value="any">
			<div class="ps-checkbox ps-dropdown__group-title">
				<input type="radio" name="peepso_search" id="peepso_search_opt_any" value="any" />
				<label for="peepso_search_opt_any">
					<span><?php echo __('Any of the words', 'peepso-core'); ?></span>
				</label>
			</div>
		</a>
		<div class="ps-dropdown__actions">
			<button class="ps-btn ps-btn--small ps-js-cancel"><?php _e('Cancel', 'peepso-core'); ?></button>
			<button class="ps-btn ps-btn--small ps-btn-primary ps-js-search"><?php _e('Search', 'peepso-core'); ?></button>
		</div>
	</div>
</span>

<?php

/** ADDITIONAL FILTERS - HOOKABLE **/

do_action('peepso_action_render_stream_filters');
?>
</div>
