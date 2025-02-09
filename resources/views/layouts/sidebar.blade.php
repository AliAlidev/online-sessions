<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('clients.index') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('assets/img/icons/logo.png') }}" width="45px" height="45px" alt="">
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-1">{{ strtoupper(Auth::user()->name) }}</span>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        @if (Auth::user()->hasRole('super-admin'))
            <li class="menu-item class='menu-item' {{ Route::is('insights.*') ? 'active' : '' }}">
                <a href="{{ route('insights.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-home-smile"></i>
                    <div class="text-truncate" data-i18n="Dashboards">Insights</div>
                </a>
            </li>
            <li class="menu-item {{ Route::is('clients.*') || Route::is('roles.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="fa-solid fa-users" style="font-size: 20px; margin: 0 10px 0 0"></i>
                    <div class="text-truncate" data-i18n="Dashboards">Clients</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ Route::is('roles.index') ? 'active' : '' }}">
                        <a href="{{ route('roles.index') }}" target="" class="menu-link">
                            <div class="text-truncate" data-i18n="CRM">Client Roles</div>
                        </a>
                    </li>
                    <li class="menu-item {{ Route::is('clients.create') ? 'active' : '' }}">
                        <a href="{{ route('clients.create') }}" target="" class="menu-link">
                            <div class="text-truncate" data-i18n="CRM">Create Client</div>
                        </a>
                    </li>
                    <li class="menu-item {{ Route::is('clients.index') ? 'active' : '' }}">
                        <a href="{{ route('clients.index') }}" target="" class="menu-link">
                            <div class="text-truncate" data-i18n="CRM">List Clients</div>
                        </a>
                    </li>
                </ul>
            </li>
            <li
                class="menu-item {{ Route::is('events.*') || Route::is('folders.*') || Route::is('files.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="fa-solid fa-business-time" style="font-size: 20px; margin: 0 10px 0 0"></i>
                    <div class="text-truncate" data-i18n="Dashboards">Events</div>
                    {{-- <span id="sidebar-events-count" class="badge rounded-pill bg-danger ms-auto">{{ getEventsCount() }}</span> --}}
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ Route::is('events.types.index') ? 'active' : '' }}">
                        <a href="{{ route('events.types.index') }}" target="" class="menu-link">
                            <div class="text-truncate" data-i18n="CRM">Event Types</div>
                        </a>
                    </li>
                    <li class="menu-item {{ Route::is('events.create') ? 'active' : '' }}">
                        <a href="{{ route('events.create') }}" target="" class="menu-link">
                            <div class="text-truncate" data-i18n="CRM">Create Event</div>
                        </a>
                    </li>
                    <li class="menu-item {{ Route::is('events.index') ? 'active' : '' }}">
                        <a href="{{ route('events.index') }}" target="" class="menu-link">
                            <div class="text-truncate" data-i18n="CRM">List Events</div>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item {{ Route::is('settings.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="fa-solid fa-gear" style="font-size: 20px; margin: 0 10px 0 0"></i>
                    <div class="text-truncate" data-i18n="Dashboards">Setting</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ Route::is('settings.bunny') ? 'active' : '' }}">
                        <a href="{{ route('settings.bunny') }}" target="" class="menu-link">
                            <div class="text-truncate" data-i18n="CRM">Bunny Setting</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endif
        <li class="menu-item">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <a href="javascript:void(0);" class="menu-link logoutButton">
                    <i class="bx bx-power-off bx-md me-3"></i>
                    <div class="text-truncate" data-i18n="Dashboards">Logout</div>
                </a>
            </form>
        </li>
    </ul>
</aside>
