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

        .update-file:hover i,
        .delete-file:hover i {
            color: white !important;
        }

        #FilePreviewer .modal-body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            padding: 5px;
        }

        #FilePreviewer #modalImage {
            width: 100%;
            height: 100%;
            object-fit: contain;
            max-height: 60vh;
        }

        #FilePreviewer #modalImageContain {
            width: 100%;
            height: 100%;
            object-fit: contain;
            max-height: 60vh;
        }

        .file-previewer:hover,
        .file-status-modal {
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- DataTable with Buttons -->
        <div class="card">
            <div class="row card-header flex-column flex-md-row pb-0">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="mb-0">{{ ucfirst($folderType) . 's List' }}<h5>
                </div>
                <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mt-0"
                    style="gap: 10px">
                    <a href="javascript:history.back()" class="btn btn-label-primary btn-sm">Back</a>
                    <div class="dt-buttons btn-group flex-wrap mb-0">
                        <button class="btn btn-sm btn-primary" data-bs-target="#CreateFileModal" data-bs-toggle="modal"
                            type="button"><span><span class="d-flex align-items-center gap-2">
                                    <span class="d-none d-sm-inline-block">Upload {{ ucfirst($folderType) }}</span>
                                    <i class="icon-base bx bx-plus icon-sm"></i>
                                </span>
                            </span>
                        </button>
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
                                <th class="control dt-orderable-none">Description</th>
                                <th class="control dt-orderable-none">Date</th>
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
                <form id="createFileForm">
                    <input type="hidden" class="uploaded-file-name-input" name="file_name">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCenterTitle">Upload New {{ ucfirst($folderType) }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-6">
                            <input type="hidden" class="uploaded-file-size" name="file_size">
                            <div class="col-md-6">
                                <label for="file" class="form-label">{{ ucfirst($folderType) }}</label>
                                <input type="file" id="file" class="form-control" name="file"
                                    accept='{{ $folderType == 'image' ? 'image/jpeg,png,jpg,webp' : 'video/mp4' }}'>
                                <small class="text-body float-start uploaded-file-name"
                                    style="color: #000; font-style: italic;"></small>
                                <small class="text-body float-start error-message-div file-error file_name-error"
                                    style="color: #ff0000 !important" hidden></small>
                            </div>
                            <div class="col-md-6">
                                <label for="userName" class="form-label">User Name</label>
                                <input type="text" id="UserName" class="form-control" name="user_name"
                                    placeholder="Enter User Name">
                                <small class="text-body float-start error-message-div user_name-error"
                                    style="color: #ff0000 !important" hidden></small>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea id="description" class="form-control" name="description" rows="4" placeholder="Enter Description"></textarea>
                                <small class="text-body float-start error-message-div description-error"
                                    style="color: #ff0000 !important" hidden></small>
                            </div>
                        </div>
                        {{-- <div class="row mb-6">
                            <div class="col-md-6">
                                <label for="fileStatus" class="form-label">{{ ucfirst($folderType) }} Status</label>
                                <select class="form-select" id="fileStatus" name="file_status">
                                    <option selected disabled>Select {{ ucfirst($folderType) }} Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                                <small class="text-body float-start error-message-div file_status-error"
                                    style="color: #ff0000 !important" hidden></small>
                            </div>
                        </div> --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"> Close </button>
                        <button type="submit" id="storeButton" class="btn btn-primary">Upload
                            <span id="spinner" style="display:none;">
                                <i class="fa fa-spinner fa-spin"></i>
                            </span>
                        </button>
                    </div>

                    <div class="progress uploadProgress" style="display: none; height: 10px;">
                        <div class="progress-bar" role="progressbar" style="width: 0%; height:10px; font-size: 7px"
                            aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            0%
                        </div>
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
                    <input type="hidden" class="uploaded-file-size" name="file_size">
                    <input type="hidden" class="uploaded-file-name-input" name="file_name">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCenterTitle">Update Uploaded {{ ucfirst($folderType) }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-6">
                            <div class="col-md-6">
                                <label for="fileInput" class="form-label">{{ ucfirst($folderType) }}</label>
                                <input type="file" class="form-control" name="file" id="fileInput"
                                    accept='{{ $folderType == 'image' ? 'image/jpeg,png,jpg,gif,svg,webp' : 'video/mp4' }}'>
                                <small class="text-body float-start uploaded-file-name"
                                    style="color: #000; font-style: italic;"></small>
                                <small class="text-body float-start error-message-div file-error"
                                    style="color: #ff0000 !important" hidden></small>
                            </div>
                            <div class="col-md-6">
                                <label for="UserNameInput" class="form-label">User Name</label>
                                <input type="text" class="form-control" name="user_name" id="userNameInput"
                                    placeholder="Enter User Name">
                                <small class="text-body float-start error-message-div user_name-error"
                                    style="color: #ff0000 !important" hidden></small>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="row mb-6">
                                <div class="col-md-12">
                                    <label for="descriptionInput" class="form-label">Description</label>
                                    <textarea id="descriptionInput" class="form-control" name="description" rows="4"
                                        placeholder="Enter Description"></textarea>
                                    <small class="text-body float-start error-message-div description-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="fileStatusInput" class="form-label">{{ ucfirst($folderType) }} Status</label>
                                <select class="form-select" name="file_status" id="fileStatusInput">
                                    <option selected disabled>Select {{ ucfirst($folderType) }} Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                                <small class="text-body float-start error-message-div file_status-error"
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
                    <div class="progress uploadProgress" style="display: none; height: 10px;">
                        <div class="progress-bar" role="progressbar" style="width: 0%; height:10px; font-size: 7px"
                            aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            0%
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ////////////// FilePreviewer modal ////////////// --}}
    <div class="modal fade" id="FilePreviewer" tabindex="-1" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <input type="hidden" id="updateFileId" name="file_id">
                <input type="hidden" class="uploaded-file-size" name="file_size">
                <input type="hidden" class="uploaded-file-name-input" name="file_name">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">{{ ucfirst($folderType) }} Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="modal-body d-flex justify-content-center align-items-center">
                        <img id="modalImage" class="img-fluid" style="display:none;" src="">
                        <video id="modalVideo" width="100%" style="display:none;" controls>
                            <source id="modalVideoSource" src="" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                </div>
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
                </form>
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
            table = $('#files-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('files.index', [request()->route('folder_id'), request()->route('type')]) }}",
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
                        name: 'user_name'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'date',
                        name: 'date'
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
                var id = $(this).data('id')
                $.ajax({
                    url: "{{ url('files/show') }}/" + id,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            $('#userNameInput').val(response.data.user_name);
                            $('#descriptionInput').text(response.data.description);
                            $('#fileStatusInput').val(response.data.file_status);
                            $('#updateFileId').val(response.data.id);
                            $('#UpdateFileModal').modal('show');
                        }
                    }
                })
            });

            $(document).on('click', '.file-previewer', function(e) {
                e.preventDefault();
                var src = $(this).attr('src');
                var type = $(this).data('type');
                if (type == 'image') {
                    $('#modalVideo').hide();
                    $('#modalImage').show().attr('src', src);
                } else {
                    $('#modalImage').hide();
                    $('#modalVideo').show().find('source').attr('src', src);
                    $('#modalVideo')[0].load();
                }
                $('#FilePreviewer').modal('show');
            });

            $(document).on('click', '.file-status-modal', function(e) {
                e.preventDefault();
                var status = $(this).data('status');
                var id = $(this).data('id');
                $('#fileStatusModalInput').val(status);
                $('#fileIdModalInput').val(id);
                $('#changeStatusModal').modal('show');
            });

            $('#FilePreviewer').on('hidden.bs.modal', function() {
                var video = $('#modalVideo')[0];
                if (video) {
                    video.pause();
                    video.currentTime = 0;
                }
            });

            $('#createFileForm').submit(function(e) {
                e.preventDefault();
                clearErrors();
                var formData = new FormData(this);
                let csrfToken = $('meta[name="csrf-token"]').attr('content');
                var submitBtn = $("#storeButton");
                formData.append("folder_type", "{{ request()->route('type') }}");
                showButtonLoader(submitBtn);
                document.querySelectorAll('.uploadProgress').forEach(element => {
                    element.style.display = 'block';
                });
                var progressBar = document.querySelectorAll('.progress-bar');
                var fileInput = document.getElementById('file');
                // Step 1: Upload video to server
                $.ajax({
                    url: '/files/upload-file',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        // Step 2: Upload to BunnyCDN
                        uploadToBunnyCreate(response.file_path, response.file_name, response
                            .upload_id, submitBtn, progressBar, table, response.file_size,
                            response.file_name_with_extension);
                    },
                    error: function(response) {
                        let errorMessages = response.responseJSON.errors;
                        Object.keys(errorMessages).forEach(function(key) {
                            let inputField = $(`.${key}-error`);
                            inputField.attr('hidden', false);
                            inputField.text(errorMessages[key][0]);
                        });
                        hideButtonLoader(submitBtn);
                    }
                });
            });

            function uploadToBunnyCreate(filePath, fileName, uploadId, submitBtn, progressBar, table, fileSize,
                fileNameWithExtension) {
                let csrfToken = $('meta[name="csrf-token"]').attr('content');
                var data = JSON.stringify({
                    file_path: filePath,
                    file_name: fileName,
                    upload_id: uploadId,
                    file_size: fileSize,
                    file_name_with_extension: fileNameWithExtension,
                    user_name: $('#UserName').val(),
                    description: $('#description').val()
                });
                // Step 3: Track BunnyCDN upload progress
                trackBunnyUploadProgress(uploadId, progressBar);

                $.ajax({
                    url: "{{ route('files.store', [request()->route('folder_id'), request()->route('type')]) }}",
                    type: 'POST',
                    data: data,
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        showAlertMessage(response.message);
                        resetForm('createFileForm');
                        progressBar.forEach(element => {
                            element.style.width = 0 + '%';
                            element.textContent = 0 + '%';
                        });
                        hideButtonLoader(submitBtn);
                        $('#CreateFileModal').modal('hide');
                        table.draw();
                    },
                    error: function(xhr) {
                        hideButtonLoader(submitBtn);
                        showErrorMessage('Error uploading file to BunnyCDN');
                    }
                });
            }

            function trackBunnyUploadProgress(uploadId, progressBars) {
                var interval = setInterval(function() {
                    fetch(`/files/uploaded-file-status/${uploadId}`)
                        .then(response => response.json())
                        .then(data => {
                            let progress = data.progress;
                            progressBars.forEach(progressBar => {
                                progressBar.style.width = progress + '%';
                                progressBar.textContent = progress + '%';
                            });

                            if (progress >= 100) {
                                clearInterval(interval);
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching progress:', error);
                        });
                }, 1000);
            }

            $('#updateFileForm').submit(function(e) {
                e.preventDefault();
                clearErrors();
                var formData = new FormData(this);
                let csrfToken = $('meta[name="csrf-token"]').attr('content');
                var submitBtn = $("#updateButton");
                formData.append("folder_type", "{{ request()->route('type') }}");
                showButtonLoader(submitBtn);
                var id = $('#updateFileId').val();
                document.querySelectorAll('.uploadProgress').forEach(element => {
                    element.style.display = 'block';
                });
                var progressBar = document.querySelectorAll('.progress-bar');
                if ($('#fileInput')[0].files.length > 0) {
                    // Step 1: Upload video to server
                    $.ajax({
                        url: '/files/upload-file',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function(response) {
                            console.log(response);

                            // Step 2: Upload to BunnyCDN
                            uploadToBunnyUpdate(response.file_path, response.file_name, response
                                .upload_id, submitBtn, progressBar, table, id, response
                                .file_size, response.file_name_with_extension);
                        },
                        error: function(response) {
                            let errorMessages = response.responseJSON.errors;
                            Object.keys(errorMessages).forEach(function(key) {
                                let inputField = $(`.${key}-error`);
                                inputField.attr('hidden', false);
                                inputField.text(errorMessages[key][0]);
                            });
                            hideButtonLoader(submitBtn);
                        }
                    });
                } else {
                    $.ajax({
                        url: "{{ url('files/update-without-file') }}/" + id,
                        type: 'POST',
                        processData: false,
                        contentType: false,
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function(response) {
                            showAlertMessage(response.message);
                            resetForm('updateFileForm');
                            hideButtonLoader(submitBtn);
                            $('#UpdateFileModal').modal('hide');
                            table.draw();
                        },
                        error: function(response) {
                            let errorMessages = response.responseJSON.errors;
                            Object.keys(errorMessages).forEach(function(key) {
                                let inputField = $(`.${key}-error`);
                                inputField.attr('hidden', false);
                                inputField.text(errorMessages[key][0]);
                            });
                            hideButtonLoader(submitBtn);
                        }
                    });
                }
            });

            function uploadToBunnyUpdate(filePath, fileName, uploadId, submitBtn, progressBar, table, fileId,
                fileSize, fileNameWithExtension) {
                let csrfToken = $('meta[name="csrf-token"]').attr('content');
                var data = JSON.stringify({
                    file_path: filePath,
                    file_name: fileName,
                    upload_id: uploadId,
                    file_id: fileId,
                    file_size: fileSize,
                    file_name_with_extension: fileNameWithExtension,
                    file_status: $('#fileStatusInput').val(),
                    user_name: $('#userNameInput').val(),
                    description: $('#descriptionInput').val()
                });
                // Step 3: Track BunnyCDN upload progress
                trackBunnyUploadProgress(uploadId, progressBar);
                $.ajax({
                    url: "{{ url('files/update') }}/" + fileId,
                    type: 'POST',
                    data: data,
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        showAlertMessage(response.message);
                        resetForm('updateFileForm');
                        progressBar.forEach(element => {
                            element.style.width = 0 + '%';
                            element.textContent = 0 + '%';
                        });
                        hideButtonLoader(submitBtn);
                        $('#UpdateFileModal').modal('hide');
                        table.draw();
                    },
                    error: function(xhr) {
                        hideButtonLoader(submitBtn);
                        showErrorMessage('Error uploading file to BunnyCDN');
                    }
                });
            }

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
            });

            $('#updateStatusForm').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                let csrfToken = $('meta[name="csrf-token"]').attr('content');
                var submitBtn = $("#updateStatusButton");
                $.ajax({
                    url: "{{ route('files.change.status') }}",
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

            $('#filesLink,#filesLinkInput').parent().attr('hidden', true);

            document.querySelectorAll('.progress-bar').forEach(progressBar => {
                progressBar.style.width = '0%';
                progressBar.textContent = '0%';
                progressBar.setAttribute('aria-valuenow', 0);
            });

            document.querySelectorAll('.uploadProgress').forEach(element => {
                element.style.display = 'none';
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
        $('input[type="file"]').on('change', function() {
            const fileInput = $(this)[0];
            const fileNameDisplay = $(this).closest('.col-md-6').find('.uploaded-file-name');
            if (fileInput.files && fileInput.files[0]) {
                let fileName = fileInput.files[0].name;
                const fileExtension = fileName.split('.').pop();
                if (fileName.length > 40) {
                    const nameWithoutExtension = fileName.substring(0, fileName.lastIndexOf('.'));
                    fileName = nameWithoutExtension.substring(0, 20) + '...' + '.' +
                        fileExtension;
                }
                fileNameDisplay.attr('hidden', false);
                fileNameDisplay.text(`Uploaded {{ ucfirst($folderType) }}: ${fileName}`);
                var fileSize = fileInput.files[0].size;
                var fileSizeInKB = (fileSize / (1024 * 1024)).toFixed(2);
                $('.uploaded-file-size').val(fileSizeInKB);
                $('.uploaded-file-name-input').val(fileName);
            } else {
                fileNameDisplay.text('');
            }
        });
    </script>
@endsection
