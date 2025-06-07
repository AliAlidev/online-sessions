<?php

// Home

use App\Models\Client;
use App\Models\Event;
use App\Models\Vendor;
use Diglactic\Breadcrumbs\Breadcrumbs;

Breadcrumbs::for('events', function ($trail) {
    $trail->push('Events List', route('events.index'));
});

Breadcrumbs::for('event', function ($trail, $eventSlug) {
    $trail->parent('events');
    $event = Event::where('bunny_event_name', $eventSlug)->first();
    $trail->push(ucfirst($event?->event_name), route('folders.index', $event?->bunny_event_name));
});

Breadcrumbs::for('folder', function ($trail, $eventSlug) {
    $trail->parent('event', $eventSlug);
});

Breadcrumbs::for('folders', function ($trail, $eventSlug, $folderSlug) {
    $folder = Event::where('bunny_event_name', $eventSlug)->first()->folders->where('bunny_folder_name', $folderSlug)->first();
    $trail->parent('event', $folder->event->bunny_event_name);
    $trail->push(ucfirst($folder->folder_name), route('folders.index', $folder->event->bunny_event_name));
});

Breadcrumbs::for('files', function ($trail, $eventSlug, $folderSlug) {
    $trail->parent('folders', $eventSlug, $folderSlug);
});

Breadcrumbs::for('roles', function ($trail) {
    $trail->push('Roles List', route('roles.index'));
});

Breadcrumbs::for('clients', function ($trail) {
    $trail->push('Clients List', route('clients.index'));
});

Breadcrumbs::for('create-client', function ($trail) {
    $trail->parent('clients');
    $trail->push('Create Client', '');
});

Breadcrumbs::for('update-client', function ($trail, $clientId) {
    $trail->parent('clients');
    $clientName = Client::find($clientId)->planner_name;
    $trail->push('Update Client - ' . $clientName, '');
});

Breadcrumbs::for('vendors', function ($trail) {
    $trail->push('Vendors List', route('vendors.index'));
});

Breadcrumbs::for('create-vendor', function ($trail) {
    $trail->parent('vendors');
    $trail->push('Create Vendor', '');
});

Breadcrumbs::for('update-vendor', function ($trail, $clientId) {
    $trail->parent('vendors');
    $clientName = Vendor::find($clientId)->vendor_name;
    $trail->push('Update Vendor - ' . $clientName, '');
});

Breadcrumbs::for('event-types', function ($trail) {
    $trail->push('Event Types List', route('events.types.index'));
});

Breadcrumbs::for('create-event', function ($trail) {
    $trail->parent('events');
    $trail->push('Create Event', '');
});

Breadcrumbs::for('update-event', function ($trail, $eventId) {
    $trail->parent('events');
    $eventName = Event::find($eventId)->event_name;
    $trail->push('Update Event - ' . $eventName, '');
});

Breadcrumbs::for('settings', function ($trail) {
    $trail->push('Bunny Setting', '');
});

Breadcrumbs::for('admins', function ($trail) {
    $trail->push('Admins List', route('users.index'));
});

Breadcrumbs::for('create-admin', function ($trail) {
    $trail->parent('admins');
    $trail->push('Create Admin', '');
});

Breadcrumbs::for('update-admin', function ($trail, $user) {
    $trail->parent('admins');
    $trail->push('Update Admin - ' . $user->name, '');
});

Breadcrumbs::for('events-users', function ($trail) {
    $trail->push('Users List', route('events.users.index'));
});

Breadcrumbs::for('create-event-user', function ($trail) {
    $trail->parent('events-users');
    $trail->push('Create User', '');
});

Breadcrumbs::for('update-event-user', function ($trail, $user) {
    $trail->parent('events-users');
    $trail->push('Update User - ' . $user->name, '');
});
