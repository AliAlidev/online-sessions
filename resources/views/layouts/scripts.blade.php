<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

<script src="{{ asset('assets/js/main.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButton = document.querySelector('.layout-menu-toggle');
        const sidebar = document.querySelector('.layout-menu');
        const mainContent = document.querySelector('.layout-page');
        const icon = toggleButton.querySelector('i');

        toggleButton.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');

            // Toggle the icon
            if (sidebar.classList.contains('collapsed')) {
                icon.classList.remove('bx-chevron-left');
                icon.classList.add('bx-chevron-right');
            } else {
                icon.classList.remove('bx-chevron-right');
                icon.classList.add('bx-chevron-left');
            }
        });
    });
</script>

@yield('scripts')
