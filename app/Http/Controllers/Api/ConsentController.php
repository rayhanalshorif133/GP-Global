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
            'ok' => env('APP_URL') . "/" . "consent/prepare/success",
            'deny' => env('APP_URL') . "/" . "consent/prepare/deny",
            'error' => env('APP_URL') . "/" . "consent/prepare/error",
        ];


        $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)
            ->post($url, [
                'amount' => $service->amount,
                'currency' => "BDT",
                'MSISDN' => $msisdn,
                'productDescription' => $service->description,
                'subscriptionPeriod' => $getSubscriptionPeriod,
                'urls' => $urls,
                'operatorId' => $serviceProviderInfo->operatorId,
                'pinRequest' => [
                    'parameters' => [
                        'serviceName' => $service->name,
                    ]
                ]
            ]);
        $responseData = $response->json();



        if($responseData == null) {
            return $this->respondWithError("Something went wrong", $responseData);
        }




        if ($responseData['resultCode'] == "SUCCESS") {
            $consent = new Consent();
            $consent->msisdn = $msisdn;
            $consent->amount = $service->amount;
            $consent->currency = "BDT";
            $consent->subscriptionPeriod = $request->subscriptionPeriod;
            $consent->urls = json_encode($urls);
            $consent->service_id = $service->id;
            $consent->response = json_encode($responseData);
            $consent->save();
            return redirect($responseData['url']);
        } else {
            return $this->respondWithError($responseData['resultDescription']);
        }
    }
}
