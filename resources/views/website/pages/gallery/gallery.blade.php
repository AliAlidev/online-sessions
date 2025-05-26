@extends('website.layout.app')

@push('styles')
    <link rel="preload" as="image" href="{{ asset('assets/img/loader/loader.gif') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Parisienne&display=swap" rel="stylesheet">
    @if ($event->setting->theme == 'dark')
        <link rel="stylesheet" href="{{ asset('assets/website/gallery-assets/css/style-dark.css') }}" />
    @else
        <link rel="stylesheet" href="{{ asset('assets/website/gallery-assets/css/style-light.css') }}" />
    @endif
    <link href="https://vjs.zencdn.net/7.15.4/video-js.css" rel="stylesheet" />
    <!-- Add these to your head -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .auth-checking {
            display: none !important;
        }
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
        .swal2-container {
            z-index: 9999 !important;
        }
        .video-player {
            width: 100%;
            display: flex;
            justify-content: center;
        }
    </style>

    <style>
        .refresh-button {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            background-color: transparent;
            color: var(--primary-02);
            border: 1px solid var(--primary-02);
            border-radius: 2px;
            font-size: 14px;
            font-family: sans-serif;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
            box-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }
        .refresh-button svg {
            width: 18px;
            height: 18px;
            fill: currentColor;
            transition: transform 0.4s ease;
        }

        .refresh-button:hover svg {
            transform: rotate(180deg);
        }

        a.disabled {
            pointer-events: none;
            cursor: default;
            background-color: #ccc !important;
        }
        .reload-btn-div {
            display:flex;
            justify-content: space-between;
            padding:10px 15px;
            margin: 15px 0px;
        }
    </style>

    <style>
        #page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white;
            z-index: 9999;
            display: flex;
            flex-direction:column;
            align-items: center;
            justify-content: center;
            gap:8px;
        }
        #page-loader p{
            color:#000;
            font-size:14px;
            font-weight:normal;
        }
    </style>

    @vite(['resources/js/app.js', 'resources/js/pages/gallery.js'])
@endpush

@section('content')
    <div id="page-loader">
        <img src="{{ asset('assets/img/loader/loader.gif') }}" width="40px" alt="">
        <p>Loading....</p>
    </div>
    <div class="main-container auth-checking">
        <div class="main-header">
            <div class="top-bar" style="position:relative">
                <a href="{{ route('landing.index', ['year' => $year, 'event_slug' => $event_slug]) }}"><img
                        class="top-bar-image" src="{{ asset($event->profile_picture) }}" alt=""></a>
                <!--<img src="{{ asset('assets/img/illustrations/a2.svg') }}" alt="" style="position:absolute;z-index:9999;top:-2px;left:12px; width:127px;fill:#ccc !important">-->     
                
                @if ($event->setting->theme == 'dark')
                    <img src="{{ asset('assets/img/illustrations/gallery-profile-crown-white.svg') }}" alt="" style="position:absolute;z-index:9999;top:-12px;left:4px; width:144px;">
                @else
                    <img src="{{ asset('assets/img/illustrations/gallery-profile-crown-black.svg') }}" alt="" style="position:absolute;z-index:9999;top:-12px;left:4px; width:144px;">
                @endif
                
                                <!--<img src="{{ asset('assets/img/illustrations/a4.svg') }}" alt="" style="position:absolute;z-index:9999;top:-16px;left:-1px; width:152px;fill:#ccc !important">-->
                <div class="event-title-date">
                    <h1 class="event-title">
                        {{ isset($event->event_alias_name) ? $event->event_alias_name : $event->event_name }}</h1>
                    <p class="event-date">{{ $event->start_date }}</p>
                </div>
            </div>
            <!-- Horizontal Scroll -->
            <div class="scroll-container">
                <div class="horizontal-scroll" id="tabs">
                    @foreach ($folders as $folder)
                        @if ($folder->is_visible)
                            <div id="folder-{{ $folder->id }}" class="folder folder-thumbnail"
                                data-type= "{{ $folder->folder_type }}" data-folder-name="{{ $folder->folder_name }}"
                                data-id="{{ $folder->id }}" data-folder-link="{{ $folder->folder_link }}"
                                data-url="{{ $folder->folder_type == 'image' ? route('landing.image', ['year' => $year, 'event_slug' => $event_slug]) : route('landing.video', ['year' => $year, 'event_slug' => $event_slug]) }}">
                                <svg style="" width="100%" height="100%" viewBox="0 0 120 100" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M0 4V96C0 98.2091 1.79086 100 4 100H116C118.209 100 120 98.2091 120 96V13.9672C120 11.7581 118.209 9.96721 116 9.96721H51.6985C50.7965 9.96721 49.921 9.66234 49.2141 9.10208L38.8209 0.865137C38.1139 0.304877 37.2384 0 36.3364 0H4C1.79086 0 0 1.79086 0 4Z"
                                        fill="var(--accent-01)" />
                                </svg>
                                <div class="tab"
                                    style="background-image: url('{{ asset($folder->folder_thumbnail) }}')">
                                </div>
                                <div class="folder-name">
                                    <p class="folder-name-text">{{ $folder->folder_name }}</p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div><!-- End Horizontal Scroll -->
                
            </div><!-- End Scroll Container -->
        </div><!-- End Main Header -->
        <div class="reload-btn-div">
            <a data-id= "{{ 'folder-' . $folder->id }}" class="refresh-button">
                <svg class="reload-icon" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.66 15.66A8 8 0 1 1 17 10h-2a6 6 0 1 0-1.76 4.24l1.42 1.42zM12 10h8l-4 4-4-4z"
                        fill="currentColor" />
                </svg>
                Reload
            </a>
            <div class="scroll-buttons">
                <button id="scroll-left" class="scroll-arrow" data-distance="-200">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 18L9 12L15 6" stroke="var(--primary-02)" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </button>
                <button id="scroll-right" class="scroll-arrow" data-distance="200">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 18L15 12L9 6" stroke="var(--primary-02)" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>
        <div id="loader-div" hidden>
            <div class="loader-container">
                <img src="{{ asset('assets/img/loader/loader.gif') }}" width="40px" alt="">
            </div>
        </div>
        <div id="gallery-div"></div>
        @yield('gallery_content')
        <!-- Footer -->
        <div class="footer">
            <p class="footer-copywrite">Powered by EVERMOMENT</p>
            <!-- Share Button -->
            <a href="" style="cursor: {{ $event->supportImageUpload() ? 'pointer' : 'not-allowed' }}"
                data-url="{{ $event->supportImageUpload() ? route('landing.share_redirect_url', ['year' => $year, 'event_slug' => $event_slug]) : '' }}"
                data-support-image-upload="{{ $event->supportImageUpload() }}" class="share-btn-div">
                <div class="share-button" style="{{ !$event->supportImageUpload() ? 'background-color:#ccc' : '' }}">
                    <img class="share-button-image"
                        src="{{ asset('assets/website/gallery-assets/images/upload-icon.svg') }}" alt=""
                        width="26px" height="26px">
                </div>
            </a>
            <!-- Scroll to Top -->
            <button id="scrollTopBtn" title="Go to top"><svg width="30px" height="30px" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><line class="scroll-to-top-cls-1" x1="16" x2="25" y1="11.5" y2="20.5"/><line class="scroll-to-top-cls-1" x1="7" x2="16" y1="20.5" y2="11.5"/></svg></button>
        </div>
    </div>
    {{-- <input type="hidden" id="delete-button-svg" data-svg="{{ asset('assets/website/gallery-assets/images/delete-icon.svg') }}"> --}}
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        const scrollTopBtn = document.getElementById("scrollTopBtn");
        window.onscroll = function() {
            scrollTopBtn.style.display = window.scrollY > 1000 ? "block" : "none";
        };
        scrollTopBtn.onclick = function() {
            window.scrollTo({ top: 0, behavior: "smooth" });
        };
    </script>
    <script>
    document.querySelectorAll('.folder-thumbnail').forEach(folder => {
        folder.addEventListener('click', function () {
            // Remove 'clicked' from all other folders
            document.querySelectorAll('.folder-thumbnail').forEach(f => f.classList.remove('clicked'));
            
            // Add 'clicked' to the current one
            this.classList.add('clicked');
        });
    });
    </script>
@endpush
