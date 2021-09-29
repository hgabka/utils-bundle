(function($) {
    $.fn.inputFilter = function(inputFilter) {
        return this.on('input keydown keyup mousedown mouseup select contextmenu drop', function() {
            let valid = !(this.validity && !this.validity.valid);
            if (valid && inputFilter(this.value)) {
                this.oldValue = this.value;
                this.oldSelectionStart = this.selectionStart;
                this.oldSelectionEnd = this.selectionEnd;
            } else if (this.hasOwnProperty("oldValue")) {
                this.value = this.oldValue;
                if ('number' !== this.type) {
                    this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
                }
            } else {
                this.value = '';
            }
        });
    };
}($));
