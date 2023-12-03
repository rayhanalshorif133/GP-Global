<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Consent;
use Illuminate\Support\Facades\Http;
use App\Models\ServiceProviderInfo;

class SubsAndUnsubsController extends Controller
{
    public function subscription(Request $request){

        try {


            $keyword = $request->keyword;
            $msisdn = $request->msisdn;
            $api_url = $request->api_url;
            $serviceProviderInfo = ServiceProviderInfo::first();
            $service = Service::select()->where('keyword', $keyword)->first();
            $url = $serviceProviderInfo->url . '/partner/v3/consent/prepare';
            $urls = [
                'ok' => url('consent/prepare/success'),
                'deny' => url('consent/prepare/deny'),
                'error' => url('consent/prepare/error'),
            ];

            $payload = [
                'amount' => $service->amount,
                'currency' => "BDT",
                'MSISDN' => $msisdn,
                'productDescription' => $service->description,
                'subscriptionPeriod' => $service->validity,
                'urls' => $urls,
                'operatorId' => $serviceProviderInfo->operatorId,
                'merchant' => $service->productId,
                'pinRequest' => [
                    'parameters' => [
                        'serviceName' => $service->name,
                    ]
                ]
            ];

            $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)->post($url,$payload);

            $responseData = $response->json();

            $consent = new Consent();
            $consent->msisdn = $msisdn;
            $consent->amount = $service->amount;
            $consent->currency = "BDT";
            $consent->subscriptionPeriod = $service->validity;
            $consent->urls = json_encode($urls);
            $consent->api_url = $api_url;
            $consent->service_id = $service->id;
            $consent->result_code = $responseData['resultCode'];
            $consent->payload = json_encode($payload);
            $consent->response = json_encode($responseData);
            $consent->save();


            if ($responseData['resultCode'] == "SUCCESS") {
                return redirect($responseData['url']);
            } else {
                return response()->json([
                    'message'  => $responseData['message'],
                ], 201);
            }

        } catch (\Throwable $th) {
            return $this->respondWithError($th->getMessage());
        }

    }

    public function unsubscription(){
        dd('unsubscription');
    }
}
