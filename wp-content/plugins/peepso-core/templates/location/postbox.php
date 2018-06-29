<div class="ps-location-wrapper ps-js-location-wrapper" style="display:block">
	<div class="ps-location ps-js-location ps-clearfix" style="position:relative;border:0 none">
		<input type="text" class="ps-input ps-input-full" placeholder="<?php _e('Enter location name...', 'peepso-core'); ?>" />
		<div class="ps-location-loading ps-js-location-loading">
			<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt="" />
		</div>
		<div class="ps-location-result ps-js-location-result">
			<div class="ps-location-map ps-js-location-map" style="display:none"></div>
			<div class="ps-location-list ps-js-location-list"></div>
			<a href="javascript:" class="ps-btn ps-btn-small ps-btn-primary ps-js-select" style="top:42px"><?php _e('Select', 'peepso-core'); ?></a>
			<a href="javascript:" class="ps-btn ps-btn-small ps-btn-danger ps-js-remove" style="top:42px"><?php _e('Remove', 'peepso-core'); ?></a>
		</div>
	</div>
	<script type="text/template" class="ps-js-location-fragment">
		<a class="ps-location-listitem {{= data.place_id ? 'ps-js-location-listitem' : '' }}" data-place-id="{{= data.place_id }}" href="javascript:" style="line-height:12px;padding-top:6px;padding-bottom:6px">
			<strong class="ps-js-location-listitem-name">{{= data.name }}</strong><br />
			<small>{{= data.description || '&nbsp;' }}</small>
		</a>
	</script>
</div>
