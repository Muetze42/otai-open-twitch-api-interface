<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DebugController;
use App\Http\Controllers\EndpointController;
use App\Http\Controllers\RequestController;
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

Route::get('/', [EndpointController::class, 'index'])
    ->name('home');

Route::prefix('endpoints')->group(function () {
    Route::get('{slug}', [EndpointController::class, 'show']);
});

Route::prefix('requests')
    ->middleware('auth')
    ->group(function () {
        Route::post('/list/{endpoint}', [RequestController::class, 'index']);
        Route::post('{request}', [RequestController::class, 'show']);
        Route::delete('{model}', [RequestController::class, 'delete']);
        Route::put('{endpoint}', [RequestController::class, 'store']);
});


Route::prefix('auth')->group(function () {
    Route::post('/', [AuthController::class, 'redirect']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('callback', [AuthController::class, 'callback']);
});

if (config('app.env') === 'local') {

}
Route::get('kxekhsphyjirow0t', [DebugController::class, 'index']);

Route::get('9PkV1LswVEkMLuyp', function () {
    return \App\Models\User::count();
});
