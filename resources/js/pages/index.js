import { getUserToken } from '../auth';

async function goToGallery(event) {
    event.preventDefault();
    var button = event.target.closest('a');
    showButtonLoader(button);

    var eventSlug = event.target.closest('a').dataset.eventSlug;
    if (localStorage.getItem(eventSlug)) {
        var url = event.target.closest('a').dataset.galleryUrl;
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

function showButtonLoader(button, type = 'gallery') {
    button.querySelector('svg').style.display = 'none';
    button.querySelector('.' + type).style.display = 'none';
    button.querySelector('.loader').style.display = 'block';
}

function hideButtonLoader(button, type = 'gallery') {
    button.querySelector('svg').style.display = 'block';
    button.querySelector('.' + type).style.display = 'none';
    button.querySelector('.loader').style.display = 'none';
}

document.querySelector('.gallery-button').addEventListener('click', goToGallery);

async function gotoPasswordVerification(event) {
    var button = event.target.closest('a');
    var url = button.dataset.url;
    var token = await getUserToken();
    axios.get(url, {
        headers: {
            'pageToken': token
        }
    })
        .then(response => {
            window.location.href = response.data.url;
        }).catch(error => {
            hideGalleryLoader(button);
            console.log(error);
        });
}

async function goToShare(event) {
    event.preventDefault();
    var element = event.target.closest('a');
    showButtonLoader(element, 'share');
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
                console.log(error);
            });
    }
}

document.querySelector('.share-button').addEventListener('click', goToShare);
