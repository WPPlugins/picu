var picu = picu || {};

picu.SendView = Backbone.View.extend({

	model: picu.appState,
	tagName: 'div',
	className: 'picu-modal',
	id: 'picu-send',

	template: _.template( jQuery( "#picu-send-selection" ).html() ),

	initialize: function( options ) {
		this.collection = options.collection;
		this.router = options.router;

		// Key bindings
		_.bindAll( this , 'keyAction' );
		$( document ).on( 'keydown', this.keyAction);
	},

	render: function() {
		var imagecount = this.collection.length;
		var selected = this.collection.where({selected: true}).length;

		var sendSelectionTemplate = this.template({selected: selected, imagecount: imagecount, title: this.model.get( 'title' )});
		this.$el.html( sendSelectionTemplate );
		return this;
	},

	events: {
		'click #picu-send-button': 'sendSelection',
		'keydown': 'keyAction'
	},

	sendSelection: function( e ) {

		e.preventDefault();

		$( '<div class="loading">loading</div>' ).insertBefore( '#picu-send-button' );
		$( '#picu-send-button' ).hide();

		// Get imageID from models in the collection where selected is true
		var selection = _.map( this.collection.where({selected: true}), function( s ){ return s.attributes.imageID; });

		// Get approval message from the textarea
		var approvalMessage = $( '#picu-approval-message' ).val();

		var current = function successCallback() {}
		current.model = this.model;
		current.router = this.router;

		//Send AJAX request
		$.post( this.model.get( 'ajaxurl' ), {

			action: 'picu_send_selection',
			security: this.model.get( 'nonce' ),
			postid: this.model.get( 'postid' ),
			selection: selection,
			approval_message: approvalMessage,
			intent: 'approve'

		}, function( response ) {

			if ( response.success == true ) {

				// Set poststatus to approved
				current.model.set({'poststatus': 'approved'});
				picu.poststatus = 'approved';

				// On success, show approved view
				location.href = "#approved";

			}
			else {
				// Show error message
				$( '.picu-collection' ).append('<div class="overlay fail"><div class="message"><p>' + response.data.message + '</p><p><a class="picu-button small primary js-close-message" href="#">OK</a></p></div></div>');

			}

		}).fail( function() {
			// Ajax fail
			$( '.picu-collection' ).append('<div class="overlay fail"><div class="message"><p>Error: Request failed.<br />Do you have a working internet connection?</p><p><a class="picu-button small primary js-close-message" href="#">OK</a></p></div></div>');
		});

	},

	keyAction: function( e ) {

		// ESC key
		if ( e.keyCode == 27 ) {
			e.preventDefault();
			this.router.navigate('index', {trigger: true} );
		}
	},

	remove: function() {
		// Unbind keydown
		$( document ).off( 'keydown', this.keyAction );
		// Remove yourself
        $( '#picu-send' ).remove();
	}

});