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

        .update-event:hover i, .delete-event:hover i {
            color: white !important;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- DataTable with Buttons -->
        <h5 class="mb-0">{{ Breadcrumbs::render('events') }}</h5>
        <div class="card">
            <div class="card-datatable">
                <div class="justify-content-between dt-layout-table" style="padding: 20px">
                    <table id="events-datatable" class="table table-responsive table-hover text-nowrap"
                        style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="control dt-orderable-none">ID</th>
                                <th class="control dt-orderable-none">Name</th>
                                <th class="control dt-orderable-none">Bunny Name</th>
                                <th class="control dt-orderable-none">Type</th>
                                <th class="control dt-orderable-none">Client</th>
                                <th class="control dt-orderable-none">Customer</th>
                                <th class="control dt-orderable-none">Venue</th>
                                <th class="control dt-orderable-none">Start Date</th>
                                <th class="control dt-orderable-none">End Date</th>
                                <th class="control dt-orderable-none">Active Duration</th>
                                <th class="control dt-orderable-none">Event Link</th>
                                <th class="control dt-orderable-none">QR Code</th>
                                <th class="control dt-orderable-none">Description</th>
                                <th class="control dt-orderable-none">Welcome Message</th>
                                <th class="control dt-orderable-none">Cover Image</th>
                                <th class="control dt-orderable-none">Profile Picture</th>
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
            table = $('#events-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('events.index') }}",
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
                        data: 'event_name',
                        name: 'event_name'
                    },
                    {
                        data: 'bunny_event_name',
                        name: 'bunny_event_name'
                    },
                    {
                        data: 'event_type',
                        name: 'event_type'
                    },
                    {
                        data: 'event_client',
                        name: 'event_client'
                    },
                    {
                        data: 'customer',
                        name: 'customer'
                    },
                    {
                        data: 'venue',
                        name: 'venue'
                    },
                    {
                        data: 'start_date',
                        name: 'start_date'
                    },
                    {
                        data: 'end_date',
                        name: 'end_date'
                    },
                    {
                        data: 'active_duration',
                        name: 'active_duration'
                    },
                    {
                        data: 'event_link',
                        name: 'event_link'
                    },
                    {
                        data: 'qr_code',
                        name: 'qr_code'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'welcome_message',
                        name: 'welcome_message'
                    },
                    {
                        data: 'cover_image',
                        name: 'cover_image'
                    },
                    {
                        data: 'profile_picture',
                        name: 'profile_picture'
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
                    extend: 'colvis',
                    text: 'Columns',
                    className: 'btn btn-primary'
                }],
                columnDefs: [{
                        targets: 11,
                        visible: false
                    },
                    {
                        targets: 12,
                        visible: false
                    },
                    {
                        targets: 13,
                        visible: false
                    },
                    {
                        targets: 14,
                        visible: false
                    }
                ]
            });

            $(document).on('click', '.delete-event', function(e) {
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
    </script>

    <script>
        $(document).ready(function() {
            setTimeout(() => {
                $('.global-alert-section').remove();
            }, 5000);
        });
    </script>
@endsection
