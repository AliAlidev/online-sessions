@extends('layouts.base')

@section('styles')
    <style>
        .url-container {
            position: relative;
            width: 100%;
            max-width: 100%;
        }

        .url-input {
            width: 100%;
            padding: 10px 100px 10px 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .copy-button {
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            padding: 0 15px;
            font-size: 14px;
            border: none;
            background-color: #696cff;
            border-color: #696cff;
            color: white;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
        }

        .copy-button:hover {
            background-color: #696cff;
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

        .color-picker {
            display: flex;
            gap: 10px;
            margin-top: 5px;
        }

        .color-box {
            width: 30px;
            height: 30px;
            border-radius: 4px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s ease-in-out;
        }

        .color-box:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .color-box.selected {
            border: 3px solid #000;
            transform: scale(1.2);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.4);
        }

        #basic-default-password2 {
            pointer-events: auto;
            /* Ensure the icon is clickable */
            z-index: 1;
            /* Ensure it's above other elements */
        }

        #qr-code-section:hover {
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xl">
                <form id="updateEvent">
                    <input type="hidden" name="event_id" id="event_id" value="{{ $event->id }}">
                    <h5 class="mb-0">{{ __('Update New Event') }}</h5>
                    <div class="card mb-6">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ __('Event Information') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-6">
                                <div class="col-md-8">
                                    <div class="row mb-6">
                                        <div class="col-md-6">
                                            <label class="form-label" for="basic-default-email">{{ __('Name') }}</label>
                                            <div class="input-group input-group-merge">
                                                <input type="text" id="basic-default-email" class="form-control" {{ $event->canUpdateName() ? '' : 'readonly disabled' }}
                                                    name="event_name" value="{{ $event->event_name }}">
                                            </div>
                                            <small class="text-body float-start error-message-div event_name-error"
                                                style="color: #ff0000 !important"></small>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="cover-image-file" class="form-label">Cover Image</label>
                                            <input class="form-control" type="file" id="cover-image-file"
                                                name="cover_image" accept="image/jpeg,png,jpg,gif,svg,webp">
                                            <small class="text-body float-start uploaded-file-name"
                                                style="color: #000; font-style: italic;"></small>
                                            <small class="text-body float-start error-message-div cover_image-error"
                                                style="color: #ff0000 !important" hidden></small>
                                        </div>
                                    </div>
                                    <div class="row mb-6">
                                        <div class="col-md-6">
                                            <label for="exampleFormControlSelect1" class="form-label">Type</label>
                                            <select class="form-select" id="exampleFormControlSelect1" name="event_type_id"
                                                aria-label="Default select example">
                                                <option selected disabled>Select Type</option>
                                                @foreach ($types as $key => $type)
                                                    <option {{ $event->event_type_id == $key ? 'selected' : '' }}
                                                        value="{{ $key }}">{{ $type }}</option>
                                                @endforeach
                                            </select>
                                            <small class="text-body float-start error-message-div event_type_id-error"
                                                style="color: #ff0000 !important" hidden></small>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="formFile" class="form-label">Profile Picture</label>
                                            <input class="form-control" type="file" id="formFile" name="profile_picture"
                                                accept="image/jpeg,png,jpg,gif,svg,webp">
                                            <small class="text-body float-start uploaded-file-name"
                                                style="color: #000; font-style: italic;"></small>
                                            <small class="text-body float-start profile_picture-error"
                                                style="color: #ff0000 !important" hidden></small>
                                        </div>
                                    </div>
                                    <div class="row mb-6">
                                        <div class="col md-6">
                                            <label for="exampleFormControlSelect1" class="form-label">Client</label>
                                            <select class="form-select" id="exampleFormControlSelect1" name="client_id"
                                                aria-label="Default select example">
                                                <option selected disabled>Select Client</option>
                                                @foreach ($clients as $key => $client)
                                                    <option {{ $event->client_id == $key ? 'selected' : '' }}
                                                        value="{{ $key }}">{{ $client }}</option>
                                                @endforeach
                                            </select>
                                            <small class="text-body float-start error-message-div client_id-error"
                                                style="color: #ff0000 !important" hidden></small>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="html5-date-input" class="form-label">Start Date</label>
                                            <input class="form-control start_date" type="date"
                                                value="{{ $event->start_date }}" name="start_date">
                                            <small class="text-body float-start error-message-div start_date-error"
                                                style="color: #ff0000 !important" hidden></small>
                                        </div>
                                    </div>
                                    <div class="row mb-6">
                                        <div class="col md-6">
                                            <label for="exampleFormControlSelect1" class="form-label">Customer</label>
                                            <input class="form-control" type="text" value="{{ $event->customer }}"
                                                id="event-customer-name" name="customer">
                                            <small class="text-body float-start error-message-div customer-error"
                                                style="color: #ff0000 !important" hidden></small>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="html5-date-input" class="form-label">End Date</label>
                                            <input class="form-control end_date" type="date"
                                                value="{{ $event->end_date }}" name="end_date">
                                            <small class="text-body float-start error-message-div end_date-error"
                                                style="color: #ff0000 !important" hidden></small>
                                        </div>
                                    </div>
                                    <div class="row mb-6">
                                        <div class="col md-6">
                                            <label for="html5-number-input" class="form-label">Venue</label>
                                            <input class="form-control" type="text" value="{{ $event->venue }}"
                                                name="venue" placeholder="Enter Venue" id="html5-number-input">
                                            <small class="text-body float-start error-message-div venue-error"
                                                style="color: #ff0000 !important" hidden></small>
                                        </div>
                                        <div class="col md-6">
                                            <label for="active-duration" class="form-label">Active Duration</label>
                                            <input class="form-control" type="text"
                                                value="{{ $event->active_duration }}" name="active_duration"
                                                id="active-duration" readonly>
                                            <small class="text-body float-start error-message-div active_duration-error"
                                                style="color: #ff0000 !important" hidden></small>
                                        </div>
                                    </div>
                                    <div class="row mb-6">
                                        <div class="col md-6">
                                            <label for="exampleFormControlTextarea1"
                                                class="form-label">Description</label>
                                            <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="description">{{ $event->description }}</textarea>
                                            <small class="text-body float-start error-message-div Description-error"
                                                style="color: #ff0000 !important" hidden></small>
                                        </div>
                                        <div class="col md-6">
                                            <label for="exampleFormControlTextarea1" class="form-label">Link</label>
                                            <div class="url-container mb-6">
                                                <input type="text" id="urlInput" class="url-input"
                                                    value="{{ $event->event_link }}" name="event_link">
                                                <button onclick="copyUrl(event)" class="copy-button">Copy</button>
                                                <small class="text-body float-start" style="color: #696cff !important"
                                                    hidden>Copied</small>
                                                <small class="text-body float-start error-message-div event_link-error"
                                                    style="color: #ff0000 !important" hidden></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-6">
                                        <div class="col-md-6">
                                            <label for="exampleFormControlTextarea1" class="form-label">Welcome
                                                Message</label>
                                            <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="welcome_message">{{ $event->welcome_message }}</textarea>
                                            <small class="text-body float-start error-message-div welcome_message-error"
                                                style="color: #ff0000 !important" hidden></small>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-password-toggle">
                                                <label class="form-label" for="basic-default-password12">Password</label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control"
                                                        id="basic-default-password12" placeholder="············"
                                                        name="event_password">
                                                    <span id="basic-default-password2"
                                                        class="input-group-text cursor-pointer">
                                                        <i class="bx bx-hide"></i> <!-- Ensure this is the eye icon -->
                                                    </span>
                                                </div>
                                            </div>
                                            <small class="text-body float-start error-message-div event_password-error"
                                                style="color: #ff0000 !important" hidden></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-12 d-flex justify-content-center">
                                            <div style="font-weight: bolder; font-size: 16px">Event Link QR Code</div>
                                        </div>
                                    </div>
                                    <div class="row qr-code mt-5">
                                        <div class="col-md-12 d-flex justify-content-center">
                                            <input type="hidden" name="qr_code" id="event_qr_code">
                                            <div id="qr-code-section"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-6">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div style="display: flex; gap: 10px" class="mb-1">
                                <h4>Event Organizer</h4>
                                <button type="button" class="btn rounded-pill btn-icon btn-primary"
                                    id="organizer-add-button">
                                    <span class="fa fa-plus" style="font-size: 15px"></span>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="organizer-container">
                                @if ($event->organizers->count() > 0)
                                    @foreach ($event->organizers as $index => $organizer)
                                        <div class="row organizer-row mb-6">
                                            <input type="hidden" class="organizer-model-id" id=""
                                                value="{{ $organizer->id }}">
                                            <div class="col-md-4">
                                                <select class="form-select organizers-organizer_id">
                                                    <option value="" selected disabled>Select Option</option>
                                                    @foreach ($clients as $key => $client)
                                                        <option {{ $organizer->organizer_id == $key ? 'selected' : '' }}
                                                            value="{{ $key }}">{{ $client }}</option>
                                                    @endforeach
                                                </select>
                                                <small class="text-body float-start error-message-div"
                                                    style="color: #ff0000 !important" hidden></small>
                                            </div>
                                            <div class="col-md-4">
                                                <select class="form-select organizers-role_in_event">
                                                    <option value="" selected disabled>Role In Event</option>
                                                    @foreach ($roles as $key => $role)
                                                        <option {{ $organizer->role_in_event == $key ? 'selected' : '' }}
                                                            value="{{ $key }}">{{ $role }}</option>
                                                    @endforeach
                                                </select>
                                                <small class="text-body float-start error-message-div"
                                                    style="color: #ff0000 !important" hidden></small>
                                            </div>
                                            <div class="col-md-4">
                                                <button type="button"
                                                    class="btn rounded-pill btn-icon btn-danger remove-row organizer-remove-button"
                                                    {{ $index != 0 ? '' : 'hidden' }}>
                                                    <span class="fa fa-minus" style="font-size: 15px"></span>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="row organizer-row mb-6">
                                        <div class="col-md-4">
                                            <select class="form-select organizers-organizer_id">
                                                <option value="" selected disabled>Select Option</option>
                                                @foreach ($clients as $key => $client)
                                                    <option value="{{ $key }}">{{ $client }}</option>
                                                @endforeach
                                            </select>
                                            <small class="text-body float-start error-message-div"
                                                style="color: #ff0000 !important" hidden></small>
                                        </div>
                                        <div class="col-md-4">
                                            <select class="form-select organizers-role_in_event">
                                                <option value="" selected disabled>Role In Event</option>
                                                @foreach ($roles as $key => $role)
                                                    <option value="{{ $key }}">{{ $role }}</option>
                                                @endforeach
                                            </select>
                                            <small class="text-body float-start error-message-div"
                                                style="color: #ff0000 !important" hidden></small>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button"
                                                class="btn rounded-pill btn-icon btn-danger remove-row organizer-remove-button"
                                                hidden>
                                                <span class="fa fa-minus" style="font-size: 15px"></span>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card mb-6">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ __('Event Setting') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-6">
                                <div class="col-md-4">
                                    <h5>General Settings</h5>
                                    <div class="row mb-6" style="margin: 3px">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked"
                                                {{ $event->setting?->image_share_guest_book == 1 ? 'checked' : '' }}
                                                name="image_share_guest_book">
                                            <label class="form-check-label" for="flexSwitchCheckChecked">Image Share -
                                                Guest Upload</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked"
                                                {{ $event->setting?->image_folders == 1 ? 'checked' : '' }}
                                                name="image_folders">
                                            <label class="form-check-label" for="flexSwitchCheckChecked">Image
                                                Folders</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked"
                                                {{ $event->setting?->video_playlist == 1 ? 'checked' : '' }}
                                                name="video_playlist">
                                            <label class="form-check-label" for="flexSwitchCheckChecked">Video
                                                Folders</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <h5>Guest Upload Folder Settings</h5>
                                    <div class="row mb-6" style="margin: 3px">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked"
                                                {{ $event->setting?->allow_upload == 1 ? 'checked' : '' }}
                                                name="allow_upload">
                                            <label class="form-check-label" for="flexSwitchCheckChecked">Enable
                                                Upload</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked"
                                                {{ $event->setting?->auto_image_approve == 1 ? 'checked' : '' }}
                                                name="auto_image_approve">
                                            <label class="form-check-label" for="flexSwitchCheckChecked">Auto Image
                                                Approval</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked"
                                                {{ $event->setting?->allow_image_download == 1 ? 'checked' : '' }}
                                                name="allow_image_download">
                                            <label class="form-check-label" for="flexSwitchCheckChecked">Allow Image
                                                Download</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <h5>Theme Settings</h5>
                                    <div class="row mb-6">
                                        <div class="col-md-6">
                                            <label class="form-label">Select Theme</label>
                                            <select class="form-select" name="theme">
                                                <option value="" selected disabled>Light/Dark</option>
                                                <option {{ $event->setting?->theme == 'light' ? 'selected' : '' }}
                                                    value="light">Light</option>
                                                <option {{ $event->setting?->theme == 'dark' ? 'selected' : '' }}
                                                    value="dark">Dark</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-6">
                                        <input type="hidden" name="accent_color" id="event-accent-color"
                                            value="{{ $event->setting?->accent_color }}">
                                        <div class="col-md-6">
                                            <label class="form-label">Select Accent Color</label>
                                            <div class="color-picker">
                                                <div class="color-box {{ $event->setting?->accent_color == '#b71c1c' ? 'selected' : '' }}"
                                                    style="background-color: #b71c1c;" data-color="#b71c1c"></div>
                                                <div class="color-box {{ $event->setting?->accent_color == '#1a237e' ? 'selected' : '' }}"
                                                    style="background-color: #1a237e;" data-color="#1a237e"></div>
                                                <div class="color-box {{ $event->setting?->accent_color == '#2e7d32' ? 'selected' : '' }}"
                                                    style="background-color: #2e7d32;" data-color="#2e7d32"></div>
                                                <div class="color-box {{ $event->setting?->accent_color == '#00838f' ? 'selected' : '' }}"
                                                    style="background-color: #00838f;" data-color="#00838f"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mt-7">
                                            <input type="color" class="form-control" id="colors-palette"
                                                value="{{ $event->setting?->accent_color }}">
                                        </div>
                                        <small class="text-body float-start error-message-div accent_color-error"
                                            style="color: #ff0000 !important" hidden></small>
                                    </div>
                                    <div class="row mb-6">
                                        <div class="col-md-6">
                                            <label class="form-label">Select Font</label>
                                            <select class="form-select" name="font">
                                                <option value="" selected disabled>Select Font</option>
                                                <option {{ $event->setting?->font == 'Arial' ? 'selected' : '' }}
                                                    value="Arial">Arial</option>
                                                <option {{ $event->setting?->font == 'Times New Roman' ? 'selected' : '' }}
                                                    value="Times New Roman">Times New Roman</option>
                                                <option {{ $event->setting?->font == 'Courier New' ? 'selected' : '' }}
                                                    value="Courier New">Courier New</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="float: right" id="updateButton">Update Event
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
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>

    <script>
        function clearErrors() {
            $('.error-message-div').each(function() {
                $(this).attr('hidden', true);
            });
        }

        $('#updateEvent').submit(function(e) {
            e.preventDefault();
            clearErrors();
            var formData = new FormData(this);
            let csrfToken = $('meta[name="csrf-token"]').attr('content');
            var submitBtn = $("#updateButton");
            var spinner = $("#spinner");
            var organizers = [];
            $('.organizer-row').each(function(index, item) {
                var clientId = $(item).find('.organizers-organizer_id :selected').val();
                var roleId = $(item).find('.organizers-role_in_event :selected').val();
                var organizerModelId = $(item).find('.organizer-model-id').val() ?? '';
                formData.append(`organizers[${index}][organizer_id]`, clientId);
                formData.append(`organizers[${index}][role_in_event]`, roleId);
                formData.append(`organizers[${index}][organizer_model_id]`, organizerModelId);
            });
            var id = $('#event_id').val();
            $.ajax({
                url: "{{ url('events/update') }}/" + id,
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
                            if (field.includes('organizers')) {
                                var fieldName = field.split('.');
                                var indexError = fieldName[1];
                                var elementError = fieldName[2];
                                $('.organizer-row').each(function(index, item) {
                                    if (indexError == index) {
                                        var errorDiv = $(item).find('.organizers-' +
                                            elementError).closest('.col-md-4').find(
                                            '.error-message-div');
                                        console.log('.organizers-' + elementError);

                                        errorDiv.text("The " + elementError +
                                            " field is required");
                                        errorDiv.attr('hidden', false);
                                    }
                                });
                            } else {
                                let inputField = $(`.${field}-error`);
                                inputField.attr('hidden', false);
                                inputField.text(messages[0]);
                            }
                        });
                    }
                    spinner.hide();
                    submitBtn.prop('disabled', false);
                }
            });
        });
    </script>

    <script>
        function copyUrl(event) {
            event.preventDefault();
            const $urlInput = $('#urlInput');
            $urlInput.select();
            document.execCommand('copy');
            const $copiedMessage = $(event.target).parent().find('.text-body');
            $copiedMessage.removeAttr('hidden');
            setTimeout(() => {
                $copiedMessage.attr('hidden', true);
            }, 5000);
        }
    </script>

    <script>
        $('#event-customer-name').on('keyup', function(e) {
            e.preventDefault();
            $('#qr-code-section').empty();
            $('#urlInput').val('');
            var customer = $(this).val();
            if (customer) {
                generateQRCode(customer);
            }
        })

        function generateQRCode(customer) {
            var dateTimeValue = $("input[name='start_date']").val();
            if (dateTimeValue == '')
                dateTimeValue = new Date();
            var year = new Date(dateTimeValue).getFullYear();
            var month = new Date(dateTimeValue).getMonth() + 1;
            var url = "{{ url('/') }}/events/" + year + "/" + month + "/" + getSlug(customer);
            $('#urlInput').val(url);
            new QRCode(document.getElementById("qr-code-section"), {
                text: url,
                width: 200,
                height: 200
            });
            const canvas = document.querySelector("#qr-code-section canvas");
            const qrCodeBase64 = canvas.toDataURL("image/png");
            $('#event_qr_code').val(qrCodeBase64);
        }

        function getSlug(inputText) {
            return inputText
                .toLowerCase()
                .trim()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
        }
    </script>

    <script>
        $(document).ready(function() {
            $('#organizer-add-button').click(function() {
                const newOrganizerRow = $('.organizer-row').first().clone();
                $(newOrganizerRow).find('.organizer-model-id').remove();
                $(newOrganizerRow).find('.error-message-div').attr('hidden', true);

                newOrganizerRow.find('select').each(function() {
                    $(this).prop('selectedIndex', 0);
                });
                const removeButton = newOrganizerRow.find('.remove-row');
                removeButton.removeAttr('hidden');
                const uniqueId = Date.now();
                newOrganizerRow.find('select').each(function() {
                    const name = $(this).attr('name');
                });
                $('.organizer-container').append(newOrganizerRow);
            });

            $(document).on('click', '.organizer-remove-button', function() {
                $(this).closest('.organizer-row').remove();
            })
        });
    </script>

    <script>
        $('.color-box').on('click', function() {
            if ($(this).hasClass('selected')) {
                $(this).removeClass('selected');
                $('#event-accent-color').val('');
            } else {
                $('.color-box').removeClass('selected');
                $(this).addClass('selected');
                const selectedColor = $(this).data('color');
                $('#event-accent-color').val(selectedColor);
                $('#colors-palette').val(selectedColor);
            }
        });

        $('#colors-palette').on('input', function() {
            const selectedColor = $(this).val();
            $('.color-box').removeClass('selected');
            $('.color-box[data-color="' + selectedColor + '"]').addClass('selected');
            $('#event-accent-color').val(selectedColor);
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

    <script>
        $('input[type="date"]').on('change', function() {
            var start = $('.start_date').val();
            var end = $('.end_date').val();
            var diff = calculateActiveDuration(start, end);
            if (diff != "Invalid dates")
                $('#active-duration').val(diff);
        });

        function calculateActiveDuration(startDate, endDate) {
            // Parse the dates
            const start = new Date(startDate);
            const end = new Date(endDate);

            // Check if the dates are valid
            if (isNaN(start) || isNaN(end)) {
                return "Invalid dates";
            }

            // Calculate differences
            let years = end.getFullYear() - start.getFullYear();
            let months = end.getMonth() - start.getMonth();
            let days = end.getDate() - start.getDate();

            // Adjust months and years if needed
            if (days < 0) {
                months -= 1;
                days += new Date(end.getFullYear(), end.getMonth(), 0).getDate();
            }
            if (months < 0) {
                years -= 1;
                months += 12;
            }

            // Build the duration string dynamically
            let duration = [];
            if (years > 0) duration.push(`${years} year${years > 1 ? "s" : ""}`);
            if (months > 0) duration.push(`${months} month${months > 1 ? "s" : ""}`);
            if (days > 0) duration.push(`${days} day${days > 1 ? "s" : ""}`);

            // Join the parts and return the result
            return duration.length > 0 ? duration.join(", ") : "No difference";
        }
    </script>

    <script>
        $(document).ready(function() {
            var customer = $('#event-customer-name').val();
            generateQRCode(customer);
        })
    </script>

    <script>
        $("#qr-code-section").click(function() {
            let qrImage = $(this).find('img').attr("src"); // Get image source
            let link = $("<a>").attr({
                href: qrImage,
                download: "QR_Code.png"
            }).appendTo("body");

            link[0].click();
            link.remove();
        });
    </script>
@endsection
