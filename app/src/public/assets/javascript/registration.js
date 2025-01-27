document.addEventListener('DOMContentLoaded', () => {
    (document.querySelectorAll('#email') || []).forEach((elem) => {
        elem.addEventListener('blur', () => {
            const checkIcon = document.getElementById('email-icon-ok');
            const exclamationIcon = document.getElementById('email-icon-error');
            const helpText = document.getElementById('email-message-error');
            const value = elem.value.trim();
            checkIcon.classList.add('is-invisible');
            exclamationIcon.classList.add('is-invisible');
            helpText.classList.add('is-hidden');
            elem.classList.remove('is-danger', 'is-success');
            if (!value) return; // do not show anything

            if (!EMAIL_REGEX.test(value)) { // show error
                exclamationIcon.classList.remove('is-invisible');
                helpText.classList.remove('is-hidden');
                elem.classList.add('is-danger');
            } else { // show ok
                checkIcon.classList.remove('is-invisible');
                helpText.classList.add('is-hidden');
                elem.classList.add('is-success');
            }
        });
    });

    (document.querySelectorAll('#username') || []).forEach((elem) => {
        elem.addEventListener('blur', () => {
            const checkIcon = document.getElementById('username-icon-ok');
            const exclamationIcon = document.getElementById('username-icon-error');
            const helpText = document.getElementById('username-message-error');
            const value = elem.value.trim();
            checkIcon.classList.add('is-invisible');
            exclamationIcon.classList.add('is-invisible');
            helpText.classList.remove('is-danger', 'is-success');
            elem.classList.remove('is-danger', 'is-success');
            if (!value) return; // do not show anything

            if (!USERNAME_REGEX.test(value)) { // show error
                exclamationIcon.classList.remove('is-invisible');
                helpText.classList.add('is-danger');
                elem.classList.add('is-danger');
            } else { // show ok
                checkIcon.classList.remove('is-invisible');
                helpText.classList.add('is-success');
                elem.classList.add('is-success');
            }
        });

        document.getElementById('password').addEventListener('blur', () => {
            checkPassword();
            checkPasswordConfirm();
        });
        document.getElementById('password_confirm').addEventListener('blur', () => {
            checkPasswordConfirm();
        });
    });

    function checkPassword() {
        const elem = document.getElementById('password');
        const checkIcon = document.getElementById('password-icon-ok');
        const exclamationIcon = document.getElementById('password-icon-error');
        const helpText = document.getElementById('password-message-error');
        const value = elem.value.trim();
        checkIcon.classList.add('is-invisible');
        exclamationIcon.classList.add('is-invisible');
        helpText.classList.remove('is-danger', 'is-success');
        elem.classList.remove('is-danger', 'is-success');
        if (!value) return;

        if (value.length < PASSWORD_MIN_LENGTH) {
            exclamationIcon.classList.remove('is-invisible');
            helpText.classList.add('is-danger');
            elem.classList.add('is-danger');
        } else {
            checkIcon.classList.remove('is-invisible');
            helpText.classList.add('is-success');
            elem.classList.add('is-success');
        }
    }

    function checkPasswordConfirm() {
        const elem = document.getElementById('password_confirm');
        const password = document.getElementById('password').value.trim();
        const checkIcon = document.getElementById('password_confirm-icon-ok');
        const exclamationIcon = document.getElementById('password_confirm-icon-error');
        const helpText = document.getElementById('password_confirm-message-error');
        const value = elem.value.trim();
        checkIcon.classList.add('is-invisible');
        exclamationIcon.classList.add('is-invisible');
        helpText.classList.add('is-invisible');
        elem.classList.remove('is-danger', 'is-success');
        if (!value) return;

        if (value !== password) {
            exclamationIcon.classList.remove('is-invisible');
            helpText.classList.remove('is-invisible');
            elem.classList.add('is-danger');
        } else {
            // Esempio: se vuoi che compaia l'OK anche per la conferma
            if (value.length >= PASSWORD_MIN_LENGTH) {
                checkIcon.classList.remove('is-invisible');
                elem.classList.add('is-success');
            }
        }
    }
});