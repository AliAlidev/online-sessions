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
                <form id="updateUserForm">
                    <h5 class="mb-0">{{ Breadcrumbs::render('update-event-user', $user) }}</h5>
                    <div class="card mb-6">
                        <div class="card-header">
                            <h5 class="mb-0">User Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col mb-6">
                                    <label for="nameWithTitle" class="form-label">Full Name</label>
                                    <input type="text" name="full_name" class="form-control"
                                        value="{{ $user->full_name }}" placeholder="Enter Full Name">
                                    <small class="text-body float-start error-message-div full_name-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                                <div class="col mb-6">
                                    <label for="nameWithTitle" class="form-label">User Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="Enter Name"
                                        value="{{ $user->name }}">
                                    <small class="text-body float-start error-message-div name-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col mb-6">
                                    <label for="nameWithTitle" class="form-label">Email</label>
                                    <input type="text" name="email" class="form-control" placeholder="Enter Email"
                                        value="{{ $user->email }}">
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
                                    <input type="text" name="phone" class="form-control" placeholder="Enter Phone"
                                        value="{{ $user->phone }}">
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
                    <div style="display: flex; gap: 10px; justify-content: flex-end">
                        <a href="javascript:history.back()" class="btn btn-label-primary">Close</a>
                        <button type="submit" class="btn btn-primary" style="float: right" id="storeButton">Update User
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
    <script>
        function clearErrors() {
            $('.error-message-div').each(function() {
                $(this).attr('hidden', true);
            });
        }

        $('#updateUserForm').submit(function(e) {
            e.preventDefault();
            clearErrors();
            var formData = new FormData(this);
            let csrfToken = $('meta[name="csrf-token"]').attr('content');
            var submitBtn = $("#storeButton");
            var spinner = $(submitBtn).find("#spinner");

            $.ajax({
                url: "{{ route('events.users.update', request()->route('id')) }}",
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
