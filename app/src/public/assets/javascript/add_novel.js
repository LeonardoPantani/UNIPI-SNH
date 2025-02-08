document.addEventListener('DOMContentLoaded', () => {
    let type = document.getElementById('novel_form')
    let form = document.querySelector('form')

    type.addEventListener('change', (el) => {
        if (el.target.value) {
            let text_div = document.getElementById('novel_text')
            let file_div = document.getElementById('novel_file')
            let text_input = text_div.querySelector('input')
            let file_input = file_div.querySelector('input')

            switch (el.target.value) {
                case 'text':
                    form.removeAttribute('enctype')
                    file_div.classList.add('is-hidden')
                    file_input.setAttribute('disabled', true)
                    text_div.classList.remove('is-hidden')
                    text_input.removeAttribute('disabled')
                    break;

                case 'file':
                    form.setAttribute('enctype', 'multipart/form-data')
                    text_div.classList.add('is-hidden')
                    text_input.setAttribute('disabled', true)
                    file_div.classList.remove('is-hidden')
                    file_input.removeAttribute('disabled')
                    break;

                default:
                    console.log('Error: form div not found')
                    return
            }
        }
    });
});