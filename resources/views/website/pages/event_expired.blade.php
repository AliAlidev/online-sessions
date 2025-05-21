@extends('website.layout.app')

@push('styles')
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="{{ asset('assets/website/lp-assets/css/swiper-bundle.min.css') }}" />
    <!-- CSS -->
    @if ($event->setting->theme == 'dark')
        <link rel="stylesheet" href="{{ asset('assets/website/lp-assets/css/style-dark.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('assets/website/lp-assets/css/style-light.css') }}">
    @endif

    <style>
        .event-hero-section {
            width: 50vw;
            position: relative;
            background-image: url('{{ asset($event->cover_image) }}');
            background-position: center;
            background-size: cover;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            position: relative;
            overflow: hidden;
        }

        @media screen and (max-width: 991px) {
            .event-hero-section {
                position: relative;
                background-image: url('{{ asset($event->cover_image) }}');
                background-position: center;
                background-size: cover;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: flex-end;
                height: 60dvh !important;
                width: 100vw;
            }
        }

        .gallery-button,
        .share-button {
            position: relative;
        }

        .loader {
            width: 40px;
            height: 40px;
            border: 3px solid var(--primary-03);
            border-top: 3px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }

        @keyframes spin {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }

            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        .expired-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: rgb(218, 87, 87)
        }
    </style>
@endpush

@section('partial_header')
    <!-- Menu Button and Items -->
    <div class="menu-button" onclick="toggleMenu()">
        <svg class="menu-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 28 28" width="28" height="28"
            fill="#000">
            <path d="M3 6h18v2H3zM3 12h18v2H3zM3 18h18v2H3z" />
        </svg>
    </div>
@endsection

@section('content')
    @include('website.layout.menu')
    <div class="container">
        <!-- Left Section -->
        <div class="event-hero-section">
            <!-- Event Details Mobile -->
            <div class="event-title-date event-title-date-mobile">
                <h1 class="event-title">
                    {{ isset($event->event_alias_name) ? $event->event_alias_name : $event->event_name }}</h1>
                <p class="event-date">{{ $event->start_date }}</p>
            </div>
        </div>
        <!-- Right Section -->
        <div class="event-details-section">
            <div class="event-details-container">
                <!-- Event Details Desktop -->
                <div class="event-title-date event-title-date-desktop">
                    <h1 class="event-title">
                        {{ isset($event->event_alias_name) ? $event->event_alias_name : $event->event_name }}</h1>
                    <p class="event-date">{{ $event->start_date }}</p>
                </div>
            </div><!-- End Event Details Container -->
            <!-- counter Section -->
            <div class="expired-container">
                <h3 class="">Your subscription has ended</h3>
            </div>
            <!-- Footer -->
            <div class="footer">
                <p class="footer-copywrite">Powered by EVERMOMENT</p>
            </div>
        </div><!-- End Event Details section -->
    </div><!-- End Container -->
@endsection

@push('scripts')
    <!-- Swiper JS -->
    <script src="{{ asset('assets/website/lp-assets/js/swiper-bundle.min.js') }}"></script>
    <!-- JavaScript -->
    <script src="{{ asset('assets/website/lp-assets/js/script.js') }}"></script>
@endpush
