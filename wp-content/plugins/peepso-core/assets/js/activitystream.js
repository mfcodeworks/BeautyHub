(function( root, $, factory ) {

	var moduleName = 'PsActivityStream',
		moduleObject = factory( moduleName, $, peepso.observer );

	// Run on document load.
	jQuery(function( $ ) {
		var $container = $( '#ps-activitystream' ),
			config, inst;

		if ( $container.length ) {
			config = peepsodata && peepsodata.activity || {};
			inst = new moduleObject( $container[0], config );
		}
	});

})( window, jQuery, function( moduleName, $, observer ) {

	// Constants.
	var EVT_SCROLL = 'scroll.ps-activity-stream',
		MY_ID      = +peepsodata.currentuserid,
		USER_ID    = +peepsodata.userid,
		POST_ID    = $( '#peepso_post_id' ).val() || undefined,
		CONTEXT    = $( '#peepso_context' ).val() || undefined;

	return peepso.createClass( moduleName, {

		/**
		 * Class constructor.
		 * @param {Element} container
		 */
		__constructor: function( container, config ) {
			this.$el = $( container );
			this.$recent = $( '#ps-activitystream-recent' );
			this.$loading = $( '#ps-activitystream-loading' );
			this.$noPosts = $( '#ps-no-posts' );
			this.$noPostsMatch = $( '#ps-no-posts-match' );
			this.$noMorePosts = $( '#ps-no-more-posts' );
			this.$filters = $( '.ps-js-activitystream-filter' );

			this.loading = false;
			this.loadEnd = false;
			this.loadPage = 1;
			this.loadLimit = +peepsodata.activity_limit_below_fold;
			this.loadButtonEnabled = +peepsodata.loadmore_enable;
			this.loadButtonRepeat = this.loadButtonEnabled ? ( +peepsodata.loadmore_repeat ) : 0;
			this.loadButtonTemplate = config.template_load_more;
			this.isPermalink = +config.is_permalink;
			this.hideFromGuest = +config.hide_from_guest;

			// Normalize load limit.
			this.loadLimit = this.loadLimit > 0 ? this.loadLimit : 3;

			// Get activitystream hidden input data.
			this.streamData = {};
			$( '[id^=peepso_stream_]' ).each( $.proxy(function( index, input ) {
				var name = input.id.replace( /^peepso_/, '' ),
					value = input.value;

				this.streamData[ name ] = value;
			}, this ) );

			// Hide loading if login popup is visible.
			$( document ).on( 'peepso_login_shown', function() {
				this.$loading.hide();
			});

			// Handle activitystream filtering.
			this.$filters.on( 'click', '.ps-js-dropdown-toggle', $.proxy( this.onFilterToggle, this ) );
			this.$filters.on( 'click', '[data-option-value]', $.proxy( this.onFilterSelect, this ) );
			this.$filters.on( 'click', '.ps-js-cancel', $.proxy( this.onFilterCancel, this ) );
			this.$filters.on( 'click', '.ps-js-apply', $.proxy( this.onFilterApply, this ) );
			this.$filters.on( 'click', '[type=text]', $.proxy( this.onFilterFocus, this ) );
			this.$filters.on( 'keyup', '[type=text]', $.proxy( this.onFilterKeyup, this ) );
			this.$filters.on( 'click', '.ps-js-search', $.proxy( this.onFilterSearch, this ) );

			// Initiate infinite load.
			var $search = $( '#ps-activitystream-search' );
			this.search( $search.eq(0).val() );

			// Listen to `peepso_stream_reset` actions.
			peepso.observer.addAction( 'peepso_stream_reset', $.proxy(function() {
				this.search( $search.eq(0).val() );
			}, this ) );

			// this._token = ( new Date ).getTime();
			// this.autoload( this._token );
		},

		/**
		 * Search activities based on keyword.
		 * @param {string} [keyword]
		 */
		search: function( keyword ) {
			var $toggle, label;

			keyword = $.trim( keyword || '' );
			this.searchKeyword = keyword;
			this.searchMode = 'exact';
			this.reset();

			// Update button.
			$toggle = $( '.ps-js-activitystream-filter' ).filter( '[data-id=peepso_search]' )
				.find( '.ps-js-dropdown-toggle' ).find( 'span' );

			if ( $toggle.length ) {
				if ( ! keyword ) {
					label = $toggle.data( 'empty' );
				} else {
					label = $toggle.data( 'keyword' );
					label += keyword + '<i class="ps-icon-remove"></i>';
					$toggle.one( 'click', 'i', $.proxy(function( e ) {
						$( '#ps-activitystream-search' ).val( '' );
						this.search( '' );
					}, this ));
				}

				$toggle.html( label );
			}
		},

		/**
		 * Start infinite load.
		 * @param {number} token
		 */
		autoload: function( token ) {
			if ( token !== this._token ) {
				return;
			}

			if ( this.loading ) {
				return;
			}

			// Check if guest should be able to see the activities.
			if ( ! MY_ID && this.hideFromGuest ) {
				this.$loading.hide();
				return false;
			}

			if ( this.shouldLoad() ) {
				this.loading = true;
				this.load( token ).done( $.proxy(function() {
					this.loading = false;
					if ( ! this.isPermalink && ! this.loadEnd ) {
						this.autoload( token );
					}
				}, this ) );

			} else if ( this.loadButtonEnabled && ! this.$loadButton ) {
				this.$loadButton = $( this.loadButtonTemplate ).insertAfter( this.$el );
				this.$loadButton.one( 'click', $.proxy(function( e ) {
					$( e.currentTarget ).remove();
					this.autoload( token );
				}, this ) );

			} else if ( ! this._onScrollEnabled ) {
				this._onScrollEnabled = true;
				$( window ).on( EVT_SCROLL, token, $.proxy( this.onScroll, this ) );
			}
		},

		/**
		 * Check whether stream should load next activities.
		 * @return {boolean}
		 */
		shouldLoad: function() {
			var $activities = this.$el.children( '.ps-js-activity' ),
				$last, limit, position, winHeight;

			// Do not try to load next activity if all activities is already loaded.
			if ( this.loadEnd ) {
				return false;
			}

			// Try to load next activities on empty stream.
			if ( ! $activities.length ) {
				return true;
			}

			// Handle fixed-number batch load of activities.
			if ( this.loadButtonEnabled && this.loadButtonRepeat ) {
				if ( $activities.length % this.loadButtonRepeat === 0 ) {
					if ( this.$loadButton ) {
						delete this.$loadButton;
						return true;
					} else {
						return false;
					}
				} else {
					return true;
				}
			}

			// Get the first of the last N activities where N is decided by limit value.
			$last = $activities.slice( 0 - this.loadLimit ).eq( 0 );
			if ( ! $last.length ) {
				return false;
			}

			// Calculate element from viewport, or from top of the document if trigger button
			// is enabled.
			if ( this.loadButtonEnabled && ! this.$loadButton ) {
				position = $last.eq(0).offset();
			} else {
				position = $last[0].getBoundingClientRect();
			}

			// Load next activities if `$last` is still inside the viewport.
			winHeight = window.innerHeight || document.documentElement.clientHeight;
			if ( position.top < winHeight ) {
				return true;
			}

			return false;
		},

		/**
		 * Load next activities in the current stream.
		 * @param {number} token
		 * @return {jQuery.Deferred}
		 */
		load: function( token ) {
			var that = this,
				transport = peepso.disableAuth().disableError(),
				url = 'activity.show_posts_per_page',
				params;

			params = _.extend({
				uid: MY_ID,
				user_id: USER_ID,
				post_id: POST_ID,
				context: CONTEXT,
				page: this.loadPage,

				// Search query.
				search: this.searchKeyword || undefined,
				search_mode: this.searchKeyword && this.searchMode || undefined,

				// Also get pinned posts on first page.
				pinned: this.loadPage === 1 ? 1 : undefined

			}, this.streamData || {} );

			// Execute filter hooks.
			params = peepso.observer.applyFilters( 'show_more_posts', params );

			return $.Deferred(function( defer ) {
				that.$loading.show();
				transport.postJson( url, params, function( json ) {

					// Discard if token not match.
					if ( token !== that._token ) {
						return;
					}

					that.$loading.hide();

					if ( json.data.found_posts > 0 ) {
						that.render( json.data.posts );
						that.loadPage++;
					} else {
						that.loadEnd = true;
					}

					if ( that.loadEnd ) {
						if ( params.page > 1 ) {
							that.$noMorePosts.show();
						} else if ( that.searchKeyword ) {
							that.$noPostsMatch.show();
						} else {
							that.$noPosts.show();
						}
					}

					defer.resolve();
				});
			});
		},

		/**
		 * Reset activitystream.
		 */
		reset: function() {
			this.loading = false;
			this.loadEnd = false;
			this.loadPage = 1;

			// Disable scroll event.
			this._onScrollEnabled = false;
			$( window ).off( EVT_SCROLL );

			// Remove load more button.
			if ( this.$loadButton ) {
				this.$loadButton.remove();
				this.$loadButton = undefined;
			}

			// Reset view.
			this.$el.empty().hide();
			this.$recent.empty();
			this.$noMorePosts.hide();
			this.$noPosts.hide();
			this.$noPostsMatch.hide();

			// Restart autoload.
			this._token = ( new Date ).getTime();
			this.autoload( this._token );
		},

		/**
		 * Render activities into the stream.
		 * @param {string} html
		 */
		render: function( html ) {
			var $posts = $( html ),
				query = this.searchKeyword,
				mode = this.searchMode,
				highlight;

			// Filter contents.
			$posts.find( '.ps-js-activity-content, .ps-comment-item, .ps-stream-quote' ).each(function() {
				var $post = $( this ),
					html = $post.html();

				html = peepso.observer.applyFilters( 'peepso_activity_content', html );
				$post.html( html );
			});

			// Highlight contents if keywords are set.
			if ( query ) {
				highlight = '<span class="ps-text--highlight">$1</span>';
				$posts.find( '.ps-js-activity-content' ).each(function() {
					var $post = $( this ),
						html = $post.html(),
						reQuery = [ query ];

					if ( mode === 'any' ) {
						reQuery = _.filter( query.split(' '), function( str ) { return str });
					}

					// Escape string to be used in regex.
					// https://stackoverflow.com/questions/3446170/escape-string-for-use-in-javascript-regex
					reQuery = _.map( reQuery, function( str ) {
						return str.replace( /[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, '\\$&' );
					});

					reQuery = RegExp( '(' + reQuery.join('|') + ')(?![^<>]+>)', 'ig' );
					html = html.replace( reQuery, highlight );
					$post.html( html );
				});
			}

			// Show container if its not already visible.
			if ( this.$el.is( ':hidden' ) ) {
				this.$el.show();
			}

			// Safely append elements into container as some of them might raise error when added
			// to the document tree, thus breaks the autoload process.
			try {
				$posts.appendTo( this.$el );
			} catch( e ) {}

			$posts.hide().fadeIn( 1000, function() {
				$( document ).trigger( 'ps_activitystream_loaded' );
			});

			// TODO: This code should be moved to comments script.
			$( 'textarea[name=comment]', $posts ).ps_autosize();
		},

		/**
		 * Handle document scroll event.
		 * @param {Event} e
		 */
		onScroll: _.throttle(function( e ) {
			var token = e.data;
			this.autoload( token );
		}, 1 ),

		/**
		 * Handle toggle filter activitystream dropdowns.
		 * @param {Event} e
		 */
		onFilterToggle: _.debounce(function( e ) {
			var $toggle = $( e.currentTarget ),
				$dropdown = $toggle.closest( '.ps-js-activitystream-filter' ),
				data = $dropdown.data( 'id' ).replace( /^peepso_/, '' ),
				value = this.streamData[ data ],
				$radio;

			if ( $dropdown.is( ':visible' ) ) {
				$radio = $dropdown.find( '[type=radio]' ).filter( '[value="' + value + '"]' );
				if ( $radio.length ) {
					$radio[0].checked = true;
				}
			}

		}, 1 ),

		/**
		 * Handle select filter activitystream dropdowns.
		 * @param {Event} e
		 */
		onFilterSelect: function( e ) {
			var $option = $( e.currentTarget ),
				$radio = $option.find( '[type=radio]' );

			e.preventDefault();
			e.stopPropagation();

			_.defer(function() {
				$radio[0].checked = true;
			});
		},

		/**
		 * Handle cancel activitystream filter.
		 * @param {Event} e
		 */
		onFilterCancel: function( e ) {
			var $button = $( e.currentTarget );
				$dropdown = $button.closest( '.ps-js-activitystream-filter' ),
				$toggle = $dropdown.find( '.ps-js-dropdown-toggle' );

			$toggle.focus();
		},

		/**
		 * Handle apply activitystream filter.
		 * @param {Event} e
		 */
		onFilterApply: function( e ) {
			var $button = $( e.currentTarget );
				$dropdown = $button.closest( '.ps-js-activitystream-filter' ),
				$toggle = $dropdown.find( '.ps-js-dropdown-toggle' ),
				$hidden = $( '#' + $dropdown.data( 'id' ) ),
				$option = $dropdown.find( '[type=radio]:checked' ).closest( '[data-option-value]' ),
				value = $option.data( 'optionValue' ),
				data = $dropdown.data( 'id' ).replace( /^peepso_/, '' );

			// Update filter data.
			$hidden.val( value );
			this.streamData[ data ] = value;

			// Update button toggle.
			$toggle.find( 'span' ).text( $option.find( 'span' ).text() );
			$toggle.find( '[class*="ps-icon-"]' ).attr( 'class',
			 	$option.find( '[class*="ps-icon-"]' ).attr( 'class' ) );

			$toggle.focus();

			// Reset activitystream.
			this.reset();
		},

		/**
		 * Handle focus on input inside filter activitystream dropdowns.
		 * @param {Event} e
		 */
		onFilterFocus: function( e ) {
			e.stopPropagation();
		},

		/**
		 * Handle keyup on input inside filter activitystream dropdowns.
		 * @param {Event} e
		 */
		onFilterKeyup: function( e ) {
			var $dropdown, $search;

			e.stopPropagation();
			if ( e.which === 13 ) {
				$dropdown = $( e.currentTarget ).closest( '.ps-js-activitystream-filter' );
				$search = $dropdown.find( '.ps-js-search' );
				$search.click();
			}
		},

		/**
		 * Handle searching.
		 */
		onFilterSearch: function( e ) {
			var $button = $( e.currentTarget );
				$dropdown = $button.closest( '.ps-js-activitystream-filter' ),
				$toggle = $dropdown.find( '.ps-js-dropdown-toggle' ),
				$hidden = $( '#' + $dropdown.data( 'id' ) ),
				$option = $dropdown.find( '[type=radio]:checked' ).closest( '[data-option-value]' ),
				$input = $dropdown.find( '[type=text]' ),
				value = $option.data( 'optionValue' ),
				data = $dropdown.data( 'id' ).replace( /^peepso_/, '' ),
				keyword = $input.val().trim(),
				label = $toggle.find( 'span' ).data( keyword ? 'keyword' : 'empty' );

			// Update filter data.
			$hidden.val( value );
			this.searchKeyword = keyword;
			this.searchMode = value;

			// Update button toggle.
			if ( ! keyword ) {
				label = $toggle.find( 'span' ).data( 'empty' );
			} else {
				label = $toggle.find( 'span' ).data( 'keyword' );
				label = label + keyword + '<i class="ps-icon-remove"></i>';
			}

			// Handle remove filter.
			$toggle.find( 'span' ).html( label ).off( 'click', 'i' )
				.one( 'click', 'i', $.proxy(function( e ) {
					var $toggle = $( e.target ).closest( '.ps-js-dropdown-toggle' ),
						$dropdown = $toggle.closest( '.ps-js-activitystream-filter' ),
						$input = $dropdown.find( '[type=text]' ),
						$hidden = $( '#' + $dropdown.data( 'id' ) );

					e.stopPropagation();
					$input.val( '' );
					$hidden.val( '' );
					$toggle.find( 'span' ).html( $toggle.find( 'span' ).data( 'empty' ) );
					this.searchKeyword = '';
					this.searchMode = '';
					this.reset();
				}, this ));

			$toggle.focus();

			// Reset activitystream.
			this.reset();
		}

	});

});
