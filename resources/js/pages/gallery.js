import { getUserToken } from '../auth';

async function checkAuthentication() {
    const token = await getUserToken();
    try {
        const response = await fetch('/events/check-token', {
            method: 'GET',
            headers: {
                'pageToken': token
            }
        });
        if (!response.ok) throw new Error('Unauthorized');
        setTimeout(() => {
            document.querySelector('.main-container').classList.remove('auth-checking');
        }, 10);
    } catch (error) {
        window.location.href = document.getElementById('main-page-url')?.dataset?.url || '/';
    }
}

checkAuthentication();

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
    $('#gallery-div').hide();
    $('#loader-div').attr('hidden', false);
    var token = await getUserToken();
    axios.post(url, {
        _token: csrfToken,
        folder_id: folderId,
    }, { headers: { 'pageToken': token } })
        .then(response => {
            var result = response.data;
            $('#gallery-div').html(result.html);

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
                           <svg viewBox="300 -100 500 900" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M446.857 400C533.645 400 604 470.355 604 557.143C604 643.93 533.645 714.286 446.857 714.286C360.07 714.286 289.714 643.93 289.714 557.143C289.714 470.355 360.07 400 446.857 400ZM289.714 0C350.92 0 400.888 48.1134 403.86 108.582L404 114.286H546.857C562.637 114.286 575.429 127.078 575.429 142.857C575.429 157.51 564.399 169.586 550.189 171.236L546.857 171.429H522.6L505.621 380.917C487.152 374.762 467.394 371.429 446.857 371.429C344.29 371.429 261.143 454.576 261.143 557.143C261.143 607.054 280.832 652.367 312.867 685.738L197.331 685.714C141.516 685.714 95.0499 642.864 90.5391 587.23L56.8 171.429H32.5714C17.919 171.429 5.84265 160.399 4.19222 146.189L4 142.857C4 128.205 15.0297 116.128 29.2394 114.478L32.5714 114.286H175.429C175.429 51.1675 226.596 0 289.714 0ZM396.312 486.404L394.333 484.751C389.461 481.376 382.959 481.376 378.087 484.751L376.109 486.404L374.456 488.382C371.081 493.254 371.081 499.757 374.456 504.628L376.109 506.607L426.667 557.163L376.183 607.643L374.529 609.621C371.155 614.493 371.155 620.995 374.529 625.867L376.183 627.846L378.161 629.499C383.033 632.874 389.535 632.874 394.407 629.499L396.386 627.846L446.867 577.363L497.411 627.91L499.39 629.563C504.261 632.938 510.764 632.938 515.636 629.563L517.614 627.91L519.267 625.931C522.642 621.06 522.642 614.557 519.267 609.685L517.614 607.707L467.067 557.163L517.693 506.608L519.346 504.63C522.721 499.758 522.721 493.256 519.346 488.384L517.693 486.405L515.715 484.752C510.843 481.377 504.341 481.377 499.469 484.752L497.49 486.405L446.867 536.963L396.312 486.404ZM289.714 57.1429C259.59 57.1429 234.91 80.4536 232.728 110.021L232.571 114.286H346.857L346.7 110.021C344.519 80.4536 319.839 57.1429 289.714 57.1429Z" fill="#CCCCCC"/>
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

                    async function deleteImage(current, instance) {
                        const imgId = current.opts.$orig.data('image-id');
                        var token = await getUserToken();
                        axios.post('/delete-image/' + imgId, {
                            _token: csrfToken,
                            folder_id: folderId
                        }, { headers: { 'pageToken': token } }).then(response => {
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
                                handleGridItemLayout();
                            }
                        });
                    };

                    const observer = new IntersectionObserver(lazyLoad, {
                        root: null,
                        rootMargin: '0px',
                        threshold: 0.1
                    });

                    const imagesToLoad = document.querySelectorAll('#gallery img[data-src]');
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
            console.log(error.response.data.message);
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
        var token = await getUserToken();
        axios.get(url, { headers: { 'pageToken': token } })
            .then(response => {
                window.location.href = response.data.url;
            }).catch(error => {
                console.log(error);
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

$(document).ready(function () {
    makeRequestWhenReady();
});

function handleGridItemLayout() {
    const gridItems = document.querySelectorAll(".grid-item");

    gridItems.forEach(item => {
        const img = item.querySelector('img');

        if (!img.hasAttribute('src'))
            return;
        // Function to handle the layout
        const adjustLayout = () => {
            const rowHeight = 10; // Match the grid-auto-rows value in CSS
            const imageHeight = img.naturalHeight;
            const imageWidth = img.naturalWidth;
            const screenWidth = window.innerWidth;

            // Check if screen width is less than 600px
            if (screenWidth < 560) {
                // On small screens, portrait images span 1.5 rows (15), landscape span 1 row (10)
                if (imageHeight > imageWidth) {
                    item.style.gridRowEnd = "span 16"; // Portrait images on small screens
                } else {
                    item.style.gridRowEnd = "span 8"; // Landscape images on small screens
                }
            } else {
                // On larger screens, portrait images span 2 rows (20), landscape span 1 row (10)
                if (imageHeight > imageWidth) {
                    item.style.gridRowEnd = "span 20"; // Portrait images on large screens
                } else {
                    item.style.gridRowEnd = "span 10"; // Landscape images on large screens
                }
            }
        };

        // Set the onload handler
        img.onload = adjustLayout;

        // Check if image is already loaded
        if (img.complete) {
            adjustLayout();
        }
    });
}

