document.addEventListener('DOMContentLoaded', () => {
    let type = document.getElementById('novel_form')

    type.addEventListener('change', (el) => {
        if (el.target.value) {
            let text_div = document.getElementById('novel_text');
            let file_div = document.getElementById('novel_file');

            switch (el.target.value) {
                case 'text':
                    file_div.setAttribute('style', 'display: none')
                    text_div.removeAttribute('style')
                    break;

                case 'file':
                    text_div.setAttribute('style', 'display: none')
                    file_div.removeAttribute('style')
                    break;

                default:
                    console.log('Error: form div not found')
                    return
            }
        }
    });
});