<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceProviderInfo;
use App\Models\Service;
use App\Models\PartnerSmsMessaging;
use App\Models\PartnerPayment;
use App\Models\InvalidAcrs;
use App\Models\Consent;
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
            $msg = $service->name  . ' পরিষেবাটি চালু হয়েছে। আপনার কাছ থেকে ' . $service->amount . '+ 16% TAX (VAT,SC) টাকা হারে কর্তন করা হবে। পরিষেবাটি বন্ধ করতে ' . $urlLink . ' এ প্রবেশ করুন।';

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
            $consent = Consent::select()->where('customer_reference', $acr_key)->first();
            $url = $serviceProviderInfo->url . '/partner/payment/v1/' . $acr_key . '/transactions/amount';
            $service = Service::select()->where('keyword', $request->service_keyword)->first();
            
            if (!$service) {
                return $this->respondWithError('Service not found');
            }

            if (!$consent) {
                return $this->respondWithError('ACR not found');
            }

            $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)
                ->post($url, [
                    "amountTransaction" => [
                        "endUserId" => $consent->customer_reference,
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
                                "purchaseCategoryCode" => "Game",
                                "mandateId" => [
                                    "subscription" => $request->subscription,
                                    "subscriptionPeriod" => $service->validity,
                                    "consentId" => $consent->consentId
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
            $partnerPayment->acr_key = $consent->customer_reference;
            $partnerPayment->referenceCode = $request->referenceCode;
            $partnerPayment->service_keyword = $request->service_keyword;
            $partnerPayment->subscription = $request->subscription;
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
            $consent = Consent::select()->where('customer_reference', $acr_key)->first();


            
            // delete acr::start
            $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)->delete($url);
            
            
            $responseData = $response->json();
            if (isset($responseData['requestError'])) {
                return $this->respondWithError("error.!!", $responseData['requestError']['serviceException']);
            }
            

            $invalidAcrs = InvalidAcrs::updateOrCreate(
                ['acr_key' => $acr_key],
                [
                    'acr_key' => $acr_key,
                    'response' => json_encode($responseData)
                ]
            );
            // delete acr::end
            
            
            // send sms::start
            $url = $serviceProviderInfo->url . '/partner/smsmessaging/v2/outbound/tel:' . $consent->msisdn . '/requests';
            $service = Service::select()->where('id', $consent->service_id)->first();
            if (!$service) {
                return $this->respondWithError('Service not found');
            }
            $msg = $service->name  . ' পরিষেবাটি সফলভাবে বন্ধ করে দেওয়া হয়েছে।';
            $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)
                ->post($url, [
                    'outboundSMSMessageRequest' =>
                    [
                        'address' => 'acr:' . $acr_key,
                        'senderAddress' => 'tel:' . $consent->msisdn,
                        'messageType' => 'ARN',
                        'outboundSMSTextMessage' =>
                        [
                            'message' => $msg
                        ],
                        'senderName' => $serviceProviderInfo->senderName

                    ]
                ]);
            $responseData = $response->json();
            // requestError
            if (isset($responseData['requestError'])) {
                return $this->respondWithError("error.!!", $responseData['requestError']['serviceException']);
            }
            // send sms::end

            

            return $this->respondWithSuccess('Acr Invalidated', $invalidAcrs);
        } catch (\Throwable $th) {
            return $this->respondWithError('Something went wrong...!', $th->getMessage());
        }
    }
}
