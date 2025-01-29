<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <h1>Welcome to the Landing Page</h1>
    <input type="hidden" id="auth-token-url" value="{{ route('get_auth_token') }}">
    <button id="call-api-button">Call API</button>
    <script src="{{ asset('sha256.min.js') }}"></script>

</body>

</html>
