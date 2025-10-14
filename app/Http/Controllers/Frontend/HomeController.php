<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Consultation;
use App\Models\ConsultationAssignment;
use App\Models\ConsultationDuration;
use App\Models\ConsultationPayment;
use App\Models\Lawyer;
use Illuminate\Support\Facades\Http;
use App\Services\ZoomService;

class HomeController extends Controller
{
    public function home(){
        return view('frontend.index');
    }

    public function about(){
        return view('frontend.about');
    }

    public function refundPolicy(){
        return view('frontend.refund-policy');
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

    public function checkUserConsultationStatus(Request $request)
    {
        $user = $request->user();
        $consultation = Consultation::where('id',$request->consultation_id)
                            ->where('user_id',$user->id)
                            ->where('status','accepted')
                            ->first();

        if(!$consultation){
            return response()->json(['status'=>false,'message'=>'No active consultation'],200);
        }

        $signature = generateZoomSignature($consultation->zoom_meeting_id, $user->id, 0);

        return response()->json([
            'status'=>true,
            'data'=>[
                'consultation_id'=>$consultation->id,
                'meeting_number'=>$consultation->zoom_meeting_id,
                'role'=>0,
                'sdk_key'=>config('services.zoom.sdk_key'),
                'signature'=>$signature
            ]
        ]);
    }
}
