<?php

use App\Http\Controllers\Api\ConsentController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// getToken Method only supports GET and POST
// Route::match(['get', 'post'], 'getToken/{keyword?}', [NDTVController::class, 'getToken'])->name('getToken');

Route::post('consent/prepare', [ConsentController::class, 'prepare'])->name('consent.prepare');



