<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\ServiceProviderInfo;
use App\Models\Consent;
use App\Models\Service;

class ConsentController extends Controller
{
    public function prepare($subscriptionPeriod = null, $keyword = null, $msisdn = null ,Request $request)
    {

        // requested method
        $method = $request->method();

        $getSubscriptionPeriod = $method == "POST" ? $request->subscriptionPeriod : $subscriptionPeriod;
        $service_keyword = $method == "POST" ? $request->keyword : $keyword;
        $msisdn = $method == "POST" ? $request->msisdn : $msisdn;

        $serviceProviderInfo = ServiceProviderInfo::first();
        $service = Service::select()->where('keyword', $service_keyword)->first();

        $url = $serviceProviderInfo->url . '/partner/v3/consent/prepare';

        $urls = [
            'ok' => url('consent/prepare/success'),
            'deny' => url('consent/prepare/deny'),
            'error' => url('consent/prepare/error'),
        ];


        $payload =
            [
                'amount' => $service->amount,
                'currency' => "BDT",
                'MSISDN' => $msisdn,
                'productDescription' => $service->description,
                'subscriptionPeriod' => $getSubscriptionPeriod,
                'urls' => $urls,
                'operatorId' => $serviceProviderInfo->operatorId,
                'merchant' => $service->productId,
                'pinRequest' => [
                    'parameters' => [
                        'serviceName' => $service->name,
                    ]
                ]
                    ];


        try {
            $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)
            ->post($url,$payload);




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
                return $this->respondWithError($responseData['resultDescription']);
            }

        } catch (\Throwable $th) {
            return $this->respondWithError($th->getMessage());
        }











    }
}
