var picu = picu || {};

picu.GalleryView.Item = Backbone.View.extend({

    model: picu.singleImage,

    template: _.template( jQuery( "#picu-gallery-item" ).html() ),

    tagName: 'div',

    className: function() {
        if ( this.model.get( 'selected' ) == true ) {
            var selected = ' selected';
        }
        else {
            var selected = '';
        }
        if ( this.model.get( 'focused' ) == true ) {
            var focused = ' focused';
        }
        else {
            var focused = '';
        }
        return 'picu-gallery-item' + selected + focused;
    },

    id: function() {
        return 'picu-image-' + this.model.get( 'number' );
    },

    initialize: function( options ) {
		this.appstate = options.appstate;
        this.listenTo( this.model, 'change', this.render );
    },

    render: function() {
        var singleImageTemplate = this.template( this.model.attributes );

        this.$el.removeClass( 'flash' );
        
        this.$el.html( singleImageTemplate );
        return this;
    },

    events: {
        'click label': 'toggleImageSelection',
        'click .picu-imgbox': 'toggleFocus'
    },

    toggleImageSelection: function() {
		picu.saveSelection( this );
    }

});