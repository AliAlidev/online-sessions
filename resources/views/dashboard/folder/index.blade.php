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

        .update-folder:hover i,
        .delete-folder:hover i {
            color: white !important;
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
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- DataTable with Buttons -->
        <h5 class="mb-0">{{ Breadcrumbs::render('folder', request()->route('event_slug')) }}</h5>
        <div class="card">

            <div class="row card-header flex-column flex-md-row pb-0">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mt-0"
                    style="gap: 10px">
                    <div class="dt-buttons btn-group flex-wrap mb-0">
                        @can('create_folder')
                            <button class="btn btn-sm btn-primary" data-bs-target="#CreateFolderModal" data-bs-toggle="modal"
                                type="button"><span><span class="d-flex align-items-center gap-2">
                                        <span class="d-none d-sm-inline-block">Add New Folder</span>
                                        <i class="icon-base bx bx-plus icon-sm"></i>
                                    </span>
                                </span>
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="card-datatable">
                <div class="justify-content-between dt-layout-table" style="padding: 20px">
                    <table id="folders-datatable" class="table table-responsive table-hover text-nowrap"
                        style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="control dt-orderable-none">ID</th>
                                <th class="control dt-orderable-none">Event</th>
                                <th class="control dt-orderable-none">Folder Name</th>
                                <th class="control dt-orderable-none">Bunny Folder Name</th>
                                <th class="control dt-orderable-none">Folder Type</th>
                                <th class="control dt-orderable-none">Description</th>
                                <th class="control dt-orderable-none">Folder Thumbnail</th>
                                <th class="control dt-orderable-none">Folder Link</th>
                                <th class="control dt-orderable-none">Order</th>
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

    {{-- ////////////// create folder modal ////////////// --}}
    <div class="modal fade" id="CreateFolderModal" tabindex="-1" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="createFolder">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCenterTitle">Create New Folder</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="file" id="create-image-compressed" name="folder_thumbnail" hidden>
                        <div class="row mb-5">
                            <div class="col-md-6">
                                <label for="folderOrder" class="form-label">Folder Order</label>
                                <input type="number" id="folderOrder" class="form-control" name="order" min="1"
                                    value="1" placeholder="Enter Folder Order">
                                <small class="text-body float-start error-message-div folder_order-error"
                                    style="color: #ff0000 !important" hidden></small>
                            </div>
                        </div>
                        <div class="row mb-5">
                            <div class="col-md-6">
                                <label for="folderName" class="form-label">Folder Name</label>
                                <input type="text" id="folderName" class="form-control" name="folder_name"
                                    placeholder="Enter Folder Name">
                                <small class="text-body float-start error-message-div folder_name-error"
                                    style="color: #ff0000 !important" hidden></small>
                            </div>
                            <div class="col-md-6">
                                <label for="folderThumbnail" class="form-label">Folder Thumbnail</label>
                                <input type="file" id="folderThumbnail" class="form-control"
                                    data-compressed-file-id="create-image-compressed"
                                    accept="image/jpeg,png,jpg,gif,svg,webp">
                                <div class="mt-2 preview-container">
                                </div>
                                <small class="text-body float-start uploaded-file-name"
                                    style="color: #000; font-style: italic;"></small>
                                <small class="text-body float-start error-message-div folder_thumbnail-error"
                                    style="color: #ff0000 !important" hidden></small>
                            </div>
                        </div>
                        <div class="row mb-5">
                            <div class="col-md-6">
                                <label for="folderType" class="form-label">Folder Type</label>
                                <select class="form-select" id="folderType" name="folder_type">
                                    <option selected disabled>Select Folder Type</option>
                                    <option value="image">Image</option>
                                    <option value="video">Video</option>
                                    <option value="link">Link</option>
                                    <option value="fake">Fake</option>
                                </select>
                                <small class="text-body float-start error-message-div folder_type-error"
                                    style="color: #ff0000 !important" hidden></small>
                            </div>
                            <div class="col-md-6" hidden>
                                <label for="folderLink" class="form-label">Folder Link</label>
                                <input type="url" id="folderLink" class="form-control" name="folder_link"
                                    placeholder="Enter Folder Link">
                                <small class="text-body float-start error-message-div folder_link-error"
                                    style="color: #ff0000 !important" hidden></small>
                            </div>
                        </div>
                        <div class="row mb-5">
                            <div class="col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea id="description" class="form-control" name="description" rows="4" placeholder="Enter Description"></textarea>
                                <small id="char-count" class="form-text text-muted">0 / 300 characters</small>
                                <small class="text-body float-start error-message-div description-error"
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

    {{-- ////////////// update folder modal ////////////// --}}
    <div class="modal fade" id="UpdateFolderModal" tabindex="-1" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="updateFolderForm">
                    <input type="hidden" name="folder_id" id="folderIdInput">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCenterTitle">Update Folder</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="file" id="update-image-compressed" name="folder_thumbnail" hidden>
                        <div class="row mb-5">
                            <div class="col-md-6" style="display: none">
                                <label for="folderOrderInput" class="form-label">Folder Order</label>
                                <input type="number" id="folderOrderInput" class="form-control" name="order"
                                    min="1" value="1" placeholder="Enter Folder Order">
                                <small class="text-body float-start error-message-div folder_order-error"
                                    style="color: #ff0000 !important" hidden></small>
                            </div>
                        </div>
                        <div class="row mb-5">
                            <div class="col-md-6">
                                <label for="folderNameInput" class="form-label">Folder Name</label>
                                <input type="text" id="folderNameInput" class="form-control" name="folder_name"
                                    placeholder="Enter Folder Name">
                                <small class="text-body float-start error-message-div folder_name-error"
                                    style="color: #ff0000 !important" hidden></small>
                            </div>
                            <div class="col-md-6">
                                <label for="folderThumbnailInput" class="form-label">Folder Thumbnail</label>
                                <input type="file" id="folderThumbnailInput" class="form-control"
                                    data-compressed-file-id="update-image-compressed"
                                    accept="image/jpeg,png,jpg,gif,svg,webp">
                                <div class="mt-2 preview-container-update">
                                    <img width="125px" height="125px" style="object-fit:contain">
                                </div>
                                <small class="text-body float-start uploaded-file-name"
                                    style="color: #000; font-style: italic;"></small>
                                <small class="text-body float-start error-message-div folder_thumbnail-error"
                                    style="color: #ff0000 !important" hidden></small>
                            </div>
                        </div>
                        <div class="row mb-5">
                            @if (getUserType() != 'event-user')
                                <div class="col-md-6" id="folderTypeDiv">
                                    <label for="folderTypeInput" class="form-label">Folder Type</label>
                                    <select class="form-select" id="folderTypeInput" name="folder_type">
                                        <option selected disabled>Select Folder Type</option>
                                        <option value="image">Image</option>
                                        <option value="video">Video</option>
                                        <option value="link">Link</option>
                                        <option value="fake">Fake</option>
                                    </select>
                                    <small class="text-body float-start error-message-div folder_type-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                            @endif
                            <div class="col-md-6" hidden>
                                <label for="folderLinkInput" class="form-label">Folder Link</label>
                                <input type="text" id="folderLinkInput" class="form-control" name="folder_link"
                                    placeholder="Enter Folder Link">
                                <small class="text-body float-start error-message-div folder_link-error"
                                    style="color: #ff0000 !important" hidden></small>
                            </div>
                        </div>
                        <div class="row mb-5">
                            <div class="col-md-12">
                                <label for="descriptionInput" class="form-label">Description</label>
                                <textarea id="descriptionInput" class="form-control" name="description" rows="4"
                                    placeholder="Enter Description"></textarea>
                                <small id="char-count" class="form-text text-muted">0 / 300 characters</small>
                                <small class="text-body float-start error-message-div description-error"
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
            table = $('#folders-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('folders.index', request()->route('event_slug')) }}",
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
                        data: 'folder_name',
                        name: 'folder_name'
                    },
                    {
                        data: 'bunny_folder_name',
                        name: 'bunny_folder_name',
                        visible: false
                    },
                    {
                        data: 'folder_type',
                        name: 'folder_type'
                    },
                    {
                        data: 'description',
                        name: 'description',
                        visible: false
                    },
                    {
                        data: 'folder_thumbnail',
                        name: 'folder_thumbnail'
                    },
                    {
                        data: 'folder_link',
                        name: 'folder_link'
                    },
                    {
                        data: 'order',
                        name: 'order'
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

            $(document).on('click', '.delete-folder', function(e) {
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

            $(document).on('click', '.update-folder', function(e) {
                e.preventDefault();
                var id = $(this).data('id')
                $.ajax({
                    url: "{{ route('folders.show', request()->route('event_slug')) }}/" + id,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            $('#folderNameInput').val(response.data.folder_name);
                            $('#folderTypeInput').val(response.data.folder_type);
                            $('#folderLinkInput').val(response.data.folder_link);
                            if (response.data.folder_thumbnail) {
                                $('.preview-container-update img').attr('src', "\\" + response
                                    .data.folder_thumbnail);
                                $('.preview-container-update').show();
                            } else {
                                $('.preview-container-update').hide();
                            }
                            if (response.data.folder_type == 'link')
                                $('#folderLinkInput').parent().hide();
                            $('#descriptionInput').text(response.data.description);
                            $('#folderIdInput').val(response.data.id);
                            if (response.data.folder_name != 'Guest Upload') {
                                $('#folderOrderInput').parent().show();
                                $('#folderOrderInput').val(response.data.order);
                            } else {
                                $('#folderOrderInput').parent().hide();
                            }

                            if (response.data.can_update_folder_name)
                                $('#folderTypeDiv').show();
                            else
                                $('#folderTypeDiv').hide();

                            $('#UpdateFolderModal').modal('show');
                        }
                    }
                })
            });

            $('#createFolder').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                let csrfToken = $('meta[name="csrf-token"]').attr('content');
                var submitBtn = $("#storeButton");
                var spinner = submitBtn.find('#spinner');

                $.ajax({
                    url: "{{ route('folders.store', request()->route('event_slug')) }}",
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
                        $('#CreateFolderModal').modal('hide');
                        resetForm('createFolder');
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

            $('#updateFolderForm').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                let csrfToken = $('meta[name="csrf-token"]').attr('content');
                var submitBtn = $("#updateButton");
                var spinner = submitBtn.find('#spinner');
                var id = $('#folderIdInput').val();
                $.ajax({
                    url: "{{ route('folders.update', request()->route('event_slug')) }}/" + id,
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
                        $('#UpdateFolderModal').modal('hide');
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

            $('#UpdateFolderModal,#CreateFolderModal').on('hidden.bs.modal', function() {
                var form = $(this).find('form').attr('id');
                resetForm(form);
            });

            $(document).on('change', '.folder_visibility', function(e) {
                e.preventDefault();
                var url = $(this).data('url');
                $.ajax({
                    url: url,
                    type: 'get',
                    success: function(response) {
                        if (response.success) {
                            showAlertMessage(response.message)
                            table.draw();
                        }
                    }
                })
            });
        });

        function resetForm(form) {
            document.getElementById(form).reset();
            $('.error-message-div').each(function() {
                $(this).attr('hidden', true);
            });
            $('.uploaded-file-name').each(function() {
                $(this).attr('hidden', true);
            });

            $('.preview-container img').attr('src', null).attr('hidden', true);

            $('#folderLink,#folderLinkInput').parent().attr('hidden', true);
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

    <script>
        $(document).ready(function() {
            $('#folderType,#folderTypeInput').on('change', function() {
                var selectedValue = $(this).val();
                if (selectedValue === 'link') {
                    $('#folderLink,#folderLinkInput').parent().attr('hidden', false);
                } else {
                    $('#folderLink,#folderLinkInput').parent().attr('hidden', true);
                }
            });
        })
    </script>

    <script src="{{ asset('assets/js/compressor.min.js') }}"></script>

    <script>
        $('#folderThumbnail, #folderThumbnailInput').on('change', function() {
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
                fileNameDisplay.text(`File: ${fileName}`);
                if (fileInput.files[0].type.startsWith('image/')) {
                    const previewContainer = $(this).attr('id') == 'folderThumbnailInput' ? $(this).closest(
                        '.col-md-6').find('.preview-container-update') : $(this).closest('.col-md-6').find(
                        '.preview-container');
                    previewContainer.empty();
                    const reader = new FileReader();
                    $(previewContainer).show();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.width = '125px';
                        img.style.height = '125px';
                        img.className = 'img-thumbnail';
                        previewContainer.append(img);
                    };
                    reader.readAsDataURL(fileInput.files[0]);
                }
                compressImages(this.id, $(this).data('compressedFileId'));
            } else {
                fileNameDisplay.text('');
            }
        });

        function compressImages(fileId, compressedFileId) {
            const input = document.getElementById(fileId);
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
                        document.getElementById(compressedFileId).files = dataTransfer.files;
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
                            document.getElementById(compressedFileId).files =
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
