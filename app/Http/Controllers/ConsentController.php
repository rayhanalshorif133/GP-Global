<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConsentController extends Controller
{
    public function consentPrepareSuccess(Request $request)
    {

        // return view('consent.prepare.success');
        return $this->respondWithSuccess('Consent prepared successfully!',$request->all());
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
