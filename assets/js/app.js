var hgabkautils = hgabkautils || {};
var richeditor = require('./richeditor.js').richeditor;
var urlChooser = require('./urlChooser.js').urlChooser;
require('../css/ajax-modal.css');
require('../css/alertify.core.css');
require('../css/alertify.default.css');
import alertify from './alertify.js';
window.alertify = alertify;
import ResponsiveHelper from './responsiveHelper.js';
window.ResponsiveHelper = ResponsiveHelper;

import {myAlert, myConfirm, myLinkConfirm} from './alertFuncs.js';
window.myAlert = myAlert;
window.myConfirm = myConfirm;
window.myLinkConfirm = myLinkConfirm;

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
