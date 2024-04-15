<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ServiceProviderInfo;
use App\Models\Service;
use App\Models\PartnerPayment;
use App\Models\Consent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;


class PINCreateController extends Controller
{


    public function createPIN(Request $request) {
        $serviceProviderInfo = ServiceProviderInfo::first();
        $url = $serviceProviderInfo->url . '/partner/pin';
        // "https://api.dob.telenordigital.com/partner/pin"
        // https://gpglobal.b2mwap.com/pin-create
        $payload = [
            "PinCreationRequest" => [
                "msisdn" => "8801323174104",
                "parameters" => [
                    "description" => "Gajal",
                ],
            ]
        ];

    $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)->post($url, $payload);
    $responseData = $response->json();
    dd($responseData);


    }


    // msisdnToAcr
    public function msisdnToAcr(Request $request){
        // https://gpglobal.b2mwap.com/msisdn-to-acr
        $msisdn = '+8801323174104';
        $serviceProviderInfo = ServiceProviderInfo::first();
        $payload = [
            "AcrCreationRequest" => [
                "msisdn" => "8801323174104",
                "pin" => "6907"
            ]
        ];

        
        $url = $serviceProviderInfo->url . '/partner/acrs';
        $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)->post($url,$payload);
        //dd($response);
        $responseData = $response->json();
        dd($responseData);
    }


    public function chargeOnDemand(Request $request){
        // $acr_key = $consent->customer_reference;
        // https://gpglobal.b2mwap.com/charge-on-demand
        $acr_key = '1jspKzXuo7ZHv391O7TBIQbcYy3h67je';
        // $consent = Consent::select()->where('customer_reference', $acr_key)->first();
        $serviceProviderInfo = ServiceProviderInfo::first();
        $url = $serviceProviderInfo->url . '/partner/payment/v1/' . $acr_key . '/transactions/amount';
        // $service = Service::select()->where('id', $consent->service_id)->first();
        $service = Service::select()->first();
        $referenceCode = $this->referenceCode();
        $subscriptionId = $this->generateRandomString(10);

        $payload = [
                "amountTransaction" => [
                "endUserId" =>  $acr_key,
                "paymentAmount" => [
                    "chargingInformation" => [
                        "amount" => 5,
                        "description" => 'Gajal',
                        "currency" => "BDT"
                    ],
                    "chargingMetaData" => [
                        "purchaseCategoryCode" => "b2mtech-Game",
                        "productId" => $service->productId,
                    ]
                ],
                "referenceCode" => $referenceCode, // "REF-1234567890987654321"
                "operatorId" => $serviceProviderInfo->operatorId,
                "transactionOperationStatus" => "Charged",
            ]
        ];

        
        $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)->post($url,$payload);
        $responseData = $response->json();
        dd($responseData);




    }

    


    function referenceCode(){
        $referenceCode =  $this->generateRandomString(20);
        $getRef = PartnerPayment::where('referenceCode', $referenceCode)->first();
        if($getRef){
            $this->referenceCode();
        }
        return $referenceCode;
    }

    function generateRandomString($length = 25) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
}
