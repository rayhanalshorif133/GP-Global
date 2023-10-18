<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\ServiceProviderInfo;
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

        $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)
            ->post($url, [
                'amount' => 5,
                'currency' => "BDT",
                'productDescription' => $product->description,
                'subscriptionPeriod' => $request->subscriptionPeriod,
                'urls' => [
                    'ok' => "ok",
                    'deny' => "http://deny.com/deny_url_test",
                    'error' => "http://error.com/error_url_test",
                ],
                'operatorId' => $serviceProviderInfo->operatorId,
                'pinRequest' => [
                    'parameters' => [
                        'serviceName' => $product->service->name,
                    ]
                ]
            ]);




        return $this->respondWithSuccess('Consent prepared successfully!', $response->json());
    }
}
