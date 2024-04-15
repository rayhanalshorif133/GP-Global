<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\SendReportMail;
use App\Models\PartnerPayment;
use App\Models\Service;
use App\Models\Subscriber;
use App\Models\OnDemandLog;
use Illuminate\Support\Carbon;


class MailController extends Controller
{
    // https://gpglobal.b2mwap.com/send-mail

    public function _construct() {
        $this->middleware('auth');
    }
    
    public function sendReportMail(){

        $email = 'rayhan.b2m.tech@gmail.com';
        $yestarDay = date('Y-m-d',strtotime("-1 days"));

        $subsAndUnsubs = DB::table('sub_un_sub_logs')
                ->where('sub_un_sub_logs.opt_date', $yestarDay)
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
        $subsAndUnsubsData = [];
        $reports = [];
        foreach ($uniqueKeywords as $keyword) {
            $reports[] = [
                'keyword' => $keyword,
                'subscount' => $subsAndUnsubs->where('keyword', $keyword)->sum('subscount'),
                'unsubscount' => $subsAndUnsubs->where('keyword', $keyword)->sum('unsubscount'),
            ];
        }

        $subsAndUnsubsData = [
            'reports' => $reports,
            'totalSubs' => $subsAndUnsubs->sum('subscount'),
            'totalUnsubs' => $subsAndUnsubs->sum('unsubscount'),
        ];



        $chargeLogs = DB::table('charge_logs')
        ->where('charge_logs.charge_date', $yestarDay)
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
        $chargeLogsData = [];
        $reports = [];
        foreach ($uniqueKeywords as $keyword) {
            $reports[] = [
                'keyword' => $keyword,
                'subscount' => $chargeLogs->where('keyword', $keyword)->sum('subscount'),
                'renewcount' => $chargeLogs->where('keyword', $keyword)->sum('renewcount'),
            ];
        }

        $chargeLogsData = [
            'reports' => $reports,
            'totalSubs' => $chargeLogs->sum('subscount'),
            'totalRenews' => $chargeLogs->sum('renewcount'),
        ];


        $yestarDay = date('d-M-Y',strtotime("-1 days"));
        $data = [
            'subject' => 'Gp Global Report-' . $yestarDay,
            'title' => 'Gp Global Report- (' . $yestarDay . ')',
            'date' => $yestarDay,
            'subsAndUnsubsData' => $subsAndUnsubsData,
            'chargeLogsData' => $chargeLogsData,
        ];


        $ccEmails = ["tushar@b2m-tech.com"];


        Mail::to($email)
            ->cc($ccEmails)
        ->send(new SendReportMail($data));


        return view('mail.send-report', ['data' => $data]);

        Log::info("Report mail sent successfully");
        return "Report mail sent successfully";

    }


    public function dataTransfer(){
        // partner_payments
        $partnerPayments = PartnerPayment::select()
            ->where('created_at', 'LIKE', '%2023-12-01%')
            ->orderBy('created_at', 'DESC')
            ->get();
        // dd($partnerPayments);
        foreach ($partnerPayments as $item) {
            $subscriber = Subscriber::select()
            ->where('acr', $item->acr_key)
            ->first();
            if($subscriber){
                $subscriber->subscriptionId = $item->subscription;
                $subscriber->consentId = $item->consentId;
                $subscriber->save();
            }
        }
        dd($partnerPayments);
    }

    public function checkData(){

        $service = Service::find(9);

        if($service->type == "on-demand"){
            $dateTime = Carbon::now();
            $onDemandLog = new OnDemandLog();
            $onDemandLog->acr_key = rand(1, 10);
            $onDemandLog->msisdn = rand(1, 10);
            $onDemandLog->tid = rand(1, 10);
            $onDemandLog->amount = rand(1, 10);
            $onDemandLog->keyword = 'TEST';
            $onDemandLog->consentId = 'N/A';
            $onDemandLog->opt_date =  $dateTime->format('Y-m-d');
            $onDemandLog->opt_time = $dateTime->format('H:i:s');
            $onDemandLog->save();
        }else{
            dd('error',$service);
        }

    
    }
}
