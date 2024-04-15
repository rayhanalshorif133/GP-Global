<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscriber;
use App\Models\OnDemandLog;
use App\Models\SubUnSubLog;
use App\Models\ChargeLog;

class CustomerLogController extends Controller
{
    public function index(Request $request)
    {

        if($request->phone){
            $subscriber_details = Subscriber::select()
                ->where('msisdn', $request->phone)
                ->get();
            $subscriptions = SubUnSubLog::select()
                ->orderBy('id', 'desc')
                ->where('msisdn', $request->phone)
                ->get()->take(50);
            $onDemands = OnDemandLog::select()
                ->orderBy('id', 'desc')
                ->where('msisdn', $request->phone)
                ->get()->take(50);
            $chargeLogs = ChargeLog::select()
                ->orderBy('id', 'desc')
                ->where('msisdn', $request->phone)
                ->get()->take(50);
            $data = [
                'subscriber_details' => $subscriber_details,
                'subscriptions' => $subscriptions,
                'onDemands' => $onDemands,
                'chargeLogs' => $chargeLogs,
            ];
            return $this->respondWithSuccess("Successfully fetched customer log", $data);
        }
        return view('customer-log.index');
    }
}
