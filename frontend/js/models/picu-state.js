var picu = picu || {};

picu.appState = Backbone.Model.extend({

	defaults: {
		nonce: false,
		postid: false,
		poststatus: false,
		title: false,
		date: false,
		description: false,
		ajaxurl: false
	}

});