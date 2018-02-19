var hgabkautils = hgabkautils || {};
var richeditor = require('./richeditor.js').richeditor;
hgabkautils.app = (function($, window, undefined) {

    var init, appScroll,
        $mainActions = $('#page-main-actions-top');


    // General App init
    init = function() {
 /*       cargobay.toggle.init();
        cargobay.scrollToTop.init();

        appScroll();

        hgabkautils.sidebartoggle.init();
        hgabkautils.sidebartree.init();
        hgabkautils.urlchoosertree.init();
        hgabkautils.sidebarsearchfocus.init();
        hgabkautils.filter.init();
        hgabkautils.sortableTable.init();
        hgabkautils.checkIfEdited.init();
        hgabkautils.preventDoubleClick.init();
        hgabkautils.datepicker.init();
        hgabkautils.autoCollapseButtons.init();
        hgabkautils.autoCollapseTabs.init();*/
        richeditor.richEditor.init();
 /*       hgabkautils.ajaxModal.init();
        hgabkautils.advancedSelect.init();

        hgabkautils.pageEditor.init();
        hgabkautils.pagepartEditor.init();

        hgabkautils.slugChooser.init();
        hgabkautils.urlChooser.init();
        hgabkautils.mediaChooser.init();
        hgabkautils.iconChooser.init();
        hgabkautils.bulkActions.init();
        hgabkautils.nestedForm.init();
        hgabkautils.appLoading.init();
        hgabkautils.tooltip.init();
        hgabkautils.colorpicker.init();
        hgabkautils.charactersLeft.init();
        hgabkautils.rangeslider.init();
        hgabkautils.googleOAuth.init();
        hgabkautils.appNodeVersionLock.init();
        hgabkautils.appEntityVersionLock.init() */
    };


    // On Scroll
    appScroll = function() {
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

                hgabkautils.mainActions.updateScroll(currentScrollY, $mainActions);
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
});
