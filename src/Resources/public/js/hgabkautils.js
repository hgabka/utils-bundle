var hgabkautils = hgabkautils || {};

hgabkautils.ajaxModal = (function ($, window, undefined) {

    var init,
        initModals, resetAjaxModals;

    init = function () {
        $(document).on('show.bs.modal', '.js-ajax-modal', initModals);
    };


    initModals = function (e) {
        var $modal = $(this);


        if (!$modal.data('loaded')) {
            var $btn = $(e.relatedTarget),
                link = $btn.data('link');

            $modal.data('loaded', true);
            $modal.find('.js-ajax-modal-body').append('<iframe class="ajax-modal__body__iframe" frameborder="0" src="' + link + '" width="100%" height="100%" scrolling="auto"></iframe>');
        }
    }


    resetAjaxModals = function () {
        $('.js-ajax-modal').off('show.bs.modal', initModals);
        $('.js-ajax-modal').on('show.bs.modal', initModals);
    };


    return {
        init: init,
        resetAjaxModals: resetAjaxModals
    };

})(jQuery, window);

hgabkautils.mediaChooser = (function(window, undefined) {

    var init, initDelBtn;

    var $body = $('body');

    init = function() {
        // Save and update preview can be found in url-chooser.js

        initDelBtn();
    };


    // Del btn
    initDelBtn = function() {
        console.log($('.js-media-chooser-del-preview-btn').length);
        $body.on('click', '.js-media-chooser-del-preview-btn', function(e) {
            var $this = $(this),
                linkedID = $this.data('linked-id'),
                $widget = $('#' + linkedID + '-widget'),
                $input = $('#' + linkedID);

            $this.parent('.media-chooser__preview').find('.media-chooser__preview__img').attr({'src': '', 'srcset': '', 'alt': ''});

            $(".media-thumbnail__icon").remove();

            $widget.removeClass('media-chooser--choosen');
            $input.val('');
        });
    };


    return {
        init: init
    };

})(window);


hgabkautils.app = (function($, window, undefined) {

    var init, appScroll,
        $mainActions = $('#page-main-actions-top');


    // General App init
    init = function() {
        hgabkautils.ajaxModal.init();
        hgabkautils.mediaChooser.init();
    };


    return {
        init: init
    };

})(jQuery, window);


$(function() {
    hgabkautils.app.init();
    var $body = $('body');

    $body.on('click', '.js-media-chooser-del-preview-btn', function(e) {
        var $this = $(this),
            linkedID = $this.data('linked-id'),
            $widget = $('#' + linkedID + '-widget'),
            $input = $('#' + linkedID);

        $this.parent('.media-chooser__preview').find('.media-chooser__preview__img').attr({'src': '', 'srcset': '', 'alt': ''});

        $(".media-thumbnail__icon").remove();

        $widget.removeClass('media-chooser--choosen');
        $input.val('');
    });

});
