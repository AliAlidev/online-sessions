@extends('website.layout.app')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Parisienne&display=swap" rel="stylesheet">
    @if ($event->setting->theme == 'dark')
        <link rel="stylesheet" href="{{ asset('assets/website/gallery-assets/css/style-dark.css') }}" />
    @else
        <link rel="stylesheet" href="{{ asset('assets/website/gallery-assets/css/style-light.css') }}" />
    @endif
    <script src="{{ asset('assets/website/gallery-assets/js/grid-script.js') }}"></script>
    <link href="https://vjs.zencdn.net/7.15.4/video-js.css" rel="stylesheet" />
    <style>
        #loader-div {
            /* display: flex; */
            align-items: center;
            justify-content: center;
            padding-top: 13%;
        }

        /* Style for the loader container */
        .loader-container {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: #555;
        }

        /* Spinner style */
        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid {{ $event->setting->accent_color != '' ? $event->setting->accent_color : '#B89148' }};
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 2s linear infinite;
            margin-right: 10px;
            /* Space between spinner and text */
        }

        /* Animation for the spinner */
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Text style for "Loading..." */
        .loading-text {
            font-size: 16px;
            color: #555;
            line-height: 50px;
            /* Vertically center the text next to the spinner */
        }
    </style>
@endpush

@section('content')
    <div class="main-container">
        <div class="main-header">
            <div class="top-bar">
                <a href="{{ route('landing.index', ['year' => $year, 'month' => $month, 'customer' => $customer]) }}"><img
                        class="top-bar-iamge" src="{{ asset($event->cover_image) }}" alt=""></a>
                <div class="event-title-date">
                    <h1 class="event-title">{{ $event->event_name }}</h1>
                    <p class="event-date">{{ $event->start_date }}</p>
                </div>
            </div>
            <!-- Horizontal Scroll -->
            <div class="scroll-container">
                <button id="scroll-left" class="scroll-arrow" onclick="scrollTabs(-200)">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 18L9 12L15 6" stroke="var(--accent-01)" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </button>
                <div class="horizontal-scroll" id="tabs">
                    @foreach ($folders as $folder)
                        <div class="folder folder-thumbnail" data-type= "{{ $folder->folder_type }}"
                            onclick="selectFolder({{ $folder }})"
                            data-url="{{ $folder->folder_type == 'image' ? route('landing.image', ['year' => $year, 'month' => $month, 'customer' => $customer]) : route('landing.video', ['year' => $year, 'month' => $month, 'customer' => $customer]) }}">
                            <svg style="" width="100%" height="100%" viewBox="0 0 120 100" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M0 4V96C0 98.2091 1.79086 100 4 100H116C118.209 100 120 98.2091 120 96V13.9672C120 11.7581 118.209 9.96721 116 9.96721H51.6985C50.7965 9.96721 49.921 9.66234 49.2141 9.10208L38.8209 0.865137C38.1139 0.304877 37.2384 0 36.3364 0H4C1.79086 0 0 1.79086 0 4Z"
                                    fill="var(--accent-01)" />
                            </svg>
                            <div class="tab" style="background-image: url('{{ asset($folder->folder_thumbnail) }}')">
                            </div>
                            <div class="folder-name">
                                <p class="folder-name-text">{{ $folder->folder_name }}</p>
                            </div>
                        </div>
                    @endforeach
                </div><!-- End Horizontal Scroll -->
                <button id="scroll-right" class="scroll-arrow" onclick="scrollTabs(200)">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 18L15 12L9 6" stroke="var(--accent-01)" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </button>
            </div><!-- End Scroll Container -->
        </div><!-- End Main Header -->

        <div id="loader-div" hidden>
            <div class="loader-container">
                <div class="loader"></div>
                <div class="loading-text">Loading...</div>
            </div>
        </div>
        <div id="gallery-div"></div>
        @yield('gallery_content')
        <!-- Footer -->
        <div class="footer">
            <p class="footer-copywrite">Powered by UP EVENTS</p>
            <!-- Share Button -->
            <a href="{{ $event->supportImageUpload() ? route('landing.share', ['year' => $year, 'month' => $month, 'customer' => $customer]) : '#' }}"
                style="cursor: {{ $event->supportImageUpload() ? 'pointer' : 'not-allowed' }}">
                <div class="share-button">
                    <img class="share-button-image"
                        src="{{ asset('assets/website/gallery-assets/images/upload-icon.svg') }}" alt=""
                        width="26px" height="26px">
                </div>
            </a>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="{{ asset('assets/website/gallery-assets/js/grid-script.js') }}"></script>
    <script>
        function scrollTabs(distance) {
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

        function loadVideo(videoUrl) {
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

        function selectFolder($folder) {
            if ($folder.folder_type == 'link') {
                window.open($folder.folder_link, '_blank', 'noopener,noreferrer');
                return false;
            }

            $('#gallery-div').hide();
            $('#loader-div').attr('hidden', false);
            var url = "{{ route('landing.image', ['year' => $year, 'month' => $month, 'customer' => $customer]) }}";
            if ($folder.folder_type == 'video')
                url = "{{ route('landing.video', ['year' => $year, 'month' => $month, 'customer' => $customer]) }}";

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    year: "{{ $year }}",
                    month: "{{ $month }}",
                    customer: "{{ $customer }}",
                    folderId: $folder.id
                },
                success: function(response) {
                    $('#gallery-div').html(response);
                    if ($folder.folder_type == 'video') {
                        console.log($folder.files);

                        $.getScript("//assets.mediadelivery.net/playerjs/player-0.1.0.min.js", function() {
                            const playerIframe = document.getElementById('videoIframe');
                            // Ensure the iframe has a valid `src` attribute
                            if (!playerIframe.src || playerIframe.src === "about:blank") {
                                var fileUrl= null;
                                $folder.files.forEach(file => {
                                    if(fileUrl == null && file.file_status == "approved"){
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
                            playerIframe.onload = function() {
                                player.pause(); // Explicitly pause the video
                                player.mute(); // Mute the video using the correct method
                            };

                            player.on('error', (error) => {
                                console.error('Error occurred:', error);
                            });
                        });
                    } else if ($folder.folder_type == 'image') {
                        $.getScript(
                            "https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js",
                            function() {
                                $('[data-fancybox="gallery"]').fancybox({
                                    buttons: [
                                        "zoom",
                                        "slideShow",
                                        "fullScreen",
                                        "{{ $event->supportImageDownload() ? 'download' : '' }}",
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
                }
            })
        }
    </script>
@endpush
