var hgabkautils = hgabkautils || {};
var richeditor = require('./richeditor.js').richeditor;
var slugChooser = require('./slugChooser.js').slugChooser;
var urlChooser = require('./urlChooser.js').urlChooser;
require('../css/ajax-modal.css');

hgabkautils.app = (function($, window, undefined) {

    var init;


    // General App init
    init = function() {
         richeditor.richEditor.init();
         slugChooser.slugChooser.init();
         urlChooser.urlChooser.init();
    };


    return {
        init: init
    };

})(jQuery, window);

$(function() {
    hgabkautils.app.init();
});
