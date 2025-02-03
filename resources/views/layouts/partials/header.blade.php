{{-- <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <ul class="navbar-nav flex-row align-items-center ms-auto">
            <!-- Place this tag where you want the button to render. -->
            <li class="nav-item lh-1 me-4">
                <a class="github-button" href="https://github.com/themeselection/sneat-html-admin-template-free"
                    data-icon="octicon-star" data-size="large" data-show-count="true"
                    aria-label="Star themeselection/sneat-html-admin-template-free on GitHub">Star</a>
            </li>

            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img src="../assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" />
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img src="../assets/img/avatars/1.png" alt
                                            class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">John Doe</h6>
                                    <small class="text-muted">Admin</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider my-1"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="bx bx-user bx-md me-3"></i><span>My Profile</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#"> <i
                                class="bx bx-cog bx-md me-3"></i><span>Settings</span> </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#">
                            <span class="d-flex align-items-center align-middle">
                                <i class="flex-shrink-0 bx bx-credit-card bx-md me-3"></i><span
                                    class="flex-grow-1 align-middle">Billing Plan</span>
                                <span class="flex-shrink-0 badge rounded-pill bg-danger">4</span>
                            </span>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider my-1"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);">
                            <i class="bx bx-power-off bx-md me-3"></i><span>Log Out</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!--/ User -->
        </ul>
    </div>
</nav> --}}

<div class="row" style="align-self: center; margin-top: 20px; width: 98%">
    <div class="col-md-3">
        <div style="width: 100%; margin-top: 10px ">
            <div class="card h-100">
                <div class="card-body" style="padding: 10px">
                    <div class="row">
                        <div class="col-md-6">
                            <small style="color: #b8c3d0">Total Events</small>
                            <div style="display: flex; flex-direction: row; gap:55px">
                                <small style="color: black; font-weight: bolder; font-size: 15px" id="sidebar-events-count">{{ getEventsCount() }}</small>
                                <small style="color: {{ $header_events_percentage_color }}; margin: 5px 0 0 0">{{ $header_events_percentage_sign . $header_events_percentage_value .'%'  }}</small>
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
                                <small style="color: {{ $header_clients_percentage_color }}; margin: 5px 0 0 0">{{ $header_clients_percentage_sign . $header_clients_percentage_value .'%'  }}</small>
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
