@extends('layouts.base')

@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <style>
        table>thead>tr>th {
            text-align: center !important;
        }

        table>tbody>tr>td {
            text-align: center !important;
        }

        #spinner {
            display: none;
            margin-left: 10px;
        }

        .fa-spinner {
            font-size: 16px;
            animation: spin 1s infinite linear;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .update-event-type:hover i,
        .delete-event-type:hover i {
            color: white !important;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- DataTable with Buttons -->
        <h5 class="mb-0">{{ Breadcrumbs::render('event-types') }}</h5>
        <div class="card">
            <div class="row card-header flex-column flex-md-row pb-0">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mt-0">
                    <div class="dt-buttons btn-group flex-wrap mb-0">
                        @haspermission('create_event_type')
                            <button class="btn btn-sm btn-primary" data-bs-target="#CreateEventTypeModal" data-bs-toggle="modal"
                                type="button"><span><span class="d-flex align-items-center gap-2">
                                        <span class="d-none d-sm-inline-block">Add Event type</span>
                                        <i class="icon-base bx bx-plus icon-sm"></i>
                                    </span>
                                </span>
                            </button>
                        @endhaspermission
                    </div>
                </div>
            </div>
            <div class="card-datatable">
                <div class="justify-content-between dt-layout-table" style="padding: 20px">
                    <table id="event-types-datatable" class="table table-responsive table-hover text-nowrap"
                        style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="control dt-orderable-none">ID</th>
                                <th class="control dt-orderable-none">Name</th>
                                <th class="control dt-orderable-none">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ////////////// create type modal ////////////// --}}
    <div class="modal fade" id="CreateEventTypeModal" tabindex="-1" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="createEventType">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCenterTitle">Create New Event Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-6">
                                <label for="nameWithTitle" class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Enter Name">
                                <small class="text-body float-start error-message-div name-error"
                                    style="color: #ff0000 !important" hidden></small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"> Close </button>
                        <button type="submit" id="storeButton" class="btn btn-primary">Create
                            <span id="spinner" style="display:none;">
                                <i class="fa fa-spinner fa-spin"></i>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ////////////// update type modal ////////////// --}}
    <div class="modal fade" id="UpdateEventTypeModal" tabindex="-1" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="updateEventTypeForm">
                    <input type="hidden" name="event_type_id" id="eventTypeId">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCenterTitle">Create New Event Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-6">
                                <label for="nameWithTitle" class="form-label">Name</label>
                                <input id="eventTypeNameInputId" type="text" name="name" class="form-control"
                                    placeholder="Enter Name" value="">
                                <small class="text-body float-start error-message-div name-error"
                                    style="color: #ff0000 !important" hidden></small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"> Close </button>
                        <button type="submit" id="updateButton" class="btn btn-primary">Update
                            <span id="spinner" style="display:none;">
                                <i class="fa fa-spinner fa-spin"></i>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        var table;
        $(document).ready(function() {
            table = $('#event-types-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('events.types.index') }}",
                scrollX: true,
                scrollY: '400px',
                scrollCollapse: true,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                ordering: false
            });

            $(document).on('click', '.delete-event-type', function(e) {
                var url = $(this).data('url');
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You are about to delete item',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteItem(url, table, e.target);
                    }
                });
            });

            $(document).on('click', '.update-event-type', function() {
                var id = $(this).data('id')
                $.ajax({
                    url: "{{ url('admin/events-types/show') }}/" + id,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            $('#eventTypeNameInputId').val(response.data.name);
                            $('#eventTypeId').val(response.data.id);
                            $('#UpdateEventTypeModal').modal('show');
                        }
                    }
                })
            });

            $('#createEventType').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                let csrfToken = $('meta[name="csrf-token"]').attr('content');
                var submitBtn = $("#storeButton");
                var spinner = submitBtn.find('#spinner');

                $.ajax({
                    url: "{{ route('events.types.store') }}",
                    type: 'POST',
                    processData: false,
                    contentType: false,
                    data: formData,
                    dataType: "json",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken
                    },
                    beforeSend: function() {
                        submitBtn.prop('disabled', true);
                        spinner.show();
                    },
                    success: function(response) {
                        spinner.hide();
                        submitBtn.prop('disabled', false);
                        $('#CreateEventTypeModal').modal('hide');
                        resetForm('createEventType');
                        table.draw();
                        showAlertMessage(response.message)
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(field, messages) {
                                let inputField = $(`.${field}-error`);
                                inputField.attr('hidden', false);
                                inputField.text(messages[0]);
                            });
                        }
                        spinner.hide();
                        submitBtn.prop('disabled', false);
                    }
                });
            });
        });


        $('#updateEventTypeForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            let csrfToken = $('meta[name="csrf-token"]').attr('content');
            var submitBtn = $("#updateButton");
            var spinner = submitBtn.find('#spinner');
            var id = $('#eventTypeId').val();
            $.ajax({
                url: "{{ url('admin/events-types/update') }}/" + id,
                type: 'POST',
                processData: false,
                contentType: false,
                data: formData,
                dataType: "json",
                headers: {
                    "X-CSRF-TOKEN": csrfToken
                },
                beforeSend: function() {
                    submitBtn.prop('disabled', true);
                    spinner.show();
                },
                success: function(response) {
                    spinner.hide();
                    submitBtn.prop('disabled', false);
                    $('#UpdateEventTypeModal').modal('hide');
                    table.draw();
                    showAlertMessage(response.message)
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(field, messages) {
                            let inputField = $(`.${field}-error`);
                            inputField.attr('hidden', false);
                            inputField.text(messages[0]);
                        });
                    }
                    spinner.hide();
                    submitBtn.prop('disabled', false);
                }
            });
        });

        $('#UpdateEventTypeModal,#CreateEventTypeModal').on('hidden.bs.modal', function() {
            var form = $(this).find('form').attr('id');
            resetForm(form);
        });

        function resetForm(form) {
            document.getElementById(form).reset();
            $('.error-message-div').each(function() {
                $(this).attr('hidden', true);
            });
        }
    </script>

    <script>
        function showAlertMessage(message) {
            var element = `<div class="global-alert-section" style="margin-right: 25px">
        <div class="bs-toast toast fade show bg-success" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    </div>`;
            $('.content-wrapper').prepend(element);
            setTimeout(() => {
                $('.global-alert-section').remove();
            }, 5000);
        }
    </script>
@endsection
