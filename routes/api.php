<?php

use App\Http\Controllers\Api\ConsentController;
use App\Http\Controllers\Api\PartnerController;
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

// customer ref:
// 55rmQvayRFfROCS005P3VAJIAh0ecthY

// "consentId":"92515f69-0a4d-485f-8e20-f386955ea731"



Route::group(['prefix' => 'consent', 'name' => 'consent.'], function () {
    Route::match(['get', 'post'], 'prepare/{subscriptionPeriod}/{productKey}/{msisdn}', [ConsentController::class, 'prepare'])
    ->name('consent.prepare');
});




Route::group(['prefix' => 'partner', 'name' => 'partner.'], function () {
    Route::post('smsmessaging/{senderNumber}', [PartnerController::class, 'smsmessaging'])->name('smsmessaging');
    Route::get('smsmessaging/unsubscribe/{acr_key}', [PartnerController::class, 'partnerMsgUnsubscribe'])->name('partnerMsgUnsubscribe');
    Route::post('payment/{acr_key}', [PartnerController::class, 'payment'])->name('payment');
    Route::delete('/acrs/{acr_key}', [PartnerController::class, 'invalidAcrs'])->name('invalidAcrs');
});


Route::get('check', function(){
    return response()->json([
        'name' => 'Abigail',
        'state' => 'CA',
    ]);
    
})->name('check-scnsjnckds');






