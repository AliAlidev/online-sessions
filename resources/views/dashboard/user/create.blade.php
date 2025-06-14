@extends('layouts.base')

@section('styles')
    <style>
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

        #basic-default-password2 {
            pointer-events: auto;
            z-index: 1;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xl">
                <form id="createUserForm">
                    <h5 class="mb-0">{{ Breadcrumbs::render('create-admin') }}</h5>
                    <div class="card mb-6">
                        <div class="card-header">
                            <h5 class="mb-0">Admin Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col mb-6">
                                    <label for="nameWithTitle" class="form-label">Full Name</label>
                                    <input type="text" name="full_name" class="form-control"
                                        placeholder="Enter Full Name">
                                    <small class="text-body float-start error-message-div full_name-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                                <div class="col mb-6">
                                    <label for="nameWithTitle" class="form-label">User Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="Enter Name">
                                    <small class="text-body float-start error-message-div name-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col mb-6">
                                    <label for="nameWithTitle" class="form-label">Email</label>
                                    <input type="text" name="email" class="form-control" placeholder="Enter Email">
                                    <small class="text-body float-start error-message-div email-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-password-toggle">
                                        <label class="form-label" for="password_id">Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="password_id"
                                                placeholder="············" name="password">
                                            <span id="basic-default-password2" class="input-group-text cursor-pointer">
                                                <i class="bx bx-hide"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <small class="text-body float-start error-message-div password-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                            </div>
                            <div class="row mb-3">
                                 <div class="col mb-6">
                                    <label for="nameWithTitle" class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control" placeholder="Enter Phone">
                                    <small class="text-body float-start error-message-div phone-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-password-toggle">
                                        <label class="form-label" for="password_confirmation_id">Password
                                            Confirmation</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="password_confirmation_id"
                                                placeholder="············" name="password_confirmation">
                                            <span id="basic-default-password2" class="input-group-text cursor-pointer">
                                                <i class="bx bx-hide"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <small class="text-body float-start error-message-div password-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-6">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ __('Permissions') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <h5>Events Permissions</h5>
                                    <div class="row mb-6" style="margin: 3px">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                id="create_event" name="permissions[]" value="create_event">
                                            <label class="form-check-label" for="create_event">Create</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                id="update_event" name="permissions[]" value="update_event">
                                            <label class="form-check-label" for="update_event">Update</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                id="delete_event" name="permissions[]" value="delete_event">
                                            <label class="form-check-label" for="delete_event">Delete</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                id="list_events" name="permissions[]" value="list_events">
                                            <label class="form-check-label" for="list_events">List</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <h5>Client Permissions</h5>
                                    <div class="row mb-6" style="margin: 3px">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                id="create_client" name="permissions[]" value="create_client">
                                            <label class="form-check-label" for="create_client">Create</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                id="update_client" name="permissions[]" value="update_client">
                                            <label class="form-check-label" for="update_client">Update</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                id="delete_client" name="permissions[]" value="delete_client">
                                            <label class="form-check-label" for="delete_client">Delete</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                id="list_clients" name="permissions[]" value="list_clients">
                                            <label class="form-check-label" for="list_clients">List</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <h5>Vendor Permissions</h5>
                                    <div class="row mb-6" style="margin: 3px">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                id="create_vendor" name="permissions[]" value="create_vendor">
                                            <label class="form-check-label" for="create_vendor">Create</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                id="update_vendor" name="permissions[]" value="update_vendor">
                                            <label class="form-check-label" for="update_vendor">Update</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                id="delete_vendor" name="permissions[]" value="delete_vendor">
                                            <label class="form-check-label" for="delete_vendor">Delete</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                id="list_vendors" name="permissions[]" value="list_vendors">
                                            <label class="form-check-label" for="list_vendors">List</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <h5>Folders Permissions</h5>
                                    <div class="row mb-6" style="margin: 3px">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                id="create_folder" name="permissions[]" value="create_folder">
                                            <label class="form-check-label" for="create_folder">Create</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                id="update_folder" name="permissions[]" value="update_folder">
                                            <label class="form-check-label" for="update_folder">Update</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                id="delete_folder" name="permissions[]" value="delete_folder">
                                            <label class="form-check-label" for="delete_folder">Delete</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                id="list_folders" name="permissions[]" value="list_folders">
                                            <label class="form-check-label" for="list_folders">List</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <h5>Event Types Permissions</h5>
                                    <div class="row mb-6" style="margin: 3px">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                id="create_event_type" name="permissions[]" value="create_event_type">
                                            <label class="form-check-label" for="create_event_type">Create</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                id="update_event_type" name="permissions[]" value="update_event_type">
                                            <label class="form-check-label" for="update_event_type">Update</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                id="delete_event_type" name="permissions[]" value="delete_event_type">
                                            <label class="form-check-label" for="delete_event_type">Delete</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                id="list_event_types" name="permissions[]" value="list_event_types">
                                            <label class="form-check-label" for="list_event_types">List</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <h5>Event Users Permissions</h5>
                                    <div class="row mb-6" style="margin: 3px">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                id="create_event_user" name="permissions[]" value="create_event_user">
                                            <label class="form-check-label" for="create_event_user">Create</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                id="update_event_user" name="permissions[]" value="update_event_user">
                                            <label class="form-check-label" for="update_event_user">Update</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                id="delete_event_user" name="permissions[]" value="delete_event_user">
                                            <label class="form-check-label" for="delete_event_user">Delete</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                id="list_event_users" name="permissions[]" value="list_event_users">
                                            <label class="form-check-label" for="list_event_users">List</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <h5>Images Permissions</h5>
                                    <div class="row mb-6" style="margin: 3px">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                id="upload_image" name="permissions[]" value="upload_image">
                                            <label class="form-check-label" for="upload_image">Upload Image</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                id="delete_image" name="permissions[]" value="delete_image">
                                            <label class="form-check-label" for="delete_image">Delete Image</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                id="update_image" name="permissions[]" value="update_image">
                                            <label class="form-check-label" for="update_image">Update Image</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <h5>Videos Permissions</h5>
                                    <div class="row mb-6" style="margin: 3px">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                id="upload_video" name="permissions[]" value="upload_video">
                                            <label class="form-check-label" for="upload_video">Upload Video</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                id="delete_video" name="permissions[]" value="delete_video">
                                            <label class="form-check-label" for="delete_video">Delete Video</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                id="update_video" name="permissions[]" value="update_video">
                                            <label class="form-check-label" for="update_video">Update Video</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <h5>Others Permissions</h5>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox"
                                            id="approve_decline_image" name="permissions[]" value="approve_decline_image">
                                        <label class="form-check-label" for="approve_decline_image">Approve/Decline Images</label>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox"
                                            id="approve_decline_video" name="permissions[]" value="approve_decline_video">
                                        <label class="form-check-label" for="approve_decline_video">Approve/Decline Videos</label>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox"
                                            id="insights" name="permissions[]" value="insights">
                                        <label class="form-check-label" for="insights">Insights</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="float: right" id="storeButton">Crete Admin
                        <span id="spinner" style="display:none;">
                            <i class="fa fa-spinner fa-spin"></i>
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function clearErrors() {
            $('.error-message-div').each(function() {
                $(this).attr('hidden', true);
            });
        }

        $('#createUserForm').submit(function(e) {
            e.preventDefault();
            clearErrors();
            var formData = new FormData(this);
            let csrfToken = $('meta[name="csrf-token"]').attr('content');
            var submitBtn = $("#storeButton");
            var spinner = $(submitBtn).find("#spinner");

            $.ajax({
                url: "{{ route('users.store') }}",
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
                    window.location.href = response.url;
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(field, messages) {
                            console.log(field);

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
    </script>
@endsection
