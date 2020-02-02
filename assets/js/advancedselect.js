require('./select2.js');
var advancedSelect = {};

advancedSelect.advancedSelect = (function(window, undefined) {

    var init;

    init = function() {
        $('.js-advanced-select').select2();
    };

    return {
        init: init
    };

})(window);

module.exports = {
    advancedSelect: advancedSelect,
};