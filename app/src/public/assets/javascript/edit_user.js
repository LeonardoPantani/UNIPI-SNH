document.addEventListener('DOMContentLoaded', () => {
    let debounceTimeout = null;
    const usernameInput = document.getElementById('username');
    const dropdown = document.getElementById('username-dropdown');
    const suggestionsContainer = document.getElementById('username-suggestions');
    const csrfToken = document.getElementById('token').value;

    usernameInput.addEventListener('input', function () {
        const query = this.value.trim();
        clearTimeout(debounceTimeout);
        if (query.length === 0) {
            dropdown.classList.remove('is-active');
            return;
        }
        debounceTimeout = setTimeout(() => {
            fetch('/api/v1/users', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ token: csrfToken, username: query }),
                credentials: "same-origin"
            })
                .then(response => response.json())
                .then(data => {
                    suggestionsContainer.innerHTML = '';
                    if (data.response && data.response.length > 0) {
                        data.response.forEach(item => {
                            const suggestionItem = document.createElement('a');
                            suggestionItem.href = "#";
                            suggestionItem.classList.add('dropdown-item');
                            suggestionItem.textContent = item;
                            suggestionItem.addEventListener('click', function (e) {
                                e.preventDefault();
                                usernameInput.value = item;
                                dropdown.classList.remove('is-active');
                            });
                            suggestionsContainer.appendChild(suggestionItem);
                        });
                        dropdown.classList.add('is-active');
                    } else {
                        dropdown.classList.remove('is-active');
                    }
                })
                .catch(() => {
                    dropdown.classList.remove('is-active');
                });
        }, 300);
    });

    document.addEventListener('click', function (e) {
        if (!dropdown.contains(e.target)) {
            dropdown.classList.remove('is-active');
        }
    });
});
