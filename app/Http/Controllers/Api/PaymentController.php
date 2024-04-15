<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Consent;
use App\Models\ServiceProviderInfo;
use App\Models\Service;
use App\Models\PartnerPayment;
use App\Models\ChargeLog;
use App\Models\Subscriber;
use App\Models\PartnerSmsMessaging;
use App\Models\SubUnSubLog;
use Illuminate\Support\Facades\Http;
use App\Models\OnDemandLog;
use Illuminate\Support\Carbon;

class PaymentController extends Controller
{
    public function payment(Request $request)
    {
        try {
            //code...
            $consent_id = $request->consent_id;
            $serviceProviderInfo = ServiceProviderInfo::first();
            $consent = Consent::find($consent_id);


            if($consent){
                $acr_key = $consent->customer_reference;
                $url = $serviceProviderInfo->url . '/partner/payment/v1/' . $acr_key . '/transactions/amount';
                $service = Service::select()->where('id', $consent->service_id)->first();
                $referenceCode = $this->referenceCode();
                $subscriptionId = $this->generateRandomString(10);

                $payload = [
                    "amountTransaction" => [
                    "endUserId" =>  $acr_key,
                    "paymentAmount" => [
                        "chargingInformation" => [
                            "amount" => $service->amount,
                            "description" => $service->name,
                            "currency" => "BDT"
                        ],
                        "chargingMetaData" => [
                            "purchaseCategoryCode" => "b2mtech-Game",
                            "productId" => $service->productId,
                            "mandateId" => [
                                "subscription" => $subscriptionId,
                                "subscriptionPeriod" => $service->validity,
                                "consentId" => $consent->consentId
                            ]
                        ]
                        ],
                        "referenceCode" => $referenceCode, // "REF-1234567890987654321"
                        "operatorId" => $serviceProviderInfo->operatorId,
                        "transactionOperationStatus" => "Charged",
                    ]
                ];

                if($service->type == "on-demand"){
                    $payload = [
                            "amountTransaction" => [
                            "endUserId" =>  $acr_key,
                            "paymentAmount" => [
                                "chargingInformation" => [
                                    "amount" => $service->amount,
                                    "description" => $service->name,
                                    "currency" => "BDT"
                                ],
                                "chargingMetaData" => [
                                    "purchaseCategoryCode" => "b2mtech-Game",
                                    "productId" => $service->productId,
                                    "mandateId" => [
                                        "consentId" => $consent->consentId
                                    ]
                                ]
                            ],
                            "referenceCode" => $referenceCode, // "REF-1234567890987654321"
                            "operatorId" => $serviceProviderInfo->operatorId,
                            "transactionOperationStatus" => "Charged",
                        ]
                    ];
                }



                $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)->post($url, $payload);
                $responseData = $response->json();


                if($consent->success_url){
                    $redirect_success_url = $consent->success_url;
                }else{
                    $redirect_success_url = $service->redirect_url;
                }

                if($consent->failed_url){
                    $redirect_failed_url = $consent->failed_url;
                }else{
                    $redirect_failed_url = $service->redirect_url;
                }




                if(isset($responseData['requestError'])){
                    $status = 0;
                }
                else{
                    $status = 1;
                }

                $payment = new PartnerPayment();
                $payment->acr_key = $consent->customer_reference;
                $payment->referenceCode = $referenceCode;
                $payment->service_keyword = $service->keyword;
                $payment->subscription = $subscriptionId;
                $payment->consentId = $consent->consentId;
                $payment->status = $status;
                $payment->payload = json_encode($payload);
                $payment->response = json_encode($responseData);
                $payment->save();

                if($status == 1){
                    // check on demand
                    if($service->type == "on-demand"){
                        $dateTime = Carbon::now();
                        $onDemandLog = new OnDemandLog();
                        $onDemandLog->acr_key = $consent->customer_reference;
                        $onDemandLog->msisdn = $consent->msisdn;
                        $onDemandLog->tid = $referenceCode;
                        $onDemandLog->amount = $service->amount;
                        $onDemandLog->keyword = $service->keyword;
                        $onDemandLog->consentId = $consent->consentId;
                        $onDemandLog->opt_date =  $dateTime->format('Y-m-d');
                        $onDemandLog->opt_time = $dateTime->format('H:i:s');
                        $onDemandLog->save();
                        $url = $redirect_success_url . '?keyword='. $service->keyword . '&msisdn=' . $consent->msisdn . '&acr=' . $consent->customer_reference . '&type=ondemand&result=success';
                    }else{
                        $chargeLog = new ChargeLog();
                        $chargeLog->acr_key = $consent->customer_reference;
                        $chargeLog->msisdn = $consent->msisdn;
                        $chargeLog->keyword = $service->keyword;
                        $chargeLog->amount = $service->amount;
                        $chargeLog->type = 'subs';
                        $chargeLog->charge_date = date('Y-m-d');
                        $chargeLog->save();
                        $subscriber = Subscriber::select()
                            ->where('msisdn', $consent->msisdn)
                            ->where('keyword', $service->keyword)
                            ->first();

                        if(!$subscriber){
                            $subscriber = new Subscriber();
                        }

                        $subscriber->msisdn = $consent->msisdn;
                        $subscriber->acr = $consent->customer_reference;
                        $subscriber->tid = $referenceCode;
                        $subscriber->status = 1;
                        $subscriber->keyword = $service->keyword;
                        $subscriber->subscriptionId = $subscriptionId;
                        $subscriber->consentId = $consent->consentId;
                        $subscriber->subs_date = now();
                        $subscriber->save();
                        // subscriber:start

                         $subUnSubLog = new SubUnSubLog();
                         $subUnSubLog->msisdn = $consent->msisdn;
                         $subUnSubLog->keyword = $service->keyword;
                         $subUnSubLog->status = 1;
                         $subUnSubLog->opt_date = date('Y-m-d');
                         $subUnSubLog->opt_time = date('H:i:s');
                         $subUnSubLog->save();
                         $url = $redirect_success_url . '?keyword='. $service->keyword . '&msisdn=' . $consent->msisdn . '&acr=' . $consent->customer_reference . '&type=subs&result=success';
                        
                        //  notify in notification api
                        if($service->notification_url){
                            $notify_url = $service->notification_url . '?msisdn=' . $consent->msisdn . '&keyword=' . $service->keyword . '&acr=' . $consent->customer_reference . '&status=1&reason=ok&op_time=' . date('Y-m-d H:i:s');
                            Http::get($notify_url);
                        }
                    
                    }
                    $sendSMSURL = url('api/partner/smsmessaging/' . $consent->msisdn) . '?serviceKeyword=' . $service->keyword . '&acr_key=' . $consent->customer_reference . '&senderName=' . $serviceProviderInfo->senderName;
                    Http::get($sendSMSURL);
                    return redirect($url);
                }else{

                    

                    // redirect
                    $url = $redirect_failed_url . '?keyword='. $service->keyword . '&msisdn=' . $consent->msisdn . '&acr=' . $consent->customer_reference . '&type=subs&result=failed';
                    if(isset($responseData['requestError'])){

                        //  notify in notification api
                        if($service->notification_url){
                            $resion = $responseData['requestError']['policyException']['text'];
                            $notify_url = $service->notification_url . '?msisdn=' . $consent->msisdn . '&keyword=' . $service->keyword . '&acr=' . $consent->customer_reference . '&status=9&reason=' . $resion .'&op_time=' . date('H:i:s');
                            Http::get($notify_url);
                        }
                        // send sms
                        // find messageId
                        $messageId = $responseData['requestError']['policyException']['messageId'];
                        if($messageId == 'POL1000'){
                            $portal_url = $service->portal_link;
                            $msg = 'আপনার মোবাইলে পর্যাপ্ত পরিমানে ব্যালেন্স নেয়, রিচার্জ করে আবার চেষ্টা করুন। বিস্তারিত ' . $portal_url . '.';
                            $this->sendErrorSMS($consent,$service,$url,$msg);
                        }

                        if($messageId == 'POL0253'){
                            $msg = 'আপনার মোবাইল নাম্বার DND list এ থাকায় নিবন্ধন প্রক্রিয়া সম্পন্ন সম্ভব হচ্ছে না।';
                            $this->sendErrorSMS($consent,$service,$url,$msg);
                        }
                    }
                    return redirect($url);
                }
            }else{
                return $this->respondWithError('Consent not found!');
            }
        } catch (\Throwable $th) {
            return $this->respondWithError("form paymnet",$th->getMessage());
        }
    }


    function referenceCode(){
        $referenceCode =  $this->generateRandomString(20);
        $getRef = PartnerPayment::where('referenceCode', $referenceCode)->first();
        if($getRef){
            $this->referenceCode();
        }
        return $referenceCode;
    }


    // sendErrorSMS
    function sendErrorSMS($consent,$service,$redirect_url,$msg){

        $senderNumber = $consent->msisdn;
        $senderNumber = substr($senderNumber, -11);
        $senderNumber = "+88" . $senderNumber;
        $acr = $consent->customer_reference;
        $keyword = $service->keyword;
        
        $serviceProviderInfo = ServiceProviderInfo::first();
        $url = $serviceProviderInfo->url . '/partner/smsmessaging/v2/outbound/tel:' . $senderNumber . '/requests';
        
        $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)
            ->post($url, [
                'outboundSMSMessageRequest' =>
                [
                    'address' => 'acr:' . $acr,
                    'senderAddress' => 'tel:' . $senderNumber,
                    'messageType' => 'ARN',
                    'outboundSMSTextMessage' => [ 'message' => $msg ],
                    'senderName' => $serviceProviderInfo->senderName,
                ]
            ]);
        $responseData = $response->json();
        $partnerSmsMessaging = new PartnerSmsMessaging();
        $partnerSmsMessaging->senderNumber = $senderNumber;
        $partnerSmsMessaging->keyword = $keyword;
        $partnerSmsMessaging->acr_key =  $acr;
        $partnerSmsMessaging->senderName = $serviceProviderInfo->senderName;
        $partnerSmsMessaging->messageType = 'ARN';
        $partnerSmsMessaging->message = $msg;
        $partnerSmsMessaging->response = json_encode($responseData);
        $partnerSmsMessaging->save();


        return redirect($redirect_url);
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
