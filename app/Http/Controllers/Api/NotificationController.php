<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\RefundNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{

     // notification
     public function notification(Request $request){
        try {
            $notification = new Notification();
            $notification->acr = $request->deactivatedSubscriptions[0]['acr'];
            $notification->subscription = $request->deactivatedSubscriptions[0]['subscription'];
            $notification->response = json_encode($request->all());
            $notification->save();
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

}
