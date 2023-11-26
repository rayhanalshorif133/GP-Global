<?php

use App\Http\Controllers\HitLogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ConsentController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\WebHomeController;
use App\Http\Controllers\ApiController;

use App\Http\Controllers\ServiceProviderInfoController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('migrate', function () {
    Artisan::call('migrate:fresh');
    dd("fresh");
});

Route::get('clear', function () {
    
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('optimize:clear');
    Artisan::call('config:cache');
    Artisan::call('optimize');
    Artisan::call('route:cache');
    return 'Clear';
});

Route::get('/', [WebHomeController::class,'index'])->name('web.home');

Auth::routes();

Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

Route::resource('service', ServiceController::class);
Route::resource('service-provider-info', ServiceProviderInfoController::class);
Route::resource('product', ProductController::class);



// consent/prepare
Route::prefix('consent/prepare/')
    ->name('consent.prepare.')
    ->group(function () {
        Route::get('success', [ConsentController::class, 'consentPrepareSuccess'])->name('success');
        Route::get('deny', [ConsentController::class, 'consentPrepareDeny'])->name('deny');
        Route::get('error', [ConsentController::class, 'consentPrepareError'])->name('error');    
});


Route::prefix('partner/smsmessaging/')
    ->name('partner.smsmessaging.')
    ->group(function () {
        Route::get('unsubscribe/{acr_key}', [PartnerController::class, 'partnerMsgUnsubscribe'])->name('unsubscribe');
        Route::post('/send-sms', [PartnerController::class, 'sendSmsWeb'])->name('send-sms.web');
    });

Route::get('renew/{acr_key}', [PartnerController::class, 'renew'])->name('acr_key.renew');
Route::get('refund/{acr_key}', [PartnerController::class, 'refund'])->name('acr_key.refund');
Route::get('unsubscribe/{acr_key}', [PartnerController::class, 'unsubscribe'])->name('acr_key.unsubscribe');
Route::get('send-sms/{acr_key}/{sender_number}/{msg}', [PartnerController::class, 'sendSms'])->name('acr_key.sendSms');

Route::prefix('hit_log')
    ->name('hit_log.')
    ->group(function () {
        Route::get('sent/{id?}', [HitLogController::class, 'sent'])->name('sent');
        Route::get('received', [HitLogController::class, 'received'])->name('received');
    });

Route::get('api', [ApiController::class, 'index'])->name('api.index');


Route::prefix('service-subscription')
->name('service.')
->group(function () {
    Route::post('/new', [ServiceController::class, 'serviceSubscription'])->name('subscription');
    Route::post('/refund', [ServiceController::class, 'serviceRefund'])->name('refund');
});


