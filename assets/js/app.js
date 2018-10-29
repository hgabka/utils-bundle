var hgabkautils = hgabkautils || {};
var richeditor = require('./richeditor.js').richeditor;
var urlChooser = require('./urlChooser.js').urlChooser;
require('../css/ajax-modal.css');

hgabkautils.app = (function($, window, undefined) {

    var init;


    // General App init
    init = function() {
         richeditor.richEditor.init();
         urlChooser.urlChooser.init();
    };


    return {
        init: init
    };

})(jQuery, window);

$(function() {
    hgabkautils.app.init();
});
