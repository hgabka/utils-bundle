(function($) {
    $.fn.inputFilter = function(inputFilter) {
        return this.on('input keydown keyup mousedown mouseup select contextmenu drop', function() {
            let $this = $(this);
            
            if (inputFilter(this.value)) {
                this.oldValue = this.value;
                this.oldSelectionStart = this.selectionStart;
                this.oldSelectionEnd = this.selectionEnd;
            } else if (undefined !== $this.data('old-value')) {
                this.value = $this.data('old-value');
                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
            } else if (this.hasOwnProperty('oldValue')) {
                this.value = this.oldValue;
                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
            } else {
                this.value = "";
            }
        });
    };
}(jQuery));
