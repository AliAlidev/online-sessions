<?php

use App\Http\Controllers\dashboard\ClientController;
use App\Http\Controllers\dashboard\EventController;
use App\Http\Controllers\dashboard\EventTypeController;
use App\Http\Controllers\dashboard\FolderController;
use App\Http\Controllers\dashboard\FolderFileController;
use App\Http\Controllers\dashboard\RoleController;
use App\Http\Controllers\dashboard\SettingController;
use App\Http\Controllers\dashboard\InsightController;
use App\Http\Controllers\dashboard\UserController;
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


Auth::routes();

Route::middleware('auth')->group(function () {
    Route::controller(InsightController::class)->prefix('admin/insights')->name('insights.')->group(function () {
        Route::match(['get', 'post'], '/', 'index')->name('index');
    });

    Route::controller(EventTypeController::class)->prefix('admin/events-types')->name('events.types.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/store', 'store')->name('store');
        Route::post('/update/{id}', 'update')->name('update');
        Route::get('/show/{id}', 'show')->name('show');
        Route::get('/delete/{id}', 'delete')->name('delete');
    });

    Route::controller(FolderController::class)->prefix('admin/{event_slug}/folders')->name('folders.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/store', 'store')->name('store');
        Route::post('/update/{id?}', 'update')->name('update');
        Route::get('/show/{id?}', 'show')->name('show');
        Route::get('/delete/{id?}', 'delete')->name('delete');
    });

    Route::controller(FolderFileController::class)->prefix('admin/{event_slug}/{folder_slug}/files')->name('files.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/store', 'store')->name('store');
        Route::post('/update/{id?}', 'update')->name('update');
        Route::get('/show/{id?}', 'show')->name('show');
        Route::get('/delete/{id?}', 'delete')->name('delete');
        Route::post('/change-status', 'changeStatus')->name('change.status');
        Route::post('/upload-file', 'uploadFile');
        Route::get('/uploaded-file-status/{uploadId}', 'uploadedFileStatus');
    });

    Route::controller(EventController::class)->prefix('admin')->name('events.')->group(function () {
        Route::get('events', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{id?}', 'edit')->name('edit');
        Route::post('/update/{id?}', 'update')->name('update');
        Route::get('/delete/{id?}', 'delete')->name('delete');
        Route::get('/show/{id?}', 'show');
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

//////////////////////////// website ////////////////////////////
Route::controller(WebsiteController::class)->name('landing.')->group(function () {
    Route::get('events/{year}/{month}/{event_slug}', 'index')->name('index');
    Route::get('events/{year}/{month}/{event_slug}/gallery', 'gallery')->name('gallery');
    Route::get('events/{year}/{month}/{event_slug}/share', 'share')->name('share');
    Route::middleware('ensure.token')->get('events/gallery-redirect-url', 'galleryRedirectUrl')->name('gallery_redirect_url');
    Route::middleware('ensure.token')->post('events/{year}/{month}/{event_slug}/image', 'image')->name('image');
    Route::middleware('ensure.token')->post('events/{year}/{month}/{event_slug}/video', 'video')->name('video');
    Route::middleware('ensure.token')->get('events/share-redirect-url', 'shareRedirectUrl')->name('share_redirect_url');
    Route::middleware('ensure.token')->post('events/share-event-image', 'shareEventImage')->name('share-event-image');
    Route::middleware('ensure.token')->post('/delete-image/{id}', 'deleteImage')->name('delete-image');
    Route::get('events/event-password', 'eventPassword')->name('event_password');
    Route::middleware('ensure.token')->get('events/check-token', 'checkToken')->name('check_token');
    Route::middleware('ensure.token')->post('/apply-event-password', 'applyEventPassword')->name('apply_event_password');
});

Route::fallback(function () {
    return redirect()->route('login');
});
