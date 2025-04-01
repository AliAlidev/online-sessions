<?php

use App\Http\Controllers\dashboard\ClientController;
use App\Http\Controllers\dashboard\EventController;
use App\Http\Controllers\dashboard\EventTypeController;
use App\Http\Controllers\dashboard\FolderController;
use App\Http\Controllers\dashboard\FolderFileController;
use App\Http\Controllers\dashboard\RoleController;
use App\Http\Controllers\Dashboard\SettingController;
use App\Http\Controllers\dashboard\InsightController;
use App\Http\Controllers\dashboard\UserController;
use App\Http\Controllers\landing\LandingPageEventController;
use App\Http\Controllers\website\WebsiteController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/landing', 'App\Http\Controllers\GuestController@landing');

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::controller(InsightController::class)->prefix('admin/insights')->name('insights.')->group(function () {
        Route::match(['get', 'post'], '/', 'index')->name('index');
    });

    Route::controller(EventTypeController::class)->prefix('admin/events-types')->name('events.types.')->group(function () {
        Route::get('/index', 'index')->name('index');
        Route::post('/store', 'store')->name('store');
        Route::post('/update/{id}', 'update')->name('update');
        Route::get('/show/{id}', 'show')->name('show');
        Route::get('/delete/{id}', 'delete')->name('delete');
    });

    Route::controller(FolderController::class)->prefix('admin/folders')->name('folders.')->group(function () {
        Route::get('/{event_id}/index', 'index')->name('index');
        Route::post('/{event_id}/store', 'store')->name('store');
        Route::post('/update/{id}', 'update')->name('update');
        Route::get('/show/{id}', 'show')->name('show');
        Route::get('/delete/{id}', 'delete')->name('delete');
    });

    Route::controller(FolderFileController::class)->prefix('admin/files')->name('files.')->group(function () {
        Route::get('/{folder_id}/index/{type}', 'index')->name('index');
        Route::post('/{folder_id}/store/{type}', 'store')->name('store');
        Route::post('/update/{id}', 'update')->name('update');
        Route::post('/update-without-file/{id}', 'updateWithoutFile')->name('update.without.file');
        Route::get('/show/{id}', 'show')->name('show');
        Route::get('/delete/{id}', 'delete')->name('delete');
        Route::post('/change-status', 'changeStatus')->name('change.status');
        Route::post('/upload-file', 'uploadFile');
        Route::get('/uploaded-file-status/{uploadId}', 'uploadedFileStatus');
    });

    Route::controller(EventController::class)->prefix('admin/events')->name('events.')->group(function () {
        Route::get('/index', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/update/{id}', 'update')->name('update');
        Route::get('/delete/{id}', 'delete')->name('delete');
        Route::get('/show/{id}', 'show');
    });

    Route::controller(RoleController::class)->prefix('admin/roles')->name('roles.')->group(function () {
        Route::get('/index', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/update/{id}', 'update')->name('update');
        Route::get('/delete/{id}', 'delete')->name('delete');
        Route::get('/show/{id}', 'show');
    });

    Route::controller(ClientController::class)->prefix('admin/clients')->name('clients.')->group(function () {
        Route::get('/index', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/update/{id}', 'update')->name('update');
        Route::get('/delete/{id}', 'delete')->name('delete');
    });

    Route::controller(UserController::class)->prefix('admin/users')->name('users.')->group(function () {
        Route::get('/index', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/update/{id}', 'update')->name('update');
        Route::get('/delete/{id}', 'delete')->name('delete');
    });

    Route::controller(SettingController::class)->prefix('admin/settings')->name('settings.')->group(function () {
        Route::match(['get', 'post'], '/bunny', 'bunnySetting')->name('bunny');
    });
});

//////////////////////////// Landing Page ////////////////////////////
// Route::controller(LandingPageEventController::class)->prefix('events')->name('landing.events.')->group(function () {
//     Route::get('/{year}/{month}/{customer}', 'index')->name('index');
// });

//////////////////////////// website ////////////////////////////
Route::controller(WebsiteController::class)->name('landing.')->group(function () {
    Route::get('events/{year}/{month}/{customer}', 'index')->name('index');
    Route::get('events/{year}/{month}/{customer}/gallery', 'gallery')->name('gallery');
    Route::post('events/{year}/{month}/{customer}/image', 'image')->name('image');
    Route::get('events/{year}/{month}/{customer}/share', 'share')->name('share');
    Route::post('events/{year}/{month}/{customer}/video', 'video')->name('video');
    Route::post('events/share-event-image', 'shareEventImage')->name('share-event-image');
});

// Route::fallback(function () {
//     return redirect()->route('login');
// });

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
