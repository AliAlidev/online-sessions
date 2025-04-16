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
    </style>

    @vite(['resources/js/pages/index.js'])
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
                <!-- Buttons Section -->
                <div class="buttons-section">
                    <a href=""
                        data-url="{{ route('landing.gallery_redirect_url', ['year' => $year, 'month' => $month, 'event_slug' => $event_slug]) }}"
                        data-gallery-url = "{{ route('landing.apply_event_password', ['year' => $year, 'month' => $month, 'event_slug' => $event_slug]) }}"
                        data-event-slug="{{ $event_slug }}" class="gallery-button">
                        <svg class="button-image" width="46" height="38" viewBox="0 0 46 38" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M44.0832 32.4167C44.0832 33.4333 43.6793 34.4084 42.9604 35.1272C42.2415 35.8461 41.2665 36.25 40.2498 36.25H5.74984C4.73317 36.25 3.75815 35.8461 3.03926 35.1272C2.32037 34.4084 1.9165 33.4333 1.9165 32.4167V11.3333C1.9165 10.3167 2.32037 9.34165 3.03926 8.62276C3.75815 7.90387 4.73317 7.5 5.74984 7.5H13.4165L17.2498 1.75H28.7498L32.5832 7.5H40.2498C41.2665 7.5 42.2415 7.90387 42.9604 8.62276C43.6793 9.34165 44.0832 10.3167 44.0832 11.3333V32.4167Z"
                                stroke="var(--primary-03)" stroke-width="3" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path
                                d="M22.9998 28.5833C27.234 28.5833 30.6665 25.1508 30.6665 20.9167C30.6665 16.6825 27.234 13.25 22.9998 13.25C18.7657 13.25 15.3332 16.6825 15.3332 20.9167C15.3332 25.1508 18.7657 28.5833 22.9998 28.5833Z"
                                stroke="var(--primary-03)" stroke-width="3" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                        <span class="button-text gallery">GALLERY</span>
                    </a>
                    <a href="" style="cursor: {{ $event->supportImageUpload() ? 'pointer' : 'not-allowed' }}"
                        data-url="{{ $event->supportImageUpload() ? route('landing.share_redirect_url', ['year' => $year, 'month' => $month, 'event_slug' => $event_slug]) : '' }}"
                        data-support-image-upload="{{ $event->supportImageUpload() }}" class="share-button">
                        <svg class="button-image" width="45" height="38" viewBox="0 0 45 38" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M43.125 24.75V32.4167C43.125 33.4333 42.6421 34.4084 41.7826 35.1272C40.923 35.8461 39.7572 36.25 38.5417 36.25H6.45833C5.24276 36.25 4.07697 35.8461 3.21743 35.1272C2.35789 34.4084 1.875 33.4333 1.875 32.4167V24.75M33.9583 11.3333L22.5 1.75M22.5 1.75L11.0417 11.3333M22.5 1.75V24.75"
                                stroke="#fff" stroke-width="3.1" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span class="button-text share">SHARE</span>
                    </a>
                </div>
                <!-- Planner Swiper Section -->
                <div class="planner-section">
                    <div class="testimonial mySwiper">
                        <div class="swiper-wrapper">
                            @foreach ($event->organizers as $organizer)
                                <div class="slide swiper-slide">
                                    <p class="event-planner-title">{{ $organizer->role->name }}</p>
                                    <h2 class="event-planner-name">{{ $organizer->client->planner_name }}</h2>
                                    <button class="social-media-button"
                                        onclick="window.open('{{ $organizer->client->contact_button_link }}', '_blank')">{{ $organizer->client->contact_button_text }}</button>
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-button-next nav-btn"></div>
                        <div class="swiper-button-prev nav-btn"></div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            </div><!-- End Event Details Container -->
            <!-- Footer -->
            <div class="footer">
                <p class="footer-copywrite">Powered by UP EVENTS</p>
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
