(function( $, factory ) {

	peepso.recaptcha = factory( $ );

	// Initialize on document load.
	$(function() {
		peepso.recaptcha.init();
	});

})( jQuery, function( $ ) {

	var url = 'https://www.google.com/recaptcha/api.js',
		config = window.peepsodata_recaptcha || {};

	return {

		/**
		 * Initialize peepso recaptcha.
		 */
		init: function() {
			var initialized = 'ps-js-initialized',
				$btns = $( '.ps-js-recaptcha' );

			// Filter out already-initialized buttons.
			$btns = $btns.not( '.' + initialized );

			if ( $btns.length ) {
				this._loadLibrary().done( $.proxy( function() {
					$btns.each( $.proxy( function( index, btn ) {
						var $btn = $( btn ).addClass( initialized );
						this._initOne( $btn );
					}, this ) );
				}, this ) );
			}
		},

		/**
		 * Initialize a peepso recaptcha tag.
		 * @param {jQuery} $btn
		 */
		_initOne: function( $btn ) {
			var evtName = 'click.ps-recaptcha',
				$form = $btn.closest( 'form' ),
				$div, recaptchaId;

			if ( $form.length ) {

				// Intercept button onclick handler.
				$btn.on( evtName, function( e ) {
					if ( ! grecaptcha.getResponse( recaptchaId ) ) {
						e.preventDefault();
						e.stopPropagation();
						grecaptcha.execute( recaptchaId );
					}
				});

				// Initialize recaptcha.
				$div = $( '<div />' ).insertBefore( $btn );
				recaptchaId = grecaptcha.render( $div[0], {
					sitekey: config.key,
					size: 'invisible',
					callback: function() {
						$btn.off( evtName );
						$form.submit();
					}
				} );
			}
		},

		/**
		 * Load Google Recaptcha API if it is not loaded yet.
		 * @return {jQuery.Deferred}
		 */
		_loadLibrary: function() {
			return $.Deferred( function( defer ) {
				var script, timer, count;

				// Check if the script is already loaded.
				$( 'script' ).each(function( index, elem ) {
					var src = $( elem ).attr( 'src' );
					if ( src && src.match( url ) ) {
						script = elem;
						return false;
					}
				});

				// Load script if it is not loaded yet.
				if ( ! script ) {
					script = document.createElement( 'script' );
					script.type = 'text/javascript';
					script.src = 'https://www.google.com/recaptcha/api.js' +
						'?onload=peepsoRecaptchaCallback&render=explicit';

					window.peepsoRecaptchaCallback = function() {
						defer.resolve();
						delete window.peepsoRecaptchaCallback;
					};

					document.body.appendChild( script );

				// Or just wait for the `grecaptcha` object to be available if it is.
				} else {
					count = 0;
					timer = setInterval( function() {
						count++;

						if ( window.grecaptcha ) {
							clearInterval( timer );
							defer.resolve();

						// Wait for 60s (120 x 500ms) just in case on slow network.
						} else if ( count > 120 ) {
							clearInterval( timer );
						}
					}, 500 )
				}
			} );
		}

	};

});
