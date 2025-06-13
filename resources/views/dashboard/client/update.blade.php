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
                <form id="updateClientForm">
                    <input type="hidden" name="client_id" value="{{ $client->id }}">
                    <h5 class="mb-0">{{ Breadcrumbs::render('update-client', $client->id) }}</h5>
                    <div class="card mb-6">
                        <div class="card-header">
                            <h5 class="mb-0">Client Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-6">
                                <div class="col-md-6">
                                    <label for="planner-name" class="form-label">Client Name</label>
                                    <input type="text" id="planner-name" class="form-control" name="planner_name"
                                        value="{{ $client->planner_name }}" placeholder="Enter Planner Name">
                                    <small class="text-body float-start error-message-div planner_name-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                                <div class="col-md-6">
                                    <label for="logo" class="form-label">Logo</label>
                                    <input type="file" id="logo" class="form-control" name="logo"
                                        accept="image/jpeg,png,jpg,gif,svg,webp">
                                    <div class="mt-2 preview-container">
                                        @if (isset($client->logo))
                                            <img width="125px" height="125px"
                                                src="{{ $client->logo ? asset($client->logo) : '' }}">
                                        @endif
                                    </div>
                                    <small class="text-body float-start uploaded-file-name"
                                        style="color: #000; font-style: italic;"></small>
                                    <small class="text-body float-start error-message-div logo-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <div class="col-md-6">
                                    <label for="planner-business-name" class="form-label">Client Business Name</label>
                                    <input type="text" id="planner-business-name" class="form-control"
                                        value="{{ $client->planner_business_name }}" name="planner_business_name"
                                        placeholder="Enter Planner Business Name">
                                    <small class="text-body float-start error-message-div planner_business_name-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone-number" class="form-label">Phone Number</label>
                                    <input type="tel" id="phone-number" class="form-control" name="phone_number"
                                        placeholder="Enter Phone Number" value="{{ $client->phone_number }}">
                                    <small class="text-body float-start error-message-div phone_number-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" id="email" class="form-control" name="email"
                                        value="{{ $client->email }}" placeholder="Enter Email">
                                    <small class="text-body float-start error-message-div email-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                                <div class="col-md-6">
                                    <label for="client_role" class="form-label">Role</label>
                                    <input type="text" id="client_role" class="form-control" name="client_role"
                                        value="{{ $client->client_role }}" placeholder="Enter Client role">
                                    <small class="text-body float-start error-message-div client_role-error"
                                        style="color: #ff0000 !important" hidden></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="display: flex; gap: 10px; justify-content: flex-end">
                        <a href="javascript:history.back()" class="btn btn-label-primary">Close</a>
                        <button type="submit" class="btn btn-primary" style="float: right" id="updateButton">Update
                            Client
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

        $('#updateClientForm').submit(function(e) {
            e.preventDefault();
            clearErrors();
            var formData = new FormData(this);
            let csrfToken = $('meta[name="csrf-token"]').attr('content');
            var submitBtn = $("#updateButton");
            var spinner = $(submitBtn).find("#spinner");
            var id = $('client_id').val();
            console.log(id);

            $.ajax({
                url: "{{ url('admin/clients/update') }}/" + id,
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
                if (fileInput.files[0].type.startsWith('image/')) {
                    const previewContainer = $(this).closest('.col-md-6').find('.preview-container');
                    previewContainer.empty();
                    const reader = new FileReader();
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
            } else {
                fileNameDisplay.text('');
            }
        });
    </script>
@endsection
