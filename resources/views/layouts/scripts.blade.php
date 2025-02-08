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

<script>
    var oldContent;

    function showLoader(button) {
        if ($(button).is('a')) {
            oldContent = $(button).find('i');
            $(button).find('i').hide();
            $(button).append(
                '<span class="spinner-border spinner-border-sm" role="status"></span>'
            );
        } else {
            oldContent = $(button).parent().find('i');
            $(button).parent().append(
                '<span class="spinner-border spinner-border-sm" role="status"></span>'
            );
            $(button).parent().find('i').hide();
        }
    }

    function hideLoader(button) {
        if ($(button).is('a')) {
            $(button).find('span').hide();
            oldContent.show();
        } else {
            $(button).parent().find('span').hide();
            oldContent.show();
        }
    }

    function deleteItem(url, table, button) {
        $.ajax({
            url: url,
            type: 'GET',
            beforeSend: function() {
                showLoader(button);
            },
            success: function(response) {
                if (response.success) {
                    hideLoader(button);
                    table.draw();
                    Swal.fire(
                        'Deleted!',
                        'The item has been deleted.',
                        'success'
                    );
                } else {
                    hideLoader(button);
                    Swal.fire(
                        'Error!',
                        'There was a problem deleting the item.',
                        'error'
                    );
                }
            },
            error: function() {
                Swal.fire(
                    'Error!',
                    'There was an error with the request.',
                    'error'
                );
            }
        });
    }
</script>

<script>
    $(document).on('click','.logoutButton', function(e) {
        e.target.closest('form').submit();
    })
</script>

@yield('scripts')
