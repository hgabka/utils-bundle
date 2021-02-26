'use strict';

require('../typeahead.js');

class DynamicCollectionHandler {
    constructor(options) {
        this.options = $.extend({
            containerSelector: '.dynamic-collection',
            boxSelector: '.dynamic-collection-box',
            inputSelector: 'input[type="text"][name$="[text]"]',
            deleteLinkSelector: '.delete-collection-box',
        }, options);

        this.addBox = this.addBox.bind(this);
        this.initContainer = this.initContainer.bind(this);
    }

    init() {
        let $container = $(this.options.containerSelector);

        if ($container.length) {
            $container.each((index, element) => {
               this.initContainer($(element));
            });
        }
    }

    initContainer($collectionContainer) {
        if ($collectionContainer.data('dynamic_collection:initialized')) {
            return;
        }

        let $boxes = $collectionContainer.find(this.options.boxSelector);
        let counter = $collectionContainer.data('counter') || $boxes.length;

        let $input = $collectionContainer.find(this.options.inputSelector);
        $input.typeahead({
            source: $collectionContainer.data('entities'),
            onSelect: (data) => this.addBox($collectionContainer, $input, counter, data.value, data.text),
        });

        $input.keypress((event) => {
            let keycode = (event.keyCode ? event.keyCode : event.which);
            if (keycode === 13) {
                event.preventDefault();
                event.stopPropagation();
                if ($input.val().length) {
                    this.addBox($collectionContainer, $input, counter, '', $input.val());
                }
            }
        });

        $collectionContainer.on('click', this.options.deleteLinkSelector, event => {
            event.preventDefault();
            $(event.currentTarget).closest(this.options.boxSelector).remove();
        });

        $collectionContainer.data('dynamic_collection:initialized', true);
    }

    addBox($collectionContainer, $input, counter, id, name) {
        let $boxes = $collectionContainer.find(this.options.boxSelector);
        let exists = false;

        $boxes.each((index, element) => {
            let $nameInput = $(element).find('input[type="hidden"][name$="[name]"]');
            if ($nameInput.val().trim() === name.trim()) {
                exists = true;
            }
        });

        if (!exists) {
            let prototype = $collectionContainer.data('prototype');
            let newWidget = prototype.replace(/__name__/g, counter);

            newWidget = newWidget.replace(/__entityid__/g, id);
            newWidget = newWidget.replace(/__entityname__/g, name);
            $collectionContainer.append($(newWidget));
            counter++;
            $collectionContainer.data('counter', counter);
        }

        $input.val('');
    }
}

export default DynamicCollectionHandler;
