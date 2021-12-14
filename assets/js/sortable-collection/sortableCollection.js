'use strict';

require('jquery-ui-sortable');
let ajaxModal = require('../ajaxmodal.js').ajaxModal;

$.fn.swapWith = function(that, animate) {
    var $this = this;
    var $that = $(that);

    // create temporary placeholder
    var $temp = $("<div>");

    if ('undefined' !== typeof animate && animate) {
        $this.hide();
        $that.hide();
        $this.before($temp);
        $that.before($this);
        $this.fadeIn({duration: 'slow', easing: 'linear'});
        $that.fadeIn({duration: 'slow', easing: 'linear'});
    } else {
        // 3-step swap
        $this.before($temp);
        $that.before($this);
    }
    $temp.after($that).remove();

    return $this;
}

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
        this.moveDown = this.moveDown.bind(this);
        this.moveUp = this.moveUp.bind(this);
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
            if (i === 1) {
                if ($moveUp.length) {
                    $moveUp.remove();
                }
                $element.removeClass('has-up');
            } else {
                if (!$moveUp.length) {
                    $container.prepend($('<span class="collection-move move-up"><i class="fa fa-sort-up"></i></span>'));
                }
                $element.addClass('has-up');
            }

            if (i === $rows.length) {
                if ($moveDown.length) {
                    $moveDown.remove();
                }
                $element.removeClass('has-down');
            } else {
                if (!$moveDown.length) {
                    $container.prepend($('<span class="collection-move move-down"><i class="fa fa-sort-down"></i></span>'));
                }
                $element.addClass('has-down');
            }

            $element.find('input[name$="[' + this.options.sortFieldName + ']"]').val(i++);
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

            $collectionHolder.on('click', '.move-down', e => {
                let $target = $(e.currentTarget);
                this.moveDown($target.closest(this.options.rowSelector));
            });

            $collectionHolder.on('click', '.move-up', e => {
                let $target = $(e.currentTarget);
                this.moveUp($target.closest(this.options.rowSelector));
            });

            this.reOrder();
        }
    }

    moveDown($box) {
        let $next = $box.next(this.options.rowSelector);
        if ($next.length) {
            $box.swapWith($next, true);

            this.reOrder();
        }
    }
    moveUp($box) {
        let $prev = $box.prev(this.options.rowSelector);
        if ($prev.length) {
            $prev.swapWith($box, true);

            this.reOrder();
        }
    }
}

export default SortableCollectionHandler;
