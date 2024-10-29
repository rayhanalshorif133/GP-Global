<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\PartnerPayment;
use Illuminate\Support\Facades\Http;
use App\Models\ServiceProviderInfo;
use App\Models\Consent;
use App\Models\RechargeAndBuy;

class RechargeAndBuyController extends Controller
{


    public function prepare(Request $request)
    {

        try {
            // check get or post request

            // http://127.0.0.1:8000/api/payment-callback/?keyword=BDG&msisdn=8801709370009&acr=9DQRqZmcTvQDZpY11faryyp68NeoftHT&type=subs&result=failed&op_time=2024-10-29%2009:47:30
            //  https://gpglobal.b2mwap.com/api/recharge-and-buy/prepare?acr=9DQRqZmcTvQDZpY11faryyp68NeoftHT&consent_id=15208565


            if (!$request->acr || !$request->consent_id) {
                return $this->respondWithError('acr and consent_id both are required');
            }

            $referenceCode = $this->referenceCode();
            $acr = $request->acr;
            $payment = PartnerPayment::select()->where('acr_key', $acr)->first();
            $service = Service::select()->where('keyword', $payment->service_keyword)->first();
            $consent = Consent::find($request->consent_id);
            $serviceProviderInfo = ServiceProviderInfo::first();

            $url = 'https://api.dob.telenordigital.com/partner/payment/v1/' . $acr . '/transactions/recharge/prepare';

            $rechargeAndBuy = new RechargeAndBuy();
            $rechargeAndBuy->acr = $acr;
            $rechargeAndBuy->msisdn = $consent->msisdn;
            $rechargeAndBuy->consent_id = $request->consent_id;
            $rechargeAndBuy->keyword = $service->keyword;
            $rechargeAndBuy->target_amount = $service->amount;
            $rechargeAndBuy->status = 0;
            $rechargeAndBuy->referenceCode = $referenceCode;
            $rechargeAndBuy->originalReferenceCode = $payment->referenceCode;
            $rechargeAndBuy->hit_url = $url;
            $rechargeAndBuy->save();


            // Payload structure
            $payload = [
                "originalReferenceCode" => $payment->referenceCode,
                "referenceCode" => $referenceCode,
                "urls" => [
                    "ok" => url('/api/recharge-and-buy/' . $rechargeAndBuy->id . '/callback/ok'),
                    "deny" => url('/api/recharge-and-buy/' . $rechargeAndBuy->id . '/callback/deny'),
                    "error" => url('/api/recharge-and-buy/' . $rechargeAndBuy->id . '/callback/error'),
                ]
            ];


            $rechargeAndBuy->payload = json_encode($payload);
            $rechargeAndBuy->save();


            $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])->post($url, $payload);


            if (isset($response['continueUrl'])) {
                $redirect_url = $response['continueUrl'];
            }

            $rechargeAndBuy->continueUrl = $redirect_url;
            $rechargeAndBuy->response = json_encode($response->json());
            $rechargeAndBuy->save();




            return redirect()->away($redirect_url);
        } catch (\Throwable $th) {
            return $this->respondWithError("form paymnet", $th->getMessage());
        }
    }
    function referenceCode()
    {
        $referenceCode =  $this->generateRandomString(20);
        $getRef = PartnerPayment::where('referenceCode', $referenceCode)->first();
        if ($getRef) {
            $this->referenceCode();
        }
        return $referenceCode;
    }

    public function callback($recharge_id, $status)
    {
        try {
            $rechargeAndBuy = RechargeAndBuy::find($recharge_id);
            $rechargeAndBuy->recharge_status = $status;
            if ($status == 'ok') {
                $rechargeAndBuy->status = 1;
                $rechargeAndBuy->save();
                $url = url('api/payment?consent_id=' . $rechargeAndBuy->consent_id);
                return redirect($url);
            } else {
                
                // redirect
                $consent = Consent::find($rechargeAndBuy->consent_id);
                $service = Service::select()->where('keyword', $rechargeAndBuy->keyword)->first();
                if($consent->failed_url){
                    $redirect_failed_url = $consent->failed_url;
                }else{
                    $redirect_failed_url = $service->redirect_url;
                }

                if (strpos($redirect_failed_url, "?") !== false) {
                    $url = $redirect_failed_url . '&keyword=' . $rechargeAndBuy->keyword . '&msisdn=' . $rechargeAndBuy->msisdn . '&acr=' . $rechargeAndBuy->acr . '&type=subs&result=failed&op_time=' . date('Y-m-d H:i:s');
                } else {
                    $url = $redirect_failed_url . '?keyword=' . $rechargeAndBuy->keyword . '&msisdn=' . $rechargeAndBuy->msisdn . '&acr=' . $rechargeAndBuy->acr . '&type=subs&result=failed&op_time=' . date('Y-m-d H:i:s');
                }

                return redirect($url);
            }
        } catch (\Throwable $th) {
            return redirect()->back();
        }
    }
}
