var hgabkautils = {};
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
var tooltip = require('./tooltip.js').tooltip;
var checkIfEdited = require('./checkifedited.js').checkIfEdited;
var ajaxModal = require('./ajaxmodal.js').ajaxModal;
var cargobayToggle = require('./cargobay/jquery.toggle.js').cargobayToggle;
var cargobayScrollToTop = require('./cargobay/jquery.scroll-to-top.js').cargobayScrollToTop;
var appLoading = require('./apploading.js').appLoading;
var slugChooser = require('./slugchooser.js').slugChooser;
var appEntityVersionLock = require('./appentityversionlock.js').appEntityVersionLock;
var appNodeVersionLock = require('./appnodeversionlock.js').appNodeVersionLock;
var iconChooser = require('./iconchooser.js').iconChooser;
var preventDoubleClick = require('./preventdoubleclick.js').preventDoubleClick;
//var advancedSelect = require('./advancedselect.js').advancedSelect;

import datePicker from './datepicker.js';
import pagepartEditor from './pageparteditor.js';
import pageEditor from './pageeditor.js';
import filter from './filter.js';

require('../css/ajax-modal.scss');
require('../css/colorpicker.css');
require('../css/bootstrap-datetimepicker.css');
require('../css/jstree.css');
require('../css/select2.css');
require('../css/extra.css');
require('../css/hg_utils.scss');

import ResponsiveHelper from './responsiveHelper.js';
window.ResponsiveHelper = ResponsiveHelper;

window.CKEDITOR_BASEPATH = '/ckeditor/';
require('ckeditor/ckeditor.js');
require('ckeditor/lang/hu.js');
require('ckeditor/lang/en.js');
require('./ckeditor/styles.js');

import {myAlert, myConfirm, myLinkConfirm} from './alertFuncs.js';
window.myAlert = myAlert;
window.myConfirm = myConfirm;
window.myLinkConfirm = myLinkConfirm;

hgabkautils.app = (function($, window, undefined) {

    var init, appScroll, $mainActions;

    // General App init
    init = function() {
        cargobayToggle.cargobayToggle.init();
        cargobayScrollToTop.scrollToTop.init();

        appScroll();

        sidebarToggle.sidebartoggle.init();
         sidebarTree.sidebartree.init();
         sidebarSearchFocus.sidebarsearchfocus.init();
         autoCollapseButtons.autoCollapseButtons.init();
         autoCollapseTabs.autoCollapseTabs.init();
         charactersLeft.charactersLeft.init();
         preventDoubleClick.preventDoubleClick.init();
         filter.filter.init();
         tooltip.tooltip.init();
         checkIfEdited.checkIfEdited.init();
         ajaxModal.ajaxModal.init();
         richeditor.richEditor.init();
        // advancedSelect.advancedSelect.init();
         urlChooser.urlChooser.init();
         slugChooser.slugChooser.init();
         urlChooserTree.urlchoosertree.init();
         nestedform.nestedForm.init();
         colorPicker.colorpicker.init();
         datePicker.datepicker.init();
         pageEditor.pageEditor.init();
         pagepartEditor.pagepartEditor.init();
         appLoading.appLoading.init();
         appEntityVersionLock.appEntityVersionLock.init();
         appNodeVersionLock.appNodeVersionLock.init();
         iconChooser.iconChooser.init();
    };

    appScroll = function() {
        $mainActions = $('#page-main-actions-top');
        if($mainActions) {
            var _onScroll, _requestTick, _update,
                latestKnownScrollY = 0,
                ticking = false;

            _onScroll = function() {
                latestKnownScrollY = window.pageYOffset;
                _requestTick();
            };

            _requestTick = function() {
                if(!ticking) {
                    window.requestAnimationFrame(_update);
                }

                ticking = true;
            };

            _update = function() {
                ticking = false;
                var currentScrollY = latestKnownScrollY;
                var mainActions = require('./mainactions.js').mainActions;

                mainActions.mainActions.updateScroll(currentScrollY, $mainActions);
            };

            window.onscroll = function(e) {
                _onScroll();
            };
        }
    };

    return {
        init: init
    };

})(jQuery, window);

$(function() {
    hgabkautils.app.init();
    
    $('.js-sortable-link').on('click', e => {
        let href = $(e.currentTarget).data('order-url');
        window.location.href = href;
    });
});
