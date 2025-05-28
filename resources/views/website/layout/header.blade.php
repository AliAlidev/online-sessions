    @stack('styles')
    <!-- Preload Fonts -->
    <link rel="preload" href="{{ asset('assets/website/fonts/roboto_regular.ttf') }}" as="font" type="font/ttf"
        crossorigin="anonymous">
    <link rel="preload" href="{{ asset('assets/website/fonts/taviraj_regular.ttf') }}" as="font" type="font/ttf"
        crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300..700;1,300..700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">    
    <style media="screen">
        :root {
            --accent-01: {{ $event->setting->accent_color != "" ? $event->setting->accent_color : '#B89148' }};
        }
    </style>
