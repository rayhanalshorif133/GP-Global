<?php

use App\Http\Controllers\LogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ConsentController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\WebHomeController;
use App\Http\Controllers\CustomerLogController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PINCreateController;
use App\Http\Controllers\MyAccountController;
use App\Http\Controllers\MailController;

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

Route::middleware('auth')->get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

Route::middleware('auth')->resource('service', ServiceController::class);
Route::middleware('auth')->resource('service-provider-info', ServiceProviderInfoController::class);
Route::middleware('auth')->resource('product', ProductController::class);



Route::middleware('auth')->get('token/create', [AuthController::class, 'createToken'])->name('token.create');


Route::get('consent/prepare/{id}/{type}',[ConsentController::class, 'consentPrepare']);


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

Route::prefix('log')
    ->name('log.')
    ->middleware('auth')
    ->group(function () {
        Route::get('subs-and-unsubs', [LogController::class, 'subsAndUnsubs'])->name('subs-and-unsubs');
        Route::get('charge', [LogController::class, 'charge'])->name('charge');
        Route::get('on-demand-charge', [LogController::class, 'ondemandCharge'])->name('ondemand.charge');
        Route::get('subs-based', [LogController::class, 'subsBased'])->name('subs-based');
        Route::get('yesterday-log', [LogController::class, 'yesterdayLog'])->name('yesterday-log');
    });

Route::get('api', [ApiController::class, 'index'])->name('api.index');


Route::prefix('service-subscription')
->name('service.')
->group(function () {
    Route::post('/new', [ServiceController::class, 'serviceSubscription'])->name('subscription');
    Route::post('/refund', [ServiceController::class, 'serviceRefund'])->name('refund');
});

Route::middleware('auth')->get('send-mail', [MailController::class, 'sendReportMail'])->name('send-mail');
Route::middleware('auth')->get('data-transfer', [MailController::class, 'dataTransfer'])->name('data-transfer');
Route::middleware('auth')->get('check-data', [MailController::class, 'checkData'])->name('check-data');

Route::prefix('customer-log')
    ->name('customer-log.')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [CustomerLogController::class, 'index'])->name('index');
    });



    
Route::get('pin-create', [PINCreateController::class, 'createPIN'])->name('pin-create');
Route::get('msisdn-to-acr', [PINCreateController::class, 'msisdnToAcr'])->name('msisdn-to-acr');
Route::get('charge-on-demand', [PINCreateController::class, 'chargeOnDemand'])->name('charge-on-demand');



Route::get('my-account',[MyAccountController::class, 'index'])->name('my-account');




