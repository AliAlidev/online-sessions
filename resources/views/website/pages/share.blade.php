@extends('website.layout.app')

@push('styles')
    <link rel="preload" as="image" href="{{ asset('assets/img/loader/loader.gif') }}">
    @if ($event->setting->theme == 'dark')
        <link rel="stylesheet" href="{{ asset('assets/website/share-assets/css/style-dark.css') }}" />
    @else
        <link rel="stylesheet" href="{{ asset('assets/website/share-assets/css/style-light.css') }}" />
    @endif

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .progress,
        .progress-stacked {
            --bs-progress-bar-bg: {{ $event->setting->accent_color != '' ? $event->setting->accent_color : '#B89148' }};
        }
    </style>
    @vite(['resources/js/app.js', 'resources/js/pages/share.js'])
@endpush

@section('content')
    <input type="hidden" id="button-main-color" value="{{ $event->setting->accent_color != '' ? $event->setting->accent_color : '#B89148' }}">
    <div id="page-loader">
        <img src="{{ asset('assets/img/loader/loader.gif') }}" width="40px" alt="">
        <p>Loading....</p>
    </div>
    <div class="main-container auth-checking">
        <input type="hidden" id="compression-ratios-file-path" value="{{ asset('/compression-ratios.json') }}">
        <div class="main-header">
            <div class="top-bar" style="position:relative">
                <a href="{{ route('landing.index', ['year' => $year, 'event_slug' => $event_slug]) }}"><img
                        class="top-bar-image" src="{{ asset($event->profile_picture) }}" alt="">
                    @if ($event->setting->theme == 'dark')
                        <img src="{{ asset('assets/img/illustrations/gallery-profile-crown-white.svg') }}" alt=""
                            style="position:absolute;z-index:9999;top:-12px;left:3px; width:144px;">
                    @else
                        <img src="{{ asset('assets/img/illustrations/gallery-profile-crown-black.svg') }}" alt=""
                            style="position:absolute;z-index:9999;top:-12px;left:3px; width:144px;">
                    @endif
                </a>
                <div class="event-title-date">
                    <h1 class="event-title">
                        {{ isset($event->event_alias_name) ? $event->event_alias_name : $event->event_name }}</h1>
                    <p class="event-date">{{ $event->start_date }}</p>
                </div>
            </div>
        </div>
        <div class="main-body">
            <input type="hidden" id="share-post-btn" data-url="{{ route('landing.share-event-image') }}">
            <input type="hidden" id="event_id" data-id="{{ $event->id }}">
            <div class="main-body-upload">
                <div class="upload-container">
                    <h1 class="share-memory-with-us">Share a memory with us</h1>
                    <form method="post" enctype="multipart/form-data" id="uploadForm">
                        <input type="file" id="image-compressed" hidden>
                        <input type="text" id="file_size" hidden>
                        <div class="drop-area" id="dropArea">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Select image to upload</p>
                            <input type="file" id="image" accept="image/*">
                        </div>
                        <small class="text-body float-start image-error" style="color: #ff0000 !important" hidden></small>
                        <div class="thumbnails" id="thumbnails"></div><br>
                        </select>
                        <!-- Label for Enter User Name field -->
                        <label for="user_name" class="user_name_label">Enter your name (Optional)</label>
                        <input type="text" name="user_name" id="user_name" placeholder="Enter your name"><br>
                        <!-- Label for Enter Caption field -->
                        <label for="caption" class="special_message_label">Share a special message with your photo!
                            (Optional)</label>
                        <!-- Changed input to textarea for multi-line caption input -->
                        <textarea name="caption" id="caption" placeholder="Enter a caption" rows="4" cols="50"></textarea><br>
                        <button type="submit" class="upload-image-btn" id="submitBtn">
                            Upload
                            <span id="spinner" style="display: none; margin-left: 8px;">
                                <i class="fas fa-spinner fa-spin"></i>
                            </span>
                        </button>
                    </form>
                    <a class="back-button"
                        href="{{ route('landing.gallery', ['year' => $year, 'event_slug' => $event_slug]) }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                            viewBox="0 0 16 16" style="vertical-align: middle; margin-right: 5px;">
                            <path fill-rule="evenodd"
                                d="M15 8a.5.5 0 0 1-.5.5H3.707l4.147 4.146a.5.5 0 0 1-.708.708l-5-5a.5.5 0 0 1 0-.708l5-5a.5.5 0 1 1 .708.708L3.707 7.5H14.5A.5.5 0 0 1 15 8z" />
                        </svg>
                        Go to Gallery
                    </a>

                </div><!-- End upload-container -->
            </div><!-- End main-body-upload -->
        </div><!-- End Main Body -->

        <div id="overlay" class="overlay hidden">
            <div class="modal" id="uploadProgressModal">
                <span id="closeXBtn" class="close-x"></span>
                <h5 class="modal-title" id="uploadProgressModalLabel">Uploading...</h5>
                <div id="progressContainer"></div>
                <button type="button" class="close-btn" id="closeModalBtn">Close</button>
            </div>
        </div>

    </div><!-- End Main Container -->
    <!-- Footer -->
    <div class="footer share-page-footer">
        <p class="footer-copywrite">Powered by EVERMOMENT</p>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/js/compressor.min.js') }}"></script>
@endpush
