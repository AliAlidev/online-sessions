@extends('layouts.base')


@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-6">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('Insights') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div style="width: 100%; margin-top: 10px ">
                            <div class="card h-100">
                                <div class="card-body" style="padding: 10px">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small style="color: #b8c3d0">Total Events</small>
                                            <div style="display: flex; flex-direction: row; gap:55px">
                                                <small style="color: black; font-weight: bolder; font-size: 15px" id="sidebar-events-count">{{ getEventsCount() }}</small>
                                                <small style="color: {{ $events_percentage_color }}; margin: 5px 0 0 0">{{ $events_percentage_sign . $events_percentage_value .'%'  }}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6 d-flex justify-content-end" style="align-items: center">
                                            <span style="justify-items: flex-end; color: #ffffff;"><i class="fa fa-folder"
                                                    style="background-color: #4fd1c5c9;padding: 10px 10px;border-radius: 10px;font-size: 17px;"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div style="width: 100%; margin-top: 10px ">
                            <div class="card h-100">
                                <div class="card-body" style="padding: 10px">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small style="color: #b8c3d0">Total Storage</small>
                                            <div style="display: flex; flex-direction: row; gap:55px">
                                                <small style="color: black; font-weight: bolder; font-size: 15px">50</small>
                                                <small style="color: #68c790; margin: 5px 0 0 0">+60%</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6 d-flex justify-content-end" style="align-items: center">
                                            <span style="justify-items: flex-end; color: #ffffff;"><i class="fa fa-folder"
                                                    style="background-color: #4fd1c5c9;padding: 10px 10px;border-radius: 10px;font-size: 17px;"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div style="width: 100%; margin-top: 10px ">
                            <div class="card h-100">
                                <div class="card-body" style="padding: 10px">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small style="color: #b8c3d0">Total Clients</small>
                                            <div style="display: flex; flex-direction: row; gap:55px">
                                                <small style="color: black; font-weight: bolder; font-size: 15px" id="sidebar-clients-count">{{ getClientsCount() }}</small>
                                                <small style="color: {{ $clients_percentage_color }}; margin: 5px 0 0 0">{{ $clients_percentage_sign . $clients_percentage_value .'%'  }}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6 d-flex justify-content-end" style="align-items: center">
                                            <span style="justify-items: flex-end; color: #ffffff;"><i class="fa fa-folder"
                                                    style="background-color: #4fd1c5c9;padding: 10px 10px;border-radius: 10px;font-size: 17px;"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div style="width: 100%; margin-top: 10px">
                            <div class="card h-100">
                                <div class="card-body" style="padding: 10px">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small style="color: #b8c3d0">Total Events</small>
                                            <div style="display: flex; flex-direction: row; gap:55px">
                                                <small style="color: black; font-weight: bolder; font-size: 15px">50</small>
                                                <small style="color: #68c790; margin: 5px 0 0 0">+60%</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6 d-flex justify-content-end" style="align-items: center">
                                            <span style="justify-items: flex-end; color: #ffffff;"><i class="fa fa-folder"
                                                    style="background-color: #4fd1c5c9;padding: 10px 10px;border-radius: 10px;font-size: 17px;"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')

@endsection
