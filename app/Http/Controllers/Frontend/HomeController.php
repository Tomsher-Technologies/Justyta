<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;

class HomeController extends Controller
{
    public function home(){
        return view('frontend.index');
    }

    public function userDashboard(){
        $lang = app()->getLocale() ?? env('APP_LOCALE','en'); 
        $services = Service::with(['translations' => function ($query) use ($lang) {
                            $query->where('lang', $lang);
                        }])
                        ->whereNotIn('slug',['law-firm-services'])
                        ->where('status', 1)
                        ->orderBy('sort_order', 'ASC')
                        ->get();

        return view('frontend.user.dashboard', compact('services'));
    }
}
