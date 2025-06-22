<html lang="en" class="light-style layout-wide customizer-hide" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free" data-style="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <title>{{ isset($event->event_alias_name) ? $event->event_alias_name : $event->event_name }}</title>
    <meta name="description" content="">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&amp;display=swap"
        rel="stylesheet">
    
    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}"
        class="template-customizer-theme-css">
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    @vite(['resources/js/app.js', 'resources/js/pages/event_password.js'])
    @vite(['resources/css/app.css'])
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <!-- CSS -->
    @if ($event->setting->theme == 'dark')
        <link rel="stylesheet" href="{{ asset('assets/website/gallery-assets/css/style-dark.css') }}" />
    @else
        <link rel="stylesheet" href="{{ asset('assets/website/gallery-assets/css/style-light.css') }}" />
    @endif
</head>

<body>
    <div class="container-xxl gallery-login-container">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!-- Register -->
                <div class="card px-sm-6 px-0 gallery-login-card">
                    <div class="card-body">
                        <div class="alert-danger-invalid-token" id="global-error-message" style="display: none">
                        </div>
                        <div style="text-align:center;margin-bottom:10px">
                            @if ($event->setting->theme == 'dark')
                                <img src="{{ asset('assets/img/illustrations/love-lock-white.svg') }}" width="80px;" alt="">
                            @else
                                <img src="{{ asset('assets/img/illustrations/love-lock-dark.svg') }}" width="80px;" alt="">
                            @endif 
                            
                                
                        </div>
                        <h5 class="mb-1 enter-password-text">Please enter the password to access our special moments.
                        </h5>
                        <form id="formAuthentication" class="mb-6"
                            action="{{ route('landing.apply_event_password') }}" method="POST">
                            <input type="hidden" id="event_slug" name="event_slug" value="{{ $event_slug }}">
                            <input type="hidden" id="year" name="year" value="{{ $year }}">
                            <div class="mb-6">
                                <!--<label for="password" class="form-label">Event Password</label>-->
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Enter password" autofocus="">
                                    <i class="bx bx-hide" id="togglePassword" onclick="togglePassword()"></i>     
                            </div>
                            <div class="mb-6">
                                <button class="btn btn-primary d-grid w-100 gallery-login-btn"
                                    id="formAuthenticationBtn" type="button">Enter Gallery</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /Register -->
            </div>
        </div>
    </div>
    <div class="gallery-login-logo">
        @if ($event->setting->theme == 'dark')
            <img src="{{ asset('assets/img/icons/logo-white.svg') }}" width="170px" alt="">
        @else
            <img src="{{ asset('assets/img/icons/logo-black.svg') }}" width="170px" alt="">
        @endif    
    </div>
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js"') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script async="" defer="" src="https://buttons.github.io/buttons.js"></script>
    <script src="{{ asset('sha256.min.js') }}"></script>
    <script src="{{ asset('js/pages/event_password.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('form').forEach(function(form) {
                form.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                    }
                });
            });
        });
    </script>
    <script>
    function togglePassword() {
        const passwordInput = document.getElementById("password");
        const toggleIcon = document.getElementById("togglePassword");

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            toggleIcon.classList.remove("bx-hide");
            toggleIcon.classList.add("bx-show");
        } else {
            passwordInput.type = "password";
            toggleIcon.classList.remove("bx-show");
            toggleIcon.classList.add("bx-hide");
        }
    }
    </script>
</body>

</html>
