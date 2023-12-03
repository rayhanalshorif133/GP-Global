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
use App\Models\SubUnSubLog;
use Illuminate\Support\Facades\Http;

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


                $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)->post($url, $payload);
                $responseData = $response->json();


                $status = isset($responseData->requestError) ? 0 : 1;

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
                    ->where('status',1)
                    ->first();

                    if(!$subscriber){
                        $subscriber = new Subscriber();
                    }

                    $subscriber->msisdn = $consent->msisdn;
                    $subscriber->acr = $consent->customer_reference;
                    $subscriber->tid = $referenceCode;
                    $subscriber->status = 1;
                    $subscriber->keyword = $service->keyword;
                    $subscriber->subs_date = date('Y-m-d');
                    $subscriber->unsubs_date = null;
                    $subscriber->save();
                    // subscriber:start

                     $subUnSubLog = new SubUnSubLog();
                     $subUnSubLog->msisdn = $consent->msisdn;
                     $subUnSubLog->keyword = $service->keyword;
                     $subUnSubLog->status = 1;
                     $subUnSubLog->opt_date = date('Y-m-d');
                     $subUnSubLog->opt_time = date('H:i:s A');
                     $subUnSubLog->save();

                    $sendSMSURL = url('api/partner/smsmessaging/' . $consent->msisdn) . '?serviceKeyword=' . $service->keyword . '&acr_key=' . $consent->customer_reference . '&senderName=' . $serviceProviderInfo->senderName;
                    Http::get($sendSMSURL);
                    // redirect
                    $url = $service->redirect_url . '?msisdn=' . $consent->msisdn . '&acr=' . $consent->customer_reference . '&type=subs&result=success';
                    return redirect($url);
                }else{
                    // redirect
                    $url = $service->redirect_url . '?msisdn=' . $consent->msisdn . '&acr=' . $consent->customer_reference . '&type=subs&result=failed';
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
