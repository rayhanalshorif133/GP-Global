<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consent;
use App\Models\Service;
use App\Models\ConsentBackLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class ConsentController extends Controller
{
    public function consentPrepare(Request $request,$id,$type)
    {


        $consent = Consent::find($id);

        $newConsentBackLog = new ConsentBackLog();
        $newConsentBackLog->msisdn = $consent->msisdn;
        if($type == 'success'){
            $newConsentBackLog->customer_reference = $request->customerReference;
        }else{
            $newConsentBackLog->customer_reference = $consent->customer_reference;
        }
        $newConsentBackLog->consentId = $consent->consentId? $consent->consentId : $request->consentId;
        $newConsentBackLog->service_id = $consent->service_id;
        $newConsentBackLog->type = $type;
        $newConsentBackLog->response = json_encode($request->all());
        $newConsentBackLog->save();




        if($type == 'success'){
            $consent->customer_reference = $request->customerReference;
            $consent->consentId = $request->consentId;
            $consent->save();

            $url = url('api/payment?consent_id=' . $consent->id);


            return redirect($url);
        }else{

            //dd($request->all()); 
            $service = Service::select()->where('id', $consent->service_id)->first();

            if($consent->failed_url){
                $redirect_url = $consent->failed_url;
            }else{
                $redirect_url = $service->redirect_url;
            }

            if($service->type == "on-demand"){
                $url = $redirect_url . '?keyword=' . $service->keyword .'&msisdn=' . $consent->msisdn . '&type=ondemand&result=failed';
            }else{
                $url = $redirect_url . '?keyword='. $service->keyword .'&msisdn=' . $consent->msisdn . '&type=subs&result=failed';
            }
            return redirect($url);
        }
    }

}
