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
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xl">
                <h5 class="mb-0">{{ __('Bunny Profile Setting') }}</h5>
                <form id="saveSettingForm">
                    <div class="card mb-6">
                        <div class="card-header">
                            <h5 class="mb-0">Global Setting</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <label for="api-key" class="form-label">Api Key</label>
                                    <input type="text" id="api-key" class="form-control" name="global[api_key]"
                                        placeholder="Enter Api Key"
                                        value="{{ isset($globalSetting['api_key']) ? $globalSetting['api_key'] : '' }}">
                                    <small class="text-body float-start error-message-div api_key-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-6">
                        <div class="card-header">
                            <h5 class="mb-0">Bunny Image</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <label for="storage-zone-name" class="form-label">Storage Zone Name</label>
                                    <input type="text" id="storage-zone-name" class="form-control"
                                        name="image[storage_zone_name]" placeholder="Enter Storage Zone Name"
                                        value="{{ isset($imageSetting['storage_zone_name']) ? $imageSetting['storage_zone_name'] : '' }}">
                                    <small class="text-body float-start error-message-div storage_zone_name-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <label for="storage-access-token" class="form-label">Storage Access Token</label>
                                    <input type="text" id="storage-access-token" class="form-control"
                                        name="image[storage_access_token]" placeholder="Enter Storage Access Token"
                                        value="{{ isset($imageSetting['storage_access_token']) ? $imageSetting['storage_access_token'] : '' }}">
                                    <small class="text-body float-start error-message-div storage_access_token-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <label for="image-pull-zone" class="form-label">Pull Zone</label>
                                    <input type="text" id="image-pull-zone" class="form-control"
                                        name="image[image_pull_zone]" placeholder="Enter Pull Zone"
                                        value="{{ isset($imageSetting['image_pull_zone']) ? $imageSetting['image_pull_zone'] : '' }}">
                                    <small class="text-body float-start error-message-div image_pull_zone-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-6">
                        <div class="card-header">
                            <h5 class="mb-0">Bunny Video</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-6">
                                <div class="col-md-8">
                                    <label for="video-api-key" class="form-label">Api Key</label>
                                    <input type="text" id="video-api-key" class="form-control"
                                        name="video[video_api_key]" placeholder="Enter Api Key"
                                        value="{{ isset($videoSetting['video_api_key']) ? $videoSetting['video_api_key'] : '' }}">
                                    <small class="text-body float-start error-message-div video_api_key-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <div class="col-md-8">
                                    <label for="video-library-id" class="form-label">Library ID</label>
                                    <input type="text" id="video-library-id" class="form-control"
                                        name="video[video_library_id]" placeholder="Enter Library ID"
                                        value="{{ isset($videoSetting['video_library_id']) ? $videoSetting['video_library_id'] : '' }}">
                                    <small class="text-body float-start error-message-div video_library_id-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <div class="col-md-8">
                                    <label for="stream-pull-zone" class="form-label">Stream Pull Zone</label>
                                    <input type="text" id="stream-pull-zone" class="form-control"
                                        name="video[stream_pull_zone]" placeholder="Enter Stream Pull Zone"
                                        value="{{ isset($videoSetting['stream_pull_zone']) ? $videoSetting['stream_pull_zone'] : '' }}">
                                    <small class="text-body float-start error-message-div stream_pull_zone-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="float: right" id="storeButton">Save
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

        $('#saveSettingForm').submit(function(e) {
            e.preventDefault();
            clearErrors();
            var formData = new FormData(this);
            let csrfToken = $('meta[name="csrf-token"]').attr('content');
            var submitBtn = $("#storeButton");
            var spinner = $(submitBtn).find("#spinner");

            $.ajax({
                url: "{{ route('settings.bunny') }}",
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
                    if (response.success) {
                        window.location.href = response.url;
                    } else {
                        showErrorMessage(response.message);
                    }

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
        $(document).ready(function(){
            setTimeout(() => {
                $('.bg-success').hide();
            }, 5000);
        })
    </script>
@endsection
