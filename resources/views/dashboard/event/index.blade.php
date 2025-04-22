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

        .update-event:hover i,
        .delete-event:hover i {
            color: white !important;
        }

        .bg-warning {
            --bs-bg-opacity: 1;
            background-color: rgb(244, 244, 53) !important;
        }

        .table-warning {
            --bs-table-bg: rgb(245, 245, 147) !important;
            --bs-table-hover-bg:
                color-mix(in sRGB, var(--bs-body-bg) 46%, var(--bs-table-bg));
            --bs-table-border-color:
                color-mix(in sRGB, var(--bs-table-bg) 98%, var(--bs-table-color));
            --bs-table-active-bg:
                color-mix(in sRGB, var(--bs-body-bg) 32.5%, var(--bs-table-bg));
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- DataTable with Buttons -->
        <h5 class="mb-0">{{ Breadcrumbs::render('events') }}</h5>
        <div class="card">
            @can('create_event')
                <div class="row card-header flex-column flex-md-row pb-0">
                    <div class="col-md-10" style="display: flex; gap: 10px;flex-direction: row">
                        <div class="col-md-2">
                            <label for="" class="form-label">Client</label>
                            <select name="filter-clients" id="filter-clients" class="form-select filter">
                                <option value="">All</option>
                                @foreach ($clients as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="" class="form-label">Status</label>
                            <select name="filter-statuses" id="filter-statuses" class="form-select filter">
                                <option value="">All</option>
                                @foreach ($statuses as $key => $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="" class="form-label">Type</label>
                            <select name="filter-types" id="filter-types" class="form-select filter">
                                <option value="">All</option>
                                @foreach ($types as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="" class="form-label">Start Date</label>
                            <input type="date" name="filter-dates" id="filter-dates" class="form-control filter">
                        </div>
                    </div>
                    <div class="col-md-2" style="text-align: right">
                        <a href="{{ route('events.create') }}" class="btn btn-primary">Create</a>
                    </div>
                    {{-- <div class="row" style="display: flex; justify-content: space-between">
                    </div> --}}
                    {{-- <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mt-0">
                    </div> --}}
                </div>
            @endcan
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
                                <th class="control dt-orderable-none">Time Remained</th>
                                <th class="control dt-orderable-none">Status</th>
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
                ajax: {
                    url: "{{ route('events.index') }}",
                    data: function(d) {
                        d.filter_date = $('#filter-dates').val();
                        d.filter_client = $('#filter-clients').val();
                        d.filter_status = $('#filter-statuses').val();
                        d.filter_type = $('#filter-types').val();
                    }
                },
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
                        name: 'bunny_event_name',
                        visible: false
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
                        name: 'customer',
                        visible: false
                    },
                    {
                        data: 'venue',
                        name: 'venue',
                        visible: false
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
                        data: 'time_reminder',
                        name: 'time_reminder'
                    },
                    {
                        data: 'event_status',
                        name: 'event_status',
                        render: function(data, type, row) {
                            if (data == "Pending") {
                                return '<span class="badge bg-warning">Pending</span>';
                            } else if (data == "Online") {
                                return '<span class="badge bg-success">Online</span>';
                            } else if (data == "Expire soon" || data == "Expired") {
                                return '<span class="badge bg-danger">' + data + '</span>';
                            }
                            return '-';
                        }
                    },
                    {
                        data: 'event_link',
                        name: 'event_link'
                    },
                    {
                        data: 'qr_code',
                        name: 'qr_code',
                        visible: false
                    },
                    {
                        data: 'description',
                        name: 'description',
                        visible: false
                    },
                    {
                        data: 'welcome_message',
                        name: 'welcome_message',
                        visible: false
                    },
                    {
                        data: 'cover_image',
                        name: 'cover_image',
                        visible: false
                    },
                    {
                        data: 'profile_picture',
                        name: 'profile_picture',
                        visible: false
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
                }]
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

            $('.filter').on('change', function() {
                table.draw();
            })
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
