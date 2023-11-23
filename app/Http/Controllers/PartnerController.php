<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PartnerController extends Controller
{
    
    public function partnerMsgUnsubscribe($acr_key){
        $url = url('api/partner/acrs/unsubscribe/' . $acr_key);
        return redirect($url);
    }
    
    public function renew($acr_key){
        $url = url('api/partner/renew/' . $acr_key);
        return redirect($url);
    }
    
    public function refund($acr_key){
        $url = url('api/partner/acrs/unsubscribe/' . $acr_key);
        return redirect($url);
    }

    public function unsubscribe($acr_key){
        $url = url('api/partner/acrs/unsubscribe/' . $acr_key);
        return redirect($url);
    }
}
