<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;

class VendorHomeController extends Controller
{
    public function dashboard(){
        $lang = app()->getLocale() ?? env('APP_LOCALE','en'); 
    
        return view('frontend.vendor.dashboard', compact('lang'));
    }

    public function lawyers(){
        $lang = app()->getLocale() ?? env('APP_LOCALE','en'); 
        
        return view('frontend.vendor.lawyers.index', compact('lang'));
    }

    public function createLawyer(){
        $lang = app()->getLocale() ?? env('APP_LOCALE','en'); 
        
        return view('frontend.vendor.lawyers.create', compact('lang'));
    }
}
