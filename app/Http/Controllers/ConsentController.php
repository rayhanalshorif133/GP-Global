<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consent;
use App\Models\ServiceProviderInfo;
use Illuminate\Support\Facades\Http;

class ConsentController extends Controller
{
    public function consentPrepareSuccess(Request $request)
    {


        // has customer reference
        // $customer_reference = Consent::select()->where('customer_reference', $request->customerReference)->first();

        // if($customer_reference){
        //     return $this->respondWithError('Consent already subscribed.');
        // }
        
        // http://127.0.0.1:8000/consent/prepare/success?customerReference=55rmQvayRFfR0CS0utPQGqbsJy8dpnFK&consentId=c7f59c7d-117f-4217-9cd9-12c0916a60c6
        // get last create consent
        $consent = Consent::latest()->first();
        $serviceProviderInfo = ServiceProviderInfo::first();
        if($consent){
            $consent->customer_reference = $request->customerReference;
            $consent->consentId = $request->consentId;
            $consent->save();

            // send notification by sms to the client
            // $url = url('api/partner/smsmessaging') . '/' . $consent->msisdn; 
            $url = url('api/check') . '?msisdn=' . $consent->msisdn; 
            return redirect($url);
            // $res = Http::post($url, [
            //     'service_keyword' => $request->serviceKeyword,
            //     'acr_key' => $request->customerReference,
            //     'senderName' => $serviceProviderInfo->senderName,
            //     ]);

            return $this->respondWithSuccess('Consent prepared successfully!',$res);
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
