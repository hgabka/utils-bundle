'use strict'

class EmailValidatorClass {
    constructor() {
        this.regExp = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    }

    validateEmail(email) {
        return this.regExp.test(email);
    }
}

let emailValidator = new EmailValidatorClass();
export default emailValidator;
