<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consent;

class ConsentController extends Controller
{
    public function consentPrepareSuccess(Request $request)
    {

        // has customer reference
        $customer_reference = Consent::select()->where('customer_reference', $request->customerReference)->first();

        if($customer_reference){
            return view('consent.prepare.already_done');
        }

        // get last create consent
        $consent = Consent::latest()->first();
        if($consent){
            $consent->customer_reference = $request->customerReference;
            $consent->consentId = $request->consentId;
            $consent->save();
        }


        return view('consent.prepare.success');
        // return $this->respondWithSuccess('Consent prepared successfully!',$request->all());
    }

    public function consentPrepareDeny(Request $request)
    {
        return view('consent.prepare.deny');
        // return $this->respondWithSuccess('Consent prepared Deny!',$request->all());
    }

    public function consentPrepareError(Request $request)
    {
        return view('consent.prepare.error');
        // return $this->respondWithSuccess('Consent prepared Error!',$request->all());
    }
}
