<?php

// Home

use App\Models\Client;
use App\Models\Event;
use App\Models\EventFolder;
use App\Models\User;
use Diglactic\Breadcrumbs\Breadcrumbs;

// Breadcrumbs::for('insights', function ($trail) {
//     $trail->push('Insights', route('insights.index'));
// });

Breadcrumbs::for('events', function ($trail) {
    // $trail->parent('insights');
    $trail->push('Events List', route('events.index'));
});

Breadcrumbs::for('event', function ($trail, $eventSlug) {
    $trail->parent('events');
    $event = Event::where('bunny_event_name', $eventSlug)->first();
    $trail->push(ucfirst($event?->event_name), route('folders.index', $event?->bunny_event_name));
});

Breadcrumbs::for('folder', function ($trail, $eventSlug) {
    $trail->parent('event', $eventSlug);
    // $trail->push('Folders List', '');
});

Breadcrumbs::for('folders', function ($trail, $eventSlug, $folderSlug) {
    $folder = Event::where('bunny_event_name', $eventSlug)->first()->folders->where('bunny_folder_name', $folderSlug)->first();
    $trail->parent('event', $folder->event->bunny_event_name);
    $trail->push(ucfirst($folder->folder_name), route('folders.index', $folder->event->bunny_event_name));
});

Breadcrumbs::for('files', function ($trail, $eventSlug, $folderSlug) {
    $trail->parent('folders', $eventSlug, $folderSlug);
    // $folder = EventFolder::where('bunny_folder_name', $folderSlug)->first();
    // $trail->push(ucfirst($folder->folder_type).'s List', '');
});

Breadcrumbs::for('roles', function ($trail) {
    // $trail->parent('insights');
    $trail->push('Roles List', route('roles.index'));
});

Breadcrumbs::for('clients', function ($trail) {
    // $trail->parent('insights');
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

Breadcrumbs::for('event-types', function ($trail) {
    // $trail->parent('insights');
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
    // $trail->parent('insights');
    $trail->push('Bunny Setting', '');
});

Breadcrumbs::for('users', function ($trail) {
    // $trail->parent('insights');
    $trail->push('Users List', route('users.index'));
});

Breadcrumbs::for('create-user', function ($trail) {
    $trail->parent('users');
    $trail->push('Create User', '');
});

Breadcrumbs::for('update-user', function ($trail, $user) {
    $trail->parent('users');
    $trail->push('Update User - ' . $user->name, '');
});
