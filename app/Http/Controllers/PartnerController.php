<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PartnerController extends Controller
{
    
    public function partnerMsgUnsubscribe($acr_key){
        $url = url('api/partner/acrs/unsubscribe/' . $acr_key);
        return redirect($url);
    }
}
