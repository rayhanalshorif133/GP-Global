<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\RefundNotification;
use App\Models\Subscriber;
use App\Models\Service;
use App\Models\SubUnSubLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{

     // notification
     public function notification(Request $request){
        try {
            $notification = new Notification();
            $notification->acr = $request->deactivatedSubscriptions[0]['acr'];
            $notification->subscription = $request->deactivatedSubscriptions[0]['subscription'];
            $notification->op_date = date('Y-m-d');
            $notification->response = json_encode($request->all());
            $notification->save();
            $this->unsubscription($notification->acr);
            return $this->respondWithSuccess('Successful notified. Thanks.');
        } catch (\Throwable $th) {
           return $this->respondWithError('Something went wrong...!', $th->getMessage());
        }
    }

    public function refundNotification(Request $request){
        try {
            $refundNotification = new RefundNotification();
            $refundNotification->transactionReference = $request->transactionReference;
            $refundNotification->timestamp = $request->timestamp;
            $refundNotification->transactionServerReference = $request->transactionServerReference;
            $refundNotification->response = json_encode($request->all());
            $refundNotification->save();
            return $this->respondWithSuccess('Successful notified. Thanks.');
        } catch (\Throwable $th) {
           return $this->respondWithError('Something went wrong...!', $th->getMessage());
        }
    }


    public function unsubscription($acr){
        $subscriber = Subscriber::select()->where('acr', $acr)->first();
        if($subscriber){
            $subscriber->status = 0;
            $subscriber->unsubs_date = now();
            $subscriber->save();
            $subUnSubLog = new SubUnSubLog();
            $subUnSubLog->msisdn = $subscriber->msisdn;
            $subUnSubLog->keyword = $subscriber->keyword;
            $subUnSubLog->status = 0;
            $subUnSubLog->flag = 'gp notify';
            $subUnSubLog->opt_date = date('Y-m-d');
            $subUnSubLog->opt_time = date('H:i:s');
            $subUnSubLog->save();

            $service = Service::select()->where('keyword', $subscriber->keyword)->first();
            // notification_url
            if($service && $service->notification_url){
                $notify_url = $service->notification_url . '?msisdn=' . $subscriber->msisdn . '&keyword=' . $service->keyword . '&acr=' . $acr . '&status=0&reason=gp_notify&op_time=' . date('Y-m-d H:i:s');
                Http::get($notify_url);
            }

        }
        return true;
    }

}
