<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Consent;
use Illuminate\Support\Facades\Http;
use App\Models\ServiceProviderInfo;
use App\Models\PartnerSmsMessaging;
use App\Models\SubUnSubLog;
use App\Models\InvalidAcrs;
use App\Models\Subscriber;
use App\Models\OnDemandCharge;
use App\Models\PartnerPayment;

class OnDemandController extends Controller
{
    
   
    public function createOtpAndSend(Request $request) {

       try {
         // check get or post request
        //  https://gpglobal.b2mwap.com/api/on-demand/create-otp-and-send?msisdn=8801323174104&keyword=GJIQ

         $msisdn = $request->msisdn;
         $keyword = $request->keyword;
         if(!$msisdn){
             return response()->json(['message' => 'Msisdn is required', 'example' => '880xxxxxxxxxx']);
         }

         if(!$keyword){
             return response()->json(['message' => 'Keyword is required', 'example' => 'GJIQ']);
         }
 
        $service =  Service::select()->where('keyword', $keyword)->first();
        if(!$service){
            return response()->json(['message' => 'Service not found']);
        }
 
         $onDemandCharge = new OnDemandCharge();
         $onDemandCharge->msisdn = $msisdn;
         $onDemandCharge->keyword = $keyword;
         
 
        
 
         $serviceProviderInfo = ServiceProviderInfo::first();
         $url = $serviceProviderInfo->url . '/partner/pin';
         $payload = [
             "PinCreationRequest" => [
                 "msisdn" => $msisdn,
                 "parameters" => [
                     "description" => $service->description,
                 ],
             ]
         ];
 
         $onDemandCharge->pin_create_payload = json_encode($payload);
         $onDemandCharge->description = $service->description;
         $onDemandCharge->save();
 
         $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)->post($url, $payload);
         $onDemandCharge->pin_create_response = $response->json();
         $onDemandCharge->save();

         if($response->json() == null && $onDemandCharge){

            $url = url('/api/on-demand/' . $onDemandCharge->id . '/charge');
            return response()->json(['message' => 'Success', 'url' => $url]);
         }
       } catch (\Throwable $th) {
              return response()->json(['message' => 'Failed', 'error' => $th->getMessage()]);
       }

    }

    public function charge(Request $request){
        try {
            $onDemandCharge = OnDemandCharge::find($request->id);
            
            if(!$onDemandCharge){
                return response()->json(['message' => 'Failed', 'error' => 'On Demand Charge not found']);
            }

            if(!$request->otp){
                return response()->json(['message' => 'Failed', 'error' => 'OTP is required']);
            }

            $onDemandCharge->otp = $request->otp;
            $onDemandCharge->charge_date = date('Y-m-d');

            // acr create
            $serviceProviderInfo = ServiceProviderInfo::first();
            $url = $serviceProviderInfo->url . '/partner/acrs';
            $payload = [
                "AcrCreationRequest" => [
                    "msisdn" => $onDemandCharge->msisdn,
                    "pin" => $onDemandCharge->otp
                ]
            ];
            $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)->post($url,$payload);
            $responseData = $response->json();
            // length
            $onDemandCharge->acr_create_response = $responseData;

            if(isset($responseData['requestError'])){
                $onDemandCharge->acr_create_messageId = $responseData['requestError']['serviceException']['messageId'];
                $onDemandCharge->acr_create_failed_text = $responseData['requestError']['serviceException']['text'];
                $onDemandCharge->save();
                return response()->json(['message' => 'Failed', 'error' => $responseData['requestError']['serviceException']['text']]);
            }
            $onDemandCharge->acr = $responseData['acr'];
            $onDemandCharge->save();
            return $this->chargeOnDemand($onDemandCharge);

        } catch (\Throwable $th) {
            return response()->json(['message' => 'Failed', 'error' => $th->getMessage()]);
        }
    }

    public function chargeOnDemand($onDemandCharge){

        $acr_key = $onDemandCharge->acr;
        $keyword = $onDemandCharge->keyword;
        
        $serviceProviderInfo = ServiceProviderInfo::first();
        $url = $serviceProviderInfo->url . '/partner/payment/v1/' . $acr_key . '/transactions/amount';
        $service = Service::select()->where('keyword', $keyword)->first();
        $referenceCode = $this->referenceCode();
        $onDemandCharge->referenceCode = $referenceCode;

        $payload = [
                "amountTransaction" => [
                "endUserId" =>  $acr_key,
                "paymentAmount" => [
                    "chargingInformation" => [
                        "amount" => $service->amount,
                        "description" => $service->description,
                        "currency" => "BDT"
                    ],
                    "chargingMetaData" => [
                        "purchaseCategoryCode" => "b2mtech-Game",
                        "productId" => $service->productId,
                    ]
                ],
                "referenceCode" => $referenceCode,
                "operatorId" => $serviceProviderInfo->operatorId,
                "transactionOperationStatus" => "Charged",
            ]
        ];

        $onDemandCharge->charge_payload = json_encode($payload);

        
        $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)->post($url,$payload);
        $responseData = $response->json();

        if(isset($responseData['requestError'])){
            $onDemandCharge->status = 0;
            $onDemandCharge->charge_response = $responseData;
            $onDemandCharge->save();
            return response()->json(['message' => 'Failed', 'error' => $responseData['requestError']['serviceException']['text']]);
        }else{
            $onDemandCharge->status = 1;
            $onDemandCharge->charge_response = json_encode($responseData);
            $onDemandCharge->charge_amount = $service->amount;
            $onDemandCharge->save();
            $data = [
                'message' => 'Success',
                'msisdn' => $onDemandCharge->msisdn,
                'acr' => $onDemandCharge->acr,
            ];
            
            return response()->json($data);
        }
    }


    // public function sendSms($onDemandCharge){
    //     try {
    //         $senderNumber = $onDemandCharge->msisdn;
    //         $keyword = $onDemandCharge->keyword;
    //         $senderNumber = substr($senderNumber, -11);
    //         $senderNumber = "+88" . $senderNumber;
    //         $serviceProviderInfo = ServiceProviderInfo::first();
    //         $url = $serviceProviderInfo->url . '/partner/smsmessaging/v2/outbound/tel:' . $senderNumber . '/requests';
    //         $service = Service::select()->where('keyword', $keyword)->first();
    //         $urlLink = $service->portal_link;
    //         $msg = $service->name  . ' পরিষেবাটি চালু হয়েছে। আপনার কাছ থেকে ' . $service->amount . '+ 16% TAX (VAT,SC) টাকা হারে কর্তন করা হবে। পরিষেবাটি বন্ধ করতে visit করুন। ' . $urlLink;
    //         dd($service);
    //     } catch (\Throwable $th) {
    //         //throw $th;
    //     }
    // }

    


    function referenceCode(){
        $referenceCode =  $this->generateRandomString(20);
        $getRef = OnDemandCharge::where('referenceCode', $referenceCode)->first();
        if($getRef){
            $this->referenceCode();
        }
        return $referenceCode;
    }
    
}
