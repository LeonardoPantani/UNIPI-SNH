document.addEventListener('DOMContentLoaded', () => {
    (document.querySelectorAll('#email') || []).forEach((elem) => {
        elem.addEventListener('blur', () => {
            const checkIcon = document.getElementById('email-icon-ok');
            const exclamationIcon = document.getElementById('email-icon-error');
            const helpText = document.getElementById('email-message-error');
            const value = elem.value.trim();
            checkIcon.classList.add('is-invisible');
            exclamationIcon.classList.add('is-invisible');
            helpText.classList.add('is-invisible');
            elem.classList.remove('is-danger', 'is-success');
            if (!value) return; // do not show anything

            if (!EMAIL_REGEX.test(value)) { // show error
                exclamationIcon.classList.remove('is-invisible');
                helpText.classList.remove('is-invisible');
                elem.classList.add('is-danger');
            } else { // show ok
                checkIcon.classList.remove('is-invisible');
                helpText.classList.add('is-invisible');
                elem.classList.add('is-success');
            }
        });
    });
});