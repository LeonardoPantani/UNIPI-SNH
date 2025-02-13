document.addEventListener('DOMContentLoaded', () => {
    const codeInputs = document.querySelectorAll('.code-input');
    const hiddenCode = document.getElementById('code');

    updateHiddenCode();

    document.getElementById('password').addEventListener('blur', () => {
        checkPassword();
        checkPasswordConfirm();
    });
    document.getElementById('password_confirm').addEventListener('blur', () => {
        checkPasswordConfirm();
    });
    
    function handleCodeInput(e) {
        const input = e.target;
        const nextIndex = parseInt(input.dataset.index) + 1;
        
        if (input.value.length === 1 && nextIndex < 5) {
            codeInputs[nextIndex].focus();
        }
        
        updateHiddenCode();
    }

    function handleBackspace(e) {
        updateHiddenCode();
    }

    function handlePaste(e) {
        e.preventDefault();
        const pasteData = (e.clipboardData || window.clipboardData).getData('text').slice(0, 5);
        
        pasteData.split('').forEach((char, index) => {
            if (index < 5) {
                codeInputs[index].value = char.toUpperCase();
            }
        });
        
        updateHiddenCode();
        codeInputs[Math.min(pasteData.length, 4)].focus();
    }

    function updateHiddenCode() {
        const code = Array.from(codeInputs)
            .map(input => input.value.trim())
            .join('')
            .toUpperCase();
        
        hiddenCode.value = code;
    
        const passwordFields = document.querySelectorAll('.password-field');
        const minicodeFields = document.querySelectorAll('div.minicode > input');
        const minicodeHelpField = document.querySelector('.code-field-message');
        const submitButton = document.querySelector('#submit');
        const shouldShow = code.length === 5;
        
        passwordFields.forEach(field => {
            field.classList.toggle('is-invisible', !shouldShow);
            field.querySelector('input').required = shouldShow;
        });

        if(shouldShow) {
            minicodeFields.forEach(field => {
                field.classList.add('is-success');
            });
            minicodeHelpField.classList.add('is-success');

            submitButton.removeAttribute('disabled');
            submitButton.classList.remove('is-disabled');
        } else {
            minicodeFields.forEach(field => {
                field.classList.remove('is-success');
            });
            minicodeHelpField.classList.remove('is-success');

            submitButton.disabled = true;
            submitButton.classList.add('is-disabled');
        }
    }

    codeInputs.forEach(input => {
        input.addEventListener('input', handleCodeInput);
        input.addEventListener('keydown', handleBackspace);
        input.addEventListener('paste', handlePaste);
    });

    let allSelected = false;

    // ctrl+a
    codeInputs.forEach(input => {
        input.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'a') {
                e.preventDefault();

                allSelected = true;
                codeInputs.forEach(input => {
                    input.classList.add('has-background-link');
                    input.value = input.value.trim();
                });
                codeInputs[0].focus();
            }
            
            if (allSelected && (e.key === 'Delete' || e.key === 'Backspace')) {
                e.preventDefault();
                codeInputs.forEach(input => {
                    input.value = '';
                    input.classList.remove('has-background-link');
                });
                codeInputs[0].focus();
                allSelected = false;
                updateHiddenCode();
            }
        });
        
        input.addEventListener('input', () => {
            if (allSelected) {
                allSelected = false;
                codeInputs.forEach(input => input.classList.remove('has-background-link'));
            }
        });
    });

    // copia con ctrl+c
    document.addEventListener('keydown', (e) => {
        if (allSelected && (e.ctrlKey || e.metaKey) && e.key === 'c') {
            e.preventDefault();
            const code = Array.from(codeInputs).map(i => i.value).join('');
            navigator.clipboard.writeText(code);
        }
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
        if (value.length >= PASSWORD_MIN_LENGTH) {
            checkIcon.classList.remove('is-invisible');
            elem.classList.add('is-success');
        }
    }
}