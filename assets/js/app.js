var hgabkautils = hgabkautils || {};
var richeditor = require('./richeditor.js').richeditor;
var urlChooser = require('./urlChooser.js').urlChooser;
var nestedform = require('./nested-form.js').nestedform;
var colorPicker = require('./colorpicker.js').colorPicker;
var sidebarToggle = require('./sidebartoggle.js').sidebarToggle;
var sidebarTree = require('./sidebartree.js').sidebarTree;
var sidebarSearchFocus = require('./sidebarsearchfocus.js').sidebarSearchFocus;
var autoCollapseButtons = require('./autocollapsebuttons.js').autoCollapseButtons;
var autoCollapseTabs = require('./autocollapsetabs.js').autoCollapseTabs;
var charactersLeft = require('./charactersleft.js').charactersLeft;
var urlChooserTree = require('./urlchoosertree.js').urlChooserTree;
import datePicker from './datepicker.js';
import filter from './filter.js';

require('../css/ajax-modal.css');
require('../css/alertify.core.css');
require('../css/alertify.default.css');
require('../css/colorpicker.css');
require('../css/bootstrap-datetimepicker.css');
require('../css/jstree.css');
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
         sidebarToggle.sidebartoggle.init();
         sidebarTree.sidebartree.init();
         sidebarSearchFocus.sidebarsearchfocus.init();
         autoCollapseButtons.autoCollapseButtons.init();
         autoCollapseTabs.autoCollapseTabs.init();
         charactersLeft.charactersLeft.init();
         filter.filter.init();
         richeditor.richEditor.init();
         urlChooser.urlChooser.init();
         urlChooserTree.urlchoosertree.init();
         nestedform.nestedForm.init();
         colorPicker.colorpicker.init();
         datePicker.datepicker.init();
    };


    return {
        init: init
    };

})(jQuery, window);

$(function() {
    hgabkautils.app.init();
});
