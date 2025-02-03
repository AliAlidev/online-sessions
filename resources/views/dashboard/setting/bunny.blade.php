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
                <form id="createClientForm">
                    <div class="card mb-6">
                        <div class="card-header">
                            <h5 class="mb-0">Bunny Image</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-6">
                                <div class="col-md-8">
                                    <label for="planner-name" class="form-label">Api Key</label>
                                    <input type="text" id="planner-name" class="form-control" name="planner_name"
                                        placeholder="Enter Api Key">
                                    <small class="text-body float-start error-message-div planner_name-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <div class="col-md-8">
                                    <label for="planner-name" class="form-label">Storage Zone Name</label>
                                    <input type="text" id="planner-name" class="form-control" name="planner_name"
                                        placeholder="Enter Api Key">
                                    <small class="text-body float-start error-message-div planner_name-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <div class="col-md-8">
                                    <label for="planner-name" class="form-label">Storage Region</label>
                                    <input type="text" id="planner-name" class="form-control" name="planner_name"
                                        placeholder="Enter Api Key">
                                    <small class="text-body float-start error-message-div planner_name-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <div class="col-md-8">
                                    <label for="planner-name" class="form-label">Event Folder</label>
                                    <input type="text" id="planner-name" class="form-control" name="planner_name"
                                        placeholder="Enter Api Key">
                                    <small class="text-body float-start error-message-div planner_name-error"
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
                                    <label for="planner-name" class="form-label">Planner Name</label>
                                    <input type="text" id="planner-name" class="form-control" name="planner_name"
                                        placeholder="Enter Planner Name">
                                    <small class="text-body float-start error-message-div planner_name-error"
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

        $('#createClientForm').submit(function(e) {
            e.preventDefault();
            clearErrors();
            var formData = new FormData(this);
            let csrfToken = $('meta[name="csrf-token"]').attr('content');
            var submitBtn = $("#storeButton");
            var spinner = $(submitBtn).find("#spinner");

            $.ajax({
                url: "{{ route('clients.store') }}",
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
                fileNameDisplay.text(`Uploaded File: ${fileName}`);
            } else {
                fileNameDisplay.text('');
            }
        });
    </script>
@endsection
