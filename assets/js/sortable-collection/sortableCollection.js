'use strict';

require('jquery-ui-sortable');
let ajaxModal = require('../ajaxmodal.js').ajaxModal;
require('../swapWith.js');

class SortableCollectionHandler
{
    constructor(options) {
        this.options = $.extend({
            containerSelector: '.sortable-collection',
            sortFieldName: 'position',
            rowSelector: '.sonata-collection-row',
            formHolderSelector: '.sonata-ba-form',
            firstAndLastButtons: false,
        }, options);
        this.reOrder = this.reOrder.bind(this);
        this.moveDown = this.moveDown.bind(this);
        this.moveUp = this.moveUp.bind(this);
        this.moveFirst = this.moveFirst.bind(this);
        this.moveLast = this.moveLast.bind(this);
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
            let $moveUp = $container.find('.move-up');
            let $moveDown = $container.find('.move-down');
            let $moveFirst = $container.find('.move-first');
            let $moveLast = $container.find('.move-last');
            if (i === 1) {
                if ($moveUp.length) {
                    $moveUp.remove();
                }
                $element.removeClass('has-up');
                $element.removeClass('has-first');
                if ($moveFirst.length) {
                    $moveFirst.remove();
                }
            } else {
                if (!$moveUp.length) {
                    $container.prepend($('<span class="collection-move move-up"><i class="fa fa-sort-up"></i></span>'));
                }
                $element.addClass('has-up');
                if (this.options.firstAndLastButtons) {
                    if (!$moveFirst.length) {
                        $container.prepend($('<span class="collection-move move-first"><i class="fa fa-angle-double-up"></i></span>'));
                    }
                    $element.addClass('has-first');
                }
            }

            if (i === $rows.length) {
                if ($moveDown.length) {
                    $moveDown.remove();
                }
                if ($moveLast.length) {
                    $moveLast.remove();
                }
                $element.removeClass('has-down');
                $element.removeClass('has-last');
            } else {
                if (!$moveDown.length) {
                    $container.prepend($('<span class="collection-move move-down"><i class="fa fa-sort-down"></i></span>'));
                }
                $element.addClass('has-down');
                if (this.options.firstAndLastButtons) {
                    if (!$moveLast.length) {
                        $container.prepend($('<span class="collection-move move-last"><i class="fa fa-angle-double-down"></i></span>'));
                    }
                    $element.addClass('has-last');
                }
            }
            $element.find('input[name$="[' + this.options.sortFieldName + ']"]').val(i++);
            this.addSortHandlers($collectionHolder);
        });
    }

    init() {
        let $collectionHolder = $(this.options.containerSelector);
        $collectionHolder.find('.sonata-collection-add i').prepend('<span style="pointer-events: none;">Hozzáadás</span>');
        $collectionHolder.addClass('sortable-collection');
        if ($collectionHolder.length) {
            $collectionHolder.sortable({
                //handle: '.sonata-collection-row',
                items: this.options.rowSelector,
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
                        //  $collectionHolder.trigger('sortable:add');
                    }, 200);
                }

                if ($target.hasClass('sonata-collection-delete')) {
                    setTimeout(_ => {
                        // $collectionHolder.trigger('sortable:delete');
                    }, 200);
                }

                setTimeout(this.reOrder, 200);
            });

            this.addSortHandlers($collectionHolder);

            this.reOrder();
        }
    }

    addSortHandlers = ($collectionHolder) => {
        $collectionHolder.off('click.sortable-collection');

        $collectionHolder.on('click.sortable-collection', '.move-down', e => {
            let $target = $(e.currentTarget);
            this.moveDown($target.closest(this.options.rowSelector));
        });

        $collectionHolder.on('click.sortable-collection', '.move-last', e => {
            let $target = $(e.currentTarget);
            this.moveLast($target.closest(this.options.rowSelector), $collectionHolder);
        });

        $collectionHolder.on('click.sortable-collection', '.move-up', e => {
            let $target = $(e.currentTarget);
            this.moveUp($target.closest(this.options.rowSelector));
        });

        $collectionHolder.on('click.sortable-collection', '.move-first', e => {
            let $target = $(e.currentTarget);
            this.moveFirst($target.closest(this.options.rowSelector), $collectionHolder);
        });

    }

    moveDown($box) {
        let $next = $box.next(this.options.rowSelector);
        if ($next.length) {
            $box.swapWith($next, true);

            this.reOrder();
        }
    }

    moveLast($box, $holder) {
        let $button = $holder.find('.sonata-collection-add').closest('div');
        $box.insertBefore($button);
        this.reOrder();
        $('body, html').animate({scrollTop: $box.offset().top - 150});
    }

    moveUp($box) {
        let $prev = $box.prev(this.options.rowSelector);
        if ($prev.length) {
            $prev.swapWith($box, true);

            this.reOrder();
        }
    }

    moveFirst($box, $holder) {
        $box.prependTo($holder);
        this.reOrder();
        $('body, html').animate({scrollTop: $holder.offset().top - 150});
    }
}

export default SortableCollectionHandler;
