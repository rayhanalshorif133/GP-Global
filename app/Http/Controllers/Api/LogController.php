<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChargeLog;
use App\Models\SubUnSubLog;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    public function subsAndUnsubs(Request $request)
    {

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

        return $this->respondWithSuccess("Successfully fetched subsAndUnsubs data",$data);
    }

    // charge
    public function charge(Request $request)
    {

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

            return $this->respondWithSuccess("Successfully fetched charge data",$data);
    }
}
