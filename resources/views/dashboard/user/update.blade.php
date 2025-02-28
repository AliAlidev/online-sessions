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
                    <h5 class="mb-0">{{ Breadcrumbs::render('update-user', $user) }}</h5>
                    <div class="card mb-6">
                        <div class="card-header">
                            <h5 class="mb-0">User Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col mb-6">
                                    <label for="nameWithTitle" class="form-label">Full Name</label>
                                    <input type="text" name="full_name" class="form-control" value="{{ $user->full_name }}"
                                        placeholder="Enter Full Name">
                                    <small class="text-body float-start error-message-div full_name-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                                <div class="col mb-6">
                                    <label for="nameWithTitle" class="form-label">User Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="Enter Name" value="{{ $user->name }}">
                                    <small class="text-body float-start error-message-div name-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col mb-6">
                                    <label for="nameWithTitle" class="form-label">Email</label>
                                    <input type="text" name="email" class="form-control" placeholder="Enter Email" value="{{ $user->email }}">
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
                                    <input type="text" name="phone" class="form-control" placeholder="Enter Phone" value="{{ $user->phone }}">
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
                                            <input class="form-check-input" type="checkbox" {{ in_array('create_event', $permissions) ? 'checked' : '' }}
                                                id="create_event" name="permissions[]" value="create_event">
                                            <label class="form-check-label" for="create_event">Create</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" {{ in_array('update_event', $permissions) ? 'checked' : '' }}
                                                id="update_event" name="permissions[]" value="update_event">
                                            <label class="form-check-label" for="update_event">Update</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" {{ in_array('delete_event', $permissions) ? 'checked' : '' }}
                                                id="delete_event" name="permissions[]" value="delete_event">
                                            <label class="form-check-label" for="delete_event">Delete</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <h5>Roles Permissions</h5>
                                    <div class="row mb-6" style="margin: 3px">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" {{ in_array('create_role', $permissions) ? 'checked' : '' }}
                                                id="create_role" name="permissions[]" value="create_role">
                                            <label class="form-check-label" for="create_role">Create</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" {{ in_array('update_role', $permissions) ? 'checked' : '' }}
                                                id="update_role" name="permissions[]" value="update_role">
                                            <label class="form-check-label" for="update_role">Update</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" {{ in_array('delete_role', $permissions) ? 'checked' : '' }}
                                                id="delete_role" name="permissions[]" value="delete_role">
                                            <label class="form-check-label" for="delete_role">Delete</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <h5>Clients Permissions</h5>
                                    <div class="row mb-6" style="margin: 3px">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" {{ in_array('create_client', $permissions) ? 'checked' : '' }}
                                                id="create_client" name="permissions[]" value="create_client">
                                            <label class="form-check-label" for="create_client">Create</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" {{ in_array('update_client', $permissions) ? 'checked' : '' }}
                                                id="update_client" name="permissions[]" value="update_client">
                                            <label class="form-check-label" for="update_client">Update</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" {{ in_array('delete_client', $permissions) ? 'checked' : '' }}
                                                id="delete_client" name="permissions[]" value="delete_client">
                                            <label class="form-check-label" for="delete_client">Delete</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <h5>Folders Permissions</h5>
                                    <div class="row mb-6" style="margin: 3px">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" {{ in_array('create_folder', $permissions) ? 'checked' : '' }}
                                                id="create_folder" name="permissions[]" value="create_folder">
                                            <label class="form-check-label" for="create_folder">Create</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" {{ in_array('update_folder', $permissions) ? 'checked' : '' }}
                                                id="update_folder" name="permissions[]" value="update_folder">
                                            <label class="form-check-label" for="update_folder">Update</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" {{ in_array('delete_folder', $permissions) ? 'checked' : '' }}
                                                id="delete_folder" name="permissions[]" value="delete_folder">
                                            <label class="form-check-label" for="delete_folder">Delete</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <h5>Images Permissions</h5>
                                    <div class="row mb-6" style="margin: 3px">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" {{ in_array('upload_image', $permissions) ? 'checked' : '' }}
                                                id="upload_image" name="permissions[]" value="upload_image">
                                            <label class="form-check-label" for="upload_image">Upload Image</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" {{ in_array('delete_image', $permissions) ? 'checked' : '' }}
                                                id="delete_image" name="permissions[]" value="delete_image">
                                            <label class="form-check-label" for="delete_image">Delete Image</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" {{ in_array('update_image', $permissions) ? 'checked' : '' }}
                                                id="update_image" name="permissions[]" value="update_image">
                                            <label class="form-check-label" for="update_image">Update Image</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <h5>Videos Permissions</h5>
                                    <div class="row mb-6" style="margin: 3px">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" {{ in_array('upload_video', $permissions) ? 'checked' : '' }}
                                                id="upload_video" name="permissions[]" value="upload_video">
                                            <label class="form-check-label" for="upload_video">Upload Video</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" {{ in_array('delete_video', $permissions) ? 'checked' : '' }}
                                                id="delete_video" name="permissions[]" value="delete_video">
                                            <label class="form-check-label" for="delete_video">Delete Video</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" {{ in_array('update_video', $permissions) ? 'checked' : '' }}
                                                id="update_video" name="permissions[]" value="update_video">
                                            <label class="form-check-label" for="update_video">Update Video</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <h5>Others Permissions</h5>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" {{ in_array('approve_decline_image', $permissions) ? 'checked' : '' }}
                                            id="approve_decline_image" name="permissions[]" value="approve_decline_image">
                                        <label class="form-check-label" for="approve_decline_image">Approve/Decline Images</label>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" {{ in_array('approve_decline_video', $permissions) ? 'checked' : '' }}
                                            id="approve_decline_video" name="permissions[]" value="approve_decline_video">
                                        <label class="form-check-label" for="approve_decline_video">Approve/Decline Videos</label>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" {{ in_array('insights', $permissions) ? 'checked' : '' }}
                                            id="insights" name="permissions[]" value="insights">
                                        <label class="form-check-label" for="insights">Insights</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="float: right" id="storeButton">Update User
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
                url: "{{ route('users.update', request()->route('id')) }}",
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
