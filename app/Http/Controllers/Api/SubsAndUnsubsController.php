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

class SubsAndUnsubsController extends Controller
{
    public function subscription(Request $request){

        try {


            $keyword = $request->keyword;
            $msisdn = $request->msisdn;
            $serviceProviderInfo = ServiceProviderInfo::first();
            $service = Service::select()->where('keyword', $keyword)->first();
            $api_url = $service->redirect_url;
            $url = $serviceProviderInfo->url . '/partner/v3/consent/prepare';
            
            $consent = new Consent();
            $consent->msisdn = $msisdn;
            $consent->amount = $service->amount;
            $consent->currency = "BDT";
            $consent->subscriptionPeriod = $service->validity;
            $consent->api_url = $api_url;
            $consent->service_id = $service->id;
            $consent->save();

            $urls = [
                'ok' => url('consent/prepare/'. $consent->id .'/success/'),
                'deny' => url('consent/prepare/'. $consent->id .'/deny'),
                'error' => url('consent/prepare/'. $consent->id .'/error'),
            ];
            $consent->urls = json_encode($urls);
            $payload = [
                'amount' => $service->amount,
                'currency' => "BDT",
                'MSISDN' => $msisdn,
                'productDescription' => $service->description,
                'subscriptionPeriod' => $service->validity,
                'urls' => $urls,
                'operatorId' => $serviceProviderInfo->operatorId,
                'merchant' => $service->productId,
                'pinRequest' => [
                    'parameters' => [
                        'serviceName' => $service->name,
                    ]
                ]
            ];

            $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)->post($url,$payload);
            $responseData = $response->json();

            $consent->result_code = $responseData['resultCode'];
            $consent->payload = json_encode($payload);
            $consent->response = json_encode($responseData);
            $consent->save();
            
            if ($responseData['resultCode'] == "SUCCESS") {
                return redirect($responseData['url']);
            } else {
                $url = $service->redirect_url . '?msisdn=' . $consent->msisdn . '&type=subs&result=failed';
                return redirect($url);
            }

        } catch (\Throwable $th) {
            return $this->respondWithError($th->getMessage());
        }

    }

    public function unsubscription(Request $request){
        // check method
        $keyword = $request->keyword;
        $acr_key = $request->acr;
        $msisdn = $request->msisdn;

        try {
            //code...
            $serviceProviderInfo = ServiceProviderInfo::first();

            $consent = Consent::select()->where('customer_reference', $acr_key)->first();

            if(!$consent){
                $consent = Consent::select()->where('msisdn', $msisdn)->first();
                if(!$consent){
                    return $this->respondWithError('Consent not found');
                }
            }

            $subscriber = Subscriber::select()
                ->where('msisdn', $consent->msisdn)
                ->where('acr', $consent->customer_reference)->first();

            if(!$subscriber){
                return response()->json([
                    'message'  => 'Subscriber not found',
                ], 201);
            }
            $subscriber->status = 0;
            $subscriber->subs_date = null;
            $subscriber->unsubs_date = date('Y-m-d');
            $subscriber->save();


             $msisdn = substr($consent->msisdn, -11);
             $msisdn = "+88" . $msisdn;

             // send sms::start
            $url = $serviceProviderInfo->url . '/partner/smsmessaging/v2/outbound/tel:' . $msisdn . '/requests';

            $service = Service::select()->where('id', $consent->service_id)->first();
            if (!$service) {
                return $this->respondWithError('Service not found');
            }
            $msg = $service->name  . ' পরিষেবাটি সফলভাবে বন্ধ করে দেওয়া হয়েছে।';
            $payload = [
                    'outboundSMSMessageRequest' =>
                        [
                            'address' => 'acr:' . $consent->customer_reference,
                            'senderAddress' => 'tel:' . $msisdn,
                            'messageType' => 'ARN',
                            'outboundSMSTextMessage' =>
                            [
                                'message' => $msg
                            ],
                            'senderName' => $serviceProviderInfo->senderName

                        ]
                ];
            $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)->post($url, $payload);
            $responseData = $response->json();

            $status = isset($responseData['requestError'])? 0 : 1;


            $new_sms = new PartnerSmsMessaging();
            $new_sms->senderNumber = $msisdn;
            $new_sms->keyword = $service->keyword;
            $new_sms->acr_key = $consent->customer_reference;
            $new_sms->senderName = $serviceProviderInfo->senderName;
            $new_sms->messageType = 'ARN';
            $new_sms->message = $msg;
            $new_sms->status = $status;
            $new_sms->payload = json_encode($payload);
            $new_sms->response = json_encode($response->json());
            $new_sms->save();

            // send sms::end

            /*Subscriber log handle*/
            $subUnSubLog = new SubUnSubLog();
            $subUnSubLog->msisdn = $consent->msisdn;
            $subUnSubLog->keyword = $service->keyword;
            $subUnSubLog->status = 0;
            $subUnSubLog->opt_date = date('Y-m-d');
            $subUnSubLog->opt_time = date('H:i:s A');
            $subUnSubLog->save();


            // $consent subscribe
            $consent->is_subscription = 0;
            $consent->save();

            // delete acr::start
            $url = $serviceProviderInfo->url . '/partner/acrs/' . $consent->customer_reference;
            $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)->delete($url);

            $responseData = $response->json();
            if (isset($responseData['requestError'])) {
                return $this->respondWithError("error.!!", $responseData['requestError']['serviceException']);
            }


            $invalidAcrs = InvalidAcrs::updateOrCreate(
                ['acr_key' => $consent->customer_reference],
                [
                    'acr_key' => $consent->customer_reference,
                    'response' => json_encode($responseData)
                ]
            );
            // delete acr::end
            return response()->json([
                'message'  => 'Unsubscribed successfully',
            ], 201);
        } catch (\Throwable $th) {
            return $this->respondWithError('Something went wrong...!', $th->getMessage());
        }

    }

    public function statusCheck(Request $request){
        try {
            $msisdn = $request->msisdn;
            $keyword = $request->keyword;

            $subscriber = Subscriber::select()
                ->where('msisdn', $msisdn)
                ->where('keyword', $keyword)->first();
            if(!$subscriber){
                return response()->json([
                    'message'  => 'Subscriber not found',
                ], 201);
            }

            $status = $subscriber->status == 1 ? 'Subscribed' : 'Unsubscribed';

            return response()->json([
                'msisdn'  => $subscriber->msisdn,
                'acr' => $subscriber->acr,
                'status'  => $status,
            ], 201);



        } catch (\Throwable $th) {
            return $this->respondWithError('Something went wrong...!', $th->getMessage());
        }
    }
}
