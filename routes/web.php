<?php

use App\Http\Controllers\dashboard\ClientController;
use App\Http\Controllers\dashboard\ClientUserController;
use App\Http\Controllers\dashboard\EventController;
use App\Http\Controllers\dashboard\EventTypeController;
use App\Http\Controllers\dashboard\EventUserController;
use App\Http\Controllers\dashboard\FolderController;
use App\Http\Controllers\dashboard\FolderFileController;
use App\Http\Controllers\dashboard\RoleController;
use App\Http\Controllers\dashboard\SettingController;
use App\Http\Controllers\dashboard\InsightController;
use App\Http\Controllers\dashboard\UserController;
use App\Http\Controllers\dashboard\VendorController;
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
    Route::controller(InsightController::class)->middleware('permission:insights')->prefix('insights')->name('insights.')->group(function () {
        Route::match(['get', 'post'], '/', 'index')->name('index');
    });

    Route::controller(EventTypeController::class)->prefix('events-types')->name('events.types.')->group(function () {
        Route::middleware('permission:list_event_types')->get('/', 'index')->name('index');
        Route::middleware('permission:create_event_type')->post('/store', 'store')->name('store');
        Route::middleware('permission:update_event_type')->post('/update/{id}', 'update')->name('update');
        Route::middleware('permission:list_event_types')->get('/show/{id}', 'show')->name('show');
        Route::middleware('permission:delete_event_type')->get('/delete/{id}', 'delete')->name('delete');
    });

    Route::controller(FolderController::class)->prefix('{event_slug}/folders')->name('folders.')->group(function () {
        Route::middleware('permission:list_folders')->get('/', 'index')->name('index');
        Route::middleware('permission:create_folder')->post('/store', 'store')->name('store');
        Route::middleware('permission:update_folder')->post('/update/{id?}', 'update')->name('update');
        Route::middleware('permission:list_folders')->get('/show/{id?}', 'show')->name('show');
        Route::middleware('permission:delete_folder')->get('/delete/{id?}', 'delete')->name('delete');
        Route::middleware('permission:update_folder')->get('/toggle-visibility/{id?}', 'toggleVisibility')->name('toggle.visibility');
    });

    Route::controller(FolderFileController::class)->prefix('{event_slug}/{folder_slug}/files')->name('files.')->group(function () {
        Route::middleware(['permission:upload_video|upload_image|update_image|update_video|approve_decline_image|approve_decline_video'])->get('/', 'index')->name('index');
        Route::middleware(['permission:upload_video|upload_image'])->post('/store', 'store')->name('store');
        Route::middleware(['permission:update_image|update_video'])->post('/update/{id?}', 'update')->name('update');
        Route::middleware(['permission:upload_video|upload_image|update_image|update_video'])->get('/show/{id?}', 'show')->name('show');
        Route::middleware(['permission:delete_image|delete_video'])->get('/delete/{id?}', 'delete')->name('delete');
        Route::middleware(['permission:approve_decline_image|approve_decline_video'])->post('/change-status', 'changeStatus')->name('change.status');
        Route::middleware(['permission:approve_decline_image|approve_decline_video'])->get('/approve-file/{id}', 'approveFile')->name('approve.file');
        Route::middleware(['permission:upload_video|upload_image|update_image|update_video'])->post('/upload-file', 'uploadFile');
    });

    Route::controller(EventController::class)->prefix('')->name('events.')->group(function () {
        Route::middleware('permission:list_events')->get('events', 'index')->name('index');
        Route::middleware('permission:create_event')->get('/create', 'create')->name('create');
        Route::middleware('permission:create_event')->post('/store', 'store')->name('store');
        Route::middleware('permission:update_event')->get('/edit/{id?}', 'edit')->name('edit');
        Route::middleware('permission:update_event')->post('/update/{id?}', 'update')->name('update');
        Route::middleware('permission:delete_event')->get('/delete/{id?}', 'delete')->name('delete');
        Route::middleware('permission:list_events')->get('/show/{id?}', 'show');
        Route::get('expired/{year}/{event_slug}', 'expired')->name('expired');
        Route::get('pending/{year}/{event_slug}', 'pending')->name('pending');
    });

    Route::controller(RoleController::class)->prefix('roles')->name('roles.')->group(function () {
        Route::middleware('permission:list_roles')->get('/index', 'index')->name('index');
        Route::middleware('permission:create_role')->get('/create', 'create')->name('create');
        Route::middleware('permission:create_role')->post('/store', 'store')->name('store');
        Route::middleware('permission:update_role')->get('/edit/{id}', 'edit')->name('edit');
        Route::middleware('permission:update_role')->post('/update/{id}', 'update')->name('update');
        Route::middleware('permission:delete_role')->get('/delete/{id}', 'delete')->name('delete');
        Route::middleware('permission:list_roles')->get('/show/{id}', 'show');
    });

    Route::controller(ClientController::class)->prefix('clients')->name('clients.')->group(function () {
        Route::middleware('permission:list_clients')->get('/index', 'index')->name('index');
        Route::middleware('permission:create_client')->get('/create', 'create')->name('create');
        Route::middleware('permission:create_client')->post('/store', 'store')->name('store');
        Route::middleware('permission:update_client')->get('/edit/{id}', 'edit')->name('edit');
        Route::middleware('permission:update_client')->post('/update/{id}', 'update')->name('update');
        Route::middleware('permission:delete_client')->get('/delete/{id}', 'delete')->name('delete');
    });

    Route::controller(ClientUserController::class)->prefix('clients-users')->name('clients.users.')->group(function () {
        Route::middleware('permission:list_clients_users')->get('/index', 'index')->name('index');
        Route::middleware('permission:create_client_user')->get('/create', 'create')->name('create');
        Route::middleware('permission:create_client_user')->post('/store', 'store')->name('store');
        Route::middleware('permission:update_client_user')->get('/edit/{id}', 'edit')->name('edit');
        Route::middleware('permission:update_client_user')->post('/update/{id}', 'update')->name('update');
        Route::middleware('permission:delete_client_user')->get('/delete/{id}', 'delete')->name('delete');
    });

    Route::controller(VendorController::class)->prefix('vendors')->name('vendors.')->group(function () {
        Route::middleware('permission:list_vendors')->get('/index', 'index')->name('index');
        Route::middleware('permission:create_vendor')->get('/create', 'create')->name('create');
        Route::middleware('permission:create_vendor')->post('/store', 'store')->name('store');
        Route::middleware('permission:update_vendor')->get('/edit/{id}', 'edit')->name('edit');
        Route::middleware('permission:update_vendor')->post('/update/{id}', 'update')->name('update');
        Route::middleware('permission:delete_vendor')->get('/delete/{id}', 'delete')->name('delete');
    });

    Route::controller(UserController::class)->prefix('users')->name('users.')->group(function () {
        Route::middleware('permission:list_users')->get('/index', 'index')->name('index');
        Route::middleware('permission:create_user')->get('/create', 'create')->name('create');
        Route::middleware('permission:create_user')->post('/store', 'store')->name('store');
        Route::middleware('permission:update_user')->get('/edit/{id}', 'edit')->name('edit');
        Route::middleware('permission:update_user')->post('/update/{id}', 'update')->name('update');
        Route::middleware('permission:delete_user')->get('/delete/{id}', 'delete')->name('delete');
    });

    Route::controller(EventUserController::class)->prefix('events/users')->name('events.users.')->group(function () {
        Route::middleware('permission:list_event_users')->get('/index', 'index')->name('index');
        Route::middleware('permission:create_event_user')->get('/create', 'create')->name('create');
        Route::middleware('permission:create_event_user')->post('/store', 'store')->name('store');
        Route::middleware('permission:update_event_user')->get('/edit/{id}', 'edit')->name('edit');
        Route::middleware('permission:update_event_user')->post('/update/{id}', 'update')->name('update');
        Route::middleware('permission:delete_event_user')->get('/delete/{id}', 'delete')->name('delete');
    });

    Route::controller(SettingController::class)->prefix('settings')->name('settings.')->group(function () {
        Route::middleware('permission:insights')->match(['get', 'post'], '/bunny', 'bunnySetting')->name('bunny');
    });
});

//////////////////////////// website ////////////////////////////
Route::controller(WebsiteController::class)->name('landing.')->group(function () {
    Route::get('increase-view/{id?}', 'increaseView')->name('increase_view');
    Route::get('events/{year}/{event_slug}', 'index')->name('index');
    Route::get('events/{year}/{event_slug}/gallery', 'gallery')->name('gallery');
    Route::get('events/{year}/{event_slug}/share', 'share')->name('share');
    Route::middleware('ensure.token')->get('events/gallery-redirect-url', 'galleryRedirectUrl')->name('gallery_redirect_url');
    Route::middleware('ensure.token')->post('events/{year}/{event_slug}/image', 'image')->name('image');
    Route::middleware('ensure.token')->post('events/{year}/{event_slug}/video', 'video')->name('video');
    Route::middleware('ensure.token')->get('events/share-redirect-url', 'shareRedirectUrl')->name('share_redirect_url');
    Route::middleware('ensure.token')->post('events/share-event-image', 'shareEventImage')->name('share-event-image');
    Route::middleware('ensure.token')->post('/delete-image/{id}', 'deleteImage')->name('delete-image');
    Route::get('events/event-password', 'eventPassword')->name('event_password');
    Route::middleware('ensure.token')->get('events/check-token', 'checkToken')->name('check_token');
    Route::middleware('ensure.token')->post('/apply-event-password', 'applyEventPassword')->name('apply_event_password');
    Route::middleware('ensure.token')->post('/check-folder-password', 'checkFolderPassword')->name('check_folder_password');
});

Route::fallback(function () {
    return redirect()->route('login');
});
