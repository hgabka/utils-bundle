var urlChooser = {};

urlChooser.urlChooser = (function (window, undefined) {

    var init, urlChooser, saveUrlChooserModal, saveMediaChooserModal, getUrlParam, adaptUrlChooser;

    var itemUrl, itemId, itemTitle, itemThumbPath, itemSvg, replacedUrl, $body = $('body');


    init = function () {
        $body = $('body');
        urlChooser();
        adaptUrlChooser();
    };

    // URL-Chooser
    urlChooser = function () {

        // Link Chooser select
        $body.on('click', '.js-url-chooser-link-select', function (e) {
            e.stopImmediatePropagation();
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
                svg = $this.data('svg'),
                id = $this.data('id'),
                title = $this.data('title'),
                cke = $this.data('cke'),
                replaceUrl = $this.closest('.thumbnail-wrapper').data('replace-url');

            // Store values
            itemUrl = path;
            itemId = id;
            itemTitle = title;
            itemThumbPath = thumbPath
            itemSvg = svg
            ;

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
                window.parent.$('#' + parentModalId).removeClass('show');
                window.parent.$('.modal-backdrop').remove();

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
            window.parent.$('#' + parentModalId).removeClass('show');
            window.parent.$('.modal-backdrop').remove();

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
                $previewSvgHolder = window.parent.$('#' + linkedInputId + '__svg_holder'),
                $previewTitle = window.parent.$('#' + linkedInputId + '__preview__title')
            ;

            $mediaChooser.addClass('media-chooser--choosen');
            $previewTitle.html(itemTitle);
            var $parent = $previewTitle.parent();
            $parent.find('.fa-file-o.media-thumbnail__icon').remove();

            if (itemThumbPath === "") {
                $parent.prepend('<i class="fa fa-file-o media-thumbnail__icon"></i>');
                $previewSvgHolder.remove();
                $previewImg.remove();
            } else {
                if (itemSvg !== '') {
                    if ($previewSvgHolder.length > 0) {
                        $previewSvgHolder.html(itemSvg);
                    } else {
                        var div = '<div id="' + linkedInputId + '__svg_holder" class="text-center media-thumbnail-svg-holder">' + itemSvg + '</div>';

                        if ($previewImg.length > 0) {
                            $previewImg.replaceWith(div);
                        } else {
                            $parent.prepend(div)
                        }
                    }
                } else {
                    if ($previewImg.length > 0) {
                        $previewImg.attr('src', itemThumbPath);
                    } else {
                        var img = '<img id="' + linkedInputId + '__preview__img" src="' + itemThumbPath + '" alt="' + itemTitle + '" class="media-thumbnail__img">';

                        if ($previewSvgHolder.length > 0) {
                            $previewSvgHolder.replaceWith(img);
                        } else {
                            $parent.prepend(img);
                        }
                    }
                }

            }

            // Close modal
            window.parent.$('#' + parentModalId).modal('hide');
            window.parent.$('#' + parentModalId).removeClass('show');
            window.parent.$('.modal-backdrop').remove();

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

module.exports = {
    urlChooser: urlChooser,
};
