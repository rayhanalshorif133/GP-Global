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
    public function subscription(Request $request)
    {

        // https://gpglobal.b2mwap.com/api/subscription?keyword=GSD&msisdn=8801323174104&success_url=sub_url&failed_url=unsubscribe___URL

        try {


            $keyword = $request->keyword;
            $msisdn = $request->msisdn;

            // $success_url = rawurldecode($request->success_url);

            // $failed_url = rawurldecode($request->failed_url);





            if (!$keyword) {
                return response()->json([
                    'message'  => 'Invalid Request',
                ], 201);
            }
            $serviceProviderInfo = ServiceProviderInfo::first();
            $service = Service::select()->where('keyword', $keyword)->first();


            $api_url = $service->redirect_url;


            $url = $serviceProviderInfo->url . '/partner/v3/consent/prepare';

            if (!$msisdn) {
                if ($request->failed_url) {
                    $redirectUrl = $request->failed_url . '?msisdn=&type=subs&result=failed';
                } else {
                    $redirectUrl = $api_url . '?msisdn=&type=subs&result=failed';
                }
                return redirect($redirectUrl);
            }
            $consent = new Consent();
            $consent->msisdn = $msisdn;
            $consent->amount = $service->amount;
            if ($request->success_url) {
                $consent->success_url = $request->success_url;
            }
            if ($request->failed_url) {
                $consent->failed_url = $request->failed_url;
            }
            $consent->currency = "BDT";
            $consent->subscriptionPeriod = $service->validity;
            $consent->api_url = $api_url;
            $consent->service_id = $service->id;
            $consent->save();

          

            $urls = [
                'ok' => url('consent/prepare/' . $consent->id . '/success/'),
                'deny' => url('consent/prepare/' . $consent->id . '/deny'),
                'error' => url('consent/prepare/' . $consent->id . '/error'),
            ];

            $consent->urls = json_encode($urls);
            $payload = [
                'amount' => $service->amount,
                'currency' => "BDT",
                'msisdn' => $msisdn,
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




            $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)->post($url, $payload);
            $responseData = $response->json();

            $consent->result_code = $responseData['resultCode'];
            $consent->payload = json_encode($payload);
            $consent->response = json_encode($responseData);
            $consent->save();




            if ($responseData['resultCode'] == "SUCCESS") {
                return redirect($responseData['url']);
            } else {
                if ($consent->failed_url) {
                    $url = $consent->failed_url . '?keyword=' . $service->keyword . '&msisdn=' . $consent->msisdn . '&type=subs&result=failed';
                } else {
                    $url = $service->redirect_url . '?keyword=' . $service->keyword . '&msisdn=' . $consent->msisdn . '&type=subs&result=failed';
                }
                return redirect($url);
            }
        } catch (\Throwable $th) {
            return $this->respondWithError($th->getMessage());
        }
    }

    public function unsubscription(Request $request)
    {
        // check method
        $keyword = $request->keyword;
        $acr_key = $request->acr;
        $msisdn = $request->msisdn;

        try {
            //code...
            $serviceProviderInfo = ServiceProviderInfo::first();

            $subscriber = Subscriber::select()->where('keyword', $keyword)->where('acr', $acr_key)->first();

            if (!$subscriber) {
                return response()->json([
                    'message'  => 'Subscriber not found',
                ], 201);
            }
            $subscriber->status = 0;
            $subscriber->unsubs_date = now();
            $subscriber->save();


            $msisdn = substr($subscriber->msisdn, -11);
            $msisdn = "+88" . $msisdn;

            // send sms::start
            $url = $serviceProviderInfo->url . '/partner/smsmessaging/v2/outbound/tel:' . $msisdn . '/requests';

            $service = Service::select()->where('keyword', $keyword)->first();
            if (!$service) {
                return $this->respondWithError('Service not found');
            }

            if ($keyword == 'GSD') {
                $msg = 'Goldenstream সার্ভিসটি সফলভাবে বন্ধ হয়েছে । পুনরায় চালু করতে ভিজিট করুন  visit https://goldenstreams.co/ ';
            } else if ($keyword == 'GAJAL') {
                $msg = 'Gajal সার্ভিসটি সফলভাবে বন্ধ হয়েছে । পুনরায় চালু করতে ভিজিট করুন  visit http://gajal.b2mwap.com/';
            } else if ($keyword == 'BDGD') {
                $msg = 'BD Gamers সার্ভিসটি সফলভাবে বন্ধ হয়েছে । পুনরায় চালু করতে ভিজিট করুন  visit http://bdgamers.club/';
            } else {
                $msg = $service->name  . ' পরিষেবাটি সফলভাবে বন্ধ করে দেওয়া হয়েছে।';
            }


            $payload = [
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
            ];
            $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)->post($url, $payload);
            $responseData = $response->json();

            $status = isset($responseData['requestError']) ? 0 : 1;


            $new_sms = new PartnerSmsMessaging();
            $new_sms->senderNumber = $msisdn;
            $new_sms->keyword = $service->keyword;
            $new_sms->acr_key = $acr_key;
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
            $subUnSubLog->msisdn = $subscriber->msisdn;
            $subUnSubLog->keyword = $service->keyword;
            $subUnSubLog->status = 0;
            $subUnSubLog->flag = "protal";
            $subUnSubLog->opt_date = date('Y-m-d');
            $subUnSubLog->opt_time = date('H:i:s');
            $subUnSubLog->save();

            // delete acr::start
            $url = $serviceProviderInfo->url . '/partner/acrs/' . $acr_key;
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

            if ($service->notification_url) {
                $notify_url = $service->notification_url . '?msisdn=' . $subscriber->msisdn . '&keyword=' . $service->keyword . '&acr=' . $acr_key . '&type=subs&status=0&reason=ok&op_time=' . date('Y-m-d H:i:s');
                Http::get($notify_url);
            }

            return response()->json([
                'message'  => 'Unsubscribed successful',
            ], 201);
        } catch (\Throwable $th) {
            return $this->respondWithError('Something went wrong...!', $th->getMessage());
        }
    }

    public function statusCheck(Request $request)
    {
        try {
            $msisdn = $request->msisdn;
            $keyword = $request->keyword;

            $subscriber = Subscriber::select()
                ->where('msisdn', $msisdn)
                ->where('keyword', $keyword)->first();
            if (!$subscriber) {
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

    public function balanceCheck(Request $request)
    {

        /* 
            https://portal.dob.telenordigital.com/assets/doc/partner/api/openapi.html#tag/Balance-Check-API/operation/balance

            Note! Not available for all the partners
        */ 

        return response()->json([
            'message'  => 'Note! Not available for all the partners',
        ], 201);

        $serviceProviderInfo = ServiceProviderInfo::first();
        $acr_key = $request->acr;

        $subscriber = Subscriber::select()->where('acr', $acr_key)->first();

        if (!$subscriber) {
            return response()->json([
                'message'  => 'Subscriber not found',
            ], 201);
        }


        $url = $serviceProviderInfo->url . '/partner/acrs/' . $acr_key . '/balance';

        $response = Http::withBasicAuth($serviceProviderInfo->username, $serviceProviderInfo->password)
            ->get($url, [
                'acr' => $acr_key,
            ]);
        return response()->json($response, 201);

        $responseData = $response->json();

        // $status = isset($responseData['requestError']) ? 0 : 1;


        $responseData = $response->json();
        if (isset($responseData['requestError'])) {
            return $this->respondWithError("error.!!", $responseData['requestError']['serviceException']);
        }

        return response()->json([
            'acr' => $acr,
        ], 201);
    }
}
