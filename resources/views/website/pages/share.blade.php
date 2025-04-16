@extends('website.layout.app')

@push('styles')
    @if ($event->setting->theme == 'dark')
        <link rel="stylesheet" href="{{ asset('assets/website/share-assets/css/style-dark.css') }}" />
    @else
        <link rel="stylesheet" href="{{ asset('assets/website/share-assets/css/style-light.css') }}" />
    @endif

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .auth-checking {
            display: none !important;
        }

        .hidden-force {
            display: none !important;
        }

        .show-force {
            display: block !important;
        }

        .upload-image-btn {
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            width: 100%;
            box-sizing: border-box;
            background-color: var(--accent-01);
        }

        #spinner {
            display: none;
            margin-left: 10px;
        }

        .fa-spinner {
            font-size: 16px;
            animation: spin 1s infinite linear;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes progress-bar-stripes {
            0% {
                background-position-x: .375rem
            }
        }

        .progress,
        .progress-stacked {
            --bs-progress-height: 0.375rem;
            --bs-progress-font-size: 0.8125rem;
            --bs-progress-bg: rgba(34, 48, 62, 0.08);
            --bs-progress-border-radius: 50rem;
            --bs-progress-box-shadow: var(--bs-box-shadow-inset);
            --bs-progress-bar-color: var(--bs-white);
            --bs-progress-bar-bg: {{ $event->setting->accent_color != '' ? $event->setting->accent_color : '#B89148' }};
            --bs-progress-bar-transition: width 0.6s ease;
            display: flex;
            overflow: hidden;
            background-color: var(--bs-progress-bg);
            border-radius: var(--bs-progress-border-radius);
            font-size: 12px;
            height: 10px
        }

        .progress-bar {
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
            color: var(--bs-progress-bar-color);
            text-align: center;
            white-space: nowrap;
            background-color: var(--bs-progress-bar-bg);
            transition: var(--bs-progress-bar-transition);
            color: white;
        }

        .retry-btn {
            --bs-blue: #007bff;
            --bs-indigo: #6610f2;
            --bs-purple: #696cff;
            --bs-pink: #e83e8c;
            --bs-red: #ff3e1d;
            --bs-orange: #fd7e14;
            --bs-yellow: #ffab00;
            --bs-green: #71dd37;
            --bs-teal: #20c997;
            --bs-cyan: #03c3ec;
            --bs-black: #22303e;
            --bs-white: #fff;
            --bs-gray-dark: #4e5965;
            --bs-gray-25: #fbfbfb;
            --bs-gray-60: #f2f3f3;
            --bs-gray-80: #edeef0;
            --bs-gray-100: #e9eaec;
            --bs-gray-200: #e4e6e8;
            --bs-gray-300: #bdc1c5;
            --bs-gray-400: #a7acb2;
            --bs-gray-500: #91979f;
            --bs-gray-600: #7a838b;
            --bs-gray-700: #646e78;
            --bs-gray-800: #4e5965;
            --bs-gray-900: #384551;
            --bs-primary: #696cff;
            --bs-secondary: #8592a3;
            --bs-success: #71dd37;
            --bs-info: #03c3ec;
            --bs-warning: #ffab00;
            --bs-danger: #ff3e1d;
            --bs-light: #dbdee0;
            --bs-dark: #2b2c40;
            --bs-gray: #91979f;
            --bs-primary-rgb: 105, 108, 255;
            --bs-secondary-rgb: 133, 146, 163;
            --bs-success-rgb: 113, 221, 55;
            --bs-info-rgb: 3, 195, 236;
            --bs-warning-rgb: 255, 171, 0;
            --bs-danger-rgb: 255, 62, 29;
            --bs-light-rgb: 219, 222, 224;
            --bs-dark-rgb: 43, 44, 64;
            --bs-gray-rgb: 145, 151, 159;
            --bs-primary-text-emphasis: #2a2b66;
            --bs-secondary-text-emphasis: #353a41;
            --bs-success-text-emphasis: #2d5816;
            --bs-info-text-emphasis: #014e5e;
            --bs-warning-text-emphasis: #664400;
            --bs-danger-text-emphasis: #66190c;
            --bs-light-text-emphasis: #646e78;
            --bs-dark-text-emphasis: #646e78;
            --bs-primary-bg-subtle: #e7e7ff;
            --bs-secondary-bg-subtle: #ebeef0;
            --bs-success-bg-subtle: #e8fadf;
            --bs-info-bg-subtle: #d7f5fc;
            --bs-warning-bg-subtle: #fff2d6;
            --bs-danger-bg-subtle: #ffe0db;
            --bs-light-bg-subtle: #f4f5f6;
            --bs-dark-bg-subtle: #dddde0;
            --bs-primary-border-subtle: #d2d3ff;
            --bs-secondary-border-subtle: #dadee3;
            --bs-success-border-subtle: #d4f5c3;
            --bs-info-border-subtle: #b3edf9;
            --bs-warning-border-subtle: #ffe6b3;
            --bs-danger-border-subtle: #ffc5bb;
            --bs-light-border-subtle: #e4e6e8;
            --bs-dark-border-subtle: #bfc0c6;
            --bs-white-rgb: 255, 255, 255;
            --bs-black-rgb: 34, 48, 62;
            --bs-font-sans-serif: "Public Sans", -apple-system, blinkmacsystemfont, "Segoe UI", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
            --bs-font-monospace: "SFMono-Regular", menlo, monaco, consolas, "Liberation Mono", "Courier New", monospace;
            --bs-gradient: linear-gradient(180deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0));
            --bs-root-font-size: 16px;
            --bs-body-font-family: var(--bs-font-sans-serif);
            --bs-body-font-size: 0.9375rem;
            --bs-body-font-weight: 400;
            --bs-body-line-height: 1.375;
            --bs-body-color: #646e78;
            --bs-body-color-rgb: 100, 110, 120;
            --bs-body-bg: #f5f5f9;
            --bs-body-bg-rgb: 245, 245, 249;
            --bs-emphasis-color: #22303e;
            --bs-emphasis-color-rgb: 34, 48, 62;
            --bs-secondary-color: #a7acb2;
            --bs-secondary-color-rgb: 167, 172, 178;
            --bs-secondary-bg: #e4e6e8;
            --bs-secondary-bg-rgb: 228, 230, 232;
            --bs-tertiary-color: rgba(100, 110, 120, 0.5);
            --bs-tertiary-color-rgb: 100, 110, 120;
            --bs-tertiary-bg: #e9eaec;
            --bs-tertiary-bg-rgb: 233, 234, 236;
            --bs-heading-color: #384551;
            --bs-link-color: #696cff;
            --bs-link-color-rgb: 105, 108, 255;
            --bs-link-decoration: none;
            --bs-link-hover-color: #5f61e6;
            --bs-link-hover-color-rgb: 95, 97, 230;
            --bs-code-color: #e83e8c;
            --bs-highlight-color: #646e78;
            --bs-highlight-bg: #ffeecc;
            --bs-border-width: 1px;
            --bs-border-style: solid;
            --bs-border-color: #e4e6e8;
            --bs-border-color-translucent: rgba(34, 48, 62, 0.175);
            --bs-border-radius: 0.375rem;
            --bs-border-radius-sm: 0.25rem;
            --bs-border-radius-lg: 0.5rem;
            --bs-border-radius-xl: 0.625rem;
            --bs-border-radius-xxl: 1rem;
            --bs-border-radius-2xl: var(--bs-border-radius-xxl);
            --bs-border-radius-pill: 50rem;
            --bs-box-shadow-inset: inset 0 1px 2px rgba(34, 48, 62, 0.075);
            --bs-focus-ring-width: 0.15rem;
            --bs-focus-ring-opacity: 0.75;
            --bs-focus-ring-color: rgba(100, 110, 120, 0.75);
            --bs-form-valid-color: #71dd37;
            --bs-form-valid-border-color: #71dd37;
            --bs-form-invalid-color: #ff3e1d;
            --bs-form-invalid-border-color: #ff3e1d;
            --bs-breakpoint-xs: 0;
            --bs-breakpoint-sm: 576px;
            --bs-breakpoint-md: 768px;
            --bs-breakpoint-lg: 992px;
            --bs-breakpoint-xl: 1200px;
            --bs-breakpoint-xxl: 1400px;
            --bs-facebook: #3b5998;
            --bs-facebook-rgb: 59, 89, 152;
            --bs-twitter: #1da1f2;
            --bs-twitter-rgb: 29, 161, 242;
            --bs-google-plus: #dd4b39;
            --bs-google-plus-rgb: 221, 75, 57;
            --bs-instagram: #e1306c;
            --bs-instagram-rgb: 225, 48, 108;
            --bs-linkedin: #0077b5;
            --bs-linkedin-rgb: 0, 119, 181;
            --bs-github: #384551;
            --bs-github-rgb: 56, 69, 81;
            --bs-dribbble: #ea4c89;
            --bs-dribbble-rgb: 234, 76, 137;
            --bs-pinterest: #cb2027;
            --bs-pinterest-rgb: 203, 32, 39;
            --bs-slack: #4a154b;
            --bs-slack-rgb: 74, 21, 75;
            --bs-reddit: #ff4500;
            --bs-reddit-rgb: 255, 69, 0;
            --bs-youtube: #f00;
            --bs-youtube-rgb: 255, 0, 0;
            --bs-vimeo: #1ab7ea;
            --bs-vimeo-rgb: 26, 183, 234;
            --prefix: bs-;
            --bs-pure-black: #000;
            --bs-icon-size: 1.25rem;
            --bs-icon-size-xs: 1rem;
            --bs-icon-size-sm: 1.125rem;
            --bs-icon-size-md: 1.375rem;
            --bs-icon-size-lg: 1.5rem;
            --bs-icon-size-xl: 2rem;
            --bs-primary-contrast: var(--bs-white);
            --bs-secondary-contrast: var(--bs-white);
            --bs-success-contrast: var(--bs-white);
            --bs-info-contrast: var(--bs-white);
            --bs-warning-contrast: var(--bs-white);
            --bs-danger-contrast: var(--bs-white);
            --bs-light-contrast: var(--bs-pure-black);
            --bs-dark-contrast: var(--bs-white);
            --bs-gray-contrast: var(--bs-white);
            --bs-facebook-contrast: var(--bs-white);
            --bs-twitter-contrast: var(--bs-white);
            --bs-google-plus-contrast: var(--bs-white);
            --bs-instagram-contrast: var(--bs-white);
            --bs-linkedin-contrast: var(--bs-white);
            --bs-github-contrast: var(--bs-white);
            --bs-dribbble-contrast: var(--bs-white);
            --bs-pinterest-contrast: var(--bs-white);
            --bs-slack-contrast: var(--bs-white);
            --bs-reddit-contrast: var(--bs-white);
            --bs-youtube-contrast: var(--bs-white);
            --bs-vimeo-contrast: var(--bs-white);
            --bs-bg-label-tint-amount: 84%;
            --bs-border-subtle-amount: 70%;
            --bs-base-color: #22303e;
            --bs-base-color-rgb: 34, 48, 62;
            --bs-paper-bg: #fff;
            --bs-paper-bg-rgb: 255, 255, 255;
            --bs-min-contrast-ratio: 1.7;
            --bs-box-shadow: 0 0.1875rem 0.5rem 0 rgba(34, 48, 62, 0.1);
            --bs-box-shadow-xs: 0 0.0625rem 0.3175rem 0 rgba(34, 48, 62, 0.06);
            --bs-box-shadow-sm: 0 0.125rem 0.375rem 0 rgba(34, 48, 62, 0.08);
            --bs-box-shadow-lg: 0 0.25rem 0.75rem 0 rgba(34, 48, 62, 0.14);
            --bs-box-shadow-xl: 0 0.3125rem 1.375rem 0 rgba(34, 48, 62, 0.18);
            --bs-floating-component-shadow: 0 0.1875rem 0.5rem 0 rgba(34, 48, 62, 0.1);
            --bs-custom-link-color: var(--bs-primary);
            --bs-navbar-bg: var(--bs-paper-bg);
            --bs-navbar-box-shadow: 0 0 10px #e4e6e8;
            --bs-navbar-border-width: 1px;
            --bs-navbar-border-color: var(--bs-paper-bg);
            --bs-menu-header-color: var(--bs-heading-color);
            --bs-nav-box-shadow: var(--bs-box-shadow);
            --bs-nav-border-color: var(--bs-paper-bg);
            --bs-menu-bg: var(--bs-paper-bg);
            --bs-menu-bg-rgb: var(--bs-paper-bg-rgb);
            --bs-menu-color: #384551;
            --bs-menu-color-rgb: 56, 69, 81;
            --bs-menu-hover-bg: #f2f3f3;
            --bs-menu-hover-color: #384551;
            --bs-menu-sub-active-bg: var(--bs-primary-bg-subtle);
            --bs-menu-sub-active-color: var(--bs-primary);
            --bs-menu-active-color: var(--bs-primary);
            --bs-menu-active-bg: var(--bs-primary-bg-subtle);
            --bs-menu-active-toggle-bg: #edeef0;
            --bs-menu-horizontal-active-bg: var(--bs-menu-bg);
            --bs-menu-box-shadow: 0 0.125rem 0.375rem 0 rgba(34, 48, 62, 0.08);
            --bs-menu-divider-color: var(--bs-white);
            --bs-menu-width: 16.25rem;
            --bs-menu-collapsed-width: 5.25rem;
            --bs-menu-item-spacer: 0.125rem;
            --bs-menu-vertical-link-padding-y: 0.3125rem;
            --bs-menu-vertical-link-padding-x: 0.9375rem;
            --bs-menu-vertical-menu-link-padding-y: 0.3125rem;
            --bs-menu-vertical-menu-level-spacer: 0.65rem;
            --bs-menu-horizontal-menu-box-shadow: var(--bs-box-shadow-lg);
            --bs-select-height: 2.375rem;
            --bs-select-color: #333;
            --bs-select-border-width: 1px;
            --bs-select-border-color: #ccc;
            --bs-select-active-border-color: #007bff;
            --bs-select-disabled-color: #999;
            --bs-select-arrow: url('data:image/svg+xml,...');
            --bs-select-dropdown-bg: #fff;
            --bs-select-dropdown-box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --fa-style-family-brands: "Font Awesome 6 Brands";
            --fa-font-brands: normal 400 1em/1 "Font Awesome 6 Brands";
            --fa-font-regular: normal 400 1em/1 "Font Awesome 6 Free";
            --fa-style-family-classic: "Font Awesome 6 Free";
            --fa-font-solid: normal 900 1em/1 "Font Awesome 6 Free";
            --swal2-container-padding: 0.625em;
            --swal2-backdrop: rgba(0, 0, 0, 0.4);
            --swal2-width: 32em;
            --swal2-padding: 0 0 1.25em;
            --swal2-border: none;
            --swal2-border-radius: 0.3125rem;
            --swal2-background: white;
            --swal2-color: #545454;
            --swal2-footer-border-color: #eee;
            --swal2-show-animation: swal2-show 0.3s;
            --swal2-hide-animation: swal2-hide 0.15s forwards;
            --swal2-input-background: transparent;
            --swal2-progress-step-background: #add8e6;
            --swal2-validation-message-background: #f0f0f0;
            --swal2-validation-message-color: #666;
            --swal2-close-button-position: initial;
            --swal2-close-button-inset: auto;
            --swal2-close-button-font-size: 2.5em;
            --swal2-close-button-color: #ccc;
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: rgba(34, 48, 62, 0);
            --bs-modal-zindex: 1090;
            --bs-modal-width: 35rem;
            --bs-modal-padding: 1.5rem;
            --bs-modal-color: ;
            --bs-modal-bg: var(--bs-paper-bg);
            --bs-modal-border-color: var(--bs-border-color);
            --bs-modal-border-width: 0;
            --bs-modal-border-radius: var(--bs-border-radius-lg);
            --bs-modal-inner-border-radius: calc(var(--bs-border-radius-lg) - 0);
            --bs-modal-header-padding-x: 0;
            --bs-modal-header-padding-y: 1.5rem;
            --bs-modal-header-padding: 1.5rem 1.5rem 0;
            --bs-modal-header-border-color: var(--bs-border-color);
            --bs-modal-header-border-width: 0;
            --bs-modal-title-line-height: 1.375;
            --bs-modal-footer-gap: 1rem;
            --bs-modal-footer-bg: ;
            --bs-modal-footer-border-color: var(--bs-border-color);
            --bs-modal-footer-border-width: 0;
            --bs-modal-margin: 1.75rem;
            --bs-modal-box-shadow: var(--bs-box-shadow-lg);
            pointer-events: auto;
            --bs-form-validation-shadow: none;
            --bs-form-validation-border-color: var(--bs-form-invalid-border-color);
            --bs-gutter-x: 1.625rem;
            --bs-gutter-y: 0;
            box-sizing: border-box;
            margin: 0;
            text-transform: none;
            -webkit-appearance: button;
            --bs-btn-font-family: ;
            --bs-btn-font-weight: 500;
            --bs-btn-line-height: 1.375;
            --bs-btn-border-width: var(--bs-border-width);
            --bs-btn-disabled-opacity: 0.45;
            padding: var(--bs-btn-padding-y) var(--bs-btn-padding-x);
            font-family: var(--bs-btn-font-family);
            font-weight: var(--bs-btn-font-weight);
            line-height: var(--bs-btn-line-height);
            color: var(--bs-btn-color);
            text-align: center;
            vertical-align: middle;
            user-select: none;
            border: var(--bs-btn-border-width) solid var(--bs-btn-border-color);
            border-radius: var(--bs-btn-border-radius);
            background-color: var(--bs-btn-bg);
            transition: all .2s ease-in-out;
            --bs-btn-padding-y: 0.317rem;
            --bs-btn-padding-x: 0.75rem;
            --bs-btn-font-size: 0.8125rem;
            --bs-btn-border-radius: var(--bs-border-radius-sm);
            --bs-btn-focus-shadow-rgb: transparent;
            --bs-btn-active-shadow-rgb: transparent;
            --bs-btn-box-shadow: 0 .125rem .25rem 0 rgba(var(--bs-btn-box-shadow-rgb), .4);
            --bs-btn-focus-box-shadow: none;
            --bs-btn-active-shadow: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            --bs-btn-bg: var(--bs-warning);
            --bs-btn-color: var(--bs-warning-contrast);
            --bs-btn-border-color: var(--bs-warning);
            --bs-btn-hover-color: var(--bs-warning-contrast);
            --bs-btn-hover-bg: color-mix(in sRGB, #000 10%, var(--bs-warning));
            --bs-btn-hover-border-color: var(--bs-btn-hover-bg);
            --bs-btn-active-color: var(--bs-warning-contrast);
            --bs-btn-active-bg: color-mix(in sRGB, #000 10%, var(--bs-warning));
            --bs-btn-active-border-color: var(--bs-btn-active-bg);
            --bs-btn-box-shadow-rgb: var(--bs-warning-rgb);
            --bs-btn-disabled-color: var(--bs-warning-contrast);
            --bs-btn-disabled-bg: var(--bs-warning);
            --bs-btn-disabled-border-color: var(--bs-warning);
            --bs-btn-group-border-color: color-mix(in sRGB, #000 10%, var(--bs-warning));
            cursor: pointer;
            box-shadow: var(--bs-btn-box-shadow);
            height: 15px;
            width: auto;
            font-size: 10px;
        }

        .alert-success {
            color: #0f5132;
            /* Text color */
            background-color: #d1e7dd;
            /* Background color */
            border: 1px solid #badbcc;
            /* Border */
            padding: 1rem 1.25rem;
            border-radius: 0.375rem;
            position: relative;
            margin-bottom: 1rem;
        }

        .alert-success a {
            color: #0f5132;
            /* Link color */
            text-decoration: underline;
        }

        .alert-success a:hover {
            color: #0c3f27;
            /* Link hover */
        }

        .alert-success hr {
            border-top: 1px solid #badbcc;
        }

        .alert-success .btn-close {
            color: #0f5132;
            filter: none;
            opacity: 1;
            position: absolute;
            top: 0.75rem;
            right: 1rem;
        }
    </style>

    @vite(['resources/js/app.js', 'resources/js/pages/share.js'])
@endpush

@section('content')
    <div class="main-container auth-checking">
        <div class="alert-danger-invalid-token" id="global-error-message" style="display: none">
            <span>
                <strong>Invalid Token:</strong> Please contact info@onlineEventShare.com
            </span>
        </div>
        <div class="main-header">
            <div class="top-bar">
                <a href="{{ route('landing.index', ['year' => $year, 'month' => $month, 'event_slug' => $event_slug]) }}"><img
                        class="top-bar-iamge" src="{{ asset($event->profile_picture) }}" alt=""></a>
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
                        <div class="row">
                            <div class="col-md-12">
                                <div id="progressContainer" style="margin-top: 20px"></div>
                            </div>
                        </div>
                    </form>
                    <a class="back-button"
                        href="{{ route('landing.gallery', ['year' => $year, 'month' => $month, 'event_slug' => $event_slug]) }}">
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

    </div><!-- End Main Container -->
    <!-- Footer -->
    <div class="footer">
        <p class="footer-copywrite">Powered by UP EVENTS</p>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/js/compressor.min.js') }}"></script>
@endpush
