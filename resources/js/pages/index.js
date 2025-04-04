async function goToGallery(event) {
    event.preventDefault();
    var url = event.target.closest('a').dataset.url;
    axios.get(url)
        .then(response => {
            window.location.href = response.data.url;
        }).catch(error => {
            var element = document.getElementById('global-error-message');
            var messageElement = document.querySelector('#global-error-message strong');
            messageElement.textContent = error.response.data.message + ": ";
            element.style.display = 'block';
        });
}

document.querySelector('.gallery-button').addEventListener('click', goToGallery);

async function goToShare(event) {
    event.preventDefault();
    var element = event.target.closest('a');
    var url = element.dataset.url;
    var supportImageUpload = element.dataset.supportImageUpload;
    if (supportImageUpload) {
        axios.get(url)
            .then(response => {
                window.location.href = response.data.url;
            }).catch(error => {
                var element = document.getElementById('global-error-message');
                var messageElement = document.querySelector('#global-error-message strong');
                messageElement.textContent = error.response.data.message + ": ";
                element.style.display = 'block';
            });
    }
}

document.querySelector('.share-button').addEventListener('click', goToShare);
