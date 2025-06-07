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
        <h5 class="mb-0">{{ Breadcrumbs::render('admins') }}</h5>
        <div class="card">
            <div class="row card-header flex-column flex-md-row pb-0">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mt-0">
                    <a href="{{ route('users.create') }}" class="btn btn-primary">Create Admin</a>
                </div>
            </div>
            <div class="card-datatable">
                <div class="justify-content-between dt-layout-table" style="padding: 20px">
                    <table id="event-types-datatable" class="table table-responsive table-hover text-nowrap"
                        style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="control dt-orderable-none">ID</th>
                                <th class="control dt-orderable-none">Full Name</th>
                                <th class="control dt-orderable-none">User Name</th>
                                <th class="control dt-orderable-none">Email</th>
                                <th class="control dt-orderable-none">Phone</th>
                                <th class="control dt-orderable-none">Permissions</th>
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
                ajax: "{{ route('users.index') }}",
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
                        data: 'full_name',
                        name: 'full_name'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone',
                        name: 'phone',
                        visible: false
                    },
                    {
                        data: 'permissions',
                        name: 'permissions',
                        visible: false
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

            $(document).on('click', '.delete-user', function(e) {
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
