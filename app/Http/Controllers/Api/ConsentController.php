<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\ServiceProviderInfo;
use App\Models\Consent;
use App\Models\Product;

class ConsentController extends Controller
{
    public function prepare(Request $request)
    {

        $serviceProviderInfo = ServiceProviderInfo::first();
        $product = Product::select()->where('product_key', $request->productKey)
            ->with('service')
            ->first();

        $url = $serviceProviderInfo->url . '/partner/v3/consent/prepare';

        $urls = [
            'ok' => env('APP_URL') . "/" . "consent/prepare/success",
            'deny' => env('APP_URL') . "/" . "consent/prepare/deny",
            'error' => env('APP_URL') . "/" . "consent/prepare/error",
        ];


        $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)
            ->post($url, [
                'amount' => $product->service->amount,
                'currency' => "BDT",
                'productDescription' => $product->description,
                'subscriptionPeriod' => $request->subscriptionPeriod,
                'urls' => $urls,
                'operatorId' => $serviceProviderInfo->operatorId,
                'pinRequest' => [
                    'parameters' => [
                        'serviceName' => $product->service->name,
                    ]
                ]
            ]);
        $responseData = $response->json();
        
       
        if ($responseData['resultCode'] == "SUCCESS") {
            $consent = new Consent();
            $consent->product_id = $product->id;
            $consent->amount = $product->service->amount;
            $consent->currency = "BDT";
            $consent->subscriptionPeriod = $request->subscriptionPeriod;
            $consent->urls = json_encode($urls);
            $consent->serviceName = $product->service->name;
            $consent->response = json_encode($responseData);
            $consent->save();
            return $this->respondWithSuccess('Consent prepared successfully!', $responseData);
        } else {
            return $this->respondWithError($responseData['resultDescription']);
        }
    }
}
