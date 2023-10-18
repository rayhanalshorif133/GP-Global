<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\ServiceProviderInfo;

class ConsentController extends Controller
{
    public function prepare(Request $request)
    {
        /* {"amount":5,
            "currency":"BDT",
            "productDescription":"Game Thief",
            "subscriptionPeriod":"P1D",
            "urls":{"ok":"http://ok.com/ok_url_test",
                    "deny":"http://deny.com/deny_url_test",
                    "error":"http://error.com/error_url_test"},
            "operatorId":"GRA-BD", 
            "pinRequest": {
                "parameters": {
                  "serviceName": "Game Thief"
                 }
             }
            }
        */
        $serviceProviderInfo = ServiceProviderInfo::first();

        $url = $serviceProviderInfo->url . '/partner/v3/consent/prepare';

        $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)
            ->post($url, [
                'amount' => $request->amount,
                'currency' => "BDT",
                'productDescription' => $request->productDescription,
                'subscriptionPeriod' => $request->subscriptionPeriod,
                'urls' => [
                    'ok' => $request->ok,
                    'deny' => $request->deny,
                    'error' => $request->error,
                ],
                'operatorId' => $serviceProviderInfo->operatorId,
                'pinRequest' => [
                    'parameters' => [
                        'serviceName' => $request->serviceName,
                    ]
                ]
            ]);

                // 'name',
                // 'keyword',
                // 'validity',



        return $this->respondWithSuccess('Consent prepared successfully!', $response->json());
    }
}
