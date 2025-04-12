<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet" />

<link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />

<!-- Core CSS -->
<link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
<link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}"
    class="template-customizer-theme-css" />
<link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

<!-- Vendors CSS -->
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
{{-- <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" /> --}}

<!-- Page CSS -->
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">

<!-- Helpers -->
<script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
<!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
<!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
<script src="{{ asset('assets/js/config.js') }}"></script>

<style>
    /* Default sidebar style */
    .layout-menu {
        transform: translateX(0);
        transition: transform 0.2s ease-in-out!important;
    }

    /* Hide sidebar when toggled */
    .layout-menu.collapsed {
        transform: translateX(-100%);
    }

    /* Adjust the main content when sidebar is collapsed */
    .layout-page {
        transition: margin-left 0.3s ease-in-out;
    }

    .layout-page.expanded {
        margin-left: 0;
    }

    .layout-menu.collapsed~.layout-page {
        padding-left: 0;
    }
</style>

@yield('styles')
