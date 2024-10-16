<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {



        $checkRole = Auth::user()->roles->pluck('name')[0];
        if($checkRole == 'service'){
            return redirect()->route('customer-log.index');
        }
      

        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('yesterday'));


        $activeSubscriptions = DB::table('subscribers')->select()
            ->where('subscribers.status', '1')->count();

        $subsAndUnsubs = DB::table('sub_un_sub_logs')
            ->where('sub_un_sub_logs.opt_date', $today)
            ->select('sub_un_sub_logs.status',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN sub_un_sub_logs.status = "1" THEN 1 ELSE 0 END) as subscount'),
                DB::raw('SUM(CASE WHEN sub_un_sub_logs.status = "0" THEN 1 ELSE 0 END) as unsubscount')
            )
            ->groupBy('sub_un_sub_logs.status')
            ->get();


        $chargeLogs = DB::table('charge_logs')
                ->where('charge_logs.charge_date', $today)
                ->select(
                    DB::raw('SUM(CASE WHEN charge_logs.type = "renew" THEN 1 ELSE 0 END) as renewcount')
                )
                ->get();

        $todaySubscriptions = $subsAndUnsubs->where('status', '1')->sum('subscount');
        $todayUnsubscriptions = $subsAndUnsubs->where('status', '0')->sum('unsubscount');
        $todayRenewCount = $chargeLogs->sum('renewcount');
        $services = Service::count();

        $dashboardLogs = [
            [
                'title' => 'Total Services',
                'count' => $services,
                'icon' => 'fa-solid fa-list',
                'color' => 'bg-purple',
                'route' => url('service')
            ],
            [
                'title' => 'Active Subscriptions',
                'count' => $activeSubscriptions,
                'icon' => 'fa-solid fa-thumbtack',
                'color' => 'bg-green',
                'route' => url('log/subs-based')
            ],
            [
                'title' => 'Today\'s Subscriptions',
                'count' => $todaySubscriptions,
                'icon' => 'fa-solid fa-thumbs-up',
                'color' => 'bg-info',
                'route' => url('log/subs-and-unsubs')
            ],
            [
                'title' => 'Today\'s Unsubscriptions',
                'count' => $todayUnsubscriptions,
                'icon' => 'fa-solid fa-thumbs-down',
                'color' => 'bg-warning',
                'route' => url('log/subs-and-unsubs')
            ],
            [
                'title' => 'Today\'s Renew Count',
                'count' => $todayRenewCount,
                'icon' => 'fa-solid fa-arrows-spin',
                'color' => 'bg-light-custom',
                'route' => url('log/charge')
            ]
        ];

        
        return view('home', compact('dashboardLogs'));
    }
}
