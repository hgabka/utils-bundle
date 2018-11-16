export function myAlert(text, okfunc, title) {
    var alert = alertify.alert(text);
    if (typeof okfunc == 'function') {
        alert.ok = okfunc;
    } else {
        alert.ok = function () {
            return false;
        }
    }
    if (typeof title !== 'undefined') {
        $('#alertify-title-text').html(title);
    }
    alert.show();
    jQuery('span.alertify-close-x').off('click');
    jQuery('span.alertify-close-x').on('click', function () {
        alert.close();
        return false;
    });
}

export function myConfirm(text, okfunc, cancelfunc) {
    var alert = alertify.confirm(text);
    if (typeof okfunc == 'function') {
        alert.ok = okfunc;
    } else {
        alert.ok = function () {
            return false;
        }
    }
    if (typeof cancelfunc == 'function') {
        alert.cancel = cancelfunc;
    } else {
        alert.cancel = function () {
            return false;
        }
    }
    alert.show();
    jQuery('span.alertify-close-x').off('click');
    jQuery('span.alertify-close-x').on('click', function () {
        alert.close();
    });
}

export function myLinkConfirm(text, link, title) {
    var alert = alertify.confirm(text);
    alert.ok = function () {
        window.location = jQuery(link).attr('href');
    }
    alert.cancel = function () {
        return false;
    }
    if (typeof title !== 'undefined') {
        $('#alertify-title-text').html(title);
    }
    alert.show();
    jQuery('span.alertify-close-x').off('click');
    jQuery('span.alertify-close-x').on('click', function () {
        alert.close();
    });
    return false;
}

$(function() {
    let $container = $('#alertifyCover');
    alertify.settings.ok = $container.data('ok');
    alertify.settings.cancel = $container.data('cancel');
    alertify.settings.focus = $container.data('focus');
});
