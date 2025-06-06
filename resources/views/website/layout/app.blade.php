<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($event->event_alias_name) ? $event->event_alias_name : $event->event_name }}</title>
    @include('website.layout.header')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <input type="hidden" id="global-event-data" value="{{ request()->route('event_slug') }}"
        data-event-gallery-url="{{ route('landing.apply_event_password', ['year' => $year, 'event_slug' => $event_slug]) }}"
        data-url="{{ route('landing.gallery_redirect_url', ['year' => $year, 'event_slug' => $event_slug]) }}"
        data-event-has-p="{{ $event->event_password != null }}">
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
