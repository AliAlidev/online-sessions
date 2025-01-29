<?php

use App\Http\Controllers\dashboard\EventController;
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

Route::controller(EventController::class)->group(function(){
    Route::get('/', 'index');
    Route::get('/event/{slug}', 'show');
});
