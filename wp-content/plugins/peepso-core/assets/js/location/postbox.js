(function( $, factory ) {

	var PsPostboxLocation = factory( $ );

	ps_observer.add_action('postbox_init', function( postbox ) {
		var inst = new PsPostboxLocation( postbox );
	}, 10, 1 );

})( jQuery, function( $ ) {

var evtSuffix = '.ps-postbox-location';

/**
 * Postbox location addon.
 */
function PsPostboxLocation() {
	this.__constructor.apply( this, arguments );
}

PsPostboxLocation.prototype = {

	__constructor: function( postbox ) {
		this.postbox = postbox;

		// element caches
		this.$doc = $( document );
		this.$toggle = postbox.$tabContext.find('#location-tab');
		this.$dropdown = postbox.$tabContext.find('#pslocation').html( peepsogeolocationdata.template_postbox );
		this.$input = this.$dropdown.find('input[type=text]');
		this.$loading = this.$dropdown.find('.ps-js-location-loading');
		this.$result = this.$dropdown.find('.ps-js-location-result');
		this.$list = this.$dropdown.find('.ps-js-location-list');
		this.$map = this.$dropdown.find('.ps-js-location-map');
		this.$select = this.$dropdown.find('.ps-js-select');
		this.$remove = this.$dropdown.find('.ps-js-remove');

		// item template
		this.listItemTemplate = peepso.template( this.$dropdown.find('.ps-js-location-fragment').text() );

		// event handler
		this.$toggle.on('click' + evtSuffix, $.proxy( this.onToggle, this ));
		this.$input.on('input' + evtSuffix, $.proxy( this.onInput, this ));
		this.$list.on('mousedown' + evtSuffix, 'a.ps-js-location-listitem', $.proxy( this.onSelectItem, this ));
		this.$select.on('mousedown' + evtSuffix, $.proxy( this.onSelect, this ));
		this.$remove.on('mousedown' + evtSuffix, $.proxy( this.onRemove, this ));

		// filters and actions
		postbox.add_action('update', this.update, 10, 2, this );
		postbox.add_filter('render_addons', this.render, 10, 1, this );
		postbox.add_filter('data', this.filterData, 10, 1, this );
		postbox.add_filter('data_validate', this.validate, 10, 2, this );
	},

	show: function() {
		this.$dropdown.removeClass('hidden');
		this.$doc.on('click' + evtSuffix, $.proxy( this.onDocumentClick, this ));

		// check whether initial value needs to be updated
		if ( this._needUpdate ) {
			this._needUpdate = false;

			if ( this._selected ) {
				this.$map.show();
				this.$select.hide();
				this.$remove.show();
				this.$result.show();

				this.updateList([{
					place_id: '',
					name: this._selected.name,
					description: this._selected.description
				}]);

				this.updateMap({
					latitude: this._selected.latitude,
					longitude: this._selected.longitude,
					zoom: this._selected.zoom
				});

			} else {
				this.$map.hide();
				this.$select.hide();
				this.$remove.hide();
				this.$result.hide();
				this.updateList([]);
			}
		}
	},

	hide: function() {
		this.$dropdown.addClass('hidden');
		this.$doc.off('click' + evtSuffix );
	},

	toggle: function() {
		if ( this.$dropdown.hasClass('hidden') ) {
			this.show();
		} else {
			this.hide();
		}
	},

	search: function( query ) {
		this.$result.hide();
		this.$loading.show();
		pslocation.search( query ).done( $.proxy(function( results ) {
			var list = [],
				description;

			for ( var i = 0; i < results.length; i++ ) {
				description = results[ i ].description;
				description = description.split(/,\s(.+)?/);
				list.push({
					place_id: results[ i ].place_id,
					name: description[ 0 ],
					description: description[ 1 ]
				});
			}

			this.updateList( list );
			this.$loading.hide();
			this.$result.show();
		}, this ));
	},

	filterData: function( data ) {
		if ( this._selected ) {
			data.location = this._selected;
		} else {
			data.location = '';
		}
		return data;
	},

	validate: function( valid, data ) {
		if ( this._selected ) {
			return true;
		}
		return valid;
	},

	render: function( list ) {
		var html;
		if ( this._selected ) {
			html  = '<i class="ps-icon-map-marker"></i>';
			html += '<b>' + this._selected.name + '</b>';
			list.push( html );
		}
		return list;
	},

	update: function( data ) {
		data = data && data.data || {};

		if ( data.location ) {
			this._selected = {
				name: data.location.name,
				description: data.location.description,
				latitude: data.location.latitude,
				longitude: data.location.longitude,
				zoom: data.location.zoom
			};

			this.$input.data('location', data.location.name );
			this.$input.data('latitude', data.location.latitude );
			this.$input.data('longitude', data.location.longitude );
			this.$input.val( data.location.name );
		} else {
			this._selected = false;
		}

		this._needUpdate = true;
		this.postbox.do_action('refresh');
	},

	updateList: function( list ) {
		var html = [];
		for ( var i = 0; i < list.length; i++ ) {
			html.push( this.listItemTemplate( list[ i ] ) );
		}
		this.$list.html( html.join('') );
	},

	updateMap: function( location ) {
		pslocation._gmap_load_library().done( $.proxy(function() {
			this.$map.show();
			pslocation._gmap_render_map( this.$map[0], location );
		}, this ));
	},

	select: function( name, lat, lng ) {
	},

	remove: function() {

	},

	destroy: function() {
		this.$toggle.off('click');
	},

	onToggle: _.throttle(function( e ) {
		e.preventDefault();
		e.stopPropagation();
		var $el = $( e.target );
		if ( ! this.$dropdown.is( $el ) && ! this.$dropdown.find( $el ).length ) {
			this.toggle();
		}
	}, 200 ),

	onInput: function() {
		var query = $.trim( this.$input.val() );
		if ( query ) {
			this.$result.hide();
			this.$loading.show();
			this._onInput( query );
		}
	},

	_onInput: _.debounce(function( query ) {
		this.search( query );
	}, 200 ),

	onSelectItem: function( e ) {
		var $item = $( e.currentTarget ),
			name = $item.find('.ps-js-location-listitem-name').text(),
			id = $item.data('place-id');

		e.preventDefault();
		e.stopPropagation();

		$item.addClass('ps-location-selected');
		$item.siblings().removeClass('ps-location-selected');
		this.$select.show();
		this.$remove.hide();
		this.$map.show();
		pslocation._gmap_get_place_detail( id ).done( $.proxy(function( place ) {
			var name = place.name,
				loc = place.geometry.location;
			this.$input.data('tmp-location', name ).data('tmp-latitude', loc.lat() ).data('tmp-longitude', loc.lng() );
			pslocation._gmap_render_map( this.$map[0], place );
		}, this ));
	},

	onSelect: function( e ) {
		e.preventDefault();
		e.stopPropagation();

		var name = this.$input.data('tmp-location'),
			latitude = this.$input.data('tmp-latitude'),
			longitude = this.$input.data('tmp-longitude');

		this.$input.data('location', name );
		this.$input.data('latitude', latitude );
		this.$input.data('longitude', longitude );
		this.$input.val( name );
		this.$select.hide();
		this.$remove.show();
		this.$dropdown.addClass('hidden');

		this._selected = {
			name: name,
			latitude: latitude,
			longitude: longitude
		};

		this.postbox.do_action('refresh');
	},

	onRemove: function( e ) {
		e.preventDefault();
		e.stopPropagation();
		this.$input.removeData('location').removeData('latitude').removeData('longitude').val('');
		this.$list.find('.ps-location-selected').removeClass('ps-location-selected');
		this.$remove.hide();
		this.$map.hide();

		this._selected = false;
		this.postbox.do_action('refresh');
	},

	onDocumentClick: function( e ) {
		var $el = $( e.target );
		if ( ! $el.closest( this.$toggle ).length ) {
			this.hide();
		}
	}

};

return PsPostboxLocation;

});
