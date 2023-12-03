<?php

use App\Http\Controllers\Api\ConsentController;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\RefundNotificationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SubsAndUnsubsController;
use App\Http\Controllers\Api\PaymentController;
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


Route::group(['prefix' => 'consent', 'name' => 'consent.'], function () {
    Route::match(['get', 'post'], 'prepare/{subscriptionPeriod}/{productKey}/{msisdn}', [ConsentController::class, 'prepare'])
    ->name('consent.prepare');
});




Route::group(['prefix' => 'partner', 'name' => 'partner.'], function () {
    Route::get('smsmessaging/{senderNumber}', [PartnerController::class, 'smsmessaging'])->name('smsmessaging');
    Route::get('smsmessaging/unsubscribe/{acr_key}', [PartnerController::class, 'partnerMsgUnsubscribe'])->name('partnerMsgUnsubscribe');
    //Route::post('payment/{acr_key}', [PartnerController::class, 'payment'])->name('payment');
    Route::match(['get', 'post'], '/payment/{acr_key}', [PartnerController::class, 'payment'])->name('payment');
    Route::get('/acrs/unsubscribe/{acr_key}', [PartnerController::class, 'invalidAcrs'])->name('invalidAcrs');
    Route::post('/send-sms', [PartnerController::class, 'sendSms'])->name('send-sms');
    // renew
    Route::get('renew/{acr_key}/{keyword}', [PartnerController::class, 'renew'])->name('renew');
    Route::get('refund/{acr_key}', [PartnerController::class, 'refund'])->name('refund');
});



// subscription
Route::match(['get', 'post'], '/subscription', [SubsAndUnsubsController::class, 'subscription'])->name('subscription');
Route::match(['get', 'post'], '/unsubscription', [SubsAndUnsubsController::class, 'unsubscription'])->name('unsubscription');

// payment
Route::match(['get', 'post'], '/payment', [PaymentController::class, 'payment'])->name('payment');




// notification
Route::middleware('basicauth')->post('notification', [NotificationController::class, 'notification'])->name('notification');
Route::middleware('basicauth')->post('refund-notification', [NotificationController::class, 'refundNotification'])->name('refund-notification');










