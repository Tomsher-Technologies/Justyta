<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TranslatorController extends Controller
{
    public function dashboard()
    {
        return view('frontend.translator.dashboard');
    }
}
