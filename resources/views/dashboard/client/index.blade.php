@extends('layouts.base')

@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/colvis/1.3.1/css/dataTables.colVis.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <style>
        table>thead>tr>th {
            text-align: center;
        }

        table>tbody>tr>td {
            text-align: center;
        }

        .dataTables_wrapper .dt-buttons {
            display: inline-block;
            margin-right: 10px;
            float: right;
        }

        .update-client:hover i,
        .delete-client:hover i {
            color: white !important;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('Clients List') }}</h5>
                <small class="text-body float-end"></small>
            </div>
            <div class="card-datatable">
                <div class="justify-content-between dt-layout-table" style="padding: 20px">
                    <table id="clients-datatable" class="table table-responsive table-hover text-nowrap"
                        style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="control dt-orderable-none">ID</th>
                                <th class="control dt-orderable-none">Planner Name</th>
                                <th class="control dt-orderable-none">Planner Business Name</th>
                                <th class="control dt-orderable-none">Phone Number</th>
                                <th class="control dt-orderable-none">Email</th>
                                <th class="control dt-orderable-none">Role</th>
                                <th class="control dt-orderable-none">Contact Button Text</th>
                                <th class="control dt-orderable-none">Contact Button Link</th>
                                <th class="control dt-orderable-none">Logo</th>
                                <th class="control dt-orderable-none">Profile Picture</th>
                                <th class="control dt-orderable-none">Description</th>
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
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        var table;
        $(document).ready(function() {
            table = $('#clients-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('clients.index') }}",
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
                        data: 'planner_name',
                        name: 'planner_name'
                    },
                    {
                        data: 'planner_business_name',
                        name: 'planner_business_name'
                    },
                    {
                        data: 'phone_number',
                        name: 'phone_number'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'role',
                        name: 'role'
                    },
                    {
                        data: 'contact_button_text',
                        name: 'contact_button_text'
                    },
                    {
                        data: 'contact_button_link',
                        name: 'contact_button_link'
                    },
                    {
                        data: 'logo',
                        name: 'logo'
                    },
                    {
                        data: 'profile_picture',
                        name: 'profile_picture'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                ordering: false,
                dom: 'lfBrtip',
                buttons: [{
                    extend: 'colvis', // Column visibility button
                    text: 'Columns', // Customize the button text
                    className: 'btn btn-primary' // Add a class for styling
                }],
                columnDefs: [{
                        targets: 8,
                        visible: false
                    },
                    {
                        targets: 9,
                        visible: false
                    },
                    {
                        targets: 10,
                        visible: false
                    }
                ]
            });

            $(document).on('click', '.delete-client', function() {
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
                        deleteItem(url, table);
                    }
                });
            });

            function deleteItem(url, table) {
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            table.draw();
                            $('#sidebar-clients-count').text(response.count);
                            Swal.fire(
                                'Deleted!',
                                'The item has been deleted.',
                                'success'
                            );
                        } else {
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
        });
    </script>

    <script>
        $(document).ready(function() {
            setTimeout(() => {
                $('.global-alert-section').remove();
            }, 5000);
        });
    </script>
@endsection
