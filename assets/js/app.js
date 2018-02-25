var hgabkautils = hgabkautils || {};
var richeditor = require('./richeditor.js').richeditor;
require('../css/ajax-modal.css');
hgabkautils.app = (function($, window, undefined) {

    var init;


    // General App init
    init = function() {
         richeditor.richEditor.init();
    };


    return {
        init: init
    };

})(jQuery, window);

$(function() {
    hgabkautils.app.init();
});
