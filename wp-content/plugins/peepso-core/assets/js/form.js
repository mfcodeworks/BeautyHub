(function( $ ) {

// privacy dropdown
$(document).on('click', '.ps-privacy-dropdown ul li a', function() {
	var $a = $( this ).closest('a'),
		$menu = $a.closest('ul'),
		$input = $menu.siblings('input'),
		$btn = $menu.siblings('.ps-btn,.ps-js-dropdown-toggle'),
		$icon = $btn.find('i'),
		$label = $btn.find('.ps-privacy-title');

	$input.val( $a.attr('data-option-value') );
	$icon.attr('class', $a.find('i').attr('class'));
	$label.html( $a.find('span').html() );
	$menu.css('display', 'none');
});

// init datepicker
function initDatepicker( $dp ) {
	if ( !$dp ) {
		return;
	}

	$dp.each(function() {
		var $input = $( this ),
			value = $input.data( 'value' ),
			startDate = $input.data( 'dateStartDate' ),
			endDate = $input.data( 'dateEndDate' ),
			yearMin = Math.min( +$input.data( 'dateRangeMin' ) || 100, 100 ),
			yearMax = Math.min( +$input.data( 'dateRangeMax' ) || 0, 100 ),
			yearRange = '-' + yearMin + ':+' + yearMax,
			date;

		$input.psDatepicker({
			startDate: startDate,
			endDate: endDate,
			yearRange: yearRange,
			onSelect: function( dateText, inst ) {
				var $input = $( this ),
					date = $input.datepicker( 'getDate' ),
					value = [];

				if ( date ) {
					value.push( date.getFullYear() );
					value.push( date.getMonth() + 1 );
					value.push( date.getDate() );

					// Add zero padding.
					value[1] = ( value[1] < 10 ? '0' : '' ) + value[1];
					value[2] = ( value[2] < 10 ? '0' : '' ) + value[2];
				}

				$input.data( 'value', value.join( '-' ) );
				$input.trigger( 'input' );
			}
		});

		if ( value ) {
			value = value.split( '-' );
			date = new Date( +value[0], +value[1] - 1, +value[2] );
			$input.psDatepicker( 'setDate', date );
		}
	});

	$dp.addClass( 'datepickerInitialized' );
}

ps_datepicker = {
	init: initDatepicker
};

$(function() {
	initDatepicker( $('#peepso-wrap .datepicker').not('.datepickerInitialized') );
});

})( jQuery );
