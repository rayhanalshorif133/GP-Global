<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class WebHomeController extends Controller
{
    public function index(){
        if(Auth::check()){
            return redirect()->route('dashboard');
        }else{
            return redirect()->route('login');
        }
    }
}
