jQuery(function($){
	$(document).ready(function() {

		$( '.picu-collection' ).on( 'click', '.js-close-message', function( e ) {
			e.preventDefault();
			$( '.overlay' ).remove();
		});

	});
});