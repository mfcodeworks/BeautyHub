(function($) {

	// Toggle verbose mode.
	$( 'input[name=peepso_admin_verbose_tabs]' ).on( 'click', _.throttle(function( evt, initial ) {
		var $labels = $( '.ps-label-optional' ),
			url = peepsodata.ajaxurl,
			action = 'peepso_admin_verbose_tabs';

		this.checked ? $labels.show() : $labels.hide();
		if ( ! initial ) {
			$.post({ url: url, data: { action: action, value: +this.checked } });
		}
	}, 1000 ) ).triggerHandler( 'click', [ true ] );

	// Toggle verbose mode.
	$( 'input[name=peepso_admin_verbose_fields]' ).on( 'click', _.throttle(function( evt, initial ) {
		var $labels = $( '.lbl-descript' );
			url = peepsodata.ajaxurl,
			action = 'peepso_admin_verbose_fields';

		this.checked ? $labels.show() : $labels.hide();
		if ( ! initial ) {
			$.post({ url: url, data: { action: action, value: +this.checked } });
		}
	}, 1000 ) ).triggerHandler( 'click', [ true ] );

	// Toggle ultimate bundle license field.
	$( 'input[name=bundle]' ).on( 'click', _.throttle(function( evt, initial ) {
		var $statuses = $('.license_status_check');
			$bundleLicense = $( 'input[name=bundle_license]' ),
			bundleLicense = $bundleLicense.val(),
			checked = this.checked;

		if ( checked ) {
			$bundleLicense.closest('.form-group').show();
			$('#field_bundle_message').hide();
		} else {
			$bundleLicense.closest('.form-group').hide();
            $('#field_bundle_message').show();
		}

		$statuses.each(function() {
			var $input = $( this ).closest('label').next('div.form-field').find('input');
			if ( checked ) {
				$input.data( 'origLicense', $input.val() );
				$input.val( bundleLicense );
				$input.attr( 'readonly', 'readonly' ).css( 'opacity', .5 );
			} else if ( ! initial ) {
				$input.removeAttr( 'readonly' ).css( 'opacity', '' );
				$input.val( $input.data( 'origLicense' ) || '' );
			}
		});
	}, 100 ) ).triggerHandler( 'click', [ true ] );

	// Toggle ultimate bundle license field.
	$( 'input[name=bundle_license]' ).on( 'input', _.throttle(function( evt ) {
		var $statuses = $('.license_status_check'),
			bundleLicense = this.value,
			checked = $( 'input[name=bundle]' )[0].checked;

		if ( ! checked ) {
			return;
		}

		$statuses.each(function() {
			var $input = $( this ).closest('label').next('div.form-field').find('input');
			$input.val( bundleLicense );
		});
	}, 100 ) );

	// Toggle repeat load more button.
	$( 'input[name=loadmore_enable]' ).on( 'click', _.throttle(function() {
		var $repeat = $( 'select[name=loadmore_repeat]' ),
			$wrapper = $repeat.closest( '.form-group' );

		if ( this.checked ) {
			$wrapper.show();
		} else {
			$wrapper.hide();
		}
	}, 100 ) ).triggerHandler( 'click' );

	// Update ajax call intensity setting.
	$( 'select[name=notification_ajax_delay_min]' ).on( 'change', _.throttle(function() {
		var $idle = $( 'select[name=notification_ajax_delay]' ),
			idleValue = +$idle.val(),
			activeValue = +this.value;

		if ( idleValue < activeValue ) {
			$idle.val( activeValue );
		}
	}, 100 ) );

	// Update ajax call intensity setting.
	$( 'select[name=notification_ajax_delay]' ).on( 'change', _.throttle(function() {
		var $active = $( 'select[name=notification_ajax_delay_min]' ),
			activeValue = +$active.val(),
			idleValue = +this.value;

		if ( idleValue < activeValue ) {
			$( this ).val( activeValue );
		}
	}, 100 ) ).triggerHandler( 'change' );

	// Check license.
	function checkLicense() {
		var statuses = $('.license_status_check'),
			plugins = {};

		if ( ! statuses.length ) {
			return;
		}

		statuses.each(function() {
			var $input,
				$el = $( this );

			plugins[ $el.attr('id') ] = $el.data('plugin-name');

			// handle input on license key field
			$input = $el.closest('label').next('div.form-field').find('input');
			$input.on('input focus blur', _.throttle(function( e ) {
				var value = this.value,
					trimmedValue = $.trim( value );

				if ( value !== trimmedValue ) {
					this.value = trimmedValue;
				}
			}, 500 ));
		});

		function periodicalCheckLicense() {
			$PeepSo.postJson("adminConfigLicense.check_license", { plugins: plugins }, function(json) {
				var valid, details, prop, icon, licenses;
				if (json.success) {
					valid   = json.data && json.data.valid || {};
					details = json.data && json.data.details || {};
					for ( prop in valid ) {

						if (+valid[prop]) {
							icon = '<i class="ace-icon fa fa-check bigger-110" style="color:green"></i>';
							$("#error_" + prop).hide();
						} else {
							icon = '<i class="ace-icon fa fa-times bigger-110" style="color:red"></i>';
							$("#error_" + prop).show();
						}

						if (details[prop]) {
							icon = icon
								+ '<br/>'
								+ details[prop];
						}

						statuses.filter("#" + prop).html( icon );
					}
				}
			});
		}

		periodicalCheckLicense();
		setInterval(function() {
			periodicalCheckLicense();
		}, 1000 * 30 );
	}

	$(document).ready(function() {

		var hide_animation_speed = 500;

		// hide report types if reporting is disabled
		var $reporting_enable = $("input[name='site_reporting_enable']");
		// Handle toggling of limit comments readonly state
		if ($reporting_enable.size() > 0) {
			$reporting_enable.on("change", function() {
				if ($(this).is(":checked")) {
					$('#field_site_reporting_types').fadeIn();
				} else {
					$('#field_site_reporting_types').fadeOut();
				}
			}).trigger("change");
		}


		// hide recaptcha fields if recaptcha is disabled
		var $recaptcha_enable = $("input[name='site_registration_recaptcha_enable']");
		// Handle toggling of limit comments readonly state
		if ($recaptcha_enable.size() > 0) {
			$recaptcha_enable.on("change", function() {
				if ($(this).is(":checked")) {
					$('#field_site_registration_recaptcha_sitekey').fadeIn(hide_animation_speed);
					$('#field_site_registration_recaptcha_secretkey').fadeIn(hide_animation_speed);
					$('#field_site_registration_recaptcha_description').fadeIn(hide_animation_speed);
				} else {
					$('#field_site_registration_recaptcha_sitekey').fadeOut(hide_animation_speed);
					$('#field_site_registration_recaptcha_secretkey').fadeOut(hide_animation_speed);
					$('#field_site_registration_recaptcha_description').fadeOut(hide_animation_speed);
				}
			}).trigger("change");
		}

        // hide T&C text if T&C is disabled
        var $terms_enable = $("input[name='site_registration_enableterms']");
        // Handle toggling of limit comments readonly state
        if ($terms_enable.size() > 0) {
            $terms_enable.on("change", function() {
                if ($(this).is(":checked")) {
                    $('#field_site_registration_terms').fadeIn(hide_animation_speed);
                } else {
                    $('#field_site_registration_terms').fadeOut(hide_animation_speed);
                }
            }).trigger("change");
        }

        // hide Privacy text if Privacy is disabled
        var $privacy_enable = $("input[name='site_registration_enableprivacy']");
        if ($privacy_enable.size() > 0) {
            $privacy_enable.on("change", function() {
                if ($(this).is(":checked")) {
                    $('#field_site_registration_privacy').fadeIn(hide_animation_speed);
                } else {
                    $('#field_site_registration_privacy').fadeOut(hide_animation_speed);
                }
            }).trigger("change");
        }

		var $avatars_wp_only = $("input[name='avatars_wordpress_only']");
		// Handle toggling of limit comments readonly state
		if ($avatars_wp_only.size() > 0) {
			$avatars_wp_only.on("change", function() {
				if ($(this).is(":checked")) {
					$('#field_avatars_peepso_only').fadeOut(hide_animation_speed);
					$('#field_avatars_peepso_gravatar').fadeOut(hide_animation_speed);
					$('#field_avatars_wordpress_only_desc').fadeIn(hide_animation_speed);
				} else {
					$('#field_avatars_peepso_only').fadeIn(hide_animation_speed);
					$('#field_avatars_peepso_gravatar').fadeIn(hide_animation_speed);
					$('#field_avatars_wordpress_only_desc').fadeOut(hide_animation_speed);
				}
			}).trigger("change");
		}

		checkLicense();

		// Handle reset all emails button
		var $resetCheck = $('#reset-check');
		if ($resetCheck.length) {
			var $resetDo = $('#reset-do');
			$resetCheck.on('click', function() {
				this.checked ? $resetDo.removeAttr('disabled') : $resetDo.attr('disabled', 'disabled');
			});
			$resetDo.on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();
				var location = window.location.href;
				location = location.replace('&reset=1', '');
				window.location = location + '&reset=1';
			});
		}

		// Irrelevant submenu items accidentally highlighted due to under admin page `.current` class,
		// so we manually un-highlight them!
		var $root, $submenus, $current,
			url = window.location.href,
			tab = url.match( /page=peepso_config(&(tab=[-a-z]+))?/i ),
			colorInactive = 'rgba(240,245,250,.7)';

		if ( tab ) {
			$root = $( '#toplevel_page_peepso li.current ul.wp-submenu-wrap' );
			$submenus = $root.find( 'a[href]' );
			$current = $submenus.filter( '[href$="' + ( tab[2] || 'tab=site' ) + '"]' );
			$submenus.not( $current ).css({
				color: colorInactive,
				fontWeight: 'normal'
			}).on( 'mouseenter', function() {
				$( this ).css({ color: '#00b9eb' });
			}).on( 'mouseleave', function() {
				$( this ).css({ color: colorInactive });
			});
		}

	});

	var errors = {};
	var changed = {};

	function initCheck() {
		var $inputs = $('input[type=text].validate');
		$inputs.off('keyup.validate').on('keyup.validate', function() {
			var $el = $(this);
			checkValue( $el, $el.data() );
		}).trigger('keyup');
	}

	var checkValueTimer = false;
	function checkValue($el, data) {
		clearTimeout(checkValueTimer);
		checkValueTimer = setTimeout(function() {
			if (data.ruleType === 'int') {
				checkNumber($el, data);
			} else if (data.ruleType === 'email') {
				checkEmail($el, data);
			} else {
				checkString($el, data);
			}
		}, 300 );
	}

	function checkString($el, data) {
		var val = $el.val(),
			name = $el.attr('name');

		data.ruleMinLength = +data.ruleMinLength;
		data.ruleMaxLength = +data.ruleMaxLength;

		if ( data.ruleMinLength && val.length < data.ruleMinLength ) {
			showError($el, data);
			errors[ name ] = true;
		} else if ( data.ruleMaxLength && val.length > data.ruleMaxLength ) {
			showError($el, data);
			errors[ name ] = true;
		} else {
			hideError($el, data);
			errors[ name ] = false;
			delete errors[ name ];
		}
		toggleSubmitButton();
	}

	function checkNumber($el, data) {
		var val = +$el.val(),
			name = $el.attr('name');

		data.ruleMin = +data.ruleMin;
		data.ruleMax = +data.ruleMax;

		if ( data.ruleMin && val < data.ruleMin ) {
			showError($el, data);
			errors[ name ] = true;
		} else if ( data.ruleMax && val > data.ruleMax ) {
			showError($el, data);
			errors[ name ] = true;
		} else {
			hideError($el, data);
			errors[ name ] = false;
			delete errors[ name ];
		}
		toggleSubmitButton();
	}

	function checkEmail($el, data) {
		var val = $el.val();
		// http://data.iana.org/TLD/tlds-alpha-by-domain.txt
		// http://stackoverflow.com/questions/201323/using-a-regular-expression-to-validate-an-email-address
		var re = /^([*+!.&#$¦\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,15})$/i;
		if ( !re.test(val) ) {
			showError($el, data);
			errors[ name ] = true;
		} else {
			hideError($el, data);
			errors[ name ] = false;
			delete errors[ name ];
		}
		toggleSubmitButton();
	}

	function showError($el, data) {
		var $error;
		if ( !$el.hasClass('error') ) {
			if ( data.ruleMessage ) {
				$error = $el.next('.validate-error');
				if ( !$error.length ) {
					$error = $([
						'<div class="validate-error tooltip bottom">',
						'<div class="tooltip-arrow"></div>',
						'<div class="tooltip-inner">',
						data.ruleMessage,
						'</div></div>'
					].join(''));
					$error.insertAfter($el);
				}
			}
			$el.addClass('error');
		}
	}

	function hideError($el, data) {
		if ( $el.hasClass('error') ) {
			$el.removeClass('error');
		}
	}

	var toggleSubmitTimer = false;
	function toggleSubmitButton() {
		clearTimeout( toggleSubmitTimer );
		toggleSubmitTimer = setTimeout( _toggleSubmitButton, 300 );
	}

	function _toggleSubmitButton() {
		var error = false,
			prop;

		for ( prop in errors ) {
			if ( errors[ prop ] ) {
				error = true;
				break;
			}
		}

		var $submit = $('#peepso button[type=submit]');
		if ( error ) {
			$submit.attr('disabled', 'disabled');
		} else {
			$submit.removeAttr('disabled');
		}
	}

	var toggleEditWarningTimer = false;
	function toggleEditWarning() {
		clearTimeout( toggleEditWarningTimer );
		toggleEditWarningTimer = setTimeout( _toggleEditWarning, 300 );
	}

	function _toggleEditWarning() {
		$('#edit_warning').show();
	}

	// Form validation.
	$(function() {
		initCheck();
		$('#peepso button[type=reset]').on('click', function() {
			setTimeout( initCheck, 100 );
			setTimeout( function() {
				$('#edit_warning').hide();
			}, 1000 );
		});

		// Show notice if any of the form fields are changed.
		// Wait for a second to skip any event auto-trigger from elements on page.
		setTimeout( function() {
			$('#peepso').find('input[type=text], textarea').on('keyup', function() {
				toggleEditWarning();
			});

			$('#peepso').find('input[type=checkbox], select').on('change', function() {
				toggleEditWarning();
			});
		}, 1000 );
	});

	$(document).ready(function() {

        /**********************************************************************************
        * Additional JS inline select
        **********************************************************************************/
	    if ($('.inline-select').length > 0) {
		$('.inline-select').on('click', function(e) {
		    $(this).closest('ul').find('a').removeClass('btn-primary');
		    $(this).closest('.form-group').find('input[type="hidden"]').val($(this).attr('data-value'));
		    $(this).addClass('btn-primary');

		    e.preventDefault();
		});
	    }

        /**********************************************************************************
        * Additional JS for opengraph config page
        **********************************************************************************/
	    var hide_animation_speed = 500;
	    var $opengraph_enable = $("input[name='opengraph_enable']");

	    if ($opengraph_enable.size() > 0) {
		$opengraph_enable.on("change", function() {
			$selector = $(this).closest('.inside').find('[id*="field_"]').not('#field_opengraph_enable');

			if ($(this).is(":checked")) {
				$selector.fadeIn(hide_animation_speed);
			} else {
				$selector.fadeOut(hide_animation_speed);
			}
		}).trigger("change");

		$('#opengraph_title').after("<p class='text-right'><span class='opengraph_title_counter'></span> Characters</p><p>Depending on where the links will be shared, the title can be cropped to even 40 characters. Keep the title short and to the point.</p>");
		$('.opengraph_title_counter').html($('#opengraph_title').val().length);

		$('#opengraph_title').on('keyup', function(e) {
		    $('.opengraph_title_counter').html($('#opengraph_title').val().length);
		});

		$('#opengraph_description').after("<p class='text-right'><span class='opengraph_description_counter'></span> Characters</p><p>Depending on where the links will be shared, the description can be cropped to even 200 characters. Keep the description short and to the point.</p>");
		$('.opengraph_description_counter').html($('#opengraph_description').val().length);

		$('#opengraph_description').on('keyup', function(e) {
		    $('.opengraph_description_counter').html($('#opengraph_description').val().length);
		});
	    }

	    /**********************************************************************************
	    * Media uploader for og:image
	    **********************************************************************************/


	    $og_image_prop = '<a class="btn btn-sm btn-info btn-img-og" href="#">Select Image</a> '
			    + '<a class="btn btn-sm btn-danger btn-img-remove" href="#">Remove Image</a>'
			    + '<span class="no-img-selected">No image selected</span>'
			    + '<img class="img-responsive img-og-preview" src="" />';

	    $('#opengraph_image').after($og_image_prop).hide();

	    function show_image_property(img)
	    {
		$('.img-og-preview').attr('src', img).show();
		$('.btn-img-remove').show();
		$('.no-img-selected').hide();
		$('#opengraph_image').val(img);
	    }

	    function hide_image_property()
	    {
		$('#opengraph_image').val('');
		$('.img-og-preview').attr('src', '');
		$('.img-og-preview, .btn-img-remove').hide();
		$('.no-img-selected').show();
	    }

	    if ($('#opengraph_image').val() != '')
	    {
		show_image_property($('#opengraph_image').val());
	    } else
	    {
		hide_image_property();
	    }

	    $('.btn-img-og').on('click', function(e) {
		var button = $(this);

		wp.media.editor.send.attachment = function(props, attachment){
		    var img = attachment.url;
		    show_image_property(img);
		};

		wp.media.editor.open(button);
	    });

	    $('.btn-img-remove').on('click', function(e) {
		hide_image_property();
		e.preventDefault();
	    });

	});

	/**********************************************************************************
        * Media uploader for landingpage
        **********************************************************************************/

        $lp_image_prop = '<a class="btn btn-sm btn-info btn-img-landing-page" href="#">Select Image</a> '
                        + '<a class="btn btn-sm btn-danger btn-lp-img-remove" href="#">Remove Image</a>'
                        + '<span class="default-img-selected">Default image selected</span>'
                        + '<img class="img-responsive img-landing-page-preview" src="" />';

        $('#landing_page_image').after($lp_image_prop).hide();
        // hide default landing page
        $('#landing_page_image_default').hide();

        function show_landing_page_image_property(img)
        {
	    $('.img-landing-page-preview').attr('src', img).show();
	    $('.btn-lp-img-remove').show();
	    $('.default-img-selected').hide();
	    $('#landing_page_image').val(img);
        }

	function hide_landing_page_image_property()
	{
	    $('#landing_page_image').val('');
            $('.img-landing-page-preview').attr('src', $('#landing_page_image_default').val());
            $('.btn-lp-img-remove').hide();
            $('.default-img-selected').show();
	}

        if ($('#landing_page_image').val() != '')
        {
	    show_landing_page_image_property($('#landing_page_image').val());
        }
        else
        {
	    hide_landing_page_image_property();
        }

        $('.btn-img-landing-page').on('click', function(e) {
            var button = $(this);

            wp.media.editor.send.attachment = function(props, attachment){
                var img = attachment.url;
                show_landing_page_image_property(img);
            };

            wp.media.editor.open(button);
        });

        $('.btn-lp-img-remove').on('click', function(e) {
	    hide_landing_page_image_property();
            e.preventDefault();
        });

    // handle dismiss peepso-new-plugin notice
    $('.notice.is-dismissible.peepso-new-plugin').on('click', '.notice-dismiss', function() {
        $.post( ajaxurl, { action: 'dismiss_new_plugin_notice' });
    });

})(jQuery);
