<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PartnerController extends Controller
{
    //
    // partnerMsgUnsubscribe
    public function partnerMsgUnsubscribe($acr_key){
        
          
        return $this->respondWithSuccess('partnerMsgUnsubscribe');
    }
}
