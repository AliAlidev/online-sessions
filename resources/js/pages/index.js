import { getUserToken } from '../auth';

async function goToGallery(event) {
    event.preventDefault();
    var eventSlug = event.target.closest('a').dataset.eventSlug;
    if (localStorage.getItem(eventSlug)) {
        var url = event.target.closest('a').dataset.galleryUrl;
        const element = document.getElementById('global-error-message');
        element.style.display = 'none';
        var token = await getUserToken();
        axios.post(url, {
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            password: localStorage.getItem(eventSlug)
        }, {
            headers: {
                'pageToken': token
            }
        })
            .then(response => {
                if (response.data.success) {
                    window.location.href = response.data.url;
                } else {
                    gotoPasswordVerification(event);
                }
            }).catch(error => {
                gotoPasswordVerification(event);
            });
    } else {
        gotoPasswordVerification(event);
    }
}

document.querySelector('.gallery-button').addEventListener('click', goToGallery);

async function gotoPasswordVerification(event) {
    var url = event.target.closest('a').dataset.url;
    var token = await getUserToken();
    axios.get(url, {
        headers: {
            'pageToken': token
        }
    })
        .then(response => {
            window.location.href = response.data.url;
        }).catch(error => {
            var element = document.getElementById('global-error-message');
            var messageElement = document.querySelector('#global-error-message strong');
            messageElement.textContent = error.response.data.message + ": ";
            element.style.display = 'block';
        });
}

async function goToShare(event) {
    event.preventDefault();
    var element = event.target.closest('a');
    var url = element.dataset.url;
    var supportImageUpload = element.dataset.supportImageUpload;
    if (supportImageUpload) {
        var token = await getUserToken();
        axios.get(url, {
            headers: {
                'pageToken': token
            }
        })
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
