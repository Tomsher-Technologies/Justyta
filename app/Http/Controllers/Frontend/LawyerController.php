<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Consultation;
use App\Models\ConsultationAssignment;
use App\Models\ConsultationDuration;
use App\Models\ConsultationPayment;
use App\Models\Lawyer;
use Illuminate\Support\Facades\Http;
use App\Services\ZoomService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Notifications\ServiceRequestSubmitted;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Carbon\Carbon;

class LawyerController extends Controller
{
    public function lawyerDashboard(){
        $lang = app()->getLocale() ?? env('APP_LOCALE','en'); 
        return view('frontend.lawyer.dashboard');
    }

    public function poll(Request $request)
    {
        $lang = app()->getLocale() ?? env('APP_LOCALE','en'); 
        $user       = Auth::guard('frontend')->user();
        $lawyer = $user->lawyer ?? null;
        $assignment = ConsultationAssignment::with('consultation')
                        ->where('lawyer_id', $lawyer->id)
                        ->where('status', 'assigned')
                        ->orderBy('assigned_at', 'asc')
                        ->first();

        if(!$assignment){
            return response()->json(['status'=>false,'message'=>'No pending consultations'],200);
        }

        return response()->json([
            'status'=>true,
            'data'=>[
                'consultation_id' => $assignment->consultation_id,
                'user_name' => $assignment->consultation?->user?->name,
                'applicant_type' => $assignment->consultation?->applicant_type,
                'litigant_type' => $assignment->consultation?->litigation_type,
                'emirate' => $assignment->consultation?->emirate?->getTranslation('name', $lang),
                'you_represent' => $assignment->consultation?->youRepresent?->getTranslation('name', $lang),
                'case_type' => $assignment->consultation?->caseType?->getTranslation('name', $lang),
                'case_stage' => $assignment->consultation?->caseStage?->getTranslation('name', $lang),
                'language' => $assignment->consultation?->languageValue?->getTranslation('name', $lang),
                'duration' => $assignment->consultation?->duration,
            ]
        ],200);
    }

    // Lawyer accept/reject
    public function lawyerResponse(Request $request)
    {
        $request->validate([
            'action'=>'required|in:accept,reject',
            'consultation_id'=>'required'
        ]);

        $lang = app()->getLocale() ?? env('APP_LOCALE','en'); 
        $user = $request->user();
        $lawyer = $user->lawyer ?? null;
        $consultation = Consultation::findOrFail($request->consultation_id);

        $assignment = ConsultationAssignment::where('consultation_id',$consultation->id)
                        ->where('lawyer_id',$lawyer->id)
                        ->first();

        $assignment->status = $request->action == 'accept' ? 'accepted' : 'rejected';
        $assignment->responded_at = now();
        $assignment->save();

        if($request->action == 'accept'){
            $consultation->status = 'accepted';
            $consultation->lawyer_id = $lawyer->id;
            $consultation->zoom_meeting_id = $consultation->id.rand(1000,9999);
            $consultation->save();

            $signature = generateZoomSignature($consultation->zoom_meeting_id, $lawyer->id, 1);

            return response()->json([
                'status'=>true,
                'data'=>[
                    'consultation_id' => $consultation->id,
                    'meeting_number' => $consultation->zoom_meeting_id,
                    'role' => 1,
                    'sdk_key' => config('services.zoom.sdk_key'),
                    'signature' => $signature
                ]
            ]);
        }

        $consultation->status = 'rejected';
        $consultation->save();

        return response()->json(['status'=>false,'message'=>'Lawyer rejected consultation']);
    }

    public function updateConsultationStatus(Request $request)
    {
        $consultation = Consultation::find($request->consultation_id);
        if ($consultation) {
            $consultation->status = $request->status;
            $consultation->save();

            unreserveLawyer($consultation->lawyer_id);
            return response()->json(['status' => true]);
        }

        return response()->json(['status' => false], 404);
    }

}
