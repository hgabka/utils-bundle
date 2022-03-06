$.fn.swapWith = function (that, animate) {
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
