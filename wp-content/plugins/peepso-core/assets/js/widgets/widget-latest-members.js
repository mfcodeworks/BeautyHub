(function( $, factory ) {

	var latestMembers = factory( $ );

	peepso.widget = peepso.widget || {};
	peepso.widget.latestMembers = latestMembers;

	// Initialize on document loaded.
	$( $.proxy( latestMembers.init, latestMembers ) );

})( jQuery, function( $ ) {

	return {

		/**
		 * Initialize member widgets.
		 */
		init: function() {
			var initialized = 'ps-js-initialized',
				$widgets = $( '.ps-js-widget-latest-members' ).not( '.' + initialized );

			$widgets.each( $.proxy( function( index, elem ) {
				var $widget = $( elem ).addClass( initialized ),
					$content = $widget.find( '.ps-js-widget-content' ),
					hideEmpty = +$widget.data( 'hideempty' ),
					showTotal = +$widget.data( 'totalmember' ),
					limit = +$widget.data( 'limit' );

				this.getData( limit, showTotal ).done( function( html, isEmpty ) {
					if ( isEmpty && hideEmpty ) {
						$content.empty();
						$widget.hide();
					} else {
						$content.html( html );
						$widget.show();
					}
				} );
			}, this ) );
		},

		/**
		 * Get member listing.
		 * @param {number} limit
		 * @param {boolean} showTotal
		 * @return {jQuery.Deferred}
		 */
		getData: function( limit, showTotal ) {
			return $.Deferred(function( defer ) {
				var url = 'widgetajax.latest_members',
					params = {};

				// Delay data fetching to give time for more important Ajax requests.
				setTimeout(function() {
					params.limit = +limit;
					params.totalmember = showTotal ? 1 : 0;
					peepso.getJson( url, params, function( json ) {
						if ( json.success ) {
							defer.resolve( json.data.html, +json.data.empty );
						} else {
							defer.reject();
						}
					});
				}, 3000 );
			});
		}

	};

});
