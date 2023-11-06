<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceProviderInfo;
use App\Models\Service;
use App\Models\PartnerSmsMessaging;
use App\Models\PartnerPayment;
use App\Models\InvalidAcrs;
use Illuminate\Support\Facades\Http;

class PartnerController extends Controller
{

    // getAcrs

    public function getAcrs(){
        return "getAcrs";
    }

    // smsmessaging
    public function smsmessaging(Request $request, $senderNumber)
    {
        

        try {
            $serviceProviderInfo = ServiceProviderInfo::first();
            $url = $serviceProviderInfo->url . '/partner/smsmessaging/v2/outbound/tel:' . $senderNumber . '/requests';
            $service = Service::select()->where('keyword', $request->serviceKeyword)->first();
            if (!$service) {
                return $this->respondWithError('Service not found');
            }
            $urlLink = url('partner/smsmessaging/unsubscribe/') . $request->acr_key;
            $msg = $service->name  . ' পরিষেবাটি চালু হয়েছে। আপনার কাছ থেকে ' . $service->amount . '+ 16% TAX (VAT,SC) টাকা হারে কর্তন করা হবে। পরিষেবাটি বন্ধ করতে' . $urlLink . 'এ প্রবেশ করুন।';

            $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)
                ->post($url, [
                    'outboundSMSMessageRequest' =>
                    [
                        'address' => 'acr:' . $request->acr_key,
                        'senderAddress' => 'tel:' . $senderNumber,
                        'messageType' => 'ARN',
                        'outboundSMSTextMessage' =>
                        [
                            'message' => $msg
                        ],
                        'senderName' => $request->senderName

                    ]
                ]);
            $responseData = $response->json();
            // requestError
            if (isset($responseData['requestError'])) {
                return $this->respondWithError("error.!!", $responseData['requestError']['serviceException']);
            }

            $partnerSmsMessaging = new PartnerSmsMessaging();
            $partnerSmsMessaging->senderNumber = $senderNumber;
            $partnerSmsMessaging->service_keyword = $request->serviceKeyword;
            $partnerSmsMessaging->acr_key = $request->acr_key;
            $partnerSmsMessaging->senderName = $request->senderName;
            $partnerSmsMessaging->messageType = 'ARN';
            $partnerSmsMessaging->message = $msg;
            $partnerSmsMessaging->response = json_encode($responseData);
            $partnerSmsMessaging->save();


            return $this->respondWithSuccess('smsmessaging', $responseData);
        } catch (\Throwable $th) {
            return $this->respondWithError('Something went wrong...!', $th->getMessage());
        }
    }

    // payment
    public function payment(Request $request, $acr_key)
    {

        try {
            $serviceProviderInfo = ServiceProviderInfo::first();
            $url = $serviceProviderInfo->url . '/partner/payment/v1/' . $acr_key . '/transactions/amount';
            $service = Service::select()->where('keyword', $request->service_keyword)->first();
            if (!$service) {
                return $this->respondWithError('Service not found');
            }
            $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)
                ->post($url, [
                    "amountTransaction" => [
                        "endUserId" => $acr_key,
                        "transactionOperationStatus" => "Charged",
                        "referenceCode" => $request->referenceCode, // "REF-1234567890987654321"
                        "paymentAmount" => [
                            "chargingInformation" => [
                                "amount" => $service->amount,
                                "description" => [
                                    $service->name
                                ],
                                "currency" => "BDT"
                            ],
                            "chargingMetaData" => [
                                "purchaseCategoryCode" => $serviceProviderInfo->purchaseCategoryCode,
                                "mandateId" => [
                                    "subscription" => $request->mandateId_subscription_num,
                                    "subscriptionPeriod" => $service->validity,
                                    "consentId" => $request->consentId
                                ]
                            ]
                        ],
                        "operatorId" => $serviceProviderInfo->operatorId
                    ]
                ]);


            $responseData = $response->json();
            // request Error
            if (isset($responseData['requestError'])) {
                return $this->respondWithError("error.!!", $responseData);
            }


            $partnerPayment = new PartnerPayment();
            $partnerPayment->acr_key = $acr_key;
            $partnerPayment->referenceCode = $request->referenceCode;
            $partnerPayment->service_keyword = $request->service_keyword;
            $partnerPayment->mandateId_subscription_num = $request->mandateId_subscription_num;
            $partnerPayment->consentId = $request->consentId;
            $partnerPayment->response = json_encode($responseData);
            $partnerPayment->save();




            return $this->respondWithSuccess('smsmessaging', $responseData);
        } catch (\Throwable $th) {
            return $this->respondWithError('Something went wrong...!', $th->getMessage());
        }
    }

    // invalidAcrs
    public function invalidAcrs($acr_key)
    {
        try {
            $serviceProviderInfo = ServiceProviderInfo::first();
            $url = $serviceProviderInfo->url . '/partner/acrs/' . $acr_key;

            $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)->delete($url);


            $responseData = $response->json();
            // request Error
            if (isset($responseData['requestError'])) {
                return $this->respondWithError("error.!!", $responseData['requestError']['serviceException']);
            }

            $invalidAcrs = new InvalidAcrs();
            $invalidAcrs->acr_key = $acr_key;
            $invalidAcrs->response = json_encode($responseData);
            $invalidAcrs->save();

            return $this->respondWithSuccess('Acr Invalidated', $responseData);
        } catch (\Throwable $th) {
            return $this->respondWithError('Something went wrong...!', $th->getMessage());
        }
    }
}
