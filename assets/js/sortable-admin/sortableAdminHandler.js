'use strict';

require('jquery-ui-sortable');

class SortableAdminHandler {
    constructor() {
        this.selector = '.table-sortable';
        this.formSelector = '#positions-form';
    }

    init() {
        let $table = $(this.selector);
        let $form = $(this.formSelector);
        let desc = 'desc' === $table.data('direction') ? true : false;

        if ($table.length > 0) {
            $(this.selector + ' tbody').sortable({
                stop: (event, ui) => {
                    if ($form.length) {
                        let $rows = $(this.selector + ' tbody tr');
                        let realIndex = desc ? $rows.length - 1 : 0;
                        $rows.each((index, element) => {
                            let $input = $form.find('input[name="positions['+$(element).data('id')+']"]');
                            $input.val(desc ? realIndex-- : realIndex++);
                        });

                        $.ajax({
                            url: $table.data('url'),
                            data: $form.serialize(),
                            method: 'POST',
                        })
                    }
                }
            });
        }
    }
}

let sortableAdminHandler = new SortableAdminHandler();
export default sortableAdminHandler;
