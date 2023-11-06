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

        // https://gpglobal.technical-content.xyz/consent/prepare/success?customerReference=55rmQvayRFfR0CS0UzeC2r7t1BLRvEI5&consentId=1ca35b58-4872-446e-8b01-12184c0eb9ed


        // has customer reference
        $customer_reference = Consent::select()->where('customer_reference', $request->customerReference)->first();

        
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
            return $this->respondWithError('Consent prepared failed!');
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
