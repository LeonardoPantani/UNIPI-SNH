document.addEventListener('DOMContentLoaded', () => {
    let type = document.getElementById('novel_form');
    let form = document.querySelector('form');

    (document.querySelectorAll('input[name="premium"]') || []).forEach((elem) => {
        elem.addEventListener('blur', () => {
            const errorMessage = document.getElementById('premium-message-error');
            const radios = document.querySelectorAll('input[name="premium"]');
            let isSelected = false;

            radios.forEach((radio) => {
                if (radio.checked) {
                    isSelected = true;
                }
            });

            if (!isSelected) {
                errorMessage.classList.remove('is-hidden');
            } else {
                errorMessage.classList.add('is-hidden');
            }
        });
    });

    (document.querySelectorAll('#title') || []).forEach((elem) => {
        elem.addEventListener('blur', () => {
            const checkIcon = document.getElementById('title-icon-ok');
            const exclamationIcon = document.getElementById('title-icon-error');
            const helpText = document.getElementById('title-message-error');
            const value = elem.value.trim();
            checkIcon.classList.add('is-invisible');
            exclamationIcon.classList.add('is-invisible');
            elem.classList.remove('is-danger', 'is-success');
            helpText.classList.remove('is-danger', 'is-success');
            if (!value) return; // do not show anything

            if (value.length > NOVEL_TITLE_MAX_LENGTH) { // show error
                exclamationIcon.classList.remove('is-invisible');
                helpText.classList.remove('is-success'); 
                helpText.classList.add('is-danger'); 
                elem.classList.add('is-danger');
            } else { // show ok
                checkIcon.classList.remove('is-invisible');
                helpText.classList.add('is-success'); 
                helpText.classList.remove('is-danger'); 
                elem.classList.add('is-success');
            }
        });
    });

    (document.querySelectorAll('#content') || []).forEach((elem) => {
        elem.addEventListener('input', () => {
            const helpText = document.getElementById('content-message-error');
            const value = elem.value.trim();
            elem.classList.remove('is-danger', 'is-success');
            helpText.classList.remove('is-danger', 'is-success');
            document.querySelector('#content-count-message-error').textContent = document.querySelector('#content').value.length;
            if (!value) return; // do not show anything

            if (value.length > NOVEL_TEXT_MAX_LENGTH) { // show error
                helpText.classList.remove('is-success'); 
                helpText.classList.add('is-danger'); 
                elem.classList.add('is-danger');
            } else { // show ok
                helpText.classList.add('is-success'); 
                helpText.classList.remove('is-danger'); 
                elem.classList.add('is-success');
            }
        });
    });

     // Aggiungiamo un event listener per gestire la classe attiva sui label
  document.querySelectorAll('.toggle-button').forEach(function(label) {
    label.addEventListener('click', function() {
      document.querySelectorAll('.toggle-button').forEach(function(btn) {
        btn.classList.remove('is-active');
      });
      this.classList.add('is-active');
    });
  });

    (document.querySelectorAll('#file') || []).forEach((elem) => {
        elem.addEventListener('change', () => {
            const fileNamePlaceholder = document.getElementById('file-name-placeholder');
            const file = elem.files[0];
            if (file) {
                fileNamePlaceholder.textContent = file.name;
            } else {
                fileNamePlaceholder.textContent = 'No file added yet';
            }
        });

        elem.addEventListener('blur', () => {
            const helpText = document.getElementById('file-message-error');
            const value = elem.value.trim();

            if (!value) { // show error
                helpText.classList.remove('is-hidden'); 
            } else { // show ok
                helpText.classList.add('is-hidden'); 
            }
        });
    });
    

    type.addEventListener('change', (el) => {
        if (el.target.value) {
            let text_div = document.getElementById('novel_text')
            let file_div = document.getElementById('novel_file')
            let text_input = text_div.querySelector('textarea')
            let file_input = file_div.querySelector('input')

            switch (el.target.value) {
                case 'text':
                    form.removeAttribute('enctype')

                    file_div.classList.add('is-hidden')
                    file_input.setAttribute('disabled', 'true')
                    file_input.removeAttribute('required')

                    text_div.classList.remove('is-hidden')
                    text_input.removeAttribute('disabled')
                    text_input.setAttribute('required', 'true')
                    break;

                case 'file':
                    form.setAttribute('enctype', 'multipart/form-data')

                    text_div.classList.add('is-hidden')
                    text_input.setAttribute('disabled', 'true')
                    text_input.removeAttribute('required')

                    file_div.classList.remove('is-hidden')
                    file_input.removeAttribute('disabled')
                    file_input.setAttribute('required', 'true')
                    break;

                default:
                    console.log('Error: form div not found')
                    return
            }
        }
    });
});