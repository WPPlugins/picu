var picu = picu || {};

picu.StatusBarView = Backbone.View.extend({

	model: picu.singleImage,

	template: _.template( jQuery( "#picu-status-bar" ).html() ),

	tagName: 'header',

	className: 'picu-status-bar',

	initialize: function( options ) {
		this.collection = options.collection;
		this.appstate = options.model;

		this.update();
		this.listenTo( this.collection, 'change', function() {
			this.update();
		} );
	},

	events: {
		'click .picu-save': 'saveSelection',
		'click .picu-filter-selected': 'filterSelected',
		'click .picu-filter-unselected': 'filterUnselected',
		'click .picu-filter-reset': 'filterReset'
	},

	update: function() {
		var all = this.collection.length;
		var selected = this.collection.where({selected: true}).length;

		// TODO: Get arguments passed to template in order
		var statusbar = this.template({all: all, selected: selected, appstate: this.appstate, zip: this.appstate.get( 'zip' ), selection_restriction: this.appstate.get('selection_restriction') });
		this.$el.html( statusbar );
	},

	filterSelected: function() {
		$( '.picu-error' ).remove();

		this.appstate.set( 'filter', 'selected' );
		$( 'body' ).removeClass( 'filter-unselected' ).addClass( 'filter-selected' );

		if ( this.collection.where({selected: true}).length <= 0 ) {
			$( '.picu-gallery' ).append('<div class="picu-error"><div class="error-inner"><h2>You have not selected any images.</h2><p><a class="error-filter-reset" href="#index"><svg viewBox="0 0 100 100"><use xlink:href="#icon_close"></use></svg>Reset filter to show all images</span></p></div></div>');
		}
	},

	filterUnselected: function() {
		$( '.picu-error' ).remove();

		this.appstate.set( 'filter', 'unselected' );
		$( 'body' ).removeClass( 'filter-selected' ).addClass( 'filter-unselected' );

		if ( this.collection.where({selected: true}).length >= this.collection.length ) {
			$( '.picu-gallery' ).append('<div class="picu-error"><div class="error-inner"><h2>You have no <em>unselected</em> images.</h2><p><a class="error-filter-reset" href="#index"><svg viewBox="0 0 100 100"><use xlink:href="#icon_close"></use></svg>Reset filter to show all images</span></p></div></div>');
		}
	},

	filterReset: function() {
		this.appstate.unset( 'filter' );
		$( 'body' ).removeClass( 'filter-selected filter-unselected' );
		$( '.picu-error' ).remove();
	},

	saveSelection: function() {
		// Hide save button, show spinner
		$( '.picu-save' ).hide();
		$( '<div class="picu-saving">loading</div>' ).insertBefore( '.picu-save' );

		var selection = _.map( this.collection.where({selected: true}), function( s ){ return s.attributes.imageID; });

		// Send AJAX request
		$.post( this.appstate.get( 'ajaxurl' ), {

			action: 'picu_send_selection',
			security: this.appstate.get( 'nonce' ),
			postid: this.appstate.get( 'postid' ),
			selection: selection,
			intent: 'temp'

		}, function( response ) {

			// Display response as overlay
			var overlayclass = '';
			if ( response.success == true ) {
				overlayclass = ' success';
			} else {
				overlayclass = ' fail';
			}

			$( '.picu-collection' ).append('<div class="overlay'+ overlayclass +'"><div class="message"><p>' + response.data.message + '</p><p><a class="picu-button small primary js-close-message">' + response.data.button_text + '</a></p></div></div>');

			// Remove spinner, show save button
			$( '.picu-saving' ).remove();
			$( '.picu-save' ).show();

		}).fail( function() {
			// Ajax fail
			$( '.picu-collection' ).append('<div class="overlay fail"><div class="message"><p>Error: Request failed.<br />Do you have a working internet connection?</p><p><a class="picu-button small primary js-close-message" href="#">OK</a></p></div></div>');

			// Remove spinner, show save button
			$( '.loading' ).remove();
			$( '.picu-save' ).show();
		});

	}

});