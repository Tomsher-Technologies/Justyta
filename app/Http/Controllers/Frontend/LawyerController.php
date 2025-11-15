<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Consultation;
use App\Models\ConsultationAssignment;
use App\Models\ConsultationDuration;
use App\Models\ConsultationPayment;
use App\Models\Lawyer;
use App\Models\Dropdown;
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

        $lawyerId = Auth::guard('frontend')->user()->lawyer->id;
        $currentYear = Carbon::now()->year;
        $year = request()->get('consultation_year', $currentYear);
        
        $notificationsResult = $result = $this->getNotifications();
        $notifications = $notificationsResult['notifications'];

        $acceptedConsultationsToday = ConsultationAssignment::with('consultation')
                                        ->where('lawyer_id', $lawyerId)
                                        ->where('status', 'accepted')
                                        ->whereDate('assigned_at', Carbon::today())
                                        ->count();

        $totalAcceptedConsultations = ConsultationAssignment::with('consultation')
                                        ->where('lawyer_id', $lawyerId)
                                        ->where('status', 'accepted')
                                        ->count();

        $totalRejections = ConsultationAssignment::with('consultation')
                                        ->where('lawyer_id', $lawyerId)
                                        ->where('status', 'rejected')
                                        ->count();

        $consultations = ConsultationAssignment::with(['consultation.user','consultation.lawyer','consultation.emirate'])
                                    ->where('lawyer_id', $lawyerId)
                                    ->whereIn('status', ['accepted', 'rejected'])
                                    ->orderBy('id', 'desc')
                                    ->limit(5)->get();
        
        $monthlyData = [];
        $monthlyData = Consultation::select(
                                    DB::raw('MONTH(created_at) as month'),
                                    DB::raw('COUNT(id) as total')
                                )
                                ->whereYear('created_at', $year)
                                ->where('lawyer_id', $lawyerId)
                                ->where('request_success', 1)
                                ->where('status', 'completed')
                                ->groupBy('month')
                                ->pluck('total', 'month')
                                ->toArray();

        return view('frontend.lawyer.dashboard', compact('acceptedConsultationsToday', 'totalAcceptedConsultations','totalRejections','notifications','consultations','monthlyData','lang','year'));
    }

    public function getNotifications()
    {
        $lang       = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $services   = \App\Models\Service::with('translations')->get();

       
        $allNotifications =  Auth::guard('frontend')->user()->notifications();

        $paginatedNot = (clone $allNotifications)
            ->orderByDesc('created_at')
            ->paginate(4);

        $notifications = collect($paginatedNot->items())
            ->map(function ($notification) use ($lang) {
                $data = $notification->data;
               
                return [
                    'id'   => $notification->id,
                    'message'   => __($notification->data['message'], [
                        'reference' => $data['reference_code'] ?? $data['reference'] ?? "",
                    ]),
                    'time'      => $notification->created_at->format('d M, Y h:i A'),
                ];
            });

        return [
            'notifications' => $notifications,
            'paginatedNot'  => $paginatedNot,
        ];
    }

    public function changeOnlineStatus(Request $request)
    {
        
        $user = Auth::guard('frontend')->user();

        if (!$user) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $isOnline = filter_var($request->status, FILTER_VALIDATE_BOOLEAN);

        $user->is_online = $isOnline ? 1 : 0;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => __('frontend.online_status_updated'),
            'is_online' => $user->is_online
        ]);
    }

    public function lawyerProfile(){
        $id = Auth::guard('frontend')->user()->lawyer->id;
        $lang = app()->getLocale() ?? env('APP_LOCALE','en'); 
        
        $lawyer = Lawyer::with('lawfirm', 'emirate')->findOrFail($id);
    
        $specialityIds = $lawyer->dropdownOptions()->wherePivot('type', 'specialities')->pluck('dropdown_option_id')->toArray();
        $languageIds = $lawyer->dropdownOptions()->wherePivot('type', 'languages')->pluck('dropdown_option_id')->toArray();

        return view('frontend.lawyer.profile', compact('lang', 'lawyer','specialityIds','languageIds'));
    }

    public function notifications(Request $request)
    {
        $lang       = app()->getLocale() ?? env('APP_LOCALE', 'en');
       
        $allShownIds = [];
        $allNotifications =  Auth::guard('frontend')->user()->notifications();

        $paginatedNot = (clone $allNotifications)
            ->orderByDesc('created_at')
            ->paginate(10);

        $notifications = collect($paginatedNot->items())
            ->map(function ($notification) use ($lang) {
                $data = $notification->data;
            
                return [
                    'id'   => $notification->id,
                    'message'   => __($notification->data['message'], [
                        'reference' => $data['reference_code'] ?? $data['reference'] ?? "",
                        'status' => $data['status'] ?? "",
                    ]),
                    'time'      => $notification->created_at->format('d M, Y h:i A'),
                ];
            });

        $allShownIds = collect($paginatedNot->items())
            ->pluck('id');
        Auth::guard('frontend')->user()->unreadNotifications()
            ->whereIn('id', $allShownIds)
            ->update(['read_at' => now()]);

        return view('frontend.lawyer.notifications', compact('notifications', 'paginatedNot'));
    }

    public function clearAllNotifications()
    {
        Auth::guard('frontend')->user()->notifications()->delete();
        return response()->json(['success' => true, 'message' =>  __('messages.notifications_cleared_successfully')]);
    }

    public function deleteSelectedNotifications(Request $request)
    {
        $ids = $request->notification_ids ?? [];

        if (!empty($ids)) {
            Auth::guard('frontend')->user()->notifications()->whereIn('id', $ids)->delete();
        }
        return response()->json(['success' => true, 'message' =>  __('messages.selected_notifications_cleared_successfully')]);
    }


    public function consultationsIndex(Request $request)
    {
        $lang = app()->getLocale() ?? env('APP_LOCALE','en'); 

        $lawyerId = Auth::guard('frontend')->user()->lawyer->id;

        $request->session()->put('last_page_consultations', url()->full());

        $conQuery = ConsultationAssignment::with([
            'consultation.user',
            'consultation.lawyer',
            'consultation.emirate',
            'consultation.caseType',
            'consultation.youRepresent',
            'consultation.languageValue'
        ])->where('lawyer_id', $lawyerId)
        ->whereIn('status', ['accepted', 'rejected']);

        if ($request->filled('specialities')) {
            $conQuery->whereHas('consultation', function ($q) use ($request) {
                $q->where('case_type', $request->specialities);
            });
        }

        if ($request->filled('language')) {
            $conQuery->whereHas('consultation', function ($q) use ($request) {
                $q->where('language', $request->language);
            });
        }

        if ($request->filled('status')) {
            $conQuery->where('status', $request->status);
        }

        if ($request->filled('daterange')) {
            $dates = explode(' to ', $request->daterange);
            if (count($dates) === 2) {
                $conQuery->whereBetween('created_at', [
                    Carbon::parse($dates[0])->startOfDay(),
                    Carbon::parse($dates[1])->endOfDay()
                ]);
            }
        }
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $conQuery->whereHas('consultation', function ($q) use ($keyword) {
                $q->where('ref_code', 'like', "%{$keyword}%")
                ->orWhereHas('user', function ($userQuery) use ($keyword) {
                    $userQuery->where('name', 'like', "%{$keyword}%")
                                ->orWhere('email', 'like', "%{$keyword}%")
                                ->orWhere('phone', 'like', "%{$keyword}%");
                });
            });
        }

        $consultations = $conQuery->orderBy('id', 'desc')->paginate(15);

        $dropdowns = Dropdown::with(['options.translations' => function ($q) {
                                $q->where('language_code', 'en');
                            }])->whereIn('slug', ['specialities', 'case_stage', 'languages'])->get()->keyBy('slug');

        return view('frontend.lawyer.consultations.index', compact('consultations',  'dropdowns'));
    }


    public function showConsultation($id)
    {
        $assignment = ConsultationAssignment::with([
            'consultation.user',
            'consultation.lawyer',
            'consultation.emirate',
            'consultation.caseType',
            'consultation.caseStage',
            'consultation.youRepresent',
            'consultation.languageValue',
            'consultation.payments',
            'lawyer' // the lawyer who accepted/rejected
        ])->findOrFail($id);

        $consultation = $assignment->consultation;

        return view('frontend.lawyer.consultations.show', compact('consultation', 'assignment'));
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
                'duration' => $assignment->consultation?->duration ?? 0,
                'role' => 1
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
                    'signature' => $signature,
                    'duration' => $consultation->duration ?? 0
                ]
            ]);
        }

        $lawyer->is_busy = 0;
        $lawyer->save();

        $nextLawyer = findBestFitLawyer($consultation);
        if($nextLawyer){
            assignLawyer($consultation, $nextLawyer->id);
            return response()->json(['status'=> false, 'message'=>'Lawyer rejected, next lawyer assigned']);
        }else{
            $consultation->status = 'rejected';
            $consultation->save();
            return response()->json(['status'=> false, 'message'=> __('frontend.rejected_no_lawyer_available')]);
        }
    }

    public function updateConsultationStatus(Request $request)
    {
        $consultation = Consultation::find($request->consultation_id);
        if ($consultation) {
            $consultation->status = $request->status;

            if($request->status == 'completed'){
                $consultation->meeting_end_time = now();
                $consultation->is_completed = 1;
            }
            $consultation->save();

            if($request->status == 'completed' || $request->status == 'rejected' || $request->status == 'cancelled' || $request->status == 'no_lawyer_available'){
                unreserveLawyer($consultation->lawyer_id);
            }

            if (Auth::guard('frontend')->user()->user_type === 'lawyer') {
                $redirectUrl = route('lawyer.consultation.ended', $consultation->id);
            } else {
                $redirectUrl = route('user.consultation.ended', $consultation->id);
            }
            
            return response()->json(['status' => true,'redirect_url' => $redirectUrl]);
        }

        return response()->json(['status' => false, 'redirect_url' => ''], 404);
    }

    public function endedCall()
    {
        return view('frontend.lawyer.consultation-ended');
    }
}
