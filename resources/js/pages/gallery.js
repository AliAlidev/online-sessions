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
    const axios = window.axios;
    axios.post(url, {
        _token: csrfToken,
        folder_id: folderId
    })
        .then(response => {
            var result = response.data;

            $('#gallery-div').html(result.html);
            if (folderType == 'video') {

                $.getScript("//assets.mediadelivery.net/playerjs/player-0.1.0.min.js", function () {
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
            } else if (folderType == 'image') {
                var canDownload = result.eventSupportDownload ? 'download' : '';
                $.getScript(
                    "https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js",
                    function () {
                        $('[data-fancybox="gallery"]').fancybox({
                            buttons: [
                                "zoom",
                                "slideShow",
                                "fullScreen",
                                canDownload,
                                "close"
                            ]
                        });
                        // Initialize lazy loading using IntersectionObserver
                        const lazyLoad = (entries, observer) => {
                            entries.forEach(entry => {
                                if (entry.isIntersecting) {
                                    const img = entry.target;
                                    img.src = img.getAttribute(
                                        'data-src'
                                    ); // Set the src to data-src
                                    img.removeAttribute(
                                        'data-src'
                                    ); // Remove the data-src attribute
                                    observer.unobserve(
                                        img); // Stop observing this image
                                }
                            });
                        };
                        const observer = new IntersectionObserver(lazyLoad, {
                            root: null,
                            rootMargin: '0px',
                            threshold: 0.1
                        });
                        // Observe each lazy image
                        const imagesToLoad = document.querySelectorAll(
                            '.gallery img[data-src]');
                        imagesToLoad.forEach(img => observer.observe(img));
                    });
            }
            document.getElementById("tabs").addEventListener("scroll", checkScroll);
            window.addEventListener("load", checkScroll);
            $('#gallery-div').fadeIn(500);
            $('#loader-div').attr('hidden', true);
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
