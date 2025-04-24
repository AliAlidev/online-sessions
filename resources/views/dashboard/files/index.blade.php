@extends('layouts.base')

@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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

        .update-file:hover i,
        .delete-file:hover i {
            color: white !important;
        }

        .hidden-force {
            display: none !important;
        }

        .show-force {
            display: block !important;
        }

        .select2-container--open .select2-dropdown {
            z-index: 9999 !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            display: flex;
            flex-wrap: wrap;
            gap: 1px;
            margin-left: 4px;
            margin-top: 0px;
        }

        .select2-container--default .select2-selection--multiple {
            height: auto !important;
            min-height: 38px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            margin-left: 1px;
            margin-top: 5px;
        }

        .file-status-modal {
            cursor: pointer;
        }

        .folder-order-div {
            width: 25px;
            height: 25px;
            background-color: #3b90c9;
            border-radius: 50%;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            justify-self: center !important;
        }
    </style>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- DataTable with Buttons -->
        <h5>{{ Breadcrumbs::render('files', request()->route('event_slug'), request()->route('folder_slug')) }}</h5>
        <div class="card">
            <div class="row card-header flex-column flex-md-row pb-0">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                </div>
                <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mt-0"
                    style="gap: 10px">
                    {{-- <a href="javascript:history.back()" class="btn btn-label-primary btn-sm">Back</a> --}}
                    <div class="dt-buttons btn-group flex-wrap mb-0">
                        @if (
                            ($folderType == 'video' && Auth::user()->hasPermissionTo('upload_video')) ||
                                ($folderType == 'image' && Auth::user()->hasPermissionTo('upload_image')))
                            <button class="btn btn-sm btn-primary" data-bs-target="#CreateFileModal" data-bs-toggle="modal"
                                type="button"><span><span class="d-flex align-items-center gap-2">
                                        <span class="d-none d-sm-inline-block">Upload {{ ucfirst($folderType) }}</span>
                                        <i class="icon-base bx bx-plus icon-sm"></i>
                                    </span>
                                </span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-datatable">
                <div class="justify-content-between dt-layout-table" style="padding: 20px">
                    <table id="files-datatable" class="table table-responsive table-hover text-nowrap" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="control dt-orderable-none">ID</th>
                                <th class="control dt-orderable-none">File</th>
                                <th class="control dt-orderable-none">Name & Size</th>
                                <th class="control dt-orderable-none">User Name</th>
                                <th class="control dt-orderable-none">Video Name</th>
                                <th class="control dt-orderable-none">Description</th>
                                <th class="control dt-orderable-none">Date</th>
                                <th class="control dt-orderable-none">Order</th>
                                <th class="control dt-orderable-none">Status</th>
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

    {{-- ////////////// create file modal ////////////// --}}
    <div class="modal fade" id="CreateFileModal" tabindex="-1" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="createFileForm" method="POST" enctype="multipart/form-data">
                    <input type="hidden" class="uploaded-file-name-input" name="file_name">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCenterTitle">Upload New {{ ucfirst($folderType) }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($folderType == 'video')
                            <div class="row">
                                <div class="col-md-4 mb-6">
                                    <label for="fileOrder" class="form-label">Order</label>
                                    <input type="number" id="fileOrder" value="1" step="1" name="file_order"
                                        class="form-control">
                                    <small class="text-body float-start error-message-div file_order-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                            </div>
                        @endif
                        <div class="row mb-6">
                            <div class="col-md-12">
                                <input type="file" id="event-file-hidden" hidden multiple>
                                <label for="event-file" class="form-label">{{ ucfirst($folderType) }}</label>
                                <input type="file" id="event-file" class="form-control event-file" name="file"
                                    multiple
                                    accept='{{ $folderType == 'image' ? 'image/jpeg,png,jpg,webp' : 'video/mp4' }}'>
                                <small class="text-body float-start uploaded-file-name"
                                    style="color: #000; font-style: italic;"></small>
                                <small class="text-body float-start error-message-div file-error file_name-error"
                                    style="color: #ff0000 !important" hidden></small>
                            </div>
                            @if ($folderType == 'image')
                                <div class="col-md-12 mt-2">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input compress_images" type="checkbox" id="compress_images"
                                            name="compress_images" value="compress_images">
                                        <label class="form-check-label" for="compress_images">Compress Images</label>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="row mb-6">
                            @if ($folderType == 'image')
                                <div class="col-md-6">
                                    <label for="userName" class="form-label">User Name</label>
                                    <input type="text" id="UserName" class="form-control" name="user_name"
                                        placeholder="Enter User Name">
                                    <small class="text-body float-start error-message-div user_name-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                            @endif
                            @if ($folderType == 'video')
                                <div class="col-md-6">
                                    <label for="VideoName" class="form-label">Video Name</label>
                                    <input type="text" id="VideoName" class="form-control" name="video_name"
                                        placeholder="Enter Video Name">
                                    <small class="text-body float-start error-message-div user_name-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                                <div class="col-md-6">
                                    <label for="video_resolution" class="form-label">Video Resolution</label>
                                    <select name="video_resolution" class="video_resolution" id="video_resolution"
                                        multiple="multiple">
                                        <option value="240p">240p</option>
                                        <option value="360p">360p</option>
                                        <option value="480p">480p</option>
                                        <option value="720p">720p</option>
                                        <option value="1080p">1080p</option>
                                        <option value="1440p">1440p</option>
                                        <option value="2160p">2160p</option>
                                    </select>
                                    <small class="text-body float-start error-message-div user_name-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                            @endif
                        </div>
                        <div class="row mb-6">
                            <div class="col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea id="description" class="form-control" name="description" rows="4" placeholder="Enter Description"></textarea>
                                <small id="char-count" class="form-text text-muted">0 / 300 characters</small>
                                <small class="text-body float-start error-message-div description-error"
                                    style="color: #ff0000 !important" hidden></small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div id="progressContainer"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"> Close </button>
                        <button type="submit" id="storeButton" class="btn btn-primary">Upload
                            <span id="spinner" style="display:none;">
                                <i class="fa fa-spinner fa-spin"></i>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ////////////// update file modal ////////////// --}}
    <div class="modal fade" id="UpdateFileModal" tabindex="-1" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="updateFileForm">
                    <input type="hidden" id="updateFileId" name="file_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCenterTitle">Update Uploaded {{ ucfirst($folderType) }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($folderType == 'video')
                            <div class="row">
                                <div class="col-md-4 mb-4">
                                    <label for="fileOrderInput" class="form-label">Order</label>
                                    <input type="number" id="fileOrderInput" value="1" step="1"
                                        name="file_order" class="form-control">
                                    <small class="text-body float-start error-message-div file_order-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                            </div>
                        @endif
                        <div class="row mb-6">
                            @if ($folderType == 'image')
                                <div class="col-md-6">
                                    <label for="UserNameInput" class="form-label">User Name</label>
                                    <input type="text" class="form-control" name="user_name" id="userNameInput"
                                        placeholder="Enter User Name">
                                    <small class="text-body float-start error-message-div user_name-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                            @endif
                            @if (
                                ($folderType == 'image' && Auth::user()->hasPermissionTo('approve_decline_image')) ||
                                    ($folderType == 'video' && Auth::user()->hasPermissionTo('approve_decline_video')))
                                <div class="col-md-6">
                                    <label for="fileStatusInput" class="form-label">{{ ucfirst($folderType) }}
                                        Status</label>
                                    <select class="form-select" name="file_status" id="fileStatusInput">
                                        <option selected disabled>Select {{ ucfirst($folderType) }} Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                    <small class="text-body float-start error-message-div file_status-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                            @else
                                <div class="col-md-6">
                                    <label for="fileStatusInput" class="form-label">{{ ucfirst($folderType) }}
                                        Status</label>
                                    <input type="text" class="form-control" name="" id="fileStatusInput"
                                        readonly>
                                </div>
                            @endif
                        </div>
                        @if ($folderType == 'video')
                            <div class="col-md-6 mb-6">
                                <label for="VideoNameInput" class="form-label">Video Name</label>
                                <input type="text" id="VideoNameInput" class="form-control" name="video_name"
                                    placeholder="Enter Video Name">
                                <small class="text-body float-start error-message-div user_name-error"
                                    style="color: #ff0000 !important" hidden></small>
                            </div>
                            <div class="row mb-6" id="video_resolution_div" style="display: none">
                                <div class="col-md-6">
                                    <label for="userName" class="form-label">Video Resolution</label>
                                    <select name="video_resolution" class="video_resolution" id="video_resolution_update"
                                        multiple="multiple">
                                        <option value="240p">240p</option>
                                        <option value="360p">360p</option>
                                        <option value="480p">480p</option>
                                        <option value="720p">720p</option>
                                        <option value="1080p">1080p</option>
                                        <option value="1440p">1440p</option>
                                        <option value="2160p">2160p</option>
                                    </select>
                                    <small class="text-body float-start error-message-div user_name-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                            </div>
                        @endif
                        <div class="row mb-6">
                            <div class="col-md-12">
                                <label for="descriptionInput" class="form-label">Description</label>
                                <textarea id="descriptionInput" class="form-control" name="description" rows="4"
                                    placeholder="Enter Description"></textarea>
                                <small id="char-count" class="form-text text-muted">0 / 300 characters</small>
                                <small class="text-body float-start error-message-div description-error"
                                    style="color: #ff0000 !important" hidden></small>
                            </div>
                        </div>
                        <div class="row">
                            <div id="progressContainer"></div>
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

    {{-- ////////////// change status modal ////////////// --}}
    <div class="modal fade" id="changeStatusModal" tabindex="-1" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <form id="updateStatusForm">
                    <input type="hidden" name="file_id" id="fileIdModalInput">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCenterTitle">Change Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label for="fileStatusModalInput" class="form-label">{{ ucfirst($folderType) }} Status</label>
                        <select class="form-select" name="file_status" id="fileStatusModalInput">
                            <option selected disabled>Select {{ ucfirst($folderType) }} Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"> Close </button>
                        <button type="submit" id="updateStatusButton" class="btn btn-primary">Update
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"></script>
    <script src="{{ asset('assets/js/compressor.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        var table;
        $(document).ready(function() {
            $('.video_resolution').select2({
                placeholder: "Select video resolutions", // Placeholder text
                allowClear: true // Allow clearing selections
            });

            table = $('#files-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('files.index', [request()->route('event_slug'), request()->route('folder_slug'), request()->route('type')]) }}",
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
                        data: 'file',
                        name: 'file'
                    },
                    {
                        data: 'name_and_size',
                        name: 'name_and_size'
                    },
                    {
                        data: 'user_name',
                        name: 'user_name',
                        visible: "{{ $folderType == 'video' ? false : true }}"
                    },
                    {
                        data: 'video_name',
                        name: 'video_name',
                        visible: "{{ $folderType == 'video' ? true : false }}"
                    },
                    {
                        data: 'description',
                        name: 'description',
                        visible: false
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'file_order',
                        name: 'file_order',
                        visible: "{{ $folderType == 'video' ? true : false }}"
                    },
                    {
                        data: 'status',
                        name: 'status'
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
                // buttons: [{
                //     extend: 'colvis', // Column visibility button
                //     text: 'Columns', // Customize the button text
                //     className: 'btn btn-primary' // Add a class for styling
                // }],
                // columnDefs: [{
                //         targets: 8,
                //         visible: false
                //     },
                //     {
                //         targets: 9,
                //         visible: false
                //     },
                //     {
                //         targets: 10,
                //         visible: false
                //     }
                // ]
            });

            $(document).on('click', '.delete-file', function(e) {
                e.preventDefault();
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

            $(document).on('click', '.update-file', function(e) {
                e.preventDefault();
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ route('files.show', [request()->route('event_slug'), request()->route('folder_slug')]) }}/" +
                        id,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            $('#userNameInput').val(response.data.user_name);
                            $('#VideoNameInput').val(response.data.video_name);
                            $('#descriptionInput').text(response.data.description);
                            $('#fileStatusInput').val(response.data.file_status);
                            $('#updateFileId').val(response.data.id);
                            $('#fileOrderInput').val(response.data.file_order);
                            $('#UpdateFileModal').modal('show');
                        }
                    }
                })
            });

            $(document).on('click', '.file-status-modal', function(e) {
                e.preventDefault();
                var status = $(this).data('status');
                var id = $(this).data('id');
                $('#fileStatusModalInput').val(status);
                $('#fileIdModalInput').val(id);
                $('#changeStatusModal').modal('show');
            });

            let allUploadsSuccessCount = 0; // Track if any upload fails
            let filesCount = 0;
            let formId = null;
            $('#createFileForm').submit(function(e) {
                e.preventDefault(); // Prevent default form submission
                formId = e.target.id;
                $('.alert').remove();
                var submitBtn = $("#storeButton");
                showButtonLoader(submitBtn);

                if ($('#' + formId + ' #compress_images').is(':checked'))
                    var files = $('#' + formId + ' #event-file-hidden')[0].files;
                else
                    var files = $('#' + formId + ' .event-file')[0].files;
                filesCount = files.length;
                if (files.length == 0) {
                    $('.file_name-error').attr('hidden', false);
                    $('.file_name-error').text('Please select files');
                    hideButtonLoader(submitBtn);
                    return false;
                }

                var progressBar = $('#' + formId).parent().find('#progressContainer');
                progressBar.empty(); // Clear previous progress bars

                let uploadQueue = []; // Queue for all files
                const MAX_CONCURRENT_UPLOADS = 6;

                // Initialize UI for all files (progress bar + Start button)
                for (let i = 0; i < files.length; i++) {
                    let fileContainer = $(`
                            <div class="mb-4" id="file-container-${i}">
                                <p class="mb-0">Stage1: File Upload ${files[i].name}</p>
                                <button class="btn btn-sm btn-primary start-btn" data-index="${i}" style="height:15px;width:auto ;font-size: 10px;">Start Upload</button>
                                <div class="progress mt-2">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="font-size:12px;height:10px"
                                        role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
                                        style="width: 0%" id="progress-bar-${i}"></div>
                                </div>
                                <p id="status-${i}" class="text-danger d-inline-block mt-1"></p>
                                <button class="btn btn-sm btn-warning retry-btn hidden-force" style="height:15px; width:auto; font-size:10px" data-index="${i}" style="font-size: 12px;">Retry</button>
                            </div>
                        `);
                    progressBar.append(fileContainer);
                    uploadQueue.push({
                        file: files[i],
                        index: i
                    }); // Add to queue
                }

                // Start uploading files (up to 6 at a time)
                processUploads(uploadQueue, MAX_CONCURRENT_UPLOADS)
                    .then(() => {
                        hideButtonLoader(submitBtn);
                    })
                    .catch(() => {
                        hideButtonLoader(submitBtn);
                    });
            });

            // Function to process uploads with concurrency control
            function processUploads(queue, maxUploads) {
                const activeUploads = []; // Array to keep track of active promises

                return new Promise((resolve, reject) => {
                    function startNextUpload() {
                        // Check if there are still files to process
                        if (queue.length === 0 && activeUploads.length === 0) {
                            resolve(); // Resolve when all files are processed
                            return;
                        }

                        // Start new uploads if we're below the max limit
                        while (queue.length > 0 && activeUploads.length < maxUploads) {
                            const {
                                file,
                                index
                            } = queue.shift(); // Get the next file from the queue

                            // Start upload and add the promise to active uploads
                            const uploadPromise = uploadFile(file, index)
                                .then(() => {
                                    // Remove this upload from the active list when completed
                                    activeUploads.splice(activeUploads.indexOf(uploadPromise), 1);
                                    startNextUpload(); // Check if we can start another upload
                                })
                                .catch(() => {
                                    // Handle failed uploads by showing retry button
                                    showRetryButton(index);
                                    activeUploads.splice(activeUploads.indexOf(uploadPromise), 1);
                                    startNextUpload(); // Check if we can start another upload
                                });

                            activeUploads.push(uploadPromise); // Track active upload
                        }
                    }

                    startNextUpload(); // Start the first batch of uploads
                });
            }

            async function uploadFile(file, index) {
                return new Promise((resolve, reject) => {
                    let formData = new FormData();
                    formData.append('file', file);
                    let csrfToken = $('meta[name="csrf-token"]').attr('content');
                    formData.append('_token', csrfToken);
                    formData.append('user_name', $('#UserName').val() == undefined ? '' : $('#UserName')
                        .val());
                    formData.append('description', $('#description').val());
                    formData.append('file_size', file.size);
                    formData.append('video_resolution', $('#video_resolution').val());
                    formData.append('video_name', $('#VideoName').val() == undefined ? '' : $(
                        '#VideoName').val());
                    formData.append('file_order', $('#fileOrder').val() == undefined ? 1 : $(
                        '#fileOrder').val());
                    $(`#file-container-${index} .start-btn`).remove(); // Remove "Start Upload" button

                    $.ajax({
                        url: "{{ route('files.store', [request()->route('event_slug'), request()->route('folder_slug')]) }}",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        xhr: function() {
                            let xhr = new window.XMLHttpRequest();
                            xhr.upload.addEventListener("progress", function(event) {
                                if (event.lengthComputable) {
                                    let percent = Math.round((event.loaded / event
                                        .total) * 100);
                                    $("#progress-bar-" + index).css("width",
                                        percent + "%").text(percent + "%");

                                    if (percent === 100) {
                                        showProcessingStatus(index);
                                    }
                                }
                            }, false);
                            return xhr;
                        },
                        success: function(response) {
                            allUploadsSuccessCount++;
                            clearInterval($("#status-" + index).data(
                                "interval")); // Stop animated dots
                            $("#progress-bar-" + index).removeClass("bg-success").addClass(
                                "bg-primary").text("Completed");
                            $("#status-" + index).addClass('hidden-force');
                            if (allUploadsSuccessCount == filesCount) {
                                resetForm(formId);
                                var element =
                                    `<div class="alert alert-success"> ${response.message}</div>`;
                                $('#' + formId).find('.modal-body').prepend(element);
                                $('#files-datatable').DataTable().draw();
                            }
                            resolve(); // Resolve the Promise after successful upload
                        },
                        error: function(jqXHR, textStatus) {
                            clearInterval($("#status-" + index).data(
                                "interval")); // Stop animated dots
                            $("#progress-bar-" + index).removeClass("bg-success").addClass(
                                "bg-danger").text("Failed");

                            let errorMessage = (textStatus === "timeout") ?
                                "Stage2: Upload timed out" : "Stage2: Failed";
                            $("#status-" + index).text(errorMessage).show();

                            showRetryButton(index); // Show retry button only when failed
                            reject(); // Reject the Promise in case of error
                        }
                    });
                });
            }

            // Show Progress Bar and Hide Start Button
            function startUpload(file, index) {
                $(`#file-container-${index} .start-btn`).remove(); // Remove "Start Upload" button
                $(`#file-container-${index} .progress`).show(); // Show progress bar
                // This ensures progress bar is shown after DOM update
                setTimeout(() => {
                    $(`#file-container-${index} .progress`).css('display', 'block');
                }, 0);
            }

            function showProcessingStatus(index) {
                let statusText = $("#status-" + index);
                let dots = 0;
                statusText.removeClass('hidden-force');
                statusText.text("Stage2: Processing");

                let interval = setInterval(() => {
                    dots = (dots + 1) % 4;
                    statusText.text("Stage2: Processing" + ".".repeat(dots));
                }, 500);

                statusText.data("interval", interval); // Save interval to clear later
            }

            function showRetryButton(index) {
                $(`#file-container-${index} .retry-btn`).removeClass('hidden-force'); // Show Retry button
            }

            function showUpdateRetryButton(index) {
                $(`#file-container-${index} .retry-update-btn`).removeClass('hidden-force'); // Show Retry button
            }

            $(document).on("click", ".retry-btn", function() {
                $(this).addClass('hidden-force');
                let index = $(this).data("index");
                let file = $('input[type=file]')[0].files[index]; // Get the failed file

                // Reset progress bar and status text
                $("#progress-bar-" + index)
                    .css("width", "0%")
                    .removeClass("bg-danger")
                    .addClass("bg-success")
                    .text("0%");

                $("#status-" + index).text("Stage2: Retrying...");

                // Retry uploading the file using the existing progress bar
                uploadFile(file, index);
            });

            $(document).on("click", ".retry-update-btn", function() {
                $(this).addClass('hidden-force');
                let index = $(this).data("index");
                let file = $('#updateFileForm input[type=file]')[0].files[index]; // Get the failed file

                // Reset progress bar and status text
                $("#updateFileForm #progress-bar-" + index)
                    .css("width", "0%")
                    .removeClass("bg-danger")
                    .addClass("bg-success")
                    .text("0%");

                $("#updateFileForm #status-" + index).text("Stage2: Retrying...");

                // Retry uploading the file using the existing progress bar
                uploadUpdatedFile(file, index);
            });

            $(document).on("click", ".start-btn", function() {
                let index = $(this).data("index");
                let file = $('input[type=file]')[0].files[index]; // Get the file

                startUpload(file, index); // Hide button, show progress
                uploadFile(file, index);
            });

            $('.event-file').on('change', function() {
                clearErrors();
                const fileInput = $(this)[0];
                const fileNameDisplay = $(this).closest('.col-md-12').find('.uploaded-file-name');
                fileNameDisplay.attr('hidden', false);
                fileNameDisplay.empty();
                filesCount = fileInput.files.length;
                allUploadsSuccessCount = 0;
                if (fileInput.files && fileInput.files.length > 0) {
                    // Iterate over each file
                    Array.from(fileInput.files).forEach((file, index) => {
                        let fileName = file.name;
                        const fileExtension = fileName.split('.').pop();
                        if (fileName.length > 40) {
                            const nameWithoutExtension = fileName.substring(0, fileName.lastIndexOf(
                                '.'));
                            fileName = nameWithoutExtension.substring(0, 20) + '...' + '.' +
                                fileExtension;
                        }
                        fileNameDisplay.append(
                            `<small>File Name: ${fileName}, Size: ${(file.size / (1024 * 1024)).toFixed(2)} MB</small><br/>`
                        );
                        $('.uploaded-file-name-input').val(fileName);
                    });

                    if ("{{ $folderType }}" == 'video') {
                        $('#video_resolution_div').show();
                    }
                } else {
                    fileNameDisplay.text('');
                }
            });

            /////////////// update section //////////
            $('#updateFileForm').submit(function(e) {
                e.preventDefault(); // Prevent default form submission
                formId = e.target.id;
                $('.alert').remove();
                var submitBtn = $("#updateButton");
                showButtonLoader(submitBtn);
                let csrfToken = $('meta[name="csrf-token"]').attr('content');
                var formData = new FormData();
                formData.append('_token', csrfToken);
                formData.append('user_name', $('#userNameInput').val() == undefined ? '' : $(
                    '#userNameInput').val());
                formData.append('video_name', $('#VideoNameInput').val() == undefined ? '' : $(
                    '#VideoNameInput').val());
                formData.append('file_order', $('#fileOrderInput').val() == undefined ? 1 : $(
                    '#fileOrderInput').val());
                formData.append('description', $('#descriptionInput').val());
                var fileId = $('#updateFileId').val();
                formData.append('file_id', fileId);
                formData.append('file_status', $('#fileStatusInput').val());
                var submitBtn = $("#updateButton");
                $.ajax({
                    url: "{{ route('files.update', [request()->route('event_slug'), request()->route('folder_slug')]) }}/" +
                        fileId,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        hideButtonLoader(submitBtn);
                        var element =
                            `<div class="alert alert-success"> ${response.message}</div>`;
                        $('#' + formId).find('.modal-body').prepend(element);
                        $('#files-datatable').DataTable().draw();
                    },
                    error: function(jqXHR, textStatus) {
                        hideButtonLoader(submitBtn);
                        let errorMessage = (textStatus === "timeout") ?
                            "Stage2: Upload timed out" : "Stage2: Failed";
                        showUpdateRetryButton(index); // Show retry button only when failed
                    }
                });
            });


            function showButtonLoader(submitBtn) {
                submitBtn.find('#spinner').show();
                submitBtn.prop('disabled', true);
            }

            function hideButtonLoader(submitBtn) {
                submitBtn.find('#spinner').hide();
                submitBtn.prop('disabled', false);
            }

            $('#UpdateFileModal,#CreateFileModal').on('hidden.bs.modal', function() {
                var form = $(this).find('form').attr('id');
                resetForm(form);
                var progressBar = $('#' + form).parent().find('#progressContainer');
                progressBar.empty();
                table.draw();
            });

            $('#updateStatusForm').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                let csrfToken = $('meta[name="csrf-token"]').attr('content');
                var submitBtn = $("#updateStatusButton");
                $.ajax({
                    url: "{{ route('files.change.status', [request()->route('event_slug'), request()->route('folder_slug')]) }}",
                    type: 'POST',
                    headers: {
                        "X-CSRF-TOKEN": csrfToken
                    },
                    processData: false,
                    contentType: false,
                    data: formData,
                    beforeSend: function() {
                        showButtonLoader(submitBtn);
                    },
                    success: function(response) {
                        hideButtonLoader(submitBtn);
                        if (response.success) {
                            $('#changeStatusModal').modal('hide');
                            table.draw();
                            showAlertMessage(response.message)
                        }
                    }
                })
            });

            $('.compress_images').on('change', function(e) {
                var formId = $(this).closest('form').attr('id');
                if (this.checked) {
                    compressImages(formId);
                } else {
                    $("#" + formId + " #event-file-hidden").val("");
                }
            });
        });

        function clearErrors() {
            $('.error-message-div').each(function() {
                $(this).attr('hidden', true);
            });
        }

        function resetForm(form) {
            document.getElementById(form).reset();
            $('.error-message-div').each(function() {
                $(this).attr('hidden', true);
            });
            $('.uploaded-file-name').each(function() {
                $(this).attr('hidden', true);
            });

            $('.video_resolution').val(null).trigger('change');

            $('.alert').remove();

            $('#filesLink,#filesLinkInput').parent().attr('hidden', true);

            $('#video_resolution_div').hide();

            // var progressBar = $('#' + form).parent().find('#progressContainer');
            // progressBar.empty();
        }

        function compressImages(formId) {
            const input = document.getElementById(formId).querySelector('#event-file');
            const files = input.files;
            if (!files.length) return;

            const targetSizeKB = 500; // Target size in KB
            const dataTransfer = new DataTransfer(); // Holds all final files

            Array.from(files).forEach((file) => {
                const fileSizeKB = file.size / 1024; // Convert bytes to KB

                // If file is already under target size, skip compression
                if (fileSizeKB <= targetSizeKB) {
                    dataTransfer.items.add(file); // Use original file
                    if (dataTransfer.files.length === files.length) {
                        document.getElementById(formId).querySelector("#event-file-hidden").files = dataTransfer
                            .files;
                    }
                    return;
                }

                // Else compress
                const quality = Math.max(0.98, targetSizeKB / fileSizeKB); // More aggressive compression if needed
                new Compressor(file, {
                    quality: quality,
                    maxWidth: 1024,
                    maxHeight: 1024,
                    success(result) {
                        const compressedFile = new File([result], file.name, {
                            type: file.type,
                            lastModified: Date.now(),
                        });
                        dataTransfer.items.add(compressedFile);
                        if (dataTransfer.files.length === files.length) {
                            document.getElementById(formId).querySelector("#event-file-hidden").files =
                                dataTransfer.files;
                        }
                    },
                    error(err) {
                        console.error("Compression error:", err);
                    }
                });
            });
        }
    </script>

    <script>
        function showAlertMessage(message) {
            var element = `<div class="d-flex justify-content-end global-alert-section" style="margin-right: 25px">
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

        function showErrorMessage(message) {
            var element = `<div class="d-flex justify-content-end global-alert-section" style="margin-right: 25px">
            <div class="bs-toast toast fade show bg-danger" role="alert" aria-live="assertive" aria-atomic="true">
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

    <script>
        $(document).ready(function() {
            $('#filesType,#filesTypeInput').on('change', function() {
                var selectedValue = $(this).val();
                if (selectedValue === 'link') {
                    $('#filesLink,#filesLinkInput').parent().attr('hidden', false);
                } else {
                    $('#filesLink,#filesLinkInput').parent().attr('hidden', true);
                }
            });
        })
    </script>

    <script>
        $(document).ready(function() {
            function addCharactersLimitation(inputId, limitation = 300) {
                const input = document.getElementById(inputId);
                const counter = input.parentNode.querySelector('#char-count');
                input.addEventListener('input', function() {
                    if (input.value.length > limitation) {
                        input.value = input.value.substring(0, limitation); // Trim excess
                    }
                    counter.textContent = `${input.value.length} / ${limitation} characters`;
                });
            }
            addCharactersLimitation('description');
            addCharactersLimitation('descriptionInput');
        })
    </script>
@endsection
