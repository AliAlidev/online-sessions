    @stack('styles')
    <!-- Preload Fonts -->
    <link rel="preload" href="{{ asset('assets/website/fonts/roboto_regular.ttf') }}" as="font" type="font/ttf"
        crossorigin="anonymous">
    <link rel="preload" href="{{ asset('assets/website/fonts/taviraj_regular.ttf') }}" as="font" type="font/ttf"
        crossorigin="anonymous">
    <style media="screen">
        :root {
            --accent-01: {{ $event->setting->accent_color != "" ? $event->setting->accent_color : '#B89148' }};
        }
    </style>
