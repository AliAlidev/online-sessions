<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Landing Page</title>
    @include('website.layout.header')
</head>

<body>
    @yield('partial_header')
    <!-- Container -->
    @yield('content')

    @stack('scripts')
</body>

</html>
