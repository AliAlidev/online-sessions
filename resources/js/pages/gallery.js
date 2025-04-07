function scrollTabs(event) {
    var element = event.target.closest('.scroll-arrow');
    var distance = element.dataset.distance;

    const tabs = document.getElementById("tabs");
    tabs.scrollBy({
        left: distance,
        behavior: 'smooth'
    });
    checkScroll();
}

function checkScroll() {
    const tabs = document.getElementById("tabs");
    const scrollLeft = tabs.scrollLeft;
    const scrollWidth = tabs.scrollWidth;
    const clientWidth = tabs.clientWidth;

    document.getElementById("scroll-left").style.display = scrollLeft > 0 ? "block" :
        "none";
    document.getElementById("scroll-right").style.display = (scrollLeft +
        clientWidth) >= (scrollWidth - 50) ?
        "none" : "block";
}

function loadVideo(event) {
    var element = event.target.closest('.video-item');
    var videoUrl = element.dataset.url;
    const playerIframe = document.getElementById('videoIframe');
    const videoSrc = `${videoUrl}?autoplay=true`;
    // Update the iframe source with the selected video
    playerIframe.src = videoSrc;
    // Initialize player.js on the iframe
    const player = new playerjs.Player(playerIframe);
    // Event listeners using player.js
    player.on('ready', () => {
        player.play(); // Play the video once loaded
    });
    player.on('error', (error) => {
        console.log('Error occurred:', error);
    });
}

async function selectFolder(event) {
    event.preventDefault();
    var element = event.target.closest('.folder');
    var folderId = element.dataset.id;
    var folderType = element.dataset.type;
    var url = element.dataset.url;
    var folderLink = element.dataset.folderLink;
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    // var $folder = element.dataset.object;
    if (folderType == 'link') {
        window.open(folderLink, '_blank', 'noopener,noreferrer');
        return false;
    }
    $('#gallery-div').empty();
    $('#loader-div').attr('hidden', false);
    axios.post(url, {
        _token: csrfToken,
        folder_id: folderId,
    }, { 'pageToken': window.axios.defaults.headers.common['pageToken'] })
        .then(response => {
            var result = response.data;
            $('#gallery-div').html(result.html);
            var macy = new Macy({
                container: '#gallery-div',
                trueOrder: true,
                waitForImages: false,
                margin: 5,
                columns: 4,
                breakAt: {
                    1200: 3,
                    768: 2,
                    480: 1
                }
            });

            if (folderType == 'video') {

                $.getScript("//assets.mediadelivery.net/playerjs/player-0.1.0.min.js").then(() => {
                    const playerIframe = document.getElementById('videoIframe');
                    // Ensure the iframe has a valid `src` attribute
                    if (!playerIframe.src || playerIframe.src === "about:blank") {
                        var fileUrl = null;
                        result.files.forEach(file => {
                            if (fileUrl == null && file.file_status == "approved") {
                                fileUrl = file.file;
                            }
                        });
                        playerIframe.src = fileUrl;
                    }

                    // Ensure that the iframe has a valid `src` before initializing the player
                    const player = new playerjs.Player(playerIframe, {
                        autoplay: false,
                        mute: true,
                        loop: true,
                        volume: 0.1,
                        controls: true,
                        fullscreen: true,
                        showTitle: false,
                        startTime: 0,
                        enableVideoInfo: true
                    });

                    // Make sure to trigger play once the player is ready
                    playerIframe.onload = function () {
                        player.pause(); // Explicitly pause the video
                        player.mute(); // Mute the video using the correct method
                    };

                    player.on('error', (error) => {
                        console.error('Error occurred:', error);
                    });
                });

                document.querySelectorAll('.video-item').forEach(video => {
                    video.addEventListener('click', loadVideo);
                });
                $('#gallery-div').show();
                $('#loader-div').attr('hidden', true);
            } else if (folderType == 'image') {
                var canDownload = result.eventSupportDownload ? 'download' : '';
                $.getScript("https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js").done(function () {
                    $('[data-fancybox="gallery"]').fancybox({
                        buttons: [
                            "zoom",
                            "slideShow",
                            "fullScreen",
                            canDownload,
                            "delete",
                            "close"
                        ],
                        btnTpl: {
                            delete: `<button data-fancybox-delete class="fancybox-button fancybox-button--delete" title="Delete" hidden>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 30 30" fill="none" stroke="#ff4d4d" stroke-width="2">
                                    <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </button>`
                        },
                        afterLoad: function (instance, current) {
                            updateDeleteButtonState(instance, current);
                        },
                        afterShow: function (instance, current) {
                            updateDeleteButtonState(instance, current);
                        },
                        beforeShow: function (instance, current) {
                            const $deleteBtn = $(instance.$refs.toolbar).find('.fancybox-button--delete');
                            $deleteBtn.off('click').on('click', function () {
                                if (!$(this).is(':disabled') && confirm('Delete this image?')) {
                                    const imgId = current.opts.$orig.data('image-id');
                                    deleteImage(current, instance);
                                }
                            });
                        }
                    });

                    function deleteImage(current, instance) {
                        const imgId = current.opts.$orig.data('image-id');
                        axios.post('/delete-image/' + imgId, {
                            _token: csrfToken,
                            folder_id: folderId
                        }).then(response => {
                            instance.close();
                            if (response.data.success) {
                                Swal.fire({
                                    position: 'top',
                                    title: 'Deleted!',
                                    text: response.message || 'Image was deleted successfully',
                                    icon: 'success',
                                    showConfirmButton: false,
                                    showCloseButton: true
                                });
                                current.opts.$orig.closest('.grid-item').remove();
                            } else {
                                Swal.fire({
                                    position: 'top',
                                    title: 'Error!',
                                    text: response.data.message || 'Delete failed',
                                    icon: 'error'
                                });
                            }
                        }).catch(xhr => {
                            instance.close();
                            Swal.fire({
                                position: 'top',
                                title: 'Error!',
                                text: xhr.responseJSON?.message || 'Delete failed',
                                icon: 'error'
                            });
                        });
                    }

                    function updateDeleteButtonState(instance, current) {
                        // Get the actual clicked element (not the clone)
                        const $orig = current.opts.$orig;
                        const canDelete = $orig.data('can-delete') === true;

                        // Find the delete button in the actual toolbar (not in clones)
                        const $toolbar = $(instance.$refs.toolbar);
                        const $deleteBtn = $toolbar.find('.fancybox-button--delete');

                        // Set disabled state and visual appearance
                        $deleteBtn.toggle(canDelete);
                    }

                    const lazyLoad = (entries, observer) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                const img = entry.target;
                                img.src = img.getAttribute('data-src');
                                img.removeAttribute('data-src');
                                observer.unobserve(img);
                                macy.recalculate(true, true);
                            }
                        });
                    };

                    const observer = new IntersectionObserver(lazyLoad, {
                        root: null,
                        rootMargin: '0px',
                        threshold: 0.1
                    });

                    const imagesToLoad = document.querySelectorAll('#gallery-div img[data-src]');
                    imagesToLoad.forEach(img => observer.observe(img));

                    $('#gallery-div').show();
                    $('#loader-div').attr('hidden', true);

                }).fail(error => {
                    console.log(error);
                });
                document.getElementById("tabs").addEventListener("scroll", checkScroll);
                window.addEventListener("load", checkScroll);
            }
        })
        .catch(error => {
            $('#loader-div').attr('hidden', true);
            var element = document.getElementById('global-error-message');
            var messageElement = document.querySelector('#global-error-message strong');
            messageElement.textContent = error.response.data.message + ": ";
            element.style.display = 'block';
        });
}

document.querySelectorAll('.folder-thumbnail').forEach(folder => {
    folder.addEventListener('click', selectFolder);
});

document.getElementById('scroll-left').addEventListener('click', scrollTabs);
document.getElementById('scroll-right').addEventListener('click', scrollTabs);


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

document.querySelector('.share-btn-div').addEventListener('click', goToShare);

async function selectGuestFolder() {
}

function makeRequestWhenReady() {
    if (window.tokenInitialized) {
        var element = $('.horizontal-scroll').children('[data-folder-name="Guest Upload"]').first();
        element.click();
    } else {
        setTimeout(makeRequestWhenReady, 100);
    }
}

$(window).on('load', function () {
    makeRequestWhenReady();
});


