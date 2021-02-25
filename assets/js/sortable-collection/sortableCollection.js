'use strict';

require('jquery-ui-sortable');
let ajaxModal = require('../ajaxmodal.js').ajaxModal;

class SortableCollectionHandler
{
    constructor(options) {
        this.options = $.extend({
            containerSelector: '.sortable-collection',
            sortFieldName: 'position',
            rowSelector: '.sonata-collection-row',
            formHolderSelector: '.sonata-ba-form'
        }, options);
        this.reOrder = this.reOrder.bind(this);
    }

    reOrder() {
        let i = 1;
        let $rows = $(this.options.containerSelector).find(this.options.rowSelector);
        $rows.each((index, element) => {
            let $element = $(element);
            let $container = $(element).find('.col-xs-11');
            let $span = $container.find('span.row-number');
            if (!$span.length) {
                $span = $('<span class="row-number"></span>');
                $container.prepend($span);
            }
            $span.html(i);

            $element.find('input[name$="[' + this.options.sortFieldName + ']"]').val(i++);
        })
    }

    init() {
        let $collectionHolder = $(this.options.containerSelector);
        $collectionHolder.find('.sonata-collection-add i').prepend('<span>Hozzáadás</span>');
        $collectionHolder.addClass('sortable-collection');
        if ($collectionHolder.length) {
            $collectionHolder.sortable({
                start: (event, ui) => {
                    ajaxModal.ajaxModal.resetAjaxModals();
                    $collectionHolder.trigger('sortable:start');
                },

                stop: (event, ui) => {
                    ajaxModal.ajaxModal.init();
                    $collectionHolder.trigger('sortable:stop');
                },

                update: (event, ui) => {
                    this.reOrder();
                    $collectionHolder.trigger('sortable:update');
                }
            });

            $collectionHolder.find('form').submit(this.reOrder);

            $collectionHolder.on('click', '.sonata-collection-add, .sonata-collection-delete', e => {
                let $target = $(e.currentTarget);

                if ($target.hasClass('sonata-collection-add')) {
                    setTimeout(_ => {
                        $collectionHolder.trigger('sortable:add');
                    }, 200);
                }

                if ($target.hasClass('sonata-collection-delete')) {
                    setTimeout(_ => {
                        $collectionHolder.trigger('sortable:delete');
                    }, 200);
                }

                setTimeout(this.reOrder, 200);
            });

            this.reOrder();
        }
    }
}

export default SortableCollectionHandler;
