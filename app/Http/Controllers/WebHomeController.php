<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class WebHomeController extends Controller
{
    public function index(){
        $services = Service::orderBy('id', 'desc')->get();
        return view('web_home',compact('services'));
        // if(Auth::check()){
        //     return redirect()->route('dashboard');
        // }else{
        //     return redirect()->route('login');
        // }
    }
}
