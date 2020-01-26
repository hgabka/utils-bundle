var hgutils = {};
hgutils.datepicker = (function($, window, undefined) {

    var init, reInit, _setDefaultDate, _initDatepicker;

    var _today = window.moment(),
        _tomorrow = window.moment(_today).add(1, 'days');

    var defaultFormat = 'DD-MM-YYYY',
        defaultCollapse = true,
        defaultKeepOpen = false,
        defaultMinDate = false,
        defaultShowDefaultDate = false,
        defaultLocale = 'hu',
        defaultStepping = 1,
		defaultWithTime = true;


    init = function() {
        $('.js-datepicker').each(function() {
            _initDatepicker($(this));
        });
    };

    reInit = function(el) {
        if (el) {
            _initDatepicker($(el));
        } else {
            $('.js-datepicker').each(function() {
                if (!$(this).hasClass('datepicker--enabled')) {
                    _initDatepicker($(this));
                }
            });
        }
    };

    _setDefaultDate = function(elMinDate) {
        if(elMinDate === 'tomorrow') {
            return _tomorrow;
        } else {
            return _today;
        }
    };


    _initDatepicker = function($el) {
        // Get Settings
        var elFormat = $el.data('format'),
            elCollapse = $el.data('collapse'),
            elKeepOpen = $el.data('keep-open'),
            elMinDate = $el.data('min-date'),
            elShowDefaultDate = $el.data('default-date'),
            elLocale = $el.data('locale'),
            elOptions = $el.data('options'),
            elStepping = $el.data('stepping');
            elWithTime = $el.data('with-time');


        // Set Settings
        var format = (elFormat !== undefined) ? elFormat : defaultFormat,
            collapse = (elCollapse !== undefined) ? elCollapse : defaultCollapse,
            keepOpen = (elKeepOpen !== undefined) ? elKeepOpen : defaultKeepOpen,
            minDate = (elMinDate === 'tomorrow') ? _tomorrow : (elMinDate === 'today') ? _today : defaultMinDate,
            locale = (elLocale !== undefined) ? elLocale : defaultLocale,
            options = (elOptions !== undefined) ? elOptions : {},
            defaultDate = (elShowDefaultDate) ? _setDefaultDate(elMinDate) : defaultShowDefaultDate,
            stepping = (elStepping !== undefined) ? elStepping : defaultStepping,
            withTime = (elWithTime !== undefined) ? elWithTime : defaultWithTime;

        // Setup
        var $input = $el.find('input'),
            $addon = $el.find('.input-group-addon');

        var defOptions = {
            format: format,
            collapse: collapse,
            keepOpen: keepOpen,
            minDate: minDate,
            defaultDate: defaultDate,
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'auto'
            },
            widgetParent: $el,
            icons: {
                time: 'fa fa-clock-o',
                date: 'fa fa-calendar',
                up: 'fa fa-chevron-up',
                down: 'fa fa-chevron-down',
                previous: 'fa fa-arrow-left',
                next: 'fa fa-arrow-right',
                today: 'fa fa-crosshairs',
                clear: 'fa fa-trash-o'
            },
            stepping: stepping,
            language: locale,
			pickTime: withTime
        };

        options = $.extend(defOptions, options);
        $input.datetimepicker(options);

        $el.addClass('datepicker--enabled');

        $addon.on('click', function() {
            $input.focus();
        });
    };


    return {
        init: init,
        reInit: reInit
    };

})(jQuery, window);

hgutils.sidebartoggle = (function(window, undefined) {

    var init,
        toggle;

    init = function() {
        toggle();
    };

    toggle = function() {
        var $appMain = $('#app__main'),
            $toggleButton = $('#app__sidebar-toggle');

        // Set default session state
        if(sessionStorage.getItem('altered-state') === 'true' && $toggleButton && document.documentElement.clientWidth >= 992) {
            $appMain.toggleClass('app__main--altered-state');
        }

        // Toggle button
        $toggleButton.on('click', function() {
            $appMain.toggleClass('app__main--altered-state');

            if($appMain.hasClass('app__main--altered-state')) {
                sessionStorage.setItem('altered-state', 'true');
            } else {
                sessionStorage.setItem('altered-state', 'false');
            }
        });
    };

    return {
        init: init
    };

})(window);
hgutils.sidebartree = (function($, window, undefined) {

    var init,
        canBeMoved,
        buildTree, searchTree,
        $sidebar = $('#app__sidebar'),
        $sidebarNavContainer = $('#app__sidebar__navigation'),
        $searchField = $('#app__sidebar__search'),
        movingConfirmation = $sidebarNavContainer.data('moving-confirmation') || "You sure?";

    init = function() {
        if($sidebarNavContainer !== 'undefined' && $sidebarNavContainer !== null) {
            buildTree();
            searchTree();
        }
    };

    canBeMoved = function (node, parent) {
        if (!node.data.page || !node.data.page.class || !parent.data.page || !parent.data.page.children) {
            return false;
        }


        for (var i = parent.data.page.children.length, e; e = parent.data.page.children[--i]; ) {
            if (e.class === node.data.page.class) {
                return true;
            }
        }

        return false;

    };

    buildTree = function() {

        // Show when ready
        $sidebarNavContainer.on('ready.jstree', function() {

            // DOCS: http://www.jstree.com/api/#/?q=.jstree%20Event&f=ready.jstree
            $sidebar.addClass('app__sidebar--tree-ready');
        });


        // Go to url
        $sidebarNavContainer.on('changed.jstree', function(e, data) {
            // DOCS: http://www.jstree.com/api/#/?q=.jstree%20Event&f=changed.jstree
            var href = data.event.currentTarget.href;

            if (data.event.ctrlKey || data.event.metaKey) {
                window.open(href);
            } else {
                document.location.href = href;
            }
        });


        // Drag and drop callback
        $sidebarNavContainer.on('move_node.jstree', function(e, data) {
            // DOCS: http://www.jstree.com/api/#/?q=.jstree%20Event&f=move_node.jstree

            // Vars
            var $container = $(this),
                parentNode = data.parent,
                reorderUrl = $container.data('reorder-url'),
                params = {
                    nodes : []
                };

            // Recalc id's
            $('#' + parentNode).find('> ul > li').each(function() {
                var newId = $(this).attr('id').replace(/node-/,'');

                params.nodes.push(newId);
            });

            if (data.old_parent !== data.parent) {
                params.parent = {};
                params.parent[data.node.id.replace(/node-/, '')] = data.parent.replace(/node-/, '');
                if (0 === params.nodes.length) {
                    params.nodes.push(data.node.id.replace(/node-/, ''));
                }
            }

            //; Save
            $.post(
                reorderUrl,
                params,
                function(){
                    console.log('move_node saved');
                }
            );
        });


        // Create
        $sidebarNavContainer.jstree({
                                        'core': {
                                            'check_callback': function (operation, node, node_parent, node_position, more) {
                                                // operation can be 'create_node', 'rename_node', 'delete_node', 'move_node' or 'copy_node'
                                                // in case of 'rename_node' node_position is filled with the new node name

                                                if(operation === 'move_node') {

                                                    // No dnd outsite root
                                                    if(!node_parent || node_parent.id === '#') {
                                                        return false;
                                                    }

                                                    // Only on same level please
                                                    if(node.parent === node_parent.id) {
                                                        return true;
                                                    }

                                                    return canBeMoved(node, node_parent) && !(more && more.core && !confirm(movingConfirmation
                                                                                                                                .replace('%title%', node.text.replace(/^\s+|\s+$/g, ''))
                                                                                                                                .replace('%target%', node_parent.text.replace(/^\s+|\s+$/g, ''))
                                                    ));

                                                } else {

                                                    return true;
                                                }
                                            }
                                        },
                                        'plugins': [
                                            'types',
                                            'search',
                                            'dnd'
                                        ],
                                        'types': {
                                            '#': {
                                                'icon': 'fa fa-home'
                                            },
                                            'default': {
                                                'icon' : 'fa fa-file-o'
                                            },
                                            'offline': {
                                                'icon': 'fa fa-chain-broken'
                                            },
                                            'folder': {
                                                'icon': 'fa fa-folder-o'
                                            },
                                            'image': {
                                                'icon': 'fa fa-picture-o'
                                            },
                                            'files': {
                                                'icon': 'fa fa-files-o'
                                            },
                                            'slideshow': {
                                                'icon': 'fa fa-desktop'
                                            },
                                            'video': {
                                                'icon': 'fa fa-film'
                                            },
                                            'media': {
                                                'icon': 'fa fa-folder-o'
                                            }
                                        },
                                        'search' : {
                                            'show_only_matches': true
                                        }
                                    });

    };

    searchTree = function() {

        if($searchField !== 'undefined' && $searchField !== null) {
            $searchField.on('keyup', function() {

                var searchValue = $searchField.val();

                $sidebarNavContainer.jstree(true).search(searchValue);
            });
        }
    };

    return {
        init: init
    };

})(jQuery, window);


hgutils.sidebarsearchfocus = (function(window, undefined) {

    var init,
        focus;

    init = function() {
        focus();
    };

    focus = function() {
        var $toggleButton = $('.app__sidebar__search-toggle-btn'),
            $searchInput = $('#app__sidebar__search');

        $toggleButton.on('click touchstart mousedown', function(e) {
            e.preventDefault();
        }).on('touchend mouseup', function() {
            $searchInput.focus();
        });
    };

    return {
        init: init
    };

})(window);

hgutils.autoCollapseButtons = (function ($, window, undefined) {

    var init, createMoreDropdown,
        buttonsVisible,
        $autoCollapseButtons, $btnGroup, $allButtons, $buttonsRedundant, $moreButtonContainer, $moreButton, $caret, $dropdownList;

    init = function () {
        buttonsVisible = 2;

        $autoCollapseButtons = $('.js-auto-collapse-buttons');
        $btnGroup = $autoCollapseButtons.find('.btn-group');
        if ($btnGroup.parent().attr('data-visible-buttons')) {
            buttonsVisible = $btnGroup.parent().data('visible-buttons');
        }
        $allButtons = $btnGroup.children('button, a'); // select only anchors and buttons

        // add more-dropdown when there are at least 2 buttons for dropdown
        if ($allButtons.length > (buttonsVisible + 1)) {
            $buttonsRedundant = $allButtons.slice(buttonsVisible);
            createMoreDropdown();
        }
    };

    createMoreDropdown = function () {
        // create dom elements
        $moreButtonContainer = $('<div class="btn-group btn-group--more">').appendTo($btnGroup);
        var label = MORE_BUTTON_LABEL
            ? MORE_BUTTON_LABEL
            : 'More';
        $moreButton = $('<button class="btn btn-default btn--raise-on-hover dropdown-toggle" data-toggle="dropdown">').text(label + ' ').appendTo($moreButtonContainer);
        $caret = $('<span class="fa fa-caret-down">').appendTo($moreButton);
        $dropdownList = $('<ul class="dropdown-menu dropdown-menu-right dropdown-menu--more">').appendTo($moreButtonContainer);

        // move buttons to dropdown list & remove styling
        $buttonsRedundant.each(function () {
            var $li = $('<li>');

            $(this).removeClass().addClass('btn-dropdown-menu js-save-btn').appendTo($li);
            $li.appendTo($dropdownList);
        });
    };

    return {
        init: init
    };

})(jQuery, window);

hgutils.autoCollapseTabs = (function($, window, undefined) {

    var $tabs, $btnMore, $dropdown,
        init, dropdownItems, tabsHeight, children, singleTabHeight, initTabLogic, replaceUrlParam, doCheck;

    init = function() {
        $tabs = $('.js-auto-collapse-tabs');
        $btnMore = $('.tab__more');
        $dropdown = $('#collapsed');
        singleTabHeight = $tabs.find('li:first-child').innerHeight(); // get single height

        initTabLogic();
        doCheck();

        $(window).on('resize', function() {
            doCheck();
        }); // when window is resized
    };

    initTabLogic = function () {
        // If there is a tab defined in the url, we activate it
        var currentTabElement = $('#currenttab');
        if (typeof(currentTabElement) != 'undefined' && currentTabElement != null && currentTabElement.val() && currentTabElement.val().length > 0) {
            $('.js-auto-collapse-tabs.nav-tabs a[href="' + $('#currenttab').val() + '"]').tab('show');
        }

        // When tab click, add the current tab in the url
        $('.js-auto-collapse-tabs.nav-tabs a').click(function (e) {
            $(this).tab('show');

            var activeTab = this.hash.substr(1);
            if (history.pushState) {
                window.history.pushState({}, null, replaceUrlParam(window.location.href, 'currenttab', activeTab));
            }

            if (typeof(currentTabElement) != 'undefined' && currentTabElement != null) {
                currentTabElement.val(activeTab);
            }
        });

        // When the form get ssubmitted, change the action url
        $('#pageadminform .js-save-btn').on('click', function() {
            var form = $('#pageadminform');
            form.attr('action', window.location.href);
        });
    };

    replaceUrlParam = function (url, paramName, paramValue) {
        var pattern = new RegExp('(' + paramName + '=).*?(&|$)'),
            newUrl = url;

        if (url.search(pattern) >= 0) {
            newUrl = url.replace(pattern, '$1' + paramValue + '$2');
        } else {
            newUrl = newUrl + (newUrl.indexOf('?')>0 ? '&' : '?') + paramName + '=' + paramValue;
        }

        return newUrl;
    };

    doCheck = function() {
        tabsHeight = $tabs.innerHeight();
        children = $tabs.children('li:not(:last-child):not(:first-child)'); // Don't count the 'more' tab and always show first tab

        if (tabsHeight >= singleTabHeight) {

            while (tabsHeight > singleTabHeight && children.size() > 0) {
                $btnMore.show(); // show immediately when first tab is added to dropdown

                // move tab to dropdown
                $(children[children.size()-1]).prependTo($dropdown);

                // recalculate
                tabsHeight = $tabs.innerHeight();
                children = $tabs.children('li:not(:last-child):not(:first-child)');
            }

        } else {
            dropdownItems = $dropdown.children('li');

            while (tabsHeight < singleTabHeight && dropdownItems.size() > 0) {
                $(dropdownItems[0]).insertBefore($tabs.children('li:last-child'));

                // recalculate
                tabsHeight = $tabs.innerHeight();
                dropdownItems = $dropdown.children('li');
            }

            if (tabsHeight > singleTabHeight) { // double chk height again
                doCheck();
            }
        }

        // hide the more button if dropdown is empty
        dropdownItems = $dropdown.children('li');
        if (dropdownItems.size() <= 0) {
            $btnMore.hide();

        } else {
            $btnMore.show();

            // check if active element is in dropdown
            if ($dropdown.children('li.active').size() > 0) {
                $btnMore.addClass('active');
            } else {
                $btnMore.removeClass('active');
            }
        }
    };

    return {
        init: init
    };

})(jQuery, window);

hgutils.mainActions = (function(window, undefined) {

    var updateScroll;

    updateScroll = function(currentScrollY, $menu) {

        if(currentScrollY >= 120){
            $menu.addClass('page-main-actions--top--show');
        }

        if(currentScrollY < 120){
            $menu.removeClass('page-main-actions--top--show');
        }
    };

    return {
        updateScroll: updateScroll
    };

})(window);

hgutils.urlChooser = (function (window, undefined) {

    var init, urlChooser, saveUrlChooserModal, saveMediaChooserModal, getUrlParam, adaptUrlChooser;

    var itemUrl, itemId, itemTitle, itemThumbPath, replacedUrl, $body = $('body');


    init = function () {
        urlChooser();
        adaptUrlChooser();
    };

    // URL-Chooser
    urlChooser = function () {

        // Link Chooser select
        $body.on('click', '.js-url-chooser-link-select', function (e) {
            e.preventDefault();

            var $this = $(this),
                slug = $this.data('slug'),
                id = $this.data('id'),
                replaceUrl = $this.closest('nav').data('replace-url');

            // Store values
            itemUrl = (slug ? slug : '');
            itemId = id;

            // Replace URL
            $.ajax({
                       url: replaceUrl,
                       type: 'GET',
                       data: {'text': itemUrl},
                       success: function (response) {
                           replacedUrl = response.text;

                           if (typeof selectionText == 'undefined' || selectionText.length == 0) {
                               selectionText = 'Selection';
                           }
                           // Update preview
                           $('#url-chooser__selection-preview').text(selectionText + ': ' + replacedUrl);
                       }
                   });
        });

        // Media Chooser select
        $body.on('click', '.js-url-chooser-media-select', function (e) {
            e.preventDefault();

            var $this = $(this),
                path = $this.data('path'),
                thumbPath = $this.data('thumb-path'),
                id = $this.data('id'),
                title = $this.data('title'),
                cke = $this.data('cke'),
                replaceUrl = $this.closest('.thumbnail-wrapper').data('replace-url');

            // Store values
            itemUrl = path;
            itemId = id;
            itemTitle = title;
            itemThumbPath = thumbPath;

            // Save
            if (!cke) {
                var isMediaChooser = $(window.frameElement).closest('.js-ajax-modal').data('media-chooser');

                if (isMediaChooser) {
                    saveMediaChooserModal(false);
                } else {
                    // Replace URL
                    $.ajax({
                               url: replaceUrl,
                               type: 'GET',
                               data: {'text': itemUrl},
                               success: function (response) {
                                   replacedUrl = response.text;
                               }
                           }).done(function () {
                        saveUrlChooserModal(false);
                    });
                }

            } else {
                saveMediaChooserModal(true);
            }
        });


        // Cancel
        $('#cancel-url-chooser-modal').on('click', function () {
            var cke = $(this).data('cke');

            if (!cke) {
                var $parentModal = $(window.frameElement).closest('.js-ajax-modal'),
                    parentModalId = $parentModal.attr('id');

                window.parent.$('#' + parentModalId).modal('hide');

            } else {
                window.close();
            }
        });


        // OK
        $(document).on('click', '#save-url-chooser-modal', function () {
            var cke = $(this).data('cke');

            saveUrlChooserModal(cke);
        });
    };


    // Save for URL-chooser
    saveUrlChooserModal = function (cke) {
        if (!cke) {
            var $parentModal = $(window.frameElement).closest('.js-ajax-modal'),
                linkedInputId = $parentModal.data('linked-input-id'),
                parentModalId = $parentModal.attr('id');

            // Set val
            window.parent.$('#' + linkedInputId).val(itemUrl).change();

            // Set proper URL
            window.parent.$('#' + linkedInputId).parent().find('.js-urlchooser-value').val(replacedUrl);

            // Close modal
            window.parent.$('#' + parentModalId).modal('hide');

        } else {
            var funcNum = getUrlParam('CKEditorFuncNum');

            // Set val
            window.opener.CKEDITOR.tools.callFunction(funcNum, replacedUrl);

            // Close window
            window.close();
        }
    };


    // Save for Media-chooser
    saveMediaChooserModal = function (cke) {
        if (!cke) {
            var $parentModal = $(window.frameElement).closest('.js-ajax-modal'),
                linkedInputId = $parentModal.data('linked-input-id'),
                parentModalId = $parentModal.attr('id');

            // Set val
            window.parent.$('#' + linkedInputId).val(itemId).change();

            // Update preview
            var $mediaChooser = window.parent.$('#' + linkedInputId + '-widget'),
                $previewImg = window.parent.$('#' + linkedInputId + '__preview__img'),
                $previewTitle = window.parent.$('#' + linkedInputId + '__preview__title');

            $mediaChooser.addClass('media-chooser--choosen');
            $previewTitle.html(itemTitle);

            if (itemThumbPath === "") {
                var $parent = $previewTitle.parent();
                $parent.prepend('<i class="fa fa-file-o media-thumbnail__icon"></i>');
            }
            else {
                $previewImg.attr('src', itemThumbPath);
            }

            // Close modal
            window.parent.$('#' + parentModalId).modal('hide');

        } else {
            var funcNum = getUrlParam('CKEditorFuncNum');

            // Set val
            window.opener.CKEDITOR.tools.callFunction(funcNum, itemUrl);

            // Close window
            window.close();
        }
    };


    // Get Url Parameters
    getUrlParam = function (paramName) {
        var reParam = new RegExp('(?:[\?&]|&amp;)' + paramName + '=([^&]+)', 'i');
        var match = window.location.search.match(reParam);

        return (match && match.length > 1) ? match[1] : '';
    };

    // Adapt the url chooser according to the selected link type.
    adaptUrlChooser = function () {
        $body.on('click', '.js-change-link-type', function (e) {
                     e.preventDefault();
                     var $form = $(this).closest('form'),
                         $urlChooser = $(this).parents('.urlchooser-wrapper'),
                         $urlChooserName = $urlChooser.data('chooser-name');

                     var values = {};

                     $.each($form.serializeArray(), function (i, field) {
                         // Only submit required values.
                         if (field.name.indexOf('link_type') != -1 || field.name.indexOf('link_url') != -1) {
                             if (field.name.indexOf($urlChooserName) != -1 && field.name.indexOf('link_url') == -1) {
                                 values[field.name] = field.value;
                             }
                         }
                         else {
                             // Main sequence can not be submitted.
                             if (field.name.indexOf('sequence') == -1) {
                                 values[field.name] = field.value;
                             }
                         }
                     });


                     // Add the selected li value.
                     values[$(this).data('name')] = $(this).data('value');

                     $.ajax({
                                url: $form.attr('action'),
                                type: $form.attr('method'),
                                data: values,
                                success: function (html) {
                                    $urlChooser.replaceWith(
                                        $(html).find('#' + $urlChooser.attr('id'))
                                    );
                                }
                            });
                 }
        );
    };

    return {
        init: init
    };

})
(window);


hgutils.urlchoosertree = (function($, window, undefined) {

    var init,
        buildTree, searchTree,
        $sidebar = $('#app__urlchooser'),
        $urlChooserContainer = $('#app__urlchooser__navigation'),
        $fetchUrl = $urlChooserContainer.data('src'),
        $searchField = $('#app__urlchooser__search');

    init = function() {
        if($urlChooserContainer !== 'undefined' && $urlChooserContainer !== null) {
            buildTree();
            searchTree();
        }
    };

    buildTree = function() {

        // Show when ready
        $urlChooserContainer.on('ready.jstree', function() {
            // DOCS: http://www.jstree.com/api/#/?q=.jstree%20Event&f=ready.jstree
            $sidebar.addClass('app__sidebar--tree-ready');
        });


        // Go to url
        $urlChooserContainer.on('changed.jstree', function(e, data) {
            // DOCS: http://www.jstree.com/api/#/?q=.jstree%20Event&f=changed.jstree
            var href = data.event.currentTarget.href;

            if (data.event.ctrlKey || data.event.metaKey) {
                window.open(href);
            } else {
                document.location.href = href;
            }
        });

        // Create
        $urlChooserContainer.jstree({
                                        'core' : {
                                            'data' : {
                                                "url" : $fetchUrl,
                                                "dataType" : "json", // needed only if you do not supply JSON headers
                                                "data" : function (node) {
                                                    return { "id" : node.id };
                                                }
                                            }
                                        },
                                        'plugins': [
                                            'types',
                                            'search'
                                        ],
                                        'types': {
                                            '#': {
                                                'icon': 'fa fa-home'
                                            },
                                            'default': {
                                                'icon' : 'fa fa-file-o'
                                            },
                                            'offline': {
                                                'icon': 'fa fa-chain-broken'
                                            },
                                            'folder': {
                                                'icon': 'fa fa-folder-o'
                                            },
                                            'image': {
                                                'icon': 'fa fa-picture-o'
                                            },
                                            'files': {
                                                'icon': 'fa fa-files-o'
                                            },
                                            'slideshow': {
                                                'icon': 'fa fa-desktop'
                                            },
                                            'video': {
                                                'icon': 'fa fa-film'
                                            },
                                            'media': {
                                                'icon': 'fa fa-folder-o'
                                            }
                                        },
                                        'search' : {
                                            'show_only_matches' : true,
                                            'ajax' : {
                                                'url': $fetchUrl + "_search",
                                                'dataType': 'json'
                                            }
                                        }
                                    });
    };

    searchTree = function() {

        if($searchField !== 'undefined' && $searchField !== null) {

            var options = {
                callback: function (value) { $urlChooserContainer.jstree(true).search(value); },
                wait: 750,
                highlight: false,
                allowSubmit: false,
                captureLength: 2
            };

            $searchField.typeWatch( options );
        }
    };

    return {
        init: init
    };

})(jQuery, window);
hgutils.mediaChooser = (function(window, undefined) {

    var init, initDelBtn;

    var $body = $('body');

    init = function() {
        // Save and update preview can be found in url-chooser.js

        initDelBtn();
    };


    // Del btn
    initDelBtn = function() {
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


hgutils.filter = (function($, window, undefined) {

    var init,
        _getElements, _calculateUniqueFilterId,
        clearAllFilters, createFilter, updateOptions, removeFilterLine;

    var $appFilter = $('#app__filter'),
        $clearAllFiltersBtn, $applyAllFiltersBtn,
        $addFirstFilterSelect, $addFilterBtn, $removeFilterBtn,
        $filterDummyLine, $filterHolder, $filterSelect;

    var $body = $('body');


    init = function() {
        if($appFilter) {
            _getElements();

            $addFirstFilterSelect.on('change', function() {
                createFilter($(this), true);
            });

            $addFilterBtn.on('click', function() {
                createFilter($(this), false);
            });

            $clearAllFiltersBtn.on('click', function() {
                clearAllFilters();
            });

            // event handlers for dynamic added elements
            $('body').on('click', '.js-remove-filter-btn', function() {
                removeFilterLine($(this));
            });

            $('body').on('change', '.js-filter-select:not(#add-first-filter)', function() {
                updateOptions($(this));
            });
        }
    };



    _getElements = function() {
        $clearAllFiltersBtn = $('#clear-all-filters');
        $applyAllFiltersBtn = $('#apply-all-filters');

        $addFirstFilterSelect = $('#add-first-filter');
        $addFilterBtn = $('#add-filter');

        $filterDummyLine = $('#filter-dummy-line');
        $filterHolder = $('#filter-holder');
        $filterSelect = $('.js-filter-select');

        $removeFilterBtn = $('.js-remove-filter-btn');
    };


    createFilter = function($this, first) {
        var uniqueid = _calculateUniqueFilterId(),
            newFilterLine = $('<div class="js-filter-line app__filter__line">').append($filterDummyLine.html());
        // Append new line
        if(first) {
            var currentLine = $this.parents('.js-filter-line');

            // Set new val to select
            newFilterLine.find('.js-filter-dummy').val(currentLine.find('.js-filter-select').val());

            // Append
            $filterHolder.append(newFilterLine);
            $addFilterBtn.removeClass('hidden');
        } else {

            // Append
            $filterHolder.append(newFilterLine);
        }

        // Set unique id
        newFilterLine.find('.js-unique-filter-id').val(uniqueid);

        // Update options
        updateOptions(newFilterLine.find('.js-filter-dummy'));

        // Show
        if(first) {
            newFilterLine.removeClass('hidden');
            currentLine.addClass('hidden');
            currentLine.find('select').val('');

        } else {
            newFilterLine.removeClass('hidden');
        }
    };



    _calculateUniqueFilterId = function() {
        var result = 1;

        $('.js-unique-filter-id').each(function() {
            var value = parseInt($(this).val(), 10);

            if(result <= value){
                result = value + 1;
            }
        });

        return result;
    };



    updateOptions = function(el) {
        var $el = $(el),
            val = $el.val().replace('.',  '_'),
            uniqueid = $el.parents('.js-filter-line').find('.js-unique-filter-id').val();

        // copy options from hidden filter dummy
        $el.parents('.js-filter-line').find('.js-filter-options').html($('#filterdummyoptions_'+ val).html());

        $el.parents('.js-filter-line').find('input, select').each(function(){
            var fieldName = $(this).attr('name');

            if (fieldName.substr(0, 7) != 'filter_') {
                $(this).attr('name', 'filter_' + $(this).attr('name'));
            }
        });

        $el.parents('.js-filter-line').find('.js-filter-options').find('input:not(.js-unique-filter-id), select').each(function() {
            var name = $(this).attr('name'),
                bracketPos = name.indexOf('[');

            if (bracketPos !== -1) {
                var arrayName = name.substr(0, bracketPos),
                    arrayIndex = name.substr(bracketPos);

                $(this).attr('name', arrayName + '_' + uniqueid + arrayIndex);

            } else {
                $(this).attr('name', $(this).attr('name') + '_' + uniqueid);
            }

            if($(this).hasClass('datepick')){
                $(this).datepicker(new Date());
            }
        });

        hgutils.datepicker.init();
    };



    removeFilterLine = function($el) {
        if($filterHolder.children('.js-filter-line').size() === 2 ){
            $('#first-filter-line option:first').attr('selected', 'selected');
            $('#first-filter-line').removeClass('hidden');
            $addFilterBtn.addClass('hidden');
        }

        $el.parents('.js-filter-line').remove();
    };



    clearAllFilters = function() {
        // Set Loading
        hgutils.appLoading.addLoading();

        // Remove all filters
        $('.app__filter__line').remove();

        // Submit
        $applyAllFiltersBtn.trigger('click');
    };



    return {
        init: init
    };

})(jQuery, window);


hgutils.app = (function($, window, undefined) {

    var init, appScroll,
        $mainActions = $('#page-main-actions-top');


    // General App init
    init = function() {
        cargobay.toggle.init();
        cargobay.scrollToTop.init();

        appScroll();

        hgutils.sidebartoggle.init();
        hgutils.sidebartree.init();
        hgutils.sidebarsearchfocus.init();
        hgutils.filter.init();
        hgutils.urlchoosertree.init();
        hgutils.checkIfEdited.init();
        /*       kunstmaanbundles.sidebarsearchfocus.init();
               kunstmaanbundles.filter.init();
               kunstmaanbundles.sortableTable.init();
               kunstmaanbundles.checkIfEdited.init();
               kunstmaanbundles.preventDoubleClick.init();
               kunstmaanbundles.datepicker.init();*/
        hgutils.datepicker.init();
        hgutils.autoCollapseButtons.init();
        hgutils.autoCollapseTabs.init();
      //  hgutils.urlChooser.init();
        hgutils.richEditor.init();
            hgutils.ajaxModal.init();
            hgutils.advancedSelect.init();

            hgutils.pageEditor.init();
            hgutils.pagepartEditor.init();
            hgutils.appLoading.init();
        hgutils.slugChooser.init();

  /*          kunstmaanbundles.slugChooser.init();
            kunstmaanbundles.urlChooser.init();
            kunstmaanbundles.mediaChooser.init();
            kunstmaanbundles.iconChooser.init();
            kunstmaanbundles.bulkActions.init();
            kunstmaanbundles.nestedForm.init();
            kunstmaanbundles.appLoading.init();
            kunstmaanbundles.tooltip.init();
            kunstmaanbundles.colorpicker.init();
            kunstmaanbundles.charactersLeft.init();
            kunstmaanbundles.rangeslider.init();
            kunstmaanbundles.googleOAuth.init();
            kunstmaanbundles.appNodeVersionLock.init();
            kunstmaanbundles.appEntityVersionLock.init()*/
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

                hgutils.mainActions.updateScroll(currentScrollY, $mainActions);
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

hgutils.pageEditor = (function(window, undefined) {

    var init,
        changeTemplate, publishLater, unpublishLater, sortable, permissions, keyCombinations;

    var $body = $('body');


    init = function() {
        $('.js-change-page-template').on('click', function() {
            changeTemplate($(this));
        });
        if($('#publish-later__check').length) {
            publishLater();
        }
        if($('#unpublish-later__check').length) {
            unpublishLater();
        }
        if($('.js-sortable-container').length) {
            sortable();
        };
        if($('#permissions-container').length) {
            permissions();
        }
        if($('#pageadminform').length) {
            keyCombinations();
        };
    };


    // Change Page Template
    changeTemplate = function($btn) {
        var $holder = $('#pagetemplate_template_holder'),
            $checkedTemplateCheckbox = $('input[name=pagetemplate_template_choice]:checked'),
            newValue = $checkedTemplateCheckbox.val(),
            modal = $btn.data(modal);

        // Hide modal
        $(modal).modal('hide');

        // Update hidden field with new value
        $holder.val(newValue);

        // Submit closest form
        $checkedTemplateCheckbox.closest('form').submit();
    };


    // Publish
    publishLater = function() {
        var _toggle;

        _toggle = function(check) {
            if(check.checked) {
                $('#publish-later').show();
                $('#publish-later-action').show();
                $('#publish-action').hide();

            } else {
                $('#publish-later').hide();
                $('#publish-later-action').hide();
                $('#publish-action').show();
            }
        };

        if($('#publish-later__check')) {
            var check = document.getElementById('publish-later__check');

            _toggle(check);

            $(check).on('change', function() {
                _toggle(this);
            });
        }
    };


    // Unpublish
    unpublishLater = function() {
        var _toggle = function(check) {

            if(check.checked) {
                $('#unpublish-later').show();
                $('#unpublish-later-action').show();
                $('#unpublish-action').hide();
            } else {
                $('#unpublish-later').hide();
                $('#unpublish-later-action').hide();
                $('#unpublish-action').show();
            }
        };

        if($('#unpublish-later__check')) {
            var check = document.getElementById('unpublish-later__check');

            _toggle(check);

            $(check).on('change', function() {
                _toggle(this);
            });
        }
    };

    // Sortable
    sortable = function() {
        $('.js-sortable-container').each(function() {
            var $this = $(this),
                id = $this.attr('id'),
                el = document.getElementById(id);

            var sortable = Sortable.create(el, {
                draggable: '.js-sortable-item',
                handle: '.js-sortable-item__handle',
                ghostClass: 'sortable-item--ghost',

                group: {
                    name: 'pagepartRegion',
                    pull: true
                },

                animation: 100,

                scroll: true,
                scrollSensitivity: 300,
                scrollSpeed: 300,

                onStart: function(evt) {
                    var $el = $(evt.item),
                        elScope = $el.data('scope');

                    // Destroy rich editors inside dragged element
                    $el.find('.js-rich-editor').each(function() {
                        hgutils.richEditor.destroySpecificRichEditor($(this));
                    });

                    // Add active class
                    $body.addClass('sortable-active');

                    // Check if drag is allowed
                    $('.js-sortable-container').on('dragover', function(e) {
                        var $element = $(this);

                        if ($element.data('scope')) {
                            var allowedPageParts = $element.data('scope').split(' ');

                            if(allowedPageParts.indexOf(elScope) > -1) {
                                $el.removeClass('sortable-item--error');
                            } else {
                                $el.addClass('sortable-item--error');
                            }
                        }

                    });
                },

                onEnd: function(evt) {
                    var $el = $(evt.item),
                        $PPcontainer = $el.parents('.js-pp-container'),
                        $contextUpdateField = $el.find('.pagepartadmin_field_updatecontextname'),
                        currentContext = $PPcontainer.data('context');

                    // Remove active class
                    $body.removeClass('sortable-active');

                    // Remove event listeners
                    $('.js-sortable-container').off('dragover');

                    // Set edited on true
                    hgutils.checkIfEdited.edited();

                    // Update context name
                    $contextUpdateField.each(function() {
                        $(this).attr('name', currentContext + $(this).data('suffix'));
                    });

                    // Enable rich editors inside dragged element
                    $el.find('.js-rich-editor').each(function() {
                        hgutils.richEditor.enableRichEditor($(this));
                    });
                }
            });
        });

        // Add active class
        $('.js-sortable-item__handle').on('mousedown', function() {
            $body.addClass('sortable-active');
        });

        // Remove active class
        $('.js-sortable-item__handle').on('mouseup', function() {
            $body.removeClass('sortable-active');
        });
    };


    // Permission
    permissions = function() {
        // Container
        var $permissionsContainer = $('#permissions-container');

        // Changes
        var changes = [];
        changes['add'] = [];
        changes['del'] = [];

        // Checkboxes
        $('.js-permission-checkbox').on('change', function() {
            var checkbox = this,
                $checkbox = $(checkbox),
                role = $checkbox.data('role'),
                permission = $checkbox.data('permission'),
                origValue = $checkbox.data('original-value');

            // Add/Remove change
            if (origValue == checkbox.checked) {
                // Remove change...
                var idx;

                if (origValue) {
                    idx = changes['del'].indexOf(role + '.' + permission);

                    if (idx != -1) {
                        changes['del'].splice(idx, 1);
                    }
                } else {
                    idx = changes['add'].indexOf(role + '.' + permission);

                    if (idx != -1) {
                        changes['add'].splice(idx, 1);
                    }
                }

            } else {
                // Add change
                if (checkbox.checked) {
                    changes['add'].push(role + '.' + permission);
                } else {
                    changes['del'].push(role + '.' + permission);
                }
            }


            // Add hidden fields
            var hiddenfieldsContainer = $("#permission-hidden-fields"),
                hiddenfields = '';

            if (changes['add'].length > 0) {
                for (var i=0; i<changes['add'].length; i++) {
                    var params = changes['add'][i].split('.');
                    hiddenfields = hiddenfields + '<input type="hidden" name="permission-hidden-fields[' + params[0] + '][ADD][]" value="' + params[1] + '">';
                }
            }

            if (changes['del'].length > 0) {
                for (var i=0; i<changes['del'].length; i++) {
                    var params = changes['del'][i].split('.');
                    hiddenfields = hiddenfields + '<input type="hidden" name="permission-hidden-fields[' + params[0] + '][DEL][]" value="' + params[1] + '">';
                }
            }

            hiddenfieldsContainer.html(hiddenfields);


            // Display changes in div?
            var isRecursive = $permissionsContainer.data('recursive');
            if(isRecursive) {
                var transPermsAdded = $permissionsContainer.data('trans-perms-added'),
                    transPermsRemoved = $permissionsContainer.data('trans-perms-removed');

                var $infoContainer= $('#permission-changes-info-container'),
                    modalHtml = '';

                // Additions
                if (changes['add'].length > 0) {
                    modalHtml = modalHtml + '<p>' + transPermsAdded;
                    modalHtml = modalHtml + '<ul>';

                    for (var i=0; i<changes['add'].length; i++) {
                        var params = changes['add'][i].split('.');

                        modalHtml = modalHtml + '<li><strong>' + params[0] + '</strong> : ' + params[1] + '</li>';
                    }

                    modalHtml = modalHtml + '</ul>';
                    modalHtml = modalHtml + '</p>';
                }

                // Deletions
                if (changes['del'].length > 0) {
                    modalHtml = modalHtml + '<p>' + transPermsRemoved;
                    modalHtml = modalHtml + '<ul>';

                    for (var i=0; i<changes['del'].length; i++) {
                        var params = changes['del'][i].split('.');

                        modalHtml = modalHtml + '<li><strong>' + params[0] + '</strong> : ' + params[1] + '</li>';
                    }

                    modalHtml = modalHtml + '</ul>';
                    modalHtml = modalHtml + '</p>';
                }

                // Setup info container
                if (modalHtml != '') {
                    $('#permission-changes-modal__body').html(modalHtml);
                    $infoContainer.removeClass('hidden');
                } else {
                    $infoContainer.addClass('hidden');
                    $('#apply-recursive').prop('checked', false);
                }
            }
        });
    };


    // Key Combinations
    keyCombinations = function() {
        $(document).on('keydown', function(e) {
            if((e.ctrlKey || e.metaKey) && e.which === 83) {
                e.preventDefault();

                hgutils.appLoading.addLoading();

                $('#pageadminform').submit();
            };
        });
    };


    return {
        init: init
    };

})(window);

hgutils.pagepartEditor = function (window) {

    var events = {
        add: [],
        edit: [],
        delete: []
    };

    var init, addPagePart, editPagePart, deletePagePart, movePagePartUp, movePagePartDown, subscribeToEvent, unSubscribeToEvent, executeEvent, reInit, reOrder;

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
        hgutils.appLoading.addLoading();

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
                       hgutils.appLoading.removeLoading();

                       // Enable leave-page modal
                       hgutils.checkIfEdited.edited();

                       // Reinit custom selects
                       hgutils.advancedSelect.init();

                       // Reset ajax-modals
                       hgutils.ajaxModal.resetAjaxModals();

                       executeEvent('add')
                   }
               });

        // Reset select
        $select.val('');
    };


    // Edit
    editPagePart = function ($btn) {
        var $targetId = $btn.data('target-id');

        // Enable "leave page" modal
        hgutils.checkIfEdited.edited();

        // Show edit view and hide preview
        $('#' + $targetId + '-edit-view').removeClass('pp__view__block--hidden');
        $('#' + $targetId + '-preview-view').addClass('pp__view__block--hidden');

        // Add edit active class
        var $container = $('#pp-' + $targetId);
        $container.addClass('pp--edit-active');

        // Reinit custom selects
        hgutils.advancedSelect.init();

        // Set Active Edit
        window.activeEdit = $targetId;

        executeEvent('edit', $container);
    };


    // Delete
    deletePagePart = function ($btn) {
        var $targetId = $btn.data('target-id'),
            $container = $('#' + $targetId + '-pp-container');

        // Enable "leave page" modal
        hgutils.checkIfEdited.edited();

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
            hgutils.checkIfEdited.edited();
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
            hgutils.checkIfEdited.edited();
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
                    if (typeof hgutils[modules[i]] === 'object' && typeof hgutils[modules[i]].reInit === 'function') {
                        // prevent duplicate modules
                        if ($.inArray(modules[i], uniqueModules) === -1) {
                            uniqueModules.push(modules[i]);
                            hgutils[modules[i]].reInit();
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

hgutils.appLoading = (function($, window, undefined) {

    var init,
        addLoading, addLoadingForms, removeLoading;

    var $body = $('body');

    init = function() {
        $('.js-add-app-loading').on('click', addLoading);
        $('.js-add-app-loading--forms').on('click', addLoadingForms);
    };

    addLoading = function() {
        $body.addClass('app--loading');
    };

    addLoadingForms = function() {
        var valid = $(this).parents('form')[0].checkValidity();

        if(valid) {
            addLoading();
        }
    };

    removeLoading = function() {
        $body.removeClass('app--loading');
    };

    return {
        init: init,
        addLoading: addLoading,
        removeLoading: removeLoading
    };

})(jQuery, window);

hgutils.checkIfEdited = (function ($, window, undefined) {

    var init, edited, _doUnload;

    var NeedCheck = $('body').hasClass('js-check-if-edited'),
        isEdited = false,


        init = function () {

            if (NeedCheck) {
                $(document).on('click', '.js-save-btn', function () {
                    window.onbeforeunload = null;
                });

                window.onbeforeunload = _doUnload;
            }
        };


    edited = function () {
        isEdited = true;
    };


    _doUnload = function () {
        if (isEdited) {
            return 'You haven\'t saved this page, are you sure you want to close it?';
        }
    };


    return {
        init: init,
        edited: edited
    };

})(jQuery, window);

hgutils.slugChooser = (function(window, undefined) {

    var init, slugChooser,
        updateSlugPreview, resetSlug;


    init = function() {
        // Init
        $('.js-slug-chooser').each(function() {
            slugChooser($(this));
        });
    };


    // Slug-Chooser
    slugChooser = function($widget) {
        var resetValue = $widget.data('reset'),
            urlprefix = $widget.data('url-prefix');

        // Setup url prefix
        if(urlprefix.length == 0 || urlprefix.indexOf('/', urlprefix.length - 1) == -1) { //endwidth
            urlprefix += '/';
        }

        // Elements
        var $input = $widget.find('.js-slug-chooser__input'),
            $preview = $widget.find('.js-slug-chooser__preview'),
            $resetBtn = $widget.find('.js-slug-chooser__reset-btn');

        // Update
        $input.on('change', function() {
            updateSlugPreview($input, $preview, urlprefix);
        });
        $input.on('keyup', function() {
            updateSlugPreview($input, $preview, urlprefix);
        });

        // Reset Btn
        $resetBtn.on('click', function() {
            resetSlug($input, resetValue);
            updateSlugPreview($input, $preview, urlprefix);
        });

        // Set initial value
        updateSlugPreview($input, $preview, urlprefix);
    };


    resetSlug = function($input, resetValue) {
        $input.val(resetValue);
    };


    updateSlugPreview = function($input, $preview, urlprefix) {
        var inputValue = $input.val();

        $preview.html('url: ' + urlprefix + inputValue);
    };


    return {
        init: init
    };

})(window);
hgutils.advancedSelect = (function(window, undefined) {

    var init;

    init = function() {
        $('.js-advanced-select').select2();
    };

    return {
        init: init
    };

})(window);

hgutils.ajaxModal = (function ($, window, undefined) {

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

hgutils.richEditor = (function (window, undefined) {

    var editor; // Holds the editor var when overriding the dialog's onOk method.

    var _ckEditorConfigs = {
        'hgUtilsDefault': {
            skin: 'bootstrapck',
            startupFocus: false,
            bodyClass: 'CKEditor',
            filebrowserWindowWidth: 970,
            filebrowserImageWindowWidth: 970,
            filebrowserImageUploadUrl: '',
            allowedContent: true,
            extraPlugins: 'listblock,indent,indentblock,indentlist,panel,button,letterspacing,richcombo,floatpanel,simplebutton,panelbutton,lineheight,dialog,dialogui,lineutils,clipboard,widget,widgetcommon',
            extraAllowedContent: '*[*](*){*}',
            toolbar: [
                {name: 'basicstyles', items: ['Source', '-', 'Save', 'NewPage', 'Preview', '-', 'Templates']},
                {name: 'lists', items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Print', 'SpellChecker', 'Scayt']},
                {name: 'dents', items: ['Undo', 'Redo', '-', 'Find', 'Replace', '-', 'SelectAll', 'RemoveFormat']},
                {name: 'links', items: ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField']},
                '/',
                {name: 'insert', items: ['Bold', 'Italic', 'Underline', 'Strike', '-', 'Subscript', 'Superscript']},
                {name: 'clipboard', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', 'Blockquote', 'CreateDiv']},
                {name: 'editing', items: ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']},
                {name: 'link', items: ['Link', 'Unlink', 'Anchor']},
                {name: 'adds', items: ['Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak']},
                '/',
                {name: 'document', items: ['Styles', 'Format', 'Font', 'FontSize', 'lineheight', 'letterspacing']},
                {name: 'color', items: ['TextColor', 'BGColor']},
                {name: 'others', items: ['Maximize', 'ShowBlocks', '-', 'About']}
            ]
        }
    };

    var init, reInit,
        enableRichEditor, destroyAllRichEditors, destroySpecificRichEditor,
        _collectEditorConfigs, _collectExternalPlugins, _customOkFunctionForTables;

    // First Init
    init = function () {
        // These objects are declared global in _ckeditor_configs.html.twig
        _collectExternalPlugins(window.externalPlugins);
        _collectEditorConfigs(window.ckEditorConfigs);

        $('.js-rich-editor').each(function () {
            if (!$(this).hasClass('js-rich-editor--enabled')) {
                enableRichEditor($(this));
            }
        });
    };

    // Needs extra destroy of existing rich editors.
    reInit = function () {
        destroyAllRichEditors();
        init();
    };

    // PRIVATE
    _collectEditorConfigs = function (customConfigs) {
        for (var key in customConfigs) {
            // Do not allow overriding of the fallback config.
            if (key === 'hgUtilsDefault') {
                throw new Error('hgUtilsDefault is a reserved name for the default ckeditor configuration. Please choose another name.');
            } else {
                // v3.3.0 breaking: Thse whole config is now configurable, instead of just the toolbar.
                // This means we require an object instead of an array.
                if (customConfigs[key].constructor === Array) {
                    throw new Error('Since v3.3.0 the whole rich editor config is editable. This means a custom config should be an object instead of an array.');
                } else {
                    _ckEditorConfigs[key] = customConfigs[key];
                }
            }
        }
    };

    _collectExternalPlugins = function (plugins) {
        if (plugins !== undefined && plugins.length > 0 && CKEDITOR !== undefined && CKEDITOR.plugins !== undefined) {
            var i = 0;
            for (; i < plugins.length; i++) {
                if (plugins[i].constructor === Array) {
                    CKEDITOR.plugins.addExternal.apply(CKEDITOR.plugins, plugins[i]);
                } else {
                    throw new Error('Plugins should be configured as an Array with the following values: [names, path, fileName] (Filename optional.)')
                }
            }
        }
    };

    // This is a slightly dirty hack to enable us to make nicer responsive tables.
    // Basically we override the onOk method from the tables plugin from ckSource, and add a wrapper div.
    // By adding a wrapper div we can overflow:scroll; on smaller screens which is a lot nicer.
    _customOkFunctionForTables = function () {

        var makeElement = function (name) {
            return new CKEDITOR.dom.element(name, editor.document);
        };

        var selection = editor.getSelection(),
            bms = this._.selectedElement && selection.createBookmarks();

        var data = {},
            table = this._.selectedElement || makeElement('table'),
            wrapper = makeElement('div');

        wrapper.setAttribute('class', 'table-wrapper');
        editor.insertElement(wrapper);

        this.commitContent(data, table);

        if (data.info) {
            var info = data.info;

            // Generate the rows and cols.
            if (!this._.selectedElement) {
                var tbody = table.append(makeElement('tbody')),
                    rows = parseInt(info.txtRows, 10) || 0,
                    cols = parseInt(info.txtCols, 10) || 0;

                for (var i = 0; i < rows; i++) {
                    var row = tbody.append(makeElement('tr'));
                    for (var j = 0; j < cols; j++) {
                        var cell = row.append(makeElement('td'));
                        cell.appendBogus();
                    }
                }
            }

            // Modify the table headers. Depends on having rows and cols generated
            // correctly so it can't be done in commit functions.

            // Should we make a <thead>?
            var headers = info.selHeaders;
            if (!table.$.tHead && ( headers == 'row' || headers == 'both' )) {
                var thead = new CKEDITOR.dom.element(table.$.createTHead());
                tbody = table.getElementsByTag('tbody').getItem(0);
                var theRow = tbody.getElementsByTag('tr').getItem(0);

                // Change TD to TH:
                for (i = 0; i < theRow.getChildCount(); i++) {
                    var th = theRow.getChild(i);
                    // Skip bookmark nodes. (#6155)
                    if (th.type == CKEDITOR.NODE_ELEMENT && !th.data('cke-bookmark')) {
                        th.renameNode('th');
                        th.setAttribute('scope', 'col');
                    }
                }
                thead.append(theRow.remove());
            }

            if (table.$.tHead !== null && !( headers == 'row' || headers == 'both' )) {
                // Move the row out of the THead and put it in the TBody:
                thead = new CKEDITOR.dom.element(table.$.tHead);
                tbody = table.getElementsByTag('tbody').getItem(0);

                var previousFirstRow = tbody.getFirst();
                while (thead.getChildCount() > 0) {
                    theRow = thead.getFirst();
                    for (i = 0; i < theRow.getChildCount(); i++) {
                        var newCell = theRow.getChild(i);
                        if (newCell.type == CKEDITOR.NODE_ELEMENT) {
                            newCell.renameNode('td');
                            newCell.removeAttribute('scope');
                        }
                    }
                    theRow.insertBefore(previousFirstRow);
                }
                thead.remove();
            }

            // Should we make all first cells in a row TH?
            if (!this.hasColumnHeaders && ( headers == 'col' || headers == 'both' )) {
                for (row = 0; row < table.$.rows.length; row++) {
                    newCell = new CKEDITOR.dom.element(table.$.rows[row].cells[0]);
                    newCell.renameNode('th');
                    newCell.setAttribute('scope', 'row');
                }
            }

            // Should we make all first TH-cells in a row make TD? If 'yes' we do it the other way round :-)
            if (( this.hasColumnHeaders ) && !( headers == 'col' || headers == 'both' )) {
                for (i = 0; i < table.$.rows.length; i++) {
                    row = new CKEDITOR.dom.element(table.$.rows[i]);
                    if (row.getParent().getName() == 'tbody') {
                        newCell = new CKEDITOR.dom.element(row.$.cells[0]);
                        newCell.renameNode('td');
                        newCell.removeAttribute('scope');
                    }
                }
            }

            // Set the width and height.
            info.txtHeight ? table.setStyle('height', info.txtHeight) : table.removeStyle('height');
            info.txtWidth ? table.setStyle('width', info.txtWidth) : table.removeStyle('width');

            if (!table.getAttribute('style')) {
                table.removeAttribute('style');
            }
        }

        // Insert the table element if we're creating one.
        if (!this._.selectedElement) {
            wrapper.append(table);
            // Override the default cursor position after insertElement to place
            // cursor inside the first cell (#7959), IE needs a while.
            setTimeout(function () {
                var firstCell = new CKEDITOR.dom.element(table.$.rows[0].cells[0]);
                var range = editor.createRange();
                range.moveToPosition(firstCell, CKEDITOR.POSITION_AFTER_START);
                range.select();
            }, 0);
        }
        // Properly restore the selection, (#4822) but don't break
        // because of this, e.g. updated table caption.
        else {
            try {
                selection.selectBookmarks(bms);
            } catch (er) {
            }
        }
    };

    // PUBLIC
    enableRichEditor = function ($el) {
        var $body = $('body'),
            elementId = $el.attr('id'),
            editorConfig;

        var dataAttrConfiguration = {
            'height': $el.attr('height') || 300,
            'filebrowserBrowseUrl': $body.data('file-browse-url'),
            'filebrowserImageBrowseUrl': $body.data('image-browse-url'),
            'filebrowserImageBrowseLinkUrl': $body.data('image-browse-url'),
            'enterMode': $el.attr('noparagraphs') ? CKEDITOR.ENTER_BR : CKEDITOR.ENTER_P,
            'shiftEnterMode': $el.attr('noparagraphs') ? CKEDITOR.ENTER_P : CKEDITOR.ENTER_BR
        }

        editorConfig = (_ckEditorConfigs.hasOwnProperty($el.data('editor-mode'))) ? _ckEditorConfigs[$el.data('editor-mode')] : _ckEditorConfigs['hgUtilsDefault'];

        // Load the data from data attrs, but don't override the ones in the config if they're set.
        for (var key in dataAttrConfiguration) {
            if (editorConfig[key] === undefined) {
                editorConfig[key] = dataAttrConfiguration[key];
            }
        }

        if ($el.data('custom-config') !== undefined) {
            $.extend(editorConfig, $el.data('custom-config'));
        }

        // Place CK
        CKEDITOR.replace(elementId, editorConfig);
        CKEDITOR.on('dialogDefinition', function (e) {
            var dialogDefinition = e.data.definition;

            if (e.data.name === 'table') {
                editor = e.editor;
                e.data.definition.onOk = _customOkFunctionForTables;
            }
            if (e.data.name === 'link') {
                editor = e.editor;
                var info = dialogDefinition.getContents('info');
                var url = info.get('url');

                url.onKeyUp = function () {
                    this.allowOnChange = !1;
                    var a = this.getDialog().getContentElement("info", "protocol"), b = this.getValue(), k = /^((javascript:)|[#\/\.\?])/i, c = /^(http|https|ftp|news):\/\/(?=.)/i.exec(b);
                    c ? (this.setValue(b.substr(c[0].length)), a.setValue(c[0].toLowerCase())) : k.test(b) && a.setValue("");

                    var nodeTranslationPtrn = new RegExp(/\[(([a-z_A-Z]+):)?NT([0-9]+)\]/g);
                    var match;
                    while (match = nodeTranslationPtrn.exec(b)) {
                        // Node translation found, so set protocol to other.
                        if (match[3]) {
                            a.setValue("");
                        }
                    }

                    var mediaPtrn = new RegExp(/\[(([a-z_A-Z]+):)?M([0-9]+)\]/g);
                    while (match = mediaPtrn.exec(b)) {
                        // Media found, so set protocol to other.
                        if (match[3]) {
                            a.setValue("");
                        }
                    }

                    this.allowOnChange = !0
                };

            }
        });

        $el.addClass('js-rich-editor--enabled');

        // Behat tests
        // Add id on iframe so that behat tests can interact
        var checkExist = setInterval(function () {

            if ($('#cke_' + elementId + ' iframe').length === 1) {
                var parts = elementId.split("_"),
                    name = parts[parts.length - 1];

                $('#cke_' + elementId + ' iframe').attr('id', 'cke_iframe_' + name);

                clearInterval(checkExist);
            }
        }, 250);
    };

    // Destroy All
    destroyAllRichEditors = function () {
        for (instance in CKEDITOR.instances) {
            var $el = $('#' + CKEDITOR.instances[instance].name);

            if ($el.hasClass('js-rich-editor')) {
                $el.removeClass('js-rich-editor--enabled');

                CKEDITOR.instances[instance].updateElement();
                CKEDITOR.instances[instance].destroy(true);
            }
            ;
        }
    };

    // Destroy Specific
    destroySpecificRichEditor = function ($el) {
        var elementId = $el.attr('id'),
            editor = CKEDITOR.instances[elementId];

        if (editor) {
            editor.destroy(true);
        }
    };

    // Returns
    return {
        init: init,
        reInit: reInit,
        enableRichEditor: enableRichEditor,
        destroyAllRichEditors: destroyAllRichEditors,
        destroySpecificRichEditor: destroySpecificRichEditor
    };

})(window);
$(function() {
    hgutils.app.init();

    $(".js-sortable-link").on("click",function() {
        var href = $(this).data('order-url');
        window.location.href = href;
    });
});
