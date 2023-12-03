<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consent;
use App\Models\Service;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class ConsentController extends Controller
{
    public function consentPrepare(Request $request,$id,$type)
    {
        $consent = Consent::find($id);
        if($type == 'success'){
            $consent->customer_reference = $request->customerReference;
            $consent->consentId = $request->consentId;
            $consent->save();

            $url = url('api/payment?consent_id=' . $consent->id);
            return redirect($url);
        }else{
            $service = Service::select()->where('id', $consent->service_id)->first();
            $url = $service->redirect_url . '?msisdn=' . $consent->msisdn . '&type=subs&result=failed';
            return redirect($url);
        }
    }

}
