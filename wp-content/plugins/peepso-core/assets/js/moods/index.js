(function( $, factory ) {

	var PsMoods = factory( $ );

	ps_observer.add_action('postbox_init', function( postbox ) {
		var inst = new PsMoods( postbox );
	}, 10, 1 );

})( jQuery, function( $ ) {

var LABEL = $('#mood-text-string').text();

// mood list
var MOODS = false;

var evtSuffix = '.ps-postbox-moods';

/**
 * Postbox location addon.
 */
function PsPostboxMood() {
	this.__constructor.apply( this, arguments );
}

PsPostboxMood.prototype = {

	/**
	 * Initialize postbox with add mood functionality.
	 * @param {PsPostbox} postbox Postbox instance in which mood functionality will be attached to.
	 */
	__constructor: function( postbox ) {
		this.postbox = postbox;

		// element caches
		this.$doc = $( document );
		this.$toggle = postbox.$tabContext.find('#mood-tab');
		this.$dropdown = postbox.$tabContext.find('.ps-js-postbox-mood');
		this.$remove = this.$dropdown.find('button');

		// event handler
		this.$toggle.on('click' + evtSuffix, $.proxy( this.toggle, this ));
		this.$dropdown.on('click' + evtSuffix, '.mood-list', $.proxy( this.select, this ));
		this.$remove.on('click' + evtSuffix, $.proxy( this.remove, this ));

		this._mapMoods();

		// filters and actions
		postbox.add_action('update', this.update, 10, 2, this );
		postbox.add_filter('render_addons', this.render, 10, 1, this );
		postbox.add_filter('data', this.filterData, 10, 1, this );
		postbox.add_filter('data_validate', this.validate, 10, 2, this );
	},

	/**
	 * Show mood dropdown.
	 */
	show: function() {
		this.$dropdown.show();
		this.$doc.on('click' + evtSuffix, $.proxy( this.onDocumentClick, this ));
	},

	/**
	 * Hide mood dropdown.
	 */
	hide: function() {
		this.$dropdown.hide();
		this.$doc.off('click' + evtSuffix );
	},

	/**
	 * Toggle mood dropdown.
	 */
	toggle: function() {
		if ( this.$dropdown.is(':visible') ) {
			this.hide();
		} else {
			this.show();
		}
	},

	/**
	 * Attach selected mood into post data.
	 * @param {object} data
	 */
	filterData: function( data ) {
		if ( this._selected ) {
			data.mood = this._selected.value;
		} else {
			data.mood = '';
		}
		return data;
	},

	validate: function( valid, data ) {
		if ( this._selected ) {
			return true;
		}
		return valid;
	},

	/**
	 * Select a mood.
	 */
	select: function( e ) {
		var $a = $( e.currentTarget );

		if ( $a.length ) {
			this._selected = {
				value: $a.data('option-value'),
				label: $a.data('option-display-value'),
				className: $a.find('i').attr('class')
			};
			this.$remove.show();
			this.postbox.do_action('refresh');
		}
	},

	/**
	 * Removes selected mood.
	 */
	remove: function() {
		this._selected = false;
		this.$remove.hide();
		this.postbox.do_action('refresh');
	},

	/**
	 * Update selected mood.
	 */
	update: function( data ) {
		data = data && data.data || {};
		if ( data.mood ) {
			this._selected = {
				value: data.mood,
				label: MOODS[ data.mood ].label,
				className: MOODS[ data.mood ].icon
			};
			this.$remove.show();
		} else {
			this._selected = false;
			this.$remove.hide();
		}
		this.postbox.do_action('refresh');
	},

	/**
	 * Render selected mood.
	 */
	render: function( list ) {
		var html;
		if ( this._selected ) {
			html  = '<i class="' + this._selected.className + '"></i>';
			html += '<b> ' + LABEL + this._selected.label + '</b>';
			list.push( html );
		}
		return list;
	},

	/**
	 *
	 */
	_mapMoods: function() {
		var $moods;

		if ( !MOODS ) {
			$moods = this.$dropdown.find('a.mood-list');
			$moods.each(function() {
				var $a = $( this ),
					$i = $a.find('i'),
					id = $a.data('option-value'),
					label = $a.data('option-display-value'),
					icon = $i.attr('class');

				MOODS || (MOODS = {});
				MOODS[ id ] = {
					icon: icon,
					label: label
				};
			});
		}
	},

	/**
	 *
	 */
	onDocumentClick: function( e ) {
		var $el = $( e.target );
		if ( ! $el.closest( this.$toggle ).length ) {
			this.hide();
		}
	}

};

return PsPostboxMood;

});



////////////////////////////////////////////////////////////////////////////////////////////////////
// PsMoods (legacy)
////////////////////////////////////////////////////////////////////////////////////////////////////

(function( $, peepso, factory ) {

	factory( $, peepso );

})( jQuery || $, peepso, function( $, peepso ) {

/**
* Javascript code to handle mood events
*/
function PsMoods()
{
	this.$postbox = null;
	this.$mood = null;
	this.$mood_remove = null;
	this.$mood_dropdown_toggle = null;
	this.mood_selected = false;
	this.can_submit = false;
}

/**
 * Defines the postbox this instance is running on.
 * Called on postbox.js _load_addons()
 * @param {object} postbox This refers to the parent postbox object which this plugin may inherit, override, and manipulate its input boxes and behavior
 */
PsMoods.prototype.set_postbox = function(postbox)
{
	this.$postbox = postbox;
};

/**
 * Initializes this instance's container and selector reference to a postbox instance.
 * Called on postbox.js _load_addons()
 */
PsMoods.prototype.init = function()
{
	if (_.isUndefined(this.$postbox))
		return;

	var _self = this;

	ps_observer.add_filter("peepso_postbox_can_submit", function(can_submit) {
		can_submit.soft.push( _self.can_submit );
		return can_submit;
	}, 20, 1);

	this.$mood = jQuery("#postbox-mood", this.$postbox);
	this.$mood_dropdown_toggle = jQuery("#mood-tab .interaction-icon-wrapper > a", this.$postbox);
	this.$mood_remove = jQuery("#postbox-mood-remove", this.$postbox);

	// Add click event on all mood links
	this.$mood.on("click", "a.mood-list", function(e) {
		_self.select_mood(e);
	});

	// Add click event on remove mood
	this.$mood_remove.on("click", function() {
		_self.remove_mood();
	});

	this.$mood_dropdown_toggle.on("click", function() {
		_self.$mood.toggle();
	});

	this.$mood_dropdown_toggle.on("peepso.interaction-hide", function() {
		_self.$mood.hide();
	});

	// close the moods popup when click outside area
	jQuery(document).on("click", function(e) {
		var mood = jQuery("#mood-tab", _self.$postbox);
		if (!mood.is(e.target) && 0 === mood.has(e.target).length)
			_self.$mood.hide();
	});

	// close the moods popup when done with the post
	this.$postbox.on("postbox.post_cancel postbox.post_saved", function() {
		_self.remove_mood();
	});

	// This handles adding the selected mood to the postbox_req variable before submitting to server
	ps_observer.add_filter("postbox_req_" + this.$postbox.guid, function(req) {
		return _self.set_mood(req);
	}, 10, 1);

	ps_observer.add_filter("peepso_postbox_addons_update", function(list) {
		var mood, html;
		if ( _self.mood_selected ) {
			mood = _self.mood_selected;
			html = '<i class="ps-emoticon ' + mood[0] + '"></i> <b>' + mood[1] + '</b>';
			list.push(html);
		}

		return list;
	}, 10, 1);
};

/**
 * Sets #postbox-mood when user clicks a mood icon
 * @param {object} e Click event
 */
PsMoods.prototype.select_mood = function(e)
{
	var a                 = jQuery(e.target).closest("a");
	var btn               = jQuery("#mood-tab", this.$postbox);
	var input             = jQuery("#postbox-mood-input", this.$postbox);
	var placeHolder       = btn.find("a");
	var menu              = a.closest("#postbox-mood");
	var $postboxcontainer = this.$postbox.$textarea.parent();

	var icon = a.find("i").attr("class");
	var label = jQuery("#mood-text-string").text() + a.attr("data-option-display-value");

	input.val(a.attr("data-option-value"));
	this.$mood_remove.show();
	menu.hide();

	this.mood_selected = [ icon, label ];
	this.can_submit = true;
	this.$postbox.on_change();
};

/**
 * Clear #postbox-mood-input when user clicks remove mood button
 */
PsMoods.prototype.remove_mood = function()
{
	jQuery("span#postmood", this.$postbox.$textarea.parent()).remove();
	jQuery("#postbox-mood-input", this.$postbox).val("");

	this.$mood_remove.hide();
	this.$mood.hide();
	this.mood_selected = false;
	this.can_submit = false;
	this.$postbox.on_change();
};

/**
 * Adds the selected mood to the postbox_req variable
 * @param {object} req postbox request
 * @return {object} req Returns modified request with mood value
 */
PsMoods.prototype.set_mood = function(req)
{
	if ("undefined" === typeof(req.mood))
		req.mood = "";

	req.mood = jQuery("#postbox-mood-input", this.$postbox).val();
	return (req);
};

/**
 * Adds a new PsMoods object to the PostBox instance.
 * @param {array} addons An array of addons that are being pluged in to the PostBox.
 */
ps_observer.add_filter('peepso_postbox_addons', function(addons) {
	addons.push(new PsMoods);
	return addons;
}, 10, 1);

});
