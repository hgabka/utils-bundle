var hgabkautils = {};
hgabkautils.sidebartoggle = (function(window, undefined) {

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
hgabkautils.sidebartree = (function($, window, undefined) {

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


hgabkautils.sidebarsearchfocus = (function(window, undefined) {

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

hgabkautils.autoCollapseButtons = (function ($, window, undefined) {

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

hgabkautils.autoCollapseTabs = (function($, window, undefined) {

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

hgabkautils.mainActions = (function(window, undefined) {

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

hgabkautils.urlChooser = (function (window, undefined) {

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

                parent.$('#' + parentModalId).modal('hide');

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
            parent.$('#' + linkedInputId).val(itemUrl).change();

            // Set proper URL
            parent.$('#' + linkedInputId).parent().find('.js-urlchooser-value').val(replacedUrl);

            // Close modal
            parent.$('#' + parentModalId).modal('hide');

        } else {
            var funcNum = getUrlParam('CKEditorFuncNum');

            // Set val
            window.opener.CKEDITOR.tools.callFunction(funcNum, itemUrl);

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
            parent.$('#' + linkedInputId).val(itemId).change();

            // Update preview
            var $mediaChooser = parent.$('#' + linkedInputId + '-widget'),
                $previewImg = parent.$('#' + linkedInputId + '__preview__img'),
                $previewTitle = parent.$('#' + linkedInputId + '__preview__title');

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
            parent.$('#' + parentModalId).modal('hide');

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
        var reParam = new RegExp('(?:[\?&]|&amp;)' + paramName + '=([^&]+)', 'i'),
            match = window.location.search.match(reParam);

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
hgabkautils.mediaChooser = (function(window, undefined) {

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

hgabkautils.app = (function($, window, undefined) {

    var init, appScroll,
        $mainActions = $('#page-main-actions-top');


    // General App init
    init = function() {
        cargobay.toggle.init();
        cargobay.scrollToTop.init();

        appScroll();

        hgabkautils.sidebartoggle.init();
        hgabkautils.sidebartree.init();
        hgabkautils.sidebarsearchfocus.init();
        /*       kunstmaanbundles.urlchoosertree.init();
               kunstmaanbundles.sidebarsearchfocus.init();
               kunstmaanbundles.filter.init();
               kunstmaanbundles.sortableTable.init();
               kunstmaanbundles.checkIfEdited.init();
               kunstmaanbundles.preventDoubleClick.init();
               kunstmaanbundles.datepicker.init();*/
        hgabkautils.autoCollapseButtons.init();
        hgabkautils.autoCollapseTabs.init();
        hgabkautils.urlChooser.init();
        /*    kunstmaanbundles.richEditor.init();
            kunstmaanbundles.ajaxModal.init();
            kunstmaanbundles.advancedSelect.init();

            kunstmaanbundles.pageEditor.init();
            kunstmaanbundles.pagepartEditor.init();

            kunstmaanbundles.slugChooser.init();
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
    $(".js-sortable-link").on("click",function() {
        var href = $(this).data('order-url');
        window.location.href = href;
    });
});
