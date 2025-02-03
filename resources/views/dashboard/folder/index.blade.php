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
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- DataTable with Buttons -->
        <div class="card">
            <div class="row card-header flex-column flex-md-row pb-0">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="mb-0">{{ __('Event Folders List') }}</h5>
                </div>
                <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mt-0" style="gap: 10px">
                    <a href="javascript:history.back()" class="btn btn-label-primary btn-sm">Back</a>
                    <div class="dt-buttons btn-group flex-wrap mb-0">
                        <button class="btn btn-sm btn-primary" data-bs-target="#CreateFolderModal" data-bs-toggle="modal"
                            type="button"><span><span class="d-flex align-items-center gap-2">
                                    <span class="d-none d-sm-inline-block">Add</span>
                                    <i class="icon-base bx bx-plus icon-sm"></i>
                                </span>
                            </span>
                        </button>
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
                                <th class="control dt-orderable-none">Folder Type</th>
                                <th class="control dt-orderable-none">Description</th>
                                <th class="control dt-orderable-none">Folder Thumbnail</th>
                                <th class="control dt-orderable-none">Folder Link</th>
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
    <div class="modal fade" id="CreateFolderModal" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="createFolder">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCenterTitle">Create New Folder</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-6">
                            <div class="col-md-6">
                                <label for="folderName" class="form-label">Folder Name</label>
                                <input type="text" id="folderName" class="form-control" name="folder_name"
                                    placeholder="Enter Folder Name">
                                <small class="text-body float-start error-message-div folder_name-error"
                                    style="color: #ff0000 !important" hidden></small>
                            </div>
                            <div class="col-md-6">
                                <label for="folderThumbnail" class="form-label">Folder Thumbnail</label>
                                <input type="file" id="folderThumbnail" class="form-control" name="folder_thumbnail"
                                    accept="image/jpeg,png,jpg,gif,svg,webp">
                                <small class="text-body float-start uploaded-file-name"
                                    style="color: #000; font-style: italic;"></small>
                                <small class="text-body float-start error-message-div folder_thumbnail-error"
                                    style="color: #ff0000 !important" hidden></small>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-md-6">
                                <label for="folderType" class="form-label">Folder Type</label>
                                <select class="form-select" id="folderType" name="folder_type">
                                    <option selected disabled>Select Folder Type</option>
                                    <option value="image">Image</option>
                                    <option value="video">Video</option>
                                    <option value="link">Link</option>
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
                        <div class="row mb-6">
                            <div class="col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea id="description" class="form-control" name="description" rows="4" placeholder="Enter Description"></textarea>
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
    <div class="modal fade" id="UpdateFolderModal" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="updateFolderForm">
                    <input type="hidden" name="folder_id" id="folderIdInput">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCenterTitle">Update Folder</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-6">
                            <div class="col-md-6">
                                <label for="folderName" class="form-label">Folder Name</label>
                                <input type="text" id="folderNameInput" class="form-control" name="folder_name"
                                    placeholder="Enter Folder Name">
                                <small class="text-body float-start error-message-div folder_name-error"
                                    style="color: #ff0000 !important" hidden></small>
                            </div>
                            <div class="col-md-6">
                                <label for="folderThumbnail" class="form-label">Folder Thumbnail</label>
                                <input type="file" id="folderThumbnailInput" class="form-control"
                                    name="folder_thumbnail" accept="image/jpeg,png,jpg,gif,svg,webp">
                                <small class="text-body float-start uploaded-file-name"
                                    style="color: #000; font-style: italic;"></small>
                                <small class="text-body float-start error-message-div folder_thumbnail-error"
                                    style="color: #ff0000 !important" hidden></small>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-md-6">
                                <label for="folderType" class="form-label">Folder Type</label>
                                <select class="form-select" id="folderTypeInput" name="folder_type">
                                    <option selected disabled>Select Folder Type</option>
                                    <option value="image">Image</option>
                                    <option value="video">Video</option>
                                    <option value="link">Link</option>
                                </select>
                                <small class="text-body float-start error-message-div folder_type-error"
                                    style="color: #ff0000 !important" hidden></small>
                            </div>
                            <div class="col-md-6" hidden>
                                <label for="folderLink" class="form-label">Folder Link</label>
                                <input type="text" id="folderLinkInput" class="form-control" name="folder_link"
                                    placeholder="Enter Folder Link">
                                <small class="text-body float-start error-message-div folder_link-error"
                                    style="color: #ff0000 !important" hidden></small>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea id="descriptionInput" class="form-control" name="description" rows="4"
                                    placeholder="Enter Description"></textarea>
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
                ajax: "{{ route('folders.index', request()->route('event_id')) }}",
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
                        data: 'event_id',
                        name: 'event_id'
                    },
                    {
                        data: 'folder_name',
                        name: 'folder_name'
                    },
                    {
                        data: 'folder_type',
                        name: 'folder_type'
                    },
                    {
                        data: 'description',
                        name: 'description'
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
                        deleteItem(url, table);
                    }
                });
            });

            $(document).on('click', '.update-folder', function(e) {
                e.preventDefault();
                var id = $(this).data('id')
                $.ajax({
                    url: "{{ url('folders/show') }}/" + id,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            $('#folderNameInput').val(response.data.folder_name);
                            $('#folderTypeInput').val(response.data.folder_type);
                            $('#folderLinkInput').val(response.data.folder_link);
                            if (response.data.folder_type == 'link')
                                $('#folderLinkInput').parent().attr('hidden', false);
                            $('#descriptionInput').text(response.data.description);
                            $('#folderIdInput').val(response.data.id);
                            $('#UpdateFolderModal').modal('show');
                        }
                    }
                })
            });

            function deleteItem(url, table) {
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            table.draw();
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

            $('#createFolder').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                let csrfToken = $('meta[name="csrf-token"]').attr('content');
                var submitBtn = $("#storeButton");
                var spinner = submitBtn.find('#spinner');

                $.ajax({
                    url: "{{ route('folders.store', request()->route('event_id')) }}",
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
                var id = $('#FolderId').val();
                $.ajax({
                    url: "{{ url('folders/update') }}/" + id,
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
        });

        function resetForm(form) {
            document.getElementById(form).reset();
            $('.error-message-div').each(function() {
                $(this).attr('hidden', true);
            });
            $('.uploaded-file-name').each(function() {
                $(this).attr('hidden', true);
            });

            $('#folderLink,#folderLinkInput').parent().attr('hidden', true);
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
                fileNameDisplay.text(`Uploaded File: ${fileName}`);
            } else {
                fileNameDisplay.text('');
            }
        });
    </script>
@endsection
