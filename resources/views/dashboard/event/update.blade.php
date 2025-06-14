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
                    <h5 class="mb-0">{{ Breadcrumbs::render('update-event', $event->id) }}</h5>
                    <div class="card mb-6">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ __('Event Information') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-6">
                                <div class="col-md-8">
                                    <div class="row mb-6">
                                        @if (!isClientUser())
                                            <div class="col-md-6">
                                                <label class="form-label" for="event-name-id">{{ __('Name') }}</label>
                                                <div class="input-group input-group-merge">
                                                    <input type="text" id="event-name-id" class="form-control"
                                                        {{ $event->canUpdateEventNameAndStartDate() ? '' : 'disabled' }}
                                                        name="event_name" value="{{ $event->event_name }}"
                                                        placeholder="Enter Event Name">
                                                </div>
                                                <small class="text-body float-start error-message-div event_name-error"
                                                    style="color: #ff0000 !important"></small>
                                            </div>
                                        @endif
                                        <div class="col-md-6">
                                            <label class="form-label"
                                                for="event-alias-name-id">{{ __('Alias Name') }}</label>
                                            <div class="input-group input-group-merge">
                                                <input type="text" id="event-alias-name-id" class="form-control"
                                                    name="event_alias_name" value="{{ $event->event_alias_name }}"
                                                    placeholder="Enter Event Alias Name">
                                            </div>
                                            <small class="text-body float-start error-message-div event_alias_name-error"
                                                style="color: #ff0000 !important"></small>
                                        </div>
                                    </div>
                                    <div class="row mb-6">
                                        <div class="col-md-6">
                                            <label for="cover-image-file" class="form-label">Cover Image</label>
                                            <input class="form-control" type="file" id="cover-image-file"
                                                name="cover_image" accept="image/jpeg,png,jpg,gif,svg,webp">
                                            <div class="mt-2 preview-container">
                                                @if (isset($event->cover_image))
                                                    <img width="125px" height="125px"
                                                        src="{{ $event->cover_image ? asset($event->cover_image) : '' }}">
                                                @endif
                                            </div>
                                            <small class="text-body float-start uploaded-file-name"
                                                style="color: #000; font-style: italic;"></small>
                                            <small class="text-body float-start error-message-div cover_image-error"
                                                style="color: #ff0000 !important" hidden></small>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="formFile" class="form-label">Profile Picture</label>
                                            <input class="form-control" type="file" id="formFile" name="profile_picture"
                                                accept="image/jpeg,png,jpg,gif,svg,webp">
                                            <div class="mt-2 preview-container">
                                                @if ($event->profile_picture)
                                                    <img width="125px" height="125px"
                                                        src="{{ $event->profile_picture ? asset($event->profile_picture) : '' }}">
                                                @endif
                                            </div>
                                            <small class="text-body float-start uploaded-file-name"
                                                style="color: #000; font-style: italic;"></small>
                                            <small class="text-body float-start profile_picture-error"
                                                style="color: #ff0000 !important" hidden></small>
                                        </div>
                                    </div>
                                    @if (!isClientUser())
                                        <div class="row mb-6">
                                            <div class="col-md-6">
                                                <label for="exampleFormControlSelect1" class="form-label">Type</label>
                                                <select class="form-select" id="exampleFormControlSelect1"
                                                    name="event_type_id" aria-label="Default select example">
                                                    <option selected disabled>Select Type</option>
                                                    @foreach ($types as $key => $type)
                                                        <option {{ $event->event_type_id == $key ? 'selected' : '' }}
                                                            value="{{ $key }}">{{ $type }}</option>
                                                    @endforeach
                                                </select>
                                                <small class="text-body float-start error-message-div event_type_id-error"
                                                    style="color: #ff0000 !important" hidden></small>
                                            </div>
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
                                        </div>
                                        <div class="row mb-6">
                                            <div class="col-md-6">
                                                <label for="html5-date-input" class="form-label">Start Date</label>
                                                <input class="form-control start_date" type="datetime-local"
                                                    id="start-date-id"
                                                    {{ $event->canUpdateEventNameAndStartDate() ? '' : 'disabled' }}
                                                    value="{{ $event->start_date }}" name="start_date">
                                                <small class="text-body float-start error-message-div start_date-error"
                                                    style="color: #ff0000 !important" hidden></small>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="html5-date-input" class="form-label">End Date</label>
                                                <input class="form-control end_date" type="datetime-local"
                                                    value="{{ $event->end_date }}" name="end_date">
                                                <small class="text-body float-start error-message-div end_date-error"
                                                    style="color: #ff0000 !important" hidden></small>
                                            </div>
                                        </div>
                                    @endif
                                    @if (!isClientUser())
                                        <div class="row mb-6">
                                            <div class="col md-6">
                                                <label for="active-duration" class="form-label">Active Duration</label>
                                                <input class="form-control" type="text"
                                                    value="{{ $event->active_duration }}" name="active_duration"
                                                    id="active-duration" readonly>
                                                <small
                                                    class="text-body float-start error-message-div active_duration-error"
                                                    style="color: #ff0000 !important" hidden></small>
                                            </div>
                                            <div class="col md-6">
                                                <label for="exampleFormControlSelect1" class="form-label">Customer</label>
                                                <input class="form-control" type="text"
                                                    value="{{ $event->customer }}" placeholder="Enter Customer Name"
                                                    id="event-customer-name" name="customer">
                                                <small class="text-body float-start error-message-div customer-error"
                                                    style="color: #ff0000 !important" hidden></small>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="row mb-6">
                                        @if (!isClientUser())
                                            <div class="col md-6">
                                                <label for="html5-number-input" class="form-label">Venue</label>
                                                <input class="form-control" type="text" value="{{ $event->venue }}"
                                                    name="venue" placeholder="Enter Venue" id="html5-number-input">
                                                <small class="text-body float-start error-message-div venue-error"
                                                    style="color: #ff0000 !important" hidden></small>
                                            </div>
                                        @endif
                                        <div class="col-md-6">
                                            <div class="form-password-toggle">
                                                <label class="form-label" for="event-password">Password</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="event-password"
                                                        placeholder="Event Password" name="event_password"
                                                        value="{{ $event->event_password }}">
                                                </div>
                                            </div>
                                            <small class="text-body float-start error-message-div event_password-error"
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
                                        <div class="col-md-6">
                                            <label for="exampleFormControlTextarea1" class="form-label">Welcome
                                                Message</label>
                                            <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="welcome_message">{{ $event->welcome_message }}</textarea>
                                            <small class="text-body float-start error-message-div welcome_message-error"
                                                style="color: #ff0000 !important" hidden></small>
                                        </div>
                                    </div>
                                    @if (!isClientUser())
                                        <div class="row mb-6">
                                            <div class="col md-6">
                                                <label for="exampleFormControlTextarea1" class="form-label">Link</label>
                                                <div class="url-container mb-6">
                                                    <input type="text" id="urlInput" class="url-input" readonly
                                                        value="{{ $event->event_link }}" name="event_link">
                                                    <button onclick="copyUrl(event)" class="copy-button">Copy</button>
                                                    <small class="text-body float-start" style="color: #696cff !important"
                                                        hidden>Copied</small>
                                                    <small class="text-body float-start error-message-div event_link-error"
                                                        style="color: #ff0000 !important" hidden></small>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
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
                        <div class="card-header align-items-center">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="enable-organizer-id"
                                    name="enable_organizer" {{ $event->enable_organizer == 1 ? 'checked' : '' }}>
                                <label class="form-check-label" for="enable-organizer-id">Enable Vendors
                                    Section</label>
                            </div>
                            @if (!isClientUser())
                                <div style="display: flex; gap: 10px; align-items: baseline;" class="mb-1">
                                    <h5>Add New Vendor</h5>
                                    <button type="button" class="btn rounded-pill btn-icon btn-primary" disabled
                                        id="organizer-add-button">
                                        <span class="fa fa-plus" style="font-size: 15px"></span>
                                    </button>
                                </div>
                            @endif
                        </div>
                        @if (!isClientUser())
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
                                                        @foreach ($vendors as $key => $vendor)
                                                            <option
                                                                {{ $organizer->organizer_id == $key ? 'selected' : '' }}
                                                                value="{{ $key }}">{{ $vendor }}</option>
                                                        @endforeach
                                                    </select>
                                                    <small class="text-body float-start error-message-div"
                                                        style="color: #ff0000 !important" hidden></small>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control organizers-role_in_event"
                                                        value="{{ $organizer->role_in_event }}"
                                                        placeholder="Role In Event" name="role_in_event">
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
                                                <input type="text" class="form-control organizers-role_in_event"
                                                    value="" placeholder="Role In Event" name="role_in_event">
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
                        @endif
                    </div>
                    <div class="card mb-6">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ __('Event Setting') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-6">
                                @if (!isClientUser())
                                    <div class="col-md-4">
                                        <h5>General Settings</h5>
                                        <div class="row mb-6" style="margin: 3px">
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox"
                                                    id="image_share_guest_book"
                                                    {{ $event->setting?->image_share_guest_book == 1 ? 'checked' : '' }}
                                                    name="image_share_guest_book">
                                                <label class="form-check-label" for="image_share_guest_book">Image Share -
                                                    Guest Upload</label>
                                            </div>
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox"
                                                    id="image_folders"
                                                    {{ $event->setting?->image_folders == 1 ? 'checked' : '' }}
                                                    name="image_folders">
                                                <label class="form-check-label" for="image_folders">Image
                                                    Folders</label>
                                            </div>
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox"
                                                    id="video_playlist"
                                                    {{ $event->setting?->video_playlist == 1 ? 'checked' : '' }}
                                                    name="video_playlist">
                                                <label class="form-check-label" for="video_playlist">Video
                                                    Folders</label>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-4">
                                    <h5>Guest Upload Folder Settings</h5>
                                    <div class="row mb-6" style="margin: 3px">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="allow_upload"
                                                {{ $event->setting?->allow_upload == 1 ? 'checked' : '' }}
                                                name="allow_upload">
                                            <label class="form-check-label" for="allow_upload">Enable
                                                Upload</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="auto_image_approve"
                                                {{ $event->setting?->auto_image_approve == 1 ? 'checked' : '' }}
                                                name="auto_image_approve">
                                            <label class="form-check-label" for="auto_image_approve">Auto Image
                                                Approval</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="allow_image_download"
                                                {{ $event->setting?->allow_image_download == 1 ? 'checked' : '' }}
                                                name="allow_image_download">
                                            <label class="form-check-label" for="allow_image_download">Allow Image
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
                                                <div class="color-box {{ $event->setting?->accent_color == '#EFBF04' ? 'selected' : '' }}"
                                                    style="background-color: #EFBF04;" data-color="#EFBF04"></div>
                                                <div class="color-box {{ $event->setting?->accent_color == '#233d4e' ? 'selected' : '' }}"
                                                    style="background-color: #233d4e;" data-color="#233d4e"></div>
                                                <div class="color-box {{ $event->setting?->accent_color == '#bd3b3b' ? 'selected' : '' }}"
                                                    style="background-color: #bd3b3b;" data-color="#bd3b3b"></div>
                                                <div class="color-box {{ $event->setting?->accent_color == '#1f2e60' ? 'selected' : '' }}"
                                                    style="background-color: #1f2e60;" data-color="#1f2e60"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mt-7">
                                            <input type="color" class="form-control" id="colors-palette"
                                                value="{{ $event->setting?->accent_color }}">
                                        </div>
                                        <small class="text-body float-start error-message-div accent_color-error"
                                            style="color: #EFBF04 !important" hidden></small>
                                    </div>
                                    {{-- <div class="row mb-6">
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
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="display: flex; gap: 10px; justify-content: flex-end">
                        <a href="javascript:history.back()" class="btn btn-label-primary">Close</a>
                        <button type="submit" class="btn btn-primary" style="float: right" id="updateButton">Update
                            Event
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
                var roleId = $(item).find('.organizers-role_in_event').val();
                var organizerModelId = $(item).find('.organizer-model-id').val() ?? '';
                formData.append(`organizers[${index}][organizer_id]`, clientId);
                formData.append(`organizers[${index}][role_in_event]`, roleId);
                formData.append(`organizers[${index}][organizer_model_id]`, organizerModelId);
            });
            var id = $('#event_id').val();
            $.ajax({
                url: "{{ route('events.update') }}/" + id,
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
        function updateLinkValue() {
            $('#urlInput').val('');
            $('#qr-code-section').empty();
            var eventName = $('#event-name-id').val();
            var dateTimeValue = $("#start-date-id").val();
            if (dateTimeValue == '')
                dateTimeValue = new Date();
            var year = new Date(dateTimeValue).getFullYear();
            var month = new Date(dateTimeValue).getMonth() + 1;
            var url = "{{ url('/') }}/events/" + year + "/" + getSlug(eventName);
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

        $('#event-name-id').on('keyup', function(e) {
            e.preventDefault();
            updateLinkValue();
        })

        $('#start-date-id').on('change', function(e) {
            e.preventDefault();
            updateLinkValue();
        })

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
                newOrganizerRow.find('input').each(function() {
                    $(this).val('');
                });
                newOrganizerRow.find('small').each(function() {
                    $(this).prop('hidden', 'hidden');
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

    <script>
        $('input[type="datetime-local"]').on('change', function() {
            var start = $('.start_date').val();
            var end = $('.end_date').val();
            var diff = calculateActiveDuration(start, end);
            if (diff != "Invalid dates")
                $('#active-duration').val(diff);
        });

        function calculateActiveDuration(startDate, endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            if (isNaN(start) || isNaN(end)) {
                return "Invalid dates";
            }
            let diff = end - start;
            if (diff < 0) {
                return "End date is before start date";
            }
            let duration = [];
            let years = end.getFullYear() - start.getFullYear();
            let tempStart = new Date(start);
            tempStart.setFullYear(start.getFullYear() + years);
            if (tempStart > end) {
                years--;
                tempStart.setFullYear(tempStart.getFullYear() - 1);
            }
            duration.push(...(years > 0 ? [`${years} year${years > 1 ? "s" : ""}`] : []));
            let months = end.getMonth() - tempStart.getMonth();
            if (months < 0) {
                months += 12;
            }
            tempStart.setMonth(tempStart.getMonth() + months);
            if (tempStart > end) {
                months--;
                tempStart.setMonth(tempStart.getMonth() - 1);
            }
            duration.push(...(months > 0 ? [`${months} month${months > 1 ? "s" : ""}`] : []));
            let days = Math.floor((end - tempStart) / (1000 * 60 * 60 * 24));
            tempStart.setDate(tempStart.getDate() + days);
            duration.push(...(days > 0 ? [`${days} day${days > 1 ? "s" : ""}`] : []));
            let hours = Math.floor((end - tempStart) / (1000 * 60 * 60));
            tempStart.setHours(tempStart.getHours() + hours);
            duration.push(...(hours > 0 ? [`${hours} hour${hours > 1 ? "s" : ""}`] : []));
            let minutes = Math.floor((end - tempStart) / (1000 * 60));
            duration.push(...(minutes > 0 ? [`${minutes} minute${minutes > 1 ? "s" : ""}`] : []));
            return duration.length > 0 ? duration.join(", ") : "No difference";
        }
    </script>

    <script>
        $(document).ready(function() {
            updateLinkValue();
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

    <script>
        $('#enable-organizer-id').on('change', function(e) {
            var element = e.target;
            if ($(element).is(':checked'))
                $('#organizer-add-button').attr('disabled', false);
            else
                $('#organizer-add-button').attr('disabled', true);
        })
    </script>
@endsection
