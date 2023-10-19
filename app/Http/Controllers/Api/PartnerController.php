<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceProviderInfo;
use App\Models\Service;
use Illuminate\Support\Facades\Http;

class PartnerController extends Controller
{
    // smsmessaging
    public function smsmessaging(Request $request, $senderNumber)
    {
        $serviceProviderInfo = ServiceProviderInfo::first();
        

        $url = $serviceProviderInfo->url . '/partner/smsmessaging/v2/outbound/tel:' . $senderNumber .'/requests';

        $service = Service::select()->where('keyword', $request->service_keyword)->frist();
        
        
        // {"outboundSMSMessageRequest":
        //     {"address":"acr:TESTRCbkZ1G0280XyRk4PX2XR1TaTER",
        //      "senderAddress":"tel:+8801323174104",
        //      "messageType":"ARN",
        //      "outboundSMSTextMessage":{"message":"{ServiceName} পরিষেবাটি চালু হয়েছে। আপনার কাছ থেকে {amount} + 16% TAX (VAT,SC) টাকা/{Period} হারে কর্তন করা হবে। পরিষেবাটি বন্ধ করতে {URLLink} এ প্রবেশ করুন।"},
        //      "senderName":"22900"}
        //   }
        $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)
            ->post($url, [
                'outboundSMSMessageRequest' => 
                [
                    'address' => 'acr:' . $request->acr_key,
                    'senderAddress' => 'tel:' . $senderNumber,
                    'messageType' => 'ARN',
                    'outboundSMSTextMessage' => 
                    [
                        'message' => '{ServiceName} পরিষেবাটি চালু হয়েছে। আপনার কাছ থেকে {amount} + 16% TAX (VAT,SC) টাকা/{Period} হারে কর্তন করা হবে। পরিষেবাটি বন্ধ করতে {URLLink} এ প্রবেশ করুন।'
                    ],
                    'senderName' => '22900'
                    
                ]
            ]);
        $responseData = $response->json();
        return $this->respondWithSuccess('smsmessaging', $responseData);

    }
}
