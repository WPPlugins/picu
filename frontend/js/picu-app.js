var picu = picu || {};

/**
 * Boot picu app
 */
picu.boot = function( container, data, appstate ) {

	container = $( container );

	/**
	 * Create a collection of images
	 * @param collection data
	 */
	var gallery = new picu.GalleryCollection( jQuery.parseJSON( data ) );


	/**
	 * Create appState model
	 * @param all the data
	 */
	var appState = new picu.appState( jQuery.parseJSON( appstate ) );

	/**
	 * Create the router
	 *
	 * @param container (html element in which our app lives)
	 * @param collection of images
	 * @param nonce to verify any ajax requests, like save or send selection
	 * @param post id
	 * @param post status
	 * @param post title
	 * @param post description
	 * @param ajax url
	 */
	var router = new picu.Router({el: container, collection: gallery, appstate: appState })

	Backbone.history.start({pushState: false});
}


/**
 * Toggle model attribute selection
 *
 */
picu.saveSelection = function( image ) {

	// Validation: Check that image.model is actually our backbone model
	if ( image.model instanceof picu.singleImage ) {

		// Set selected attribute
		image.model.set( 'selected', ! image.model.get( 'selected' ) );

		// Change class like we already do in the lightbox template
		if ( image.model.get( 'selected' ) == true ) {
			image.$el.addClass( 'selected' );
		}
		else {
			image.$el.removeClass( 'selected' );
		}
	}
}