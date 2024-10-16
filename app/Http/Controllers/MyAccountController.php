<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ChargeLog;
use App\Models\SubUnSubLog;
use App\Models\ConsentBackLog;
use App\Models\Consent;
use App\Models\PartnerPayment;
use Illuminate\Support\Facades\DB;

class MyAccountController extends Controller
{

    public function index(Request $request)
    {
        try {
            $keyword = $request->query->get('keyword');
            $msisdn = $request->query->get('msisdn');
            $is_success = $request->query->get('success');
            $service = DB::table('services')->select()->where('keyword', $keyword)->first();
            $subscribers = DB::table('subscribers')->select()
                ->where('msisdn', $msisdn)
                ->where('keyword', $keyword)
                ->where('status', '1')
                ->first();
            return view('my-account', compact('is_success','msisdn','keyword','service','subscribers'));
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
