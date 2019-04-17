(function (global) {
    'use strict';
    var on = (function () {
        if (document.addEventListener) {
            return function (el, event, fn) {
                if (el) {
                    el.addEventListener(event, fn, false);
                }
            };
        } else if (document.attachEvent) {
            return function (el, event, fn) {
                if (el) {
                    el.attachEvent('on' + event, fn);
                }
            };
        }
    }());
    var off = (function () {
        if (document.removeEventListener) {
            return function (el, event, fn) {
                el.removeEventListener(event, fn, false);
            };
        } else if (document.detachEvent) {
            return function (el, event, fn) {
                el.detachEvent('on' + event, fn);
            };
        }
    }());

    function prevent(event) {
        if (event) {
            if (event.preventDefault) {
                event.preventDefault();
            } else {
                event.returnValue = false;
            }
        }
    }

    var transition = (function () {
        var t, type;
        var supported = false;
        var el = document.createElement('fakeelement');
        var transitions = {'WebkitTransition': 'webkitTransitionEnd', 'MozTransition': 'transitionend', 'OTransition': 'otransitionend', 'transition': 'transitionend'};
        for (t in transitions) {
            if (el.style[t] !== undefined) {
                type = transitions[t];
                supported = true;
                break;
            }
        }
        return {type: type, supported: supported};
    }());
    var dialog = (function () {
        var CLASS_BASE = 'alertify';
        var CLASS_TYPE = CLASS_BASE + ' alertify--';
        var CLASS_COVER_SHOW = 'alertify-cover';
        var CLASS_COVER_HIDE = CLASS_COVER_SHOW + ' alertify-hidden';
        var isDialogOpen = false;
        var keys = {ENTER: 13, ESC: 27};
        var parent, transitionTimeout;

        function build() {
            var btnOK = document.getElementById('alertifyButtonOk');
            var btnCancel = document.getElementById('alertifyButtonCancel');
            var titleEl = document.getElementById('alertifyTitle');
            var input = document.getElementById('alertifyInput');
            if (titleEl) {
                titleEl.innerHTML = parent.message;
            }
            if (btnOk) {
                btnOK.innerHTML = parent.settings.ok;
            }
            if (btnCancel) {
                btnCancel.innerHTML = parent.settings.cancel;
            }
            if (input) {
                input.value = parent.value || '';
            }
        }

        function handleTransitionEvent(event) {
            prevent(event);
            clearTimeout(transitionTimeout);
            setFocus();
            if (typeof parent.onfocus === 'function') {
                parent.onfocus();
            }
            off(parent.el, transition.type, handleTransitionEvent);
        }

        function setFocus(reset) {
            var input = document.getElementById('alertifyInput');
            var btnOK = document.getElementById('alertifyButtonOk');
            var btnWrapper = document.getElementById('alertifyButtons');
            var btnCancel = document.getElementById('alertifyButtonCancel');
            on(document.body, 'keyup', onKeyup);
            if (parent.type === 'prompt') {
                input.focus();
                input.select();
            } else if (reset) {
                if (parent.type === 'alert') {
                    btnOK.focus();
                } else {
                    btnWrapper.children[0].focus();
                }
            } else {
                switch (parent.settings.focus) {
                    case'ok':
                        btnOK.focus();
                        break;
                    case'cancel':
                        btnCancel.focus();
                        break;
                    default:
                        btnOK.focus();
                }
            }
        }

        function onReset(event) {
            prevent(event);
            setFocus(true);
        }

        function onOK(event) {
            var input = document.getElementById('alertifyInput');
            prevent(event);
            parent.close();
            if (typeof parent.ok === 'function') {
                parent.ok(input.value);
            }
        }

        function onCancel(event) {
            prevent(event);
            parent.close();
            if (typeof parent.cancel === 'function') {
                parent.cancel();
            }
        }

        function onKeyup(event) {
            var keyCode = event.keyCode;
            if (keyCode === keys.ENTER) {
                onOK(event);
            }
            if (keyCode === keys.ESC && /prompt|confirm/.test(parent.type)) {
                onCancel(event);
            }
        }

        function setSettings(settings) {
            this.settings = settings;
        }

        return {
            el: document.getElementById('alertifyDialog'), activeElement: document.body, settings: {ok: 'OK', cancel: 'Cancel', focus: 'ok'}, isOpen: function () {
                return isDialogOpen;
            }, show: function () {
                this.el = document.getElementById('alertifyDialog');
                this.settings = {ok: $(this.el).data('ok'), cancel: $(this.el).data('cancel'), focus: $(this.el).data('focus')};
                this.activeElement = document.body;
                if (isDialogOpen) {
                    return false;
                } else {
                    var btnFocusReset = document.getElementById('alertifyFocusReset');
                    var coverEl = document.getElementById('alertifyCover');
                    var btnOK = document.getElementById('alertifyButtonOk');
                    var btnCancel = document.getElementById('alertifyButtonCancel');
                    isDialogOpen = true;
                    parent = this;
                    build();
                    dialog.activeElement = document.activeElement;
                    on(btnOK, 'click', onOK);
                    on(btnCancel, 'click', onCancel);
                    on(btnFocusReset, 'focus', onReset);
                    if (transition.supported) {
                        on(this.el, transition.type, handleTransitionEvent);
                        clearTimeout(transitionTimeout);
                        transitionTimeout = setTimeout(handleTransitionEvent, 1000);
                    }
                    coverEl.className = CLASS_COVER_SHOW;
                    this.el.className = CLASS_TYPE + this.type;
                    if (!transition.supported) {
                        setFocus();
                    }
                    if (typeof this.onshow === 'function') {
                        this.onshow();
                    }
                    return true;
                }
            }, close: function () {
                var btnOK = document.getElementById('alertifyButtonOk');
                var btnCancel = document.getElementById('alertifyButtonCancel');
                var btnFocusReset = document.getElementById('alertifyFocusReset');
                var coverEl = document.getElementById('alertifyCover');
                off(btnOK, 'click', onOK);
                off(btnCancel, 'click', onCancel);
                off(document.body, 'keyup', onKeyup);
                off(btnFocusReset, 'focus', onReset);
                coverEl.className = CLASS_COVER_HIDE;
                this.el.className += ' alertify-close';
                dialog.activeElement.focus();
                isDialogOpen = false;
                if (typeof this.onclose === 'function') {
                    this.onclose();
                }
            }
        };
    }());

    function AlertifyAlert(message) {
        this.message = message;
        this.type = 'alert';
    }

    AlertifyAlert.prototype = dialog;

    function AlertifyConfirm(message) {
        this.message = message;
        this.type = 'confirm';
    }

    AlertifyConfirm.prototype = dialog;

    function AlertifyPrompt(message, value) {
        this.message = message;
        this.value = value;
        this.type = 'prompt';
    }

    AlertifyPrompt.prototype = dialog;

    function Alertify() {
        return {
            settings: dialog.settings, alert: function (message) {
                return new AlertifyAlert(message);
            }, confirm: function (message) {
                return new AlertifyConfirm(message);
            }, prompt: function (message, value) {
                return new AlertifyPrompt(message, value);
            }
        };
    }

    if (typeof define === 'function') {
        define([], function () {
            return new Alertify();
        });
    } else if (!global.alertify) {
        global.alertify = new Alertify();
    }
}(this));
