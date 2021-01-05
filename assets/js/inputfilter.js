(function ($) {
    $.fn.inputFilter = function (inputFilter) {
        return this.on('input keydown keyup mousedown mouseup select contextmenu drop keypress', function (event) {
            let $this = $(this);
            if ('keypress' === event.type) {
                if ('number' === $this.attr('type')) {
                    let key = event.charCode || event.keyCode || 0;
                    if (!(key === 8 ||
                        key === 9 ||
                        key === 13 ||
                        key === 46 ||
                        key === 110 ||
                        key === 190 ||
                        (key >= 35 && key <= 40) ||
                        (key >= 48 && key <= 57)
                    )) {
                        event.preventDefault();
                    }
                }
            } else {
                if (inputFilter(this.value, this)) {
                    this.oldValue = this.value;
                    this.oldSelectionStart = this.selectionStart;
                    this.oldSelectionEnd = this.selectionEnd;
                } else if (undefined !== $this.data('old-value')) {
                    this.value = $this.data('old-value');
                    if ('number' !== $this.attr('type')) {
                        this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
                    }
                } else if (this.hasOwnProperty('oldValue')) {
                    this.value = this.oldValue;
                    if ('number' !== $this.attr('type')) {
                        this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
                    }
                } else {
                    this.value = "";
                }
            }
        });
    };
}(jQuery));
