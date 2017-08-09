var picu = picu || {};

picu.singleImage = Backbone.Model.extend({

    defaults: {
        number: '1',
        imageID: '',
        title: 'image title',
        description: 'image description',
        imagePath: 'images/placeholder.jpg',
        imagePath_small: 'images/placeholder.jpg',
        orientation: 'landscape',
        selected: false
    }

});