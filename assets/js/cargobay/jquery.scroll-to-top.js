var cargobayScrollToTop = {};

cargobayScrollToTop.scrollToTop = (function($, window, undefined) {

    var init,
        duration,
        defaultDuration,
        $hook;

    init = function() {
        defaultDuration = 300;
        $hook = $('.js-scroll-to-top');
        $hook.on('click', function(e) {
            e.preventDefault();

            var $this = $(this),
                dataDuration = $this.data('animation-duration');

            duration = (typeof dataDuration !== undefined && !isNaN(dataDuration)) ? dataDuration : defaultDuration;

            $('html, body').animate({scrollTop: 0}, duration);
        });
    };

    return {
        init: init
    };

}(jQuery, window));

module.exports = {
    cargobayScrollToTop: cargobayScrollToTop,
};
