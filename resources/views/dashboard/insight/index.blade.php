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
        <div class="card mb-6">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('Insights') }}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-6">
                    <div class="col-md-3">
                        <input class="form-control start_date" type="date" value="" name="start_date"
                            id="start-date-id">
                        <small class="text-body float-start error-message-div start_date-error"
                            style="color: #ff0000 !important; display: none"></small>
                    </div>
                    <div class="col-md-3">
                        <input class="form-control end_date" type="date" value="" name="end_date">
                        <small class="text-body float-start error-message-div end_date-error"
                            style="color: #ff0000 !important;display: none"></small>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary search-btn"><span class="fa fa-search"></span>
                            <span id="spinner" style="display:none;">
                                <i class="fa fa-spinner fa-spin"></i>
                            </span>
                        </button>
                    </div>
                </div>
                <div id="insightsDiv"></div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            let today = new Date();
            let firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), +2);
            let formattedDate = firstDayOfMonth.toISOString().split("T")[0];
            $('.start_date').val(formattedDate);

            let lastDay = new Date(today.getFullYear(), today.getMonth()+1, +1);
            let lastDayFormatted = lastDay.toISOString().split("T")[0];
            $('.end_date').val(lastDayFormatted);

            $('.search-btn').on('click', function() {
                var startDate = $('.start_date').val();
                var endDate = $('.end_date').val();

                if (startDate == '') {
                    $('.start_date-error').html('Please select start date.');
                    $('.start_date-error').show();
                    return false;
                } else {
                    $('.start_date-error').hide();
                }
                if (endDate == '') {
                    $('.end_date-error').html('Please select end date.');
                    $('.end_date-error').show();
                    return false;
                } else {
                    $('.end_date-error').hide();
                }

                var searchBtn = $(this);
                var spinner = $(searchBtn).find("#spinner");
                searchBtn.prop('disabled', true);
                spinner.show();
                $('#insightsDiv').html('');

                $.ajax({
                    url: "{{ route('insights.index') }}",
                    type: "POST",
                    data: {
                        start: startDate,
                        end: endDate,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        spinner.hide();
                        searchBtn.prop('disabled', false);
                        $('#insightsDiv').html(response);
                    },
                    error: function(xhr) {
                        spinner.hide();
                        searchBtn.prop('disabled', false);
                    }
                })
            });
        });
    </script>
@endsection
