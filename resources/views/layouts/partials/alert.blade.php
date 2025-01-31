@if (session('success'))
    <div class="d-flex justify-content-end global-alert-section" style="margin-right: 25px">
        <div class="bs-toast toast fade show bg-success" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                {{-- <i class="bx bx-bell me-2"></i> --}}
                {{-- <div class="me-auto fw-medium">Bootstrap</div>
            <small>11 mins ago</small> --}}
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                {{ session('success') }}
            </div>
        </div>
    </div>
@endif
