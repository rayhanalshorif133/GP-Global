<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Callback;
use App\Models\HitLog;
use App\Models\Service;
use Illuminate\Http\Request;

class CallBackController extends Controller
{
    public function callback(Request $request)
    {
        try {
            $data = $request->all();
            $callback = new Callback();
            $callback->aocTransID = $data['aocTransID'];
            $callback->raw_data = json_encode($request->data);
            $callback->save();
            
            
            $getHitLog = HitLog::select()
                ->where('aocTransID', $request->aocTransID)
                ->first();
            $service = Service::where('keyword', $getHitLog->keyword)->first();        
            
            $redirect = $service->redirect_url . "?aocTransID=" . $request->aocTransID;

            return redirect($redirect);
        } catch (\Throwable $th) {
            return $this->respondWithError('Server Error', $th->getMessage(), 500);
        }
    }
}
