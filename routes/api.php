<?php

use App\Http\Controllers\Images\ImagesIndexController;
use App\Http\Controllers\Images\StoreImageController;
use App\Http\Controllers\Users\SelfController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Broadcast::routes(['middleware' => ['auth:sanctum']]);

Route::middleware(['auth:sanctum'])->group(function() {
    Route::get('/user', SelfController::class)
        ->name('users.self');

    Route::post('/images', StoreImageController::class)
        ->name('images.store');

    Route::get('/images', ImagesIndexController::class)
        ->name('images.index');
});
