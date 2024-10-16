<?php

use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\RefundNotificationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SubsAndUnsubsController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\LogController;
use App\Http\Controllers\Api\OnDemandController;
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





Route::group(['prefix' => 'partner', 'name' => 'partner.'], function () {
    Route::get('smsmessaging/{senderNumber}', [PartnerController::class, 'smsmessaging'])->name('smsmessaging');
    Route::get('smsmessaging/unsubscribe/{acr_key}', [PartnerController::class, 'partnerMsgUnsubscribe'])->name('partnerMsgUnsubscribe');
    Route::get('/acrs/unsubscribe/{acr_key}', [PartnerController::class, 'invalidAcrs'])->name('invalidAcrs');
    
    Route::match(['get', 'post'], '/send-sms', [PartnerController::class, 'sendSms'])->name('send-sms');
    // renew
    Route::get('renew/{acr_key}/{keyword}', [PartnerController::class, 'renew'])->name('renew');
    Route::get('refund/{acr_key}', [PartnerController::class, 'refund'])->name('refund');
});



// subscription
// http://127.0.0.1:8000/api/subscription?api_url=https://www.google.com&keyword=BDG&msisdn=8801323174104
Route::match(['get', 'post'], '/subscription', [SubsAndUnsubsController::class, 'subscription'])->name('subscription');

// http://127.0.0.1:8000/api/unsubscription?keyword=BDG&acr=sadjcnjcndkjnsacn&msisdn=8801323174104
Route::match(['get', 'post'], '/unsubscription', [SubsAndUnsubsController::class, 'unsubscription'])->name('unsubscription');

Route::match(['get', 'post'], '/payment', [PaymentController::class, 'payment'])->name('payment');

// https://gpglobal.b2mwap.com/api/status-check?msisdn=8801307345647&keyword=BDGD
Route::match(['get', 'post'], '/status-check', [SubsAndUnsubsController::class, 'statusCheck'])->name('status-check');




// notification
Route::middleware('basicauth')->post('notification', [NotificationController::class, 'notification'])->name('notification');
Route::middleware('basicauth')->post('refund-notification', [NotificationController::class, 'refundNotification'])->name('refund-notification');


// logs
Route::prefix('log')
    ->name('api.log.')
    ->group(function () {
        // http://localhost:3000/api/log/subs-and-unsubs?start_date=2023-12-06&end_date=2023-12-06
        Route::get('subs-and-unsubs', [LogController::class, 'subsAndUnsubs'])->name('subs-and-unsubs');

        // http://localhost:3000/api/log/charge?start_date=2023-12-06&end_date=2023-12-06
        Route::get('charge', [LogController::class, 'charge'])->name('charge');
    });


// OnDemandController
Route::group(['prefix' => 'on-demand/', 'name' => 'on-demand.'], function () {
    Route::match(['get', 'post'], 'create-otp-and-send', [OnDemandController::class, 'createOtpAndSend'])
        ->name('create-otp-and-send');

    Route::match(['get', 'post'], '{id}/charge', [OnDemandController::class, 'charge'])
        ->name('charge');
});








