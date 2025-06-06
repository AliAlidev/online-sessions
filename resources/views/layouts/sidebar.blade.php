<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('clients.index') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('assets/img/icons/logo-black.svg') }}" width="190px" alt="">
            </span>

        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block">
            <i class="bx bx-chevron-left bx-sm d-flex align-items-center justify-content-center"></i>
        </a>
    </div>


    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        @can(['insights'])
            <li class="menu-item class='menu-item' {{ Route::is('insights.*') ? 'active' : '' }}">
                <a href="{{ route('insights.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bxs-home-smile" style="font-size: 22px;"></i>
                    <div class="text-truncate" data-i18n="Dashboards">Insights</div>
                </a>
            </li>
        @endcan
        @if (Auth::user()->hasRole('super-admin'))
            <li class="menu-item {{ Route::is('users.*') || Route::is('clients.*') ||  Route::is('events.users.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="bx bxs-user-pin" style="font-size: 20px; margin: 0 10px 0 0"></i>
                    <div class="text-truncate" data-i18n="Dashboards">Users</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ Route::is('users.*') ? 'active' : '' }}">
                        <a href="{{ route('users.index') }}" target="" class="menu-link">
                            <div class="text-truncate" data-i18n="CRM">Admin Users</div>
                        </a>
                    </li>
                    @canany(['create_client', 'update_client', 'delete_client', 'list_clients'])
                        <li class="menu-item {{ Route::is('clients.*') ? 'active' : '' }}">
                            <a href="{{ route('clients.index') }}" class="menu-link">
                                <div class="text-truncate" data-i18n="Dashboards">Client Users</div>
                            </a>
                        </li>
                    @endcanany
                    <li class="menu-item {{ Route::is('events.users.*') ? 'active' : '' }}">
                        <a href="{{ route('events.users.index') }}" target="" class="menu-link">
                            <div class="text-truncate" data-i18n="CRM">Event Users</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endif
        @canany(['create_vendor', 'update_vendor', 'delete_vendor', 'list_vendors'])
            <li class="menu-item {{ Route::is('vendors.*') || Route::is('vendors.*') ? 'active' : '' }}">
                <a href="{{ route('vendors.index') }}" class="menu-link">
                    <i class="fa-solid fa-users" style="font-size: 20px; margin: 0 10px 0 0"></i>
                    <div class="text-truncate" data-i18n="Dashboards">Vendors</div>
                </a>
            </li>
        @endcanany
        @canany(['create_event', 'update_event', 'delete_event', 'list_events', 'create_event_type',
            'update_event_type', 'delete_event_type', 'list_event_types'])
            <li
                class="menu-item {{ (Route::is('events.*') && !Route::is('events.users.*')) || Route::is('folders.*') || Route::is('files.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="bx bxs-calendar-event" style="font-size: 22px; margin: 0 10px 0 0"></i>
                    <div class="text-truncate" data-i18n="Dashboards">Events</div>
                </a>
                <ul class="menu-sub">
                    @canany(['create_event', 'update_event', 'delete_event', 'list_events'])
                        <li class="menu-item {{ Route::is('events.index') ? 'active' : '' }}">
                            <a href="{{ route('events.index') }}" target="" class="menu-link">
                                <div class="text-truncate" data-i18n="CRM">All Events</div>
                            </a>
                        </li>
                    @endcanany
                    @canany(['create_event_type', 'update_event_type', 'delete_event_type', 'list_event_types'])
                        <li class="menu-item {{ Route::is('events.types.index') ? 'active' : '' }}">
                            <a href="{{ route('events.types.index') }}" target="" class="menu-link">
                                <div class="text-truncate" data-i18n="CRM">Event Types</div>
                            </a>
                        </li>
                    @endcanany
                </ul>
            </li>
        @endcanany
        @if (Auth::user()->hasRole('super-admin'))
            <li class="menu-item {{ Route::is('settings.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="bx bxs-cog" style="font-size: 20px; margin: 0 10px 0 0"></i>
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
