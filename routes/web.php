<?php

use App\Http\Controllers\dashboard\EventController;
use App\Http\Controllers\landing\LandingPageEventController;
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

Route::get('/landing', 'App\Http\Controllers\GuestController@landing');

Route::controller(EventController::class)->prefix('events')->name('events.')->group(function(){
    Route::get('/index', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update/{id}', 'update')->name('update');
    Route::get('/delete/{id}', 'delete')->name('delete');
    Route::get('/show/{slug}', 'show');
});



//////////////////////////// Landing Page ////////////////////////////
Route::controller(LandingPageEventController::class)->prefix('events')->name('landing.events.')->group(function(){
    Route::get('/{year}/{month}/{customer}', 'index')->name('index');
});
