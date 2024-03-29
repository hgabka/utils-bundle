var pagepartEditor = {};
var richeditor = require('./richeditor.js').richeditor;
var urlChooser = require('./urlChooser.js').urlChooser;
var nestedform = require('./nested-form.js').nestedform;
var appLoading = require('./apploading.js').appLoading;
var checkIfEdited = require('./checkifedited.js').checkIfEdited;
var ajaxModal = require('./ajaxmodal.js').ajaxModal;
var colorPicker = require('./colorpicker.js').colorPicker;
var charactersLeft = require('./charactersleft.js').charactersLeft;
var tooltip = require('./tooltip.js').tooltip;

require('./sortable.js');
import datePicker from './datepicker.js';
require('./velocity.js');

pagepartEditor.pagepartEditor = function (window) {

    var events = {
        add: [],
        edit: [],
        delete: []
    };

    var init, addPagePart, editPagePart, deletePagePart, movePagePartUp, movePagePartDown, subscribeToEvent,
        unSubscribeToEvent, executeEvent, reInit, reOrder, resizePagePartView, resizeAllRegionPp;

    init = function () {
        var $body = $('body');

        // Add
        $body.on('change', '.js-add-pp-select', function () {
            addPagePart($(this));
        });

        // Edit
        $body.on('click', '.js-edit-pp-btn', function () {
            editPagePart($(this));
        });

        // Del
        $body.on('click', '.js-delete-pp-btn', function () {
            deletePagePart($(this));
        });

        // Move up
        $body.on('click', '.js-move-up-pp-btn', function () {
            movePagePartUp($(this));
        });

        // Move down
        $body.on('click', '.js-move-down-pp-btn', function () {
            movePagePartDown($(this));
        });

        $body.on('click', '.js-resize-pp-view-btn', function () {
            resizePagePartView($(this));
        });

        $body.on('click', '.js-resize-all-pp', function (e) {
            resizeAllRegionPp($(this));

            e.preventDefault();
        });
    };


    // Add
    addPagePart = function ($select) {
        if (!$select.val()) {
            return false;
        }

        var $targetContainer = $select.closest('.js-pp-container'),
            requestUrl = $select.data('url');

        // Get necessary data
        var pageClassName = $targetContainer.data('pageclassname'),
            pageId = $targetContainer.data('pageid'),
            context = $targetContainer.data('context'),
            ppType = $select.val();

        // Set Loading
        appLoading.appLoading.addLoading();

        // Ajax Request
        $.ajax({
            url: requestUrl,
            data: {
                'pageclassname': pageClassName,
                'pageid': pageId,
                'context': context,
                'type': ppType
            },
            async: true,
            success: function (data) {
                // Add PP
                var firstSelect = $select.hasClass('js-add-pp-select--first');
                var elem;
                if (firstSelect) {
                    elem = $('#parts-' + context).prepend(data);
                } else {
                    elem = $select.closest('.js-sortable-item').after(data);
                }

                // Create a temporary node of the new PP
                var $temp = $('<div>');
                $temp.append(data);

                // Check if some javascript needs to be reinitialised for this PP
                reInit($temp);

                // Remove Loading
                appLoading.appLoading.removeLoading();

                // Enable leave-page modal
                checkIfEdited.checkIfEdited.edited();


                // Reset ajax-modals
                ajaxModal.ajaxModal.resetAjaxModals();

                executeEvent('add');
                
                if (typeof Admin !== 'undefined') {
                    let $container = firstSelect ? $('#parts-' + context).find('.js-sortable-item').first() : $select.closest('.js-sortable-item').nextAll('.js-sortable-item').first();
                    if ($container.length > 0) {
                        Admin.setup_select2($container);
                        Admin.setup_icheck($container);
                        Admin.setup_checkbox_range_selection($container);
                        Admin.setup_form_tabs_for_errors($container);
                        Admin.setup_inline_form_errors($container);
                        Admin.setup_collection_counter($container);
                    }
                }
            }
        });

        // Reset select
        $select.val('');
    };


    // Edit
    editPagePart = function ($btn) {
        var $targetId = $btn.data('target-id');

        // Enable "leave page" modal
        checkIfEdited.checkIfEdited.edited();

        // Show edit view and hide preview
        $('#' + $targetId + '-edit-view').removeClass('pp__view__block--hidden');
        $('#' + $targetId + '-preview-view').addClass('pp__view__block--hidden');

        // Add edit active class
        var $container = $('#pp-' + $targetId);
        $container.addClass('pp--edit-active');

        // Reinit custom selects
       // hgutils.advancedSelect.init();

        // Set Active Edit
        window.activeEdit = $targetId;

        executeEvent('edit', $container);
    };


    // Delete
    deletePagePart = function ($btn) {
        var $targetId = $btn.data('target-id'),
            $container = $('#' + $targetId + '-pp-container');

        // Enable "leave page" modal
        checkIfEdited.checkIfEdited.edited();

        // Slideup and empty container
        $container.velocity('slideUp', {
            duration: 300
        });

        $container.empty();

        // Check is-deleted checkbox
        $('#' + $targetId + '-is-deleted').prop('checked', true);

        // Hide delete modal
        $('#delete-pagepart-modal-' + $targetId).modal('hide');
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
        executeEvent('delete', $container);
    };


    // Move up
    movePagePartUp = function ($btn) {
        var $targetId = $btn.data('target-id');

        var $currentPp = $btn.parents('#' + $targetId + '-pp-container');
        var $previousPp = $currentPp.prevAll('.sortable-item:first');

        // ReInit the modules. This is needed for a known bug in CKEDITOR. When moving a element with a ckeditor in
        // The DOM, the ckeditor needs to be reinitialized.
        reInit($currentPp);
        if ($previousPp.length) {
            $($previousPp).before($currentPp);
            // Update display order.
            reOrder($currentPp.parent());
            // Enable "leave page" modal
            checkIfEdited.checkIfEdited.edited();
        }

        $currentPp.velocity('scroll', {
            duration: 500,
            offset: -200,
            easing: 'ease-in-out'
        });

        // Set Active Edit
        window.activeEdit = $targetId;
    };


    // Move down
    movePagePartDown = function ($btn) {
        var $targetId = $btn.data('target-id');

        var $currentPp = $btn.parents('#' + $targetId + '-pp-container');
        var $nextPp = $currentPp.nextAll('.sortable-item:first');
        // ReInit the modules. This is needed for a known bug in CKEDITOR. When moving a element with a ckeditor in
        // The DOM, the ckeditor needs to be reinitialized.
        reInit($currentPp);
        if ($nextPp.length) {
            $($nextPp).after($currentPp);
            // Update display order.
            reOrder($currentPp.parent());
            // Enable "leave page" modal
            checkIfEdited.checkIfEdited.edited();
        }

        $currentPp.velocity('scroll', {
            duration: 500,
            offset: -200,
            easing: 'ease-in-out'
        });

        // Set Active Edit
        window.activeEdit = $targetId;
    };

    //Resize
    resizePagePartView = function ($btn) {
        var $targetId = $btn.data('target-id');

        var $parentEl = $("#" + $targetId);
        var $target = $('#' + $targetId + '-preview-view');
        var $resizeTarget = $target.parent();

        $resizeTarget.toggleClass('action--maximize');
        $btn.toggleClass('pp__actions__action--resize-max');

        if ($resizeTarget.hasClass('action--maximize')) {
            $btn.find('i').removeClass('fa-minus').addClass('fa-plus');
            $resizeTarget.velocity({"height": "7rem"}, {duration: 400, easing: 'ease-in-out'});
        } else {
            $btn.find('i').removeClass('fa-plus').addClass('fa-minus');
            $resizeTarget.velocity({"height": "100%"}, {duration: 400, easing: 'ease-in-out'});
        }

    };

    resizeAllRegionPp = function ($btn) {
        var $target = $btn.data('target');

        var $parentEl = $("#" + $target);
        var $resizeTargets = $parentEl.find('.pp__view');

        if($btn.hasClass('region__actions__min')) {
            $parentEl.velocity({"height": "11.8rem"}, {duration: 400, easing: 'ease-in-out'});
            $parentEl.addClass('action--maximize');
        }else if($btn.hasClass('region__actions__max')) {
            $parentEl.velocity({"height": "100%"}, {duration: 400, easing: 'ease-in-out'});
            $parentEl.removeClass('action--maximize');
        }
    };

    // subsribe to an event.
    subscribeToEvent = function (eventName, callBack) {
        if (!events.hasOwnProperty(eventName)) {
            throw new Error("PagePartEditor: I cannot let you subscribe to the unknown event named: " + eventName);
        }
        events[eventName].push(callBack);
    };
    unSubscribeToEvent = function (eventName, callback) {
        if (!events.hasOwnProperty(eventName)) {
            throw new Error("PagePartEditor: I cannot let you unSubscribe to the unknown event named: " + eventName);
        }
        events = events.filter(function (cb) {
            return cb !== callback
        });
    };
    executeEvent = function (eventName, target) {
        events[eventName].forEach(function (cb) {
            cb({target: target})
        })
    };
    reInit = function($el) {
        var uniqueModules = [];

        $el.find('*[data-reinit-js]').each(function() {
            // Get modules from data attribute
            var modules = $(this).data('reinit-js');

            if (modules) {
                for (var i = 0; i < modules.length; i++) {
                    // Check if there really is a module with the given name and it if has a public reInit function
                    if ($.inArray(modules[i], uniqueModules) === -1) {
                        uniqueModules.push(modules[i]);
                        switch (modules[i]) {
                            case 'richEditor':
                                richeditor.richEditor.reInit();
                                break;
                            case 'nestedForm':
                                nestedform.nestedForm.reInit();
                                break;
                            case 'colorpicker':
                                colorPicker.colorpicker.reInit();
                                break;
                            case 'datepicker':
                                datePicker.datepicker.reInit();
                                break;
                            case 'charactersLeft':
                                charactersLeft.charactersLeft.reInit();
                                break;
                            case 'tooltip':
                                tooltip.tooltip.reInit();
                                break;
                        }
                    }
                }
            }
        });
    };
    reOrder = function($container) {
        var i = 0;
        $container.children('.sortable-item:visible').each(function() {
            var $sortEl = $(this).find('#' + $(this).data('sortkey'));
            $sortEl.val(i++);
        });
    };
    return {
        init: init,
        subscribeToEvent: subscribeToEvent,
        unSubscribeToEvent: unSubscribeToEvent
    };

}(window);

export default pagepartEditor;
