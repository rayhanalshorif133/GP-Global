<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceProviderInfo;
use Illuminate\Support\Facades\Http;

class PartnerController extends Controller
{
    
    public function partnerMsgUnsubscribe($acr_key){
        $url = url('api/partner/acrs/unsubscribe/' . $acr_key);
        return redirect($url);
    }
    
    public function renew($acr_key){
        $url = url('api/partner/renew/' . $acr_key);
        return redirect($url);
    }
    
    public function refund($acr_key){
        $url = url('api/partner/refund/' . $acr_key);
        return redirect($url);
    }

    public function unsubscribe($acr_key){
        $url = url('api/partner/acrs/unsubscribe/' . $acr_key);
        return redirect($url);
    }

    public function sendSms($acr_key, $sender_number, $msg){
        $url = url('api/partner/acrs/send-sms/' . $acr_key . '/' . $sender_number . '/' . $msg);
        $serviceProviderInfo = ServiceProviderInfo::first();
        $url = $serviceProviderInfo->url . '/partner/acrs/' . $acr_key;
    
    
        
        
        // send sms::start
        $url = $serviceProviderInfo->url . '/partner/smsmessaging/v2/outbound/tel:'. $sender_number . '/requests';
        $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)
            ->post($url, [
                'outboundSMSMessageRequest' =>
                [
                    'address' => 'acr:55rmQvayRFfR0CS059O5ZSWQI0yX3SIz',
                    'senderAddress' => 'tel:' . $sender_number,
                    'messageType' => 'ARN',
                    'outboundSMSTextMessage' =>
                    [
                        'message' => $msg,
                    ],
                    'senderName' => $serviceProviderInfo->senderName
    
                ]
            ]);
    
        $responseData = $response->json();
    
        return $this->respondWithSuccess('Successfully send sms', $responseData);
    }
    

    public function sendSmsWeb(Request $request){

        $serviceProviderInfo = ServiceProviderInfo::first();
        // send sms::start
        $url = $serviceProviderInfo->url . '/partner/smsmessaging/v2/outbound/tel:'. $request->phone_number . '/requests';
        $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)
            ->post($url, [
                'outboundSMSMessageRequest' =>
                [
                    'address' => 'acr:' . $request->acr_key,
                    'senderAddress' => 'tel:' . $request->phone_number,
                    'messageType' => 'ARN',
                    'outboundSMSTextMessage' =>
                    [
                        'message' => $request->msg,
                    ],
                    'senderName' => $serviceProviderInfo->senderName

                ]
            ]);


        flash()->addSuccess('SMS send successfully!');
        return redirect()->back();
    }
}
