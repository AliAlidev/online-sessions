async function checkPassword(event) {
    event.preventDefault();
    var url = event.target.closest('form').getAttribute('action');
    const element = document.getElementById('global-error-message');
    element.style.display = 'none';
    window.axios.post(url, {
        _token: $('meta[name="csrf-token"]').attr('content'),
        password: $('#password').val(),
        event_slug: $('#event_slug').val(),
        year: $('#year').val(),
        month: $('#month').val()
    })
        .then(response => {
            if (response.data.success) {
                localStorage.setItem($('#event_slug').val(), $('#password').val());
                window.location.href = response.data.url;
            } else {
                element.textContent = response.data.message;
                element.style.display = 'block';
            }
        }).catch(error => {
            if (error.response?.data?.message) {
                element.textContent = error.response.data.message;
                element.style.display = 'block';
            } else {
                element.textContent = 'Something went wrong. Please try again.';
                element.style.display = 'block';
            }
        });
}

document.querySelector('#formAuthenticationBtn').addEventListener('click', checkPassword);
