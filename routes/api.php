<?php

use App\Http\Controllers\GuestController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::get('action', 'App\Http\Controllers\GuestController@apiAction');

Route::middleware('auth:api')->post('user', [GuestController::class, 'action']);
Route::post('auth', [GuestController::class, 'getAuthToken'])->name('get_auth_token');
