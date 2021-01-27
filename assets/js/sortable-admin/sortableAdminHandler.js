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

        if ($table.length > 0) {
            $(this.selector + ' tbody').sortable({
                stop: (event, ui) => {
                    if ($form.length) {
                        let realIndex = 0;
                        $(this.selector + ' tbody tr').each((index, element) => {
                            let $input = $form.find('input[name="positions['+$(element).data('id')+']"]');
                            $input.val(realIndex++);
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
