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

class LogController extends Controller
{

    public function _construct() {
        $this->middleware('auth');
    }

    // subscription
    public function subsAndUnsubs(Request $request)
    {



        if (request()->ajax()) {

            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $subsAndUnsubs = DB::table('sub_un_sub_logs')
                    ->whereBetween('sub_un_sub_logs.opt_date', [$start_date, $end_date])
                    ->select(
                        'sub_un_sub_logs.keyword',
                        'sub_un_sub_logs.status',
                        DB::raw('COUNT(*) as total'),
                        DB::raw('SUM(CASE WHEN sub_un_sub_logs.status = "1" THEN 1 ELSE 0 END) as subscount'),
                        DB::raw('SUM(CASE WHEN sub_un_sub_logs.status = "0" THEN 1 ELSE 0 END) as unsubscount')
                    )
                    ->groupBy('sub_un_sub_logs.keyword', 'sub_un_sub_logs.status')
                    ->get();
            $uniqueKeywords = $subsAndUnsubs->pluck('keyword')->unique()->toArray();

            // sum as par unique keywords
            $data = [];
            foreach ($uniqueKeywords as $keyword) {
                $data[] = [
                    'keyword' => $keyword,
                    'subscount' => $subsAndUnsubs->where('keyword', $keyword)->sum('subscount'),
                    'unsubscount' => $subsAndUnsubs->where('keyword', $keyword)->sum('unsubscount'),
                ];
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('log.subs-and-unsubs');
    }

    // charge
    public function charge(Request $request)
    {
        if (request()->ajax()) {
            $start_date = new \DateTime($request->start_date);
            $end_date = new \DateTime($request->end_date);


            $chargeLogs = DB::table('charge_logs')
                ->whereBetween('charge_logs.charge_date', [$start_date, $end_date])
                ->select(
                    'charge_logs.keyword',
                    'charge_logs.type',
                    'charge_logs.charge_date',
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(CASE WHEN charge_logs.type = "subs" THEN 1 ELSE 0 END) as subscount'),
                    DB::raw('SUM(CASE WHEN charge_logs.type = "renew" THEN 1 ELSE 0 END) as renewcount')
                )
                ->groupBy('charge_logs.keyword', 'charge_logs.type', 'charge_logs.charge_date')
                ->orderBy('charge_logs.charge_date', 'asc')
                ->get();

            $uniqueKeywords = $chargeLogs->pluck('keyword')->unique()->toArray();

                // sum as par unique keywords
            $data = [];
            foreach ($uniqueKeywords as $keyword) {
                $data[] = [
                    'keyword' => $keyword,
                    'subscount' => $chargeLogs->where('keyword', $keyword)->sum('subscount'),
                    'renewcount' => $chargeLogs->where('keyword', $keyword)->sum('renewcount'),
                ];
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('log.charge');
    } 
    
    // charge
    public function ondemandCharge(Request $request)
    {
        
        if (request()->ajax()) {
            $start_date = new \DateTime($request->start_date);
            $end_date = new \DateTime($request->end_date);


            $onDemandChargeLogs = DB::table('on_demand_charges')
                ->whereBetween('on_demand_charges.charge_date', [$start_date, $end_date])
                ->select(
                    'on_demand_charges.keyword',
                    DB::raw('COUNT(*) as total_charge'),
                )
                ->groupBy('on_demand_charges.keyword')
                ->get();

            return DataTables::of($onDemandChargeLogs)
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('log.on-demand-charge');
    }

    // subscription based
    public function subsBased(Request $request)
    {
        $date = $request->date;        

        if($date == null) {
            $subBasedLogs = DB::table('subscribers')
            ->select(
                'subscribers.keyword',
                DB::raw('COUNT(*) as total'),
            )
            ->where('subscribers.status', '1')
            ->groupBy('subscribers.keyword')
            ->get();
        }else{
            $subBasedLogs = DB::table('subscribers')
            ->select(
                'subscribers.keyword',
                DB::raw('COUNT(*) as total'),
            )
            ->where('subscribers.subs_date', '<=', $date)
            ->where('subscribers.status', '1')
            ->groupBy('subscribers.keyword')
            ->get();
        }
        
        
        return view('log.subs-based', compact('subBasedLogs'));
    }


    private function commonLogCalculation($request){

        $keyword = $request->keyword;
        if($keyword == null){
            $keyword = 'BDGD';
        }

        $date = $request->date;
        if($date == null){
            $date = date('Y-m-d', strtotime('yesterday'));
        }

        $service = DB::table('services')->select()->where('keyword', $keyword)->first();
        
        $consent = Consent::select()->where('created_at', 'like' , '%' . $date . '%')
                ->where('service_id', '=', $service->id)
                ->get()->count();
        $consent_back_log = ConsentBackLog::select()->where('created_at', 'like' , '%' . $date . '%')
                    ->where('service_id', '=', $service->id)
                    ->get();
    
        $consent_back_log_success = $consent_back_log->where('type', 'success')->count();
        $consent_back_log_fail = $consent_back_log->whereNotIn('type', 'success')->count();

        $payments = PartnerPayment::select()
                        ->where('service_keyword', $service->keyword)
                        ->where('created_at', 'like' , '%' . $date . '%')
                        ->get();
        $payment_success = $payments->where('status', '1')->count();
        $payment_fail = $payments->where('status', '0')->count();


    
        $data = [
            "date" => $date,
            "total_otp_sent" => $consent,
            "total_back_from_otp_page" => $consent_back_log->count(),
            "otp_match" => $consent_back_log_success,
            "otp_fail" => $consent_back_log_fail,
            "payment_request" => $payments->count(),
            "payment_success" => $payment_success,
            "insufficient_credit" => $payment_fail
        ];
        return $data;
    }

    // yesterdayLog
    // https://gpglobal.b2mwap.com/log/yesterday-log?date=2023-12-19
    public function yesterdayLog(Request $request){

        
        $isTable = $request->table == null? false : true;
        if($isTable){
            return $this->tableResponse($request);
        }else{
            $data = $this->commonLogCalculation($request);
            $msg = 'Date: ' . $data['date'] . ' send data';
            return $this->respondWithSuccess($msg, $data);
        } 
    }



    // Export as Excel
    // https://gpglobal.b2mwap.com/log/yesterday-log?start_date=2023-12-26&end_date=2023-12-30&table=true

    public function tableResponse($request){
        
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $keyword = $request->keyword;
        $sendData = [];
        $countDate = date_diff(date_create($start_date), date_create($end_date))->format('%a') + 1;

        $keyword = $request->keyword;
        if($keyword == null){
            $keyword = 'BDGD';
        }

        $service = DB::table('services')->select()->where('keyword', $keyword)->first();

        for($i = 0; $i < $countDate; $i++){
            $date = date('Y-m-d', strtotime($start_date . ' + ' . $i . ' days'));
            $request->merge(['date' => $date, 'service' => $service]);
            $sendData[] = $this->commonLogCalculationBYDB($request);
        }
        return view('log.report_table', compact('sendData'));
    }

    protected function commonLogCalculationBYDB($request){

        $date = $request->date;
        if($date == null){
            $date = date('Y-m-d', strtotime('yesterday'));
        }

        $total_otp_sent = DB::table('consents')->select('id')
                ->where('created_at', 'like' , '%' . $date . '%')
                ->where('service_id', '=', $request->service->id)
                ->get()->count();
                
        $consent_back_log = DB::table('consent_back_logs')->select('id','type')->where('created_at', 'like' , '%' . $date . '%')
            ->where('service_id', '=', $request->service->id)
            ->get();

        $consent_back_log_success = $consent_back_log->where('type', 'success')->count();
        $consent_back_log_fail = $consent_back_log->whereNotIn('type', 'success')->count();
        
        $payments = DB::table('partner_payments')->select()->where('service_keyword', $request->service->keyword)
                        ->where('created_at', 'like' , '%' . $date . '%')
                        ->get();
        $payment_success = $payments->where('status', '1')->count();
        $payment_fail = $payments->where('status', '0')->count();


        $data = [
            "date" => $date,
            "total_otp_sent" => $total_otp_sent,
            "total_back_from_otp_page" => $consent_back_log->count(),
            "otp_match" => $consent_back_log_success,
            "otp_fail" => $consent_back_log_fail,
            "payment_request" => $payments->count(),
            "payment_success" => $payment_success,
            "insufficient_credit" => $payment_fail
        ];

        return $data;
    }


}
