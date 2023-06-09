/**
 * Utilities for the admin dashboard
 */

jQuery( document ).ready( function( $ ) {
	'use strict';

	// Select Icon on Click.
	$( 'body' ).on( 'click', '.js-selectable-icon', function ( ev ) {
		ev.preventDefault();
		var $this = $( this );
		$this.siblings( '.js-icon-input' ).val( $this.data( 'iconname' ) ).change();
	} );

} );


/********************************************************
 			Backbone code for repeating fields in widgets
********************************************************/

// Namespace for Backbone elements.
window.MedicPress = {
	Models:    {},
	ListViews: {},
	Views:     {},
	Utils:     {},
};

/**
 ******************** Backbone Models *******************
 */

_.extend( MedicPress.Models, {
	OpeningTimeItem: Backbone.Model.extend( {
		defaults: {
			'day': '',
			'time': '',
			'less-important': '',
		}
	} )
} );

/**
 ******************** Backbone Views *******************
 */

// Generic single view that others can extend from.
MedicPress.Views.Abstract = Backbone.View.extend( {
	initialize: function ( params ) {
		this.templateHTML = params.templateHTML;

		return this;
	},

	render: function () {
		this.$el.html( Mustache.render( this.templateHTML, this.model.attributes ) );

		return this;
	},

	destroy: function ( ev ) {
		ev.preventDefault();

		this.remove();
		this.model.trigger( 'destroy' );
	},
} );

_.extend( MedicPress.Views, {
	// View of a single opening time item.
	OpeningTimeItem: MedicPress.Views.Abstract.extend( {
		className: 'pt-widget-single-opening-time-item',

		events: {
			'click .js-pt-remove-opening-time-item': 'destroy',
		},

		render: function () {
			this.$el.html( Mustache.render( this.templateHTML, this.model.attributes ) );

			this.$( 'input.js-less-important-checkbox' ).prop( 'checked', 'on' === this.model.get( 'less-important' ) );

			return this;
		},
	} )
} );



/**
 ******************** Backbone ListViews *******************
 *
 * Parent container for multiple view nodes.
 */

MedicPress.ListViews.Abstract = Backbone.View.extend( {

	initialize: function ( params ) {
		this.widgetId     = params.widgetId;
		this.itemsModel   = params.itemsModel;
		this.itemView     = params.itemView;
		this.itemTemplate = params.itemTemplate;

		// Cached reference to the element in the DOM.
		this.$items = this.$( params.itemsClass );

		// Collection of items(locations, people, testimonials,...).
		this.items = new Backbone.Collection( [], {
			model: this.itemsModel
		} );

		// Listen to adding of the new items.
		this.listenTo( this.items, 'add', this.appendOne );

		return this;
	},

	addNew: function ( ev ) {
		ev.preventDefault();

		var currentMaxId = this.getMaxId();

		this.items.add( new this.itemsModel( {
			id: (currentMaxId + 1)
		} ) );

		return this;
	},

	getMaxId: function () {
		if ( this.items.isEmpty() ) {
			return -1;
		}
		else {
			var itemWithMaxId = this.items.max( function ( item ) {
				return parseInt( item.id, 10 );
			} );

			return parseInt( itemWithMaxId.id, 10 );
		}
	},

	appendOne: function ( item ) {
		var renderedItem = new this.itemView( {
			model:        item,
			templateHTML: jQuery( this.itemTemplate + this.widgetId ).html()
		} ).render();

		var currentWidgetId = this.widgetId;

		// If the widget is in the initialize state (hidden), then do not append a new item.
		if ( '__i__' !== currentWidgetId.slice( -5 ) ) {
			this.$items.append( renderedItem.el );
		}

		return this;
	}
} );

_.extend( MedicPress.ListViews, {
	// Collection of all opening time items, but associated with each individual widget.
	OpeningTimeItems: MedicPress.ListViews.Abstract.extend( {
		events: {
			'click .js-pt-add-opening-time-item': 'addNew'
		}
	} )
} );

/**
 ******************** Repopulate Functions *******************
 */

_.extend( MedicPress.Utils, {
	// Generic repopulation function used in all repopulate functions
	repopulateGeneric: function ( collectionType, parameters, json, widgetId ) {
		var collection = new collectionType( parameters );

		// Convert to array if needed
		if ( _( json ).isObject() ) {
			json = _( json ).values();
		}

		// Add all items to collection of newly created view
		collection.items.add( json, { parse: true } );
	},

	/**
	 * Function which adds the existing opening time items to the DOM
	 * @param  {json} openingTimeItemJSON
	 * @param  {string} widgetId ID of widget from PHP $this->id
	 * @return {void}
	 */
	repopulateOpeningTimeItems: function ( openingTimeItemJSON, widgetId ) {
		var parameters = {
			el:           '#opening-time-items-' + widgetId,
			widgetId:     widgetId,
			itemsClass:   '.opening-time-items',
			itemTemplate: '#js-pt-opening-time-item-',
			itemsModel:   MedicPress.Models.OpeningTimeItem,
			itemView:     MedicPress.Views.OpeningTimeItem,
		};

		this.repopulateGeneric( MedicPress.ListViews.OpeningTimeItems, parameters, openingTimeItemJSON, widgetId );
	}
} );
