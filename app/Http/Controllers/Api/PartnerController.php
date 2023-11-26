<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceProviderInfo;
use App\Models\Service;
use App\Models\PartnerSmsMessaging;
use App\Models\PartnerPayment;
use App\Models\InvalidAcrs;
use App\Models\Refund;
use App\Models\Consent;
use App\Models\Subscriber;
use App\Models\SubUnSubLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

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

             // sender number validation::start
             $senderNumber = substr($senderNumber, -11);
             $senderNumber = "+88" . $senderNumber;
             // sender number validation::end

            $serviceProviderInfo = ServiceProviderInfo::first();
            $url = $serviceProviderInfo->url . '/partner/smsmessaging/v2/outbound/tel:' . $senderNumber . '/requests';
            $service = Service::select()->where('keyword', $request->serviceKeyword)->first();
            if (!$service) {
                return $this->respondWithError('Service not found');
            }
            $urlLink = url('unsubscribe') . "/" . $request->acr_key;
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

            // Subscriber::start
            $subscriber = new Subscriber();
            $subscriber->msisdn = $senderNumber;
            $subscriber->opr = "GP";
            $subscriber->status = 1;
            $subscriber->service = $service->name;
            $subscriber->subs_date = date('Y-m-d H:i:s A');
            $subscriber->charge_status = "charged";
            $subscriber->charge_date = date('Y-m-d H:i:s A');
            $subscriber->save();
            /*Subscriber log handle*/
            $subUnSubLog = new SubUnSubLog();
            $subUnSubLog->msisdn = $senderNumber;
            $subUnSubLog->service = $service->name;
            $subUnSubLog->status = 1;
            $subUnSubLog->opt_date = date('Y-m-d');
            $subUnSubLog->opt_time = date('H:i:s A');
            $subUnSubLog->save();
            // Subscriber::end
            
            // return view('consent.prepare.success');
            return $this->respondWithSuccess('Your service has started successfully.',$subscriber);
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

             // sender number validation::start
             $msisdn = substr($consent->msisdn, -11);
             $msisdn = "+88" . $msisdn;
             // sender number validation::end
            
            
            // send sms::start
            $url = $serviceProviderInfo->url . '/partner/smsmessaging/v2/outbound/tel:' . $msisdn . '/requests';

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
                        'senderAddress' => 'tel:' . $msisdn,
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

            /*Subscriber log handle*/
            $subUnSubLog = new SubUnSubLog();
            $subUnSubLog->msisdn = $consent->msisdn;
            $subUnSubLog->service = $service->name;
            $subUnSubLog->status = 0;
            $subUnSubLog->opt_date = date('Y-m-d');
            $subUnSubLog->opt_time = date('H:i:s A');
            $subUnSubLog->save();     
            
            
            // $consent subscribe
            $consent->is_subscription = 0;
            $consent->save();

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
            
            

            return $this->respondWithSuccess('Acr Invalidated', $invalidAcrs);
        } catch (\Throwable $th) {
            return $this->respondWithError('Something went wrong...!', $th->getMessage());
        }
    }

    // sendSms
    public function sendSms(Request $request){
            $serviceProviderInfo = ServiceProviderInfo::first();
        
            
            // send sms::start
            $url = $serviceProviderInfo->url . '/partner/smsmessaging/v2/outbound/tel:'. $request->phone . '/requests';
            $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)
                ->post($url, [
                    'outboundSMSMessageRequest' =>
                    [
                        'address' => 'acr:' . $request->acr,
                        'senderAddress' => 'tel:' . $request->phone,
                        'messageType' => 'ARN',
                        'outboundSMSTextMessage' =>
                        [
                            'message' => $request->msg,
                        ],
                        'senderName' => $serviceProviderInfo->senderName

                    ]
                ]);

            $responseData = $response->json();

            return $this->respondWithSuccess('Successfully send sms', $responseData);
    }

    // renew
    // http://localhost:3000/api/partner/renew/55rmQvayRFfR0CS0KOntVYT0yERlgHVK
    public function renew($acr_key){
        $serviceProviderInfo = ServiceProviderInfo::first();
        $consent = Consent::select()->where('customer_reference', $acr_key)->first();
        $url = $serviceProviderInfo->url . '/partner/acrs/' . $acr_key;
        // sender number validation::start
        $senderNumber = substr($consent->msisdn, -11);
        $senderNumber = "+88" . $senderNumber;
        // sender number validation::end
        $service = Service::select()->where('id', $consent->service_id)->first();
        $urlLink = url('unsubscribe') . "/" . $acr_key;


        $url = $serviceProviderInfo->url . '/partner/acrs/'. $acr_key . '/renew';


        $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)->post($url);

        $responseData = $response->json();


        // send sms::start
            $url = $serviceProviderInfo->url . '/partner/smsmessaging/v2/outbound/tel:'. $senderNumber . '/requests';
            $msg = $service->name  . ' পরিষেবাটি পুনর্নবীকরণ হয়েছে। আপনার কাছ থেকে ' . $service->amount . '+ 16% TAX (VAT,SC) টাকা হারে কর্তন করা হবে। পরিষেবাটি বন্ধ করতে ' . $urlLink . ' এ প্রবেশ করুন।';
            $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)
                ->post($url, [
                    'outboundSMSMessageRequest' =>
                    [
                        'address' => 'acr:' . $acr_key,
                        'senderAddress' => 'tel:' . $senderNumber,
                        'messageType' => 'ARN',
                        'outboundSMSTextMessage' =>
                        [
                            'message' => $msg,
                        ],
                        'senderName' => $serviceProviderInfo->senderName

                    ]
                ]);


            $responseData = $response->json();

        return $this->respondWithSuccess('Successfully renew', $acr_key);
    }

    // refund service
    public function refund($acr_key){
        $serviceProviderInfo = ServiceProviderInfo::first();
        $consent = Consent::select()->where('customer_reference', $acr_key)->first();
        $url = $serviceProviderInfo->url . '/partner/payment/v1/' . $acr_key . "/transactions/amount";
        // sender number validation::start
        $senderNumber = substr($consent->msisdn, -11);
        $senderNumber = "+88" . $senderNumber;
        // sender number validation::end
        $service = Service::select()->where('id', $consent->service_id)->first();  
        
        $referenceCode = $this->referenceCode();


        $postBody = [
            "amountTransaction" => [
                "endUserId" => $acr_key,
                "paymentAmount" => [
                    "chargingInformation" => [
                        "amount" => $service->amount,
                        "currency" => "BDT",
                        "description" => "Product 1 des"
                    ],
                    "chargingMetaData" => [
                        "purchaseCategoryCode" => "b2mtech-Game",
                        "mandateId" => [
                            "subscriptionPeriod" => $service->validity,
                            "consentId" => $consent->consentId
                        ]
                    ]
                ],
                "referenceCode" => $referenceCode,
                "transactionOperationStatus" => "Charged",
            ]
        ];


        $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)
                ->post($url, $postBody);
        
        $response = json_decode($response, true);


        Refund::create([
            'acr_key' => $acr_key,
            'consentId' => $consent->consentId,
            'referenceCode' => $referenceCode,
            'service_keyword' => $service->keyword,
            'sent_response' => json_encode($postBody),
            'get_response' => json_encode($response),
            'status' => true,
        ]);

        // send sms::start
        $url = $serviceProviderInfo->url . '/partner/smsmessaging/v2/outbound/tel:'. $senderNumber . '/requests';
        $msg = $service->name  . ' পরিষেবাটি Refund করা হয়েছে।';
        $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)
            ->post($url, [
                'outboundSMSMessageRequest' =>
                [
                    'address' => 'acr:' . $acr_key,
                    'senderAddress' => 'tel:' . $senderNumber,
                    'messageType' => 'ARN',
                    'outboundSMSTextMessage' =>
                    [
                        'message' => $msg,
                    ],
                    'senderName' => $serviceProviderInfo->senderName

                ]
            ]);
        
        
        return $this->respondWithSuccess('Successfully refund', $response);
    }

    function referenceCode(){
        $referenceCode = "REF-" . $this->generateRandomString(12);
        $getRef = Refund::where('referenceCode', $referenceCode)->first();
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
