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

    public function privacyPolicy(){
        return view('frontend.privacy-policy');
    }
    public function termsConditions(){
        return view('frontend.terms-conditions');
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
        $user = auth()->guard('frontend')->user();
        $consultation = Consultation::where('id',$request->consultation_id)
                            ->where('user_id',$user->id)
                            ->first();

        if($consultation && $consultation->status == 'accepted') {
            $signature = generateZoomSignature($consultation->zoom_meeting_id, $user->id, 0);

            return response()->json([
                'status'=>true,
                'data'=>[
                    'consultation_id'=>$consultation->id,
                    'meeting_number'=>$consultation->zoom_meeting_id,
                    'role'=> 0,
                    'sdk_key'=>config('services.zoom.sdk_key'),
                    'signature'=>$signature,
                    'duration'=>$consultation->duration ?? 0
                ]
            ]);
        } else {
            return response()->json(['status'=>false,'message'=>'No active consultation', 'data' => $consultation->status],200);
        }
    }

    public function consultationCancel(Request $request)
    {
        $lang = app()->getLocale() ?? env('APP_LOCALE','en');
        $data = getPageDynamicContent('consultancy_request_failed',$lang);
        return view('frontend.user.consultation-cancel', compact('data'));
    }

    public function saveStartTime(Request $request)
    {
        $consult = Consultation::find($request->consultation_id);

        $consult->status = 'in_progress';

        if (!$consult->meeting_start_time) {
            $start_time = $request->start_time / 1000; 
            $consult->meeting_start_time = date('Y-m-d H:i:s');
        }
        $consult->save();
        return response()->json(['success' => true]);
    }

    public function getStartTime($id)
    {
        $consult = Consultation::find($id);

        return response()->json([
            'start_time' => strtotime($consult->meeting_start_time) * 1000 ?? null
        ]);
    }

    public function statusConsultation($consultationId)
    {
        // Fetch consultation by ID
        $consultation = Consultation::find($consultationId);

        if (!$consultation) {
            return response()->json([
                'status' => 'not_found'
            ], 404);
        }

        return response()->json([
            'status' => $consultation->status, // e.g., 'completed', 'ongoing'
        ]);
    }


}
