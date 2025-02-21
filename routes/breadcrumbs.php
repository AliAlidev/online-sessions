<?php

// Home

use App\Models\Client;
use App\Models\Event;
use App\Models\EventFolder;
use App\Models\User;
use Diglactic\Breadcrumbs\Breadcrumbs;

Breadcrumbs::for('insights', function ($trail) {
    $trail->push('Insights', route('insights.index'));
});

Breadcrumbs::for('events', function ($trail) {
    $trail->parent('insights');
    $trail->push('Events List', route('events.index'));
});

Breadcrumbs::for('event', function ($trail, $eventId) {
    $trail->parent('events');
    $eventName = Event::find($eventId)->event_name;
    $trail->push(ucfirst($eventName), '');
});

Breadcrumbs::for('folder', function ($trail, $eventId) {
    $trail->parent('event', $eventId);
    $trail->push('Folders List', '');
});

Breadcrumbs::for('folders', function ($trail, $folderId) {
    $folder = EventFolder::find($folderId);
    $trail->parent('event', $folder->event_id);
    $folder = EventFolder::find($folderId);
    $trail->push(ucfirst($folder->folder_name), route('folders.index', $folder->event_id));
});

Breadcrumbs::for('files', function ($trail, $folderId) {
    $trail->parent('folders', $folderId);
    $folder = EventFolder::find($folderId);
    $trail->push(ucfirst($folder->folder_type).'s List', '');
});

Breadcrumbs::for('roles', function ($trail) {
    $trail->parent('insights');
    $trail->push('Roles List', route('roles.index'));
});

Breadcrumbs::for('clients', function ($trail) {
    $trail->parent('insights');
    $trail->push('Clients List', route('clients.index'));
});

Breadcrumbs::for('create-client', function ($trail) {
    $trail->parent('clients');
    $trail->push('Create Client', '');
});

Breadcrumbs::for('update-client', function ($trail, $clientId) {
    $trail->parent('clients');
    $clientName= Client::find($clientId)->planner_name;
    $trail->push('Update Client - '. $clientName, '');
});

Breadcrumbs::for('event-types', function ($trail) {
    $trail->parent('insights');
    $trail->push('Event Types List', route('events.types.index'));
});

Breadcrumbs::for('create-event', function ($trail) {
    $trail->parent('events');
    $trail->push('Create Event', '');
});

Breadcrumbs::for('update-event', function ($trail, $eventId) {
    $trail->parent('events');
    $eventName = Event::find($eventId)->event_name;
    $trail->push('Update Event - '. $eventName, '');
});

Breadcrumbs::for('settings', function ($trail) {
    $trail->parent('insights');
    $trail->push('Bunny Setting', '');
});

Breadcrumbs::for('users', function ($trail) {
    $trail->parent('insights');
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
