<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consent;
use App\Models\ServiceProviderInfo;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class ConsentController extends Controller
{
    public function consentPrepareSuccess(Request $request)
    {



        // has customer reference
        $customer_reference = Consent::select()
            ->where('customer_reference', $request->customerReference)
            ->where('is_subscription', 1)
            ->first();


        if($customer_reference){
            return $this->respondWithError('Consent already subscribed.');
        }

        // get last create consent
        $consent = Consent::latest()->with('service')->first();
        $serviceProviderInfo = ServiceProviderInfo::first();

        if($consent){
            $consent->customer_reference = $request->customerReference;
            $consent->consentId = $request->consentId;
            $consent->save();
            $url = url('api/partner/smsmessaging/' . $consent->msisdn) . '?serviceKeyword=' . $consent->service->keyword . '&acr_key=' . $request->customerReference . '&senderName=' . $serviceProviderInfo->senderName;
            return redirect($url);
        }else{
            return $this->respondWithError('Consent prepared failed!' );
        }
    }

    public function consentPrepareDeny(Request $request)
    {
        // return view('consent.prepare.deny');
        return $this->respondWithSuccess('Consent prepared Deny!',$request->all());
    }

    public function consentPrepareError(Request $request)
    {
        // return view('consent.prepare.error');
        return $this->respondWithSuccess('Consent prepared Error!',$request->all());
    }

}
