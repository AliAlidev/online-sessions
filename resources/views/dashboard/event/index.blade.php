@extends('layouts.base')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xl">
                <div class="card mb-6">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('Create New Event') }}</h5>
                        <small class="text-body float-end">{{ Auth::user()->name ?? null }}</small>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-6">
                                                <label class="form-label"
                                                    for="basic-default-email">{{ __('Name') }}</label>
                                                <div class="input-group input-group-merge">
                                                    <input type="text" id="basic-default-email" class="form-control"
                                                        placeholder="john.doe" aria-label="john.doe"
                                                        aria-describedby="basic-default-email2">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-6">
                                                <label for="formFile" class="form-label">Thumbnail</label>
                                                <input class="form-control" type="file" id="formFile">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-6">
                                                <label class="form-label"
                                                    for="basic-default-email">{{ __('Alias Name') }}</label>
                                                <div class="input-group input-group-merge">
                                                    <input type="text" id="basic-default-email" class="form-control"
                                                        placeholder="" aria-label=""
                                                        aria-describedby="basic-default-email2">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-6">
                                                <label for="html5-date-input" class="form-label">Start Date</label>
                                                <input class="form-control" type="date" value="2021-06-18"
                                                    id="html5-date-input">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col md-6">
                                            <div class="mb-6">
                                                <label for="exampleFormControlSelect1" class="form-label">Type</label>
                                                <select class="form-select" id="exampleFormControlSelect1"
                                                    aria-label="Default select example">
                                                    <option selected="">Open this select menu</option>
                                                    <option value="1">One</option>
                                                    <option value="2">Two</option>
                                                    <option value="3">Three</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col md-6">
                                            <div class="mb-6">
                                                <label for="html5-date-input" class="form-label">End Date</label>
                                                <input class="form-control" type="date" value="2021-06-18"
                                                    id="html5-date-input">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col md-6">
                                            <div class="mb-6">
                                                <label for="exampleFormControlSelect1" class="form-label">Planner</label>
                                                <select class="form-select" id="exampleFormControlSelect1"
                                                    aria-label="Default select example">
                                                    <option selected="">Open this select menu</option>
                                                    <option value="1">One</option>
                                                    <option value="2">Two</option>
                                                    <option value="3">Three</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col md-6">
                                            <label for="html5-number-input" class="form-label">Active Duration</label>
                                            <input class="form-control" type="number" value="18"
                                                id="html5-number-input">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col md-6">
                                            <div class="mb-6">
                                                <label for="exampleFormControlSelect1" class="form-label">Client</label>
                                                <select class="form-select" id="exampleFormControlSelect1"
                                                    aria-label="Default select example">
                                                    <option selected="">Open this select menu</option>
                                                    <option value="1">One</option>
                                                    <option value="2">Two</option>
                                                    <option value="3">Three</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col md-6">
                                            <label for="html5-url-input" class="form-label">Link</label>
                                            <input class="form-control" type="url" value="https://themeselection.com"
                                                id="html5-url-input">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col md-6">
                                            <div class="mb-6">
                                                <label for="exampleFormControlTextarea1" class="form-label">Welcome
                                                    Message</label>
                                                <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div class="col md-6">
                                            <div class="form-password-toggle">
                                                <label class="form-label" for="basic-default-password12">Password</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control"
                                                        id="basic-default-password12" placeholder="············"
                                                        aria-describedby="basic-default-password2">
                                                    <span id="basic-default-password2"
                                                        class="input-group-text cursor-pointer"><i
                                                            class="bx bx-show"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col md-6">
                                            <div class="mb-6">
                                                <label for="location" class="form-label">Location</label>
                                                <input class="form-control" type="text" value="" id="location">
                                            </div>
                                        </div>
                                        <div class="col md-6">
                                            <div class="mb-6">
                                                <label for="exampleFormControlSelect1" class="form-label">Sponsors</label>
                                                <select class="form-select select2" multiple id="exampleFormControlSelect1"
                                                    aria-label="Default select example">
                                                    <option value="1">One</option>
                                                    <option value="2">Two</option>
                                                    <option value="3">Three</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-6">
                                        <label for="exampleFormControlTextarea1" class="form-label">Description</label>
                                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                                    </div>
                                </div>


                            </div>

                            <button type="submit" class="btn btn-primary">Send</button>
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
@endsection
