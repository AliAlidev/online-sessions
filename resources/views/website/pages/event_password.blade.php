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
    <style type="text/css">
        .layout-menu-fixed .layout-navbar-full .layout-menu,
        .layout-page {
            padding-top: 0px !important;
        }

        .content-wrapper {
            padding-bottom: 0px !important;
        }

        #basic-default-password2 {
            pointer-events: auto;
            z-index: 1;
        }

        .alert-danger-invalid-token {
            color: #842029;
            background-color: #f8d7da;
            border: 1px solid #f5c2c7;
            padding: 1rem 1.25rem;
            font-weight: 500;
            border-radius: 0;
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
        .gallery-login-container{
            background-image: url('{{ asset('assets/img/illustrations/wedding-patterrn-light.svg') }}');
            background-color: rgb(231,231,231,0.4);
            background-size: 200px;
            background-repeat:repeat;
        }
        .gallery-login-card{
            background-color: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
        }
        .gallery-login-btn{
            background-color: #292929;
            box-shadow: unset !important;
        }
        .gallery-login-btn:hover{
            background-color: #292929 !important;
            box-shadow: unset !important;
        }
        .enter-password-text {
            text-align: center;
            line-height: normal;
            color: #292929;
            text-align:center;
            line-height: 22px;
            margin: 20px 0 !important;
        }
        .form-control:focus, .form-select:focus {
            border-color: unset !important;
        }
        .authentication-wrapper.authentication-basic .authentication-inner:before{
            background-image: unset;
        }
        .light-style .authentication-wrapper.authentication-basic .authentication-inner:after{
            background-image: unset;
        }
        .form-control {
             --bs-input-border-color: color-mix(in sRGB, var(--bs-base-color) 52%, var(--bs-paper-bg));
        }
        .form-control:focus{
            border-width: 1px;
            box-shadow: unset;
        }
        .gallery-login-logo{
            position:absolute;
            bottom: 15px;
            left: calc(50vw - 85px);
        }
        button#formAuthenticationBtn {
            box-shadow: unset;
            border: unset;
        }
    </style>
    <script src="{{ asset('assets/js/config.js') }}"></script>
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
                            <img src="{{ asset('assets/img/illustrations/love-lock.svg') }}" width="80px;" alt="">
                        </div>
                        <h5 class="mb-1 enter-password-text">Please enter the password to access our special moments.</h5>
                        <form id="formAuthentication" class="mb-6"
                            action="{{ route('landing.apply_event_password') }}" method="POST">
                            <input type="hidden" id="event_slug" name="event_slug" value="{{ $event_slug }}">
                            <input type="hidden" id="year" name="year" value="{{ $year }}">
                            <div class="mb-6">
                                <!--<label for="password" class="form-label">Event Password</label>-->
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Enter password" autofocus="">
                            </div>
                            <div class="mb-6">
                                <button class="btn btn-primary d-grid w-100 gallery-login-btn" id="formAuthenticationBtn"
                                    type="button">Enter Gallery</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /Register -->
            </div>
        </div>
    </div>
    <div class="gallery-login-logo">
            <img src="{{ asset('assets/img/icons/logo-black.svg') }}" width="170px" alt="">    
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
</body>

</html>
