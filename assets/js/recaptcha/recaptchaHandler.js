'use strict';

class RecaptchaHandler {
    constructor(options) {
        this.options = $.extend({
            submitSelector: 'button[type="submit"]',
            captchaHolderSelector: '.captcha-holder',
            hiddenInputName: 'g-recaptcha-response',
            checkValidity: true,
        }, options);
    }

    init() {
        $(this.options.submitSelector).click(e => {
            const $form = $(e.currentTarget).closest('form');
            const $holder = $form.find(this.options.captchaHolderSelector);
            if (this.options.checkValidity) {
                if (!$form[0].checkValidity()) {
                    $form[0].reportValidity();

                    return false;
                }
            }
            if ($holder.length > 0) {
                e.preventDefault();
                const sitekey = $holder.data('sitekey');
                const action = $holder.data('action');
                grecaptcha.ready(() => {
                    grecaptcha.execute(sitekey, {action: action}).then(token => {
                        $form.find(`input[name="${this.options.hiddenInputName}"]`).val(token);
                        $form.submit();
                    });
                });
            }
        });
    }
}

export default RecaptchaHandler;
