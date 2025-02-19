document.addEventListener('DOMContentLoaded', () => {
    function checkPasswordOld() {
        const elem = document.getElementById('password_old');
        const checkIcon = document.getElementById('password_old-icon-ok');
        const exclamationIcon = document.getElementById('password_old-icon-error');
        const helpText = document.getElementById('password_old-message-error');
        const value = elem.value.trim();
        checkIcon.classList.add('is-invisible');
        exclamationIcon.classList.add('is-invisible');
        elem.classList.remove('is-danger', 'is-success');
        if (!value) return;

        if (!PASSWORD_REGEX.test(value)) {
            exclamationIcon.classList.remove('is-invisible');
            helpText.classList.remove('is-hidden');
            elem.classList.add('is-danger');
        } else {
            checkIcon.classList.remove('is-invisible');
            helpText.classList.add('is-hidden');
            elem.classList.add('is-success');
        }
    }

    function checkPasswordNew() {
        const elem = document.getElementById('password_new');
        const checkIcon = document.getElementById('password_new-icon-ok');
        const exclamationIcon = document.getElementById('password_new-icon-error');
        const helpText = document.getElementById('password_new-message-error');
        const value = elem.value.trim();
        checkIcon.classList.add('is-invisible');
        exclamationIcon.classList.add('is-invisible');
        helpText.classList.remove('is-danger', 'is-success');
        elem.classList.remove('is-danger', 'is-success');
        if (!value) return;

        if (!PASSWORD_REGEX.test(value)) {
            exclamationIcon.classList.remove('is-invisible');
            helpText.classList.add('is-danger');
            elem.classList.add('is-danger');
        } else {
            checkIcon.classList.remove('is-invisible');
            helpText.classList.add('is-success');
            elem.classList.add('is-success');
        }
    }

    function checkPasswordNewConfirm() {
        const elem = document.getElementById('password_new_confirm');
        const password = document.getElementById('password_new').value.trim();
        const checkIcon = document.getElementById('password_new_confirm-icon-ok');
        const exclamationIcon = document.getElementById('password_new_confirm-icon-error');
        const helpText = document.getElementById('password_new_confirm-message-error');
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
            if (value.length >= PASSWORD_MIN_LENGTH) {
                checkIcon.classList.remove('is-invisible');
                elem.classList.add('is-success');
            }
        }
    }

    document.getElementById('password_old').addEventListener('blur', () => {
        checkPasswordOld();
    });
    document.getElementById('password_new').addEventListener('blur', () => {
        checkPasswordNew();
        checkPasswordNewConfirm();
    });
    document.getElementById('password_new_confirm').addEventListener('blur', () => {
        checkPasswordNewConfirm();
    });
});