<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConsentController extends Controller
{
    public function consentPrepareSuccess()
    {

        // return view('consent.prepare.success');
        return $this->respondWithSuccess('Consent prepared successfully!');
    }

    public function consentPrepareDeny()
    {
        // return view('consent.prepare.deny');
        return $this->respondWithSuccess('Consent prepared Deny!');
    }

    public function consentPrepareError()
    {
        // return view('consent.prepare.error');
        return $this->respondWithSuccess('Consent prepared Error!');
    }
}
