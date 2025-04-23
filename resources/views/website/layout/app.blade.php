<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($event->event_alias_name) ? $event->event_alias_name : $event->event_name }}</title>
    @include('website.layout.header')
    <style>
        .alert-danger-invalid-token {
            color: #842029;
            background-color: #f8d7da;
            border: 1px solid #f5c2c7;
            padding: 1rem 1.25rem;
            font-weight: 500;
            border-radius: 0;

            /* Position at top of page */
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1050;
            /* Ensure it's on top of other elements */

            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 60px;
        }

        .alert-danger-invalid-token .btn-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            line-height: 1;
            color: #842029;
            cursor: pointer;
            padding: 0;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <input type="hidden" id="global-event-data" value="{{ request()->route('event_slug') }}"
        data-event-gallery-url="{{ route('landing.apply_event_password', ['year' => $year, 'event_slug' => $event_slug]) }}"
        data-url="{{ route('landing.gallery_redirect_url', ['year' => $year, 'event_slug' => $event_slug]) }}">
</head>

<body>
    <input type="hidden" id="auth-token-url" value="{{ route('get_auth_token') }}">
    <input type="hidden" id="main-page-url"
        data-url="{{ route('landing.index', [request()->route('year'), request()->route('event_slug')]) }}">
    @yield('partial_header')

    <!-- Container -->
    @yield('content')

    <script src="{{ asset('sha256.min.js') }}"></script>
    @stack('scripts')
</body>

</html>
