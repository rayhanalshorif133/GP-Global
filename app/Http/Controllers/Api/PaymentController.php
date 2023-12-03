<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Consent;
use App\Models\ServiceProviderInfo;
use App\Models\Service;
use App\Models\PartnerPayment;
use App\Models\ChargeLog;
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


                $status = isset($responseData['requestError']) ? 0 : 1;

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

                    // charge log:start
                    $chargeLog = new ChargeLog();
                    $chargeLog->acr_key = $consent->customer_reference;
                    $chargeLog->msisdn = $consent->msisdn;
                    $chargeLog->keyword = $service->keyword;
                    $chargeLog->amount = $service->amount;
                    $chargeLog->type = 'subs';
                    $chargeLog->charge_date = date('Y-m-d');
                    $chargeLog->save();
                    // charge log:end
                    // send sms for payment success
                    $sendSMSURL = url('api/partner/smsmessaging/' . $consent->msisdn) . '?serviceKeyword=' . $service->keyword . '&acr_key=' . $consent->customer_reference . '&senderName=' . $serviceProviderInfo->senderName;
                    Http::get($sendSMSURL);
                    // redirect
                    $url = $consent->api_url . '?msisdn=' . $consent->msisdn . 'acr=' . $consent->customer_reference . '&type=subs&result=success';
                    return redirect($url);
                }else{
                    // redirect
                    $url = $consent->api_url . '?msisdn=' . $consent->msisdn . 'acr=' . $consent->customer_reference . '&type=subs&result=failed';
                    return redirect($url);
                }



                return $this->respondWithSuccess('Payment success!', $request->all());
            }else{
                return $this->respondWithError('Consent not found!');
            }
        } catch (\Throwable $th) {
            return $this->respondWithError($th->getMessage());
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
