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
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xl">
                <div class="card mb-6">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('Update Event') }} </h5>
                        <small class="text-body float-end">{{ ucfirst($event->event_name) }}</small>
                    </div>
                    <div class="card-body">
                        <form id="updateEvent">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-6">
                                                <label class="form-label"
                                                    for="basic-default-email">{{ __('Name') }}</label>
                                                <div class="input-group input-group-merge">
                                                    <input type="text" id="basic-default-email" class="form-control"
                                                        name="event_name">
                                                </div>
                                                <small class="text-body float-start event_name-error"
                                                    style="color: #ff0000 !important"></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-6">
                                                <label for="formFile" class="form-label">Cover Image</label>
                                                <input class="form-control" type="file" id="formFile"
                                                    name="cover_image">
                                                <small class="text-body float-start cover_image-error"
                                                    style="color: #ff0000 !important" hidden></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-6">
                                                <label for="exampleFormControlSelect1" class="form-label">Type</label>
                                                <select class="form-select" id="exampleFormControlSelect1" name="event_type"
                                                    aria-label="Default select example">
                                                    <option selected="">Open this select menu</option>
                                                    <option value="1">One</option>
                                                    <option value="2">Two</option>
                                                    <option value="3">Three</option>
                                                </select>
                                                <small class="text-body float-start event_type-error"
                                                    style="color: #ff0000 !important" hidden></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-6">
                                                <label for="formFile" class="form-label">Profile Picture</label>
                                                <input class="form-control" type="file" id="formFile"
                                                    name="profile_picture">
                                                <small class="text-body float-start profile_picture-error"
                                                    style="color: #ff0000 !important" hidden></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col md-6">
                                            <div class="mb-6">
                                                <label for="exampleFormControlSelect1" class="form-label">Client</label>
                                                <select class="form-select" id="exampleFormControlSelect1" name="client_id"
                                                    aria-label="Default select example">
                                                    <option selected="">Open this select menu</option>
                                                    <option value="1">One</option>
                                                    <option value="2">Two</option>
                                                    <option value="3">Three</option>
                                                </select>
                                                <small class="text-body float-start client_id-error"
                                                    style="color: #ff0000 !important" hidden></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-6">
                                                <label for="html5-date-input" class="form-label">Start Date</label>
                                                <input class="form-control" type="date" value="2021-06-18"
                                                    name="start_date" id="html5-date-input">
                                                <small class="text-body float-start start_date-error"
                                                    style="color: #ff0000 !important" hidden></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col md-6">
                                            <div class="mb-6">
                                                <label for="exampleFormControlSelect1" class="form-label">Customer</label>
                                                <input class="form-control" type="text" value="" name="customer"
                                                    id="html5-date-input">
                                                <small class="text-body float-start customer-error"
                                                    style="color: #ff0000 !important" hidden></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-6">
                                                <label for="html5-date-input" class="form-label">End Date</label>
                                                <input class="form-control" type="date" value="2021-06-18"
                                                    name="end_date" id="html5-date-input">
                                                    <small class="text-body float-start end_date-error"
                                                    style="color: #ff0000 !important" hidden></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col md-6">
                                            <div class="mb-6">
                                                <label for="html5-number-input" class="form-label">Venue</label>
                                                <input class="form-control" type="text" value="" name="venue"
                                                    placeholder="Enter Venue" id="html5-number-input">
                                                    <small class="text-body float-start venue-error"
                                                    style="color: #ff0000 !important" hidden></small>
                                            </div>
                                        </div>
                                        <div class="col md-6">
                                            <div class="mb-6">
                                                <label for="html5-number-input" class="form-label">Active Duration</label>
                                                <input class="form-control" type="number" value=""
                                                    name="active_duration" id="html5-number-input">
                                                    <small class="text-body float-start active_duration-error"
                                                    style="color: #ff0000 !important" hidden></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col md-6">
                                            <div class="mb-6">
                                                <label for="exampleFormControlTextarea1"
                                                    class="form-label">Description</label>
                                                <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="description"></textarea>
                                                <small class="text-body float-start active_duration-error"
                                                    style="color: #ff0000 !important" hidden></small>
                                            </div>
                                        </div>
                                        <div class="col md-6">
                                            <label for="exampleFormControlTextarea1" class="form-label">Link</label>
                                            <div class="url-container mb-6">
                                                <input type="text" id="urlInput" class="url-input" value="">
                                                <button onclick="copyUrl(event)" class="copy-button"
                                                    name="event_link">Copy</button>
                                                <small class="text-body float-start" style="color: #696cff !important"
                                                    hidden>Copied</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-6">
                                                <label for="exampleFormControlTextarea1" class="form-label">Welcome
                                                    Message</label>
                                                <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="welcome_message"></textarea>
                                                <small class="text-body float-start welcome_message-error"
                                                    style="color: #ff0000 !important" hidden></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="col md-6">
                                                <div class="form-password-toggle">
                                                    <label class="form-label"
                                                        for="basic-default-password12">Password</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control"
                                                            id="basic-default-password12" placeholder="············"
                                                            aria-describedby="basic-default-password2"
                                                            name="event_password">
                                                        <span id="basic-default-password2"
                                                            class="input-group-text cursor-pointer"><i
                                                                class="bx bx-show"></i></span>
                                                    </div>
                                                    <small class="text-body float-start event_password-error"
                                                    style="color: #ff0000 !important" hidden></small>
                                                </div>
                                            </div>
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
                                            <img width="200px" height="200px" src="" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>

    <script>
        function copyUrl(event) {
            event.preventDefault();
            const urlInput = document.getElementById('urlInput');
            urlInput.select();
            urlInput.setSelectionRange(0, 99999); // For mobile devices
            document.execCommand('copy');
            const coppedMessage = event.target.parentElement.querySelector(".text-body");
            coppedMessage.removeAttribute('hidden');
            setTimeout(() => {
                coppedMessage.setAttribute('hidden', true);
            }, 5000);
        }
    </script>
@endsection
