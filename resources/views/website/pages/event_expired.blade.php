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
                <p class="event-date">{{ \Carbon\Carbon::parse($event->start_date)->format('Y-m-d') }}</p>
            </div>
        </div>
        <!-- Right Section -->
        <div class="event-details-section">
            <div class="event-details-container">
                <!-- Event Details Desktop -->
                <div class="event-title-date event-title-date-desktop">
                    <h1 class="event-title">
                        {{ isset($event->event_alias_name) ? $event->event_alias_name : $event->event_name }}</h1>
                    <p class="event-date">{{ \Carbon\Carbon::parse($event->start_date)->format('Y-m-d') }}</p>
                </div>
            </div><!-- End Event Details Container -->
            <!-- counter Section -->
            <div class="expired-container">
                <svg class="artwork" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1120 700" width="300"><circle cx="292.61" cy="213" r="213" fill="#f2f2f2"/><path fill="#2f2e41" d="M0 51.14c0 77.5 48.62 140.21 108.7 140.21"/><path fill="#03c39d" d="M108.7 191.35c0-78.37 54.26-141.78 121.3-141.78M39.38 58.17c0 73.61 31 133.18 69.32 133.18"/><path fill="#2f2e41" d="M108.7 191.35c0-100.14 62.71-181.17 140.2-181.17"/><path fill="#a8a8a8" d="M85.83 192.34s15.42-.48 20.06-3.78 23.72-7.26 24.87-1.96 23.17 26.4 5.76 26.53-40.44-2.7-45.07-5.53-5.62-15.26-5.62-15.26z"/><path d="M136.83 211.28c-17.4.15-40.44-2.7-45.07-5.53-3.53-2.15-4.94-9.87-5.41-13.43l-.52.02s.98 12.43 5.62 15.26 27.67 5.67 45.07 5.53c5.03-.04 6.76-1.83 6.67-4.47-.7 1.6-2.62 2.6-6.36 2.62z" opacity=".2"/><ellipse cx="198.61" cy="424.5" fill="#3f3d56" rx="187" ry="25.44"/><ellipse cx="198.61" cy="424.5" opacity=".1" rx="157" ry="21.36"/><ellipse cx="836.61" cy="660.5" fill="#3f3d56" rx="283" ry="38.5"/><ellipse cx="310.61" cy="645.5" fill="#3f3d56" rx="170" ry="23.13"/><path fill="none" stroke="#2f2e41" stroke-miterlimit="10" stroke-width="2" d="M462.6 626c90 23 263-30 282-90M309.6 259s130-36 138 80-107 149-17 172M184.01 537.28s39.07-10.82 41.48 24.05-32.16 44.78-5.11 51.7"/><path fill="#2f2e41" d="M778.7 563.24l-7.87 50.3s-38.78 20.6-11.52 21.2 155.74 0 155.74 0 24.84 0-14.55-21.81l-7.87-52.72z"/><path d="M753.83 634.2c6.2-5.51 17-11.25 17-11.25l7.87-50.3 113.93.1 7.87 49.59c9.19 5.09 14.88 8.99 18.2 11.98 5.07-1.16 10.6-5.45-18.2-21.4l-7.87-52.71-113.93 3.03-7.87 50.3s-32.6 17.31-17 20.66z" opacity=".1"/><rect width="513.25" height="357.52" x="578.43" y="212.69" fill="#2f2e41" rx="18.05"/><path fill="#3f3d56" d="M595.7 231.78h478.71v267.84H595.7z"/><circle cx="835.06" cy="223.29" r="3.03" fill="#f2f2f2"/><path fill="#2f2e41" d="M1091.69 520.82v31.34a18.04 18.04 0 01-18.05 18.05H596.48a18.04 18.04 0 01-18.05-18.05v-31.34zM968.98 667.47v6.06H642.97v-4.85l.45-1.21 8.03-21.82h310.86l6.67 21.82zM1094.44 661.53c-.6 2.54-2.84 5.22-7.9 7.75-18.18 9.1-55.15-2.42-55.15-2.42s-28.48-4.85-28.48-17.57a22.72 22.72 0 012.5-1.49c7.64-4.04 32.98-14.02 77.92.42a18.74 18.74 0 018.54 5.6c1.82 2.13 3.25 4.84 2.57 7.71z"/><path d="M1094.44 661.53c-22.25 8.53-42.09 9.17-62.44-4.97-10.27-7.13-19.6-8.9-26.6-8.76 7.65-4.04 33-14.02 77.93.42a18.74 18.74 0 018.54 5.6c1.82 2.13 3.25 4.84 2.57 7.71z" opacity=".1"/><ellipse cx="1066.54" cy="654.13" fill="#f2f2f2" rx="7.88" ry="2.42"/><circle cx="835.06" cy="545.67" r="11.51" fill="#f2f2f2"/><path d="M968.98 667.47v6.06H642.97v-4.85l.45-1.21h325.56z" opacity=".1"/><path fill="#2f2e41" d="M108.61 159h208v242h-208z"/><path fill="#3f3d56" d="M87.61 135h250v86h-250zM87.61 237h250v86h-250zM87.61 339h250v86h-250z"/><path fill="#03c39d" d="M271.61 150h16v16h-16z" opacity=".4"/><path fill="#03c39d" d="M294.61 150h16v16h-16z" opacity=".8"/><path fill="#03c39d" d="M317.61 150h16v16h-16z"/><path fill="#03c39d" d="M271.61 251h16v16h-16z" opacity=".4"/><path fill="#03c39d" d="M294.61 251h16v16h-16z" opacity=".8"/><path fill="#03c39d" d="M317.61 251h16v16h-16z"/><path fill="#03c39d" d="M271.61 352h16v16h-16z" opacity=".4"/><path fill="#03c39d" d="M294.61 352h16v16h-16z" opacity=".8"/><path fill="#03c39d" d="M317.61 352h16v16h-16z"/><circle cx="316.61" cy="538" r="79" fill="#2f2e41"/><path fill="#2f2e41" d="M280.61 600h24v43h-24zM328.61 600h24v43h-24z"/><ellipse cx="300.61" cy="643.5" fill="#2f2e41" rx="20" ry="7.5"/><ellipse cx="348.61" cy="642.5" fill="#2f2e41" rx="20" ry="7.5"/><circle cx="318.61" cy="518" r="27" fill="#fff"/><circle cx="318.61" cy="518" r="9" fill="#3f3d56"/><path fill="#03c39d" d="M239.98 464.53c-6.38-28.57 14-57.43 45.54-64.47s62.27 10.4 68.64 38.98-14.51 39.1-46.05 46.14-61.75 7.92-68.13-20.65z"/><ellipse cx="417.22" cy="611.34" fill="#2f2e41" rx="39.5" ry="12.4" transform="rotate(-23.17 156.4 637.65)"/><ellipse cx="269.22" cy="664.34" fill="#2f2e41" rx="39.5" ry="12.4" transform="rotate(-23.17 8.4 690.65)"/><path fill="#fff" d="M362.6 561c0 7.73-19.9 23-42 23s-43-14.27-43-22 20.92-6 43-6 42-2.73 42 5z"/></svg>
                <h3 class="event-subscription-ended-text">Event subscription has ended</h3>
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
