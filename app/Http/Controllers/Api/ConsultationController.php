<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Consultation;
use App\Models\ConsultationAssignment;
use App\Models\ConsultationDuration;
use App\Models\ConsultationPayment;
use App\Models\Lawyer;
use Illuminate\Support\Facades\Http;
use App\Services\ZoomService;

class ConsultationController extends Controller
{

    
    public function store(Request $request)
    {
        $data = $request->validate([
            'applicant_type' => 'required|in:company,individual',
            'litigation_type'=> 'required|in:local,federal',
            'consultant_type'=> 'required|in:normal,vip',
            'emirate_id'     => 'required|integer',
            'you_represent'  => 'required',
            'case_type'      => 'required',
            'case_stage'     => 'required',
            'language'       => 'required',
            'duration'       => 'required|numeric',
            'lawyer_id'      => 'nullable|exists:lawyers,id'
        ]);

        $lang       = $request->header('lang') ?? env('APP_LOCALE','en');
        $user       = $request->user();

        $consultation = Consultation::create([
            'user_id'=> $user->id,
            ...$data
        ]);

        $lawyer = findBestFitLawyer($consultation);

        if ($lawyer) {
            reserveLawyer($lawyer->id, $consultation->id);
        } else {
            $consultation->delete();
            return response()->json([
                    'status'    => false,
                    'message'   => __('frontend.no_lawyer_available'),
                    'data'      => [],
                ], 200);
        }
    
        $base = ConsultationDuration::where('type', $request->consultant_type)
                                    ->where('duration', $request->duration)
                                    ->where('status', 1)
                                    ->first();

        $total_amount = (float)($base->amount ?? 0);

        $consultation->update([
            'amount' => $total_amount
        ]);
        $currency = env('APP_CURRENCY','AED');
        $payment = [];

        $total_amount = 0;
        if($total_amount > 0) {
            $customer = [
                'email' => $user->email,
                'name'  => $user->name,
                'phone' => $user->phone
            ];
            $orderReference = $consultation->id .'--'.$consultation->ref_code;

            $payment = createMobOrder($customer, $total_amount, $currency, $orderReference);

            if (isset($payment['_links']['payment']['href'])) {
                $paymentData = ConsultationPayment::create([
                                'consultation_id' => $consultation->id,
                                'user_id' => $user->id,
                                'amount' => $total_amount,
                                'type' => 'initial',
                                'status' => 'pending',
                                'payment_reference' => $payment['reference'] ?? NULL
                            ]);

                return response()->json([
                    'status'    => true,
                    'message'   => __('messages.request_submit_success'),
                    'data'      => json_encode($payment),
                ], 200);

            }else{
                return response()->json([
                    'status'    => false,
                    'message'   => __('frontend.request_submit_failed'),
                    'data'      => json_encode($payment),
                ], 200);
            }
        }else{
            // return response()->json([
            //     'status'    => false,
            //     'message'   => __('frontend.request_submit_failed'),
            //     'data'      => json_encode($payment),
            // ], 200);

            $consultation->refresh();
           
            if ($consultation->lawyer_id) {
                assignLawyer($consultation, $consultation->lawyer_id);
                $consultation->status = 'waiting_lawyer';
                $consultation->save();
            } else {
                // Backup case: find a lawyer again if not reserved
                $lawyer = findBestFitLawyer($consultation);
                if ($lawyer) {
                    assignLawyer($consultation, $lawyer->id);
                    $consultation->status = 'waiting_lawyer';
                    $consultation->save();
                } else {
                    $consultation->status = 'no_lawyer_available';
                    $consultation->save();
                }
            }

            $pageData = getPageDynamicContent('consultancy_payment_success',$lang);
            $waitingMessage = getPageDynamicContent('consultancy_waiting_page',$lang);

            return response()->json([
                'status' => true,
                'message'=> $pageData['content'] ?? __('frontend.lawyer_assigned_waiting_response'),
                'data' => [
                    'consultation_id' => $consultation->id ?? null,
                    'ref_code' => $consultation->ref_code ?? null,
                    'success_message' => $pageData['content'] ?? __('frontend.lawyer_assigned_waiting_response'),
                    'waiting_message' => $waitingMessage['content'] ?? __('frontend.lawyer_assigned_waiting_response'),
                ]
            ],200);
        }
    }

    public function paymentSuccess(Request $request)
    {
        $paymentReference = $request->query('ref') ?? NULL;
        if($paymentReference){
            $token = getAccessToken();

            $baseUrl = config('services.ngenius.base_url');
            $outletRef = config('services.ngenius.outlet_ref');

            $response = Http::withToken($token)->get("{$baseUrl}/transactions/outlets/" . $outletRef . "/orders/{$paymentReference}");
            $data = $response->json();

            $orderRef = $data['merchantOrderReference'] ?? NULL;
            $serviceData = explode('--', $orderRef);

            $consultationId = $serviceData[0];
            $serviceRequestCode = $serviceData[1];
            
            $status = $data['_embedded']['payment'][0]['state'] ?? null;
            $paid_amount = $data['_embedded']['payment'][0]['amount']['value'] ?? 0;

            $paidAmount = ($paid_amount != 0) ? $paid_amount/100 : 0;
        
            $lang       = $request->header('lang') ?? env('APP_LOCALE','en');

            if ($status === 'PURCHASED' || $status === 'CAPTURED') {
                $servicePayment = ConsultationPayment::where('payment_reference', $paymentReference)
                                                ->where('consultation_id', $consultationId)
                                                ->first();

                if ($servicePayment) {
                    $servicePayment->update(['status' => 'completed']);
                }

                $consultation = Consultation::findOrFail($consultationId);
                if ($consultation->lawyer_id) {
                    assignLawyer($consultation, $consultation->lawyer_id);
                    $consultation->status = 'waiting_lawyer';
                    $consultation->save();
                } else {
                    $lawyer = findBestFitLawyer($consultation);
                    if ($lawyer) {
                        assignLawyer($consultation, $lawyer->id);
                        $consultation->status = 'waiting_lawyer';
                        $consultation->save();
                    } else {
                        $consultation->status = 'no_lawyer_available';
                        $consultation->save();
                        return response()->json(['status' => false,'message'=> __('frontend.no_lawyer_available')],200);
                    }
                }

                // $pageData = getPageDynamicContent('consultancy_payment_success',$lang);
                $waitingMessage = getPageDynamicContent('consultancy_waiting_page',$lang);

                return response()->json([
                    'status' => true,
                    'message'=> $waitingMessage['content'] ?? __('frontend.lawyer_assigned_waiting_response'),
                    'data' => [
                        'consultation_id' => $consultation->id ?? null,
                        'ref_code' => $consultation->ref_code ?? null,
                        'success_message' => $waitingMessage['content'] ?? __('frontend.lawyer_assigned_waiting_response'),
                        'waiting_message' => $waitingMessage['content'] ?? __('frontend.lawyer_assigned_waiting_response'),
                    ]
                ],200);
            }else{
                $consultation = Consultation::find($consultationId);
                unreserveLawyer($consultation->lawyer_id);
                $consultation->delete();
            }
        }
        return response()->json(['status'=>false, 'message'=>__('frontend.payment_failed')],200);
    }

    public function paymentCancel(Request $request)
    {
        $consultation_id = $request->query('consultation_id') ?? null;

        if ($consultation_id) {
            $consultation = Consultation::find($consultation_id);
            unreserveLawyer($consultation->lawyer_id);
            $consultation->delete();
        }
        
        return response()->json(['status'=>false, 'message'=>__('frontend.payment_failed')],200);
    }

    public function poll(Request $request)
    {
        $lang       = $request->header('lang') ?? env('APP_LOCALE','en');
        $user       = $request->user();
        $userId   = $user->id ?? null; 

        $lawyer = Lawyer::where('user_id', $userId)->first();
        $lawyerId = $lawyer->id ?? null;

        $assignment = ConsultationAssignment::with('consultation')
                                            ->where('lawyer_id', $lawyerId)
                                            ->where('status', 'assigned')
                                            ->orderBy('assigned_at', 'asc')
                                            ->first();

        if (!$assignment) {
            return response()->json(['status' => false,'message' => 'No pending consultations'], 200);
        }
        $data = [
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
        ];

        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $data
        ], 200);
    }


    public function lawyerResponse(Request $request, ZoomService $zoomService){
        $request->validate([
            'action'=>'required|in:accept,reject',
            'consultation_id'=>'required'
        ]);
      
        $lang       = $request->header('lang') ?? env('APP_LOCALE','en');
        $user       = $request->user();
        $userId   = $user->id ?? null; 
        $consultationId = $request->consultation_id;

        $consultation = Consultation::findOrFail($consultationId);

        $lawyer = Lawyer::where('user_id', $userId)->first();
        $lawyerId = $lawyer->id ?? null;

        $assignment = ConsultationAssignment::where('consultation_id',$consultation->id)
                        ->where('lawyer_id',$lawyerId)
                        ->first();

        $assignment->status = $request->action == 'accept' ? 'accepted' : 'rejected';
        $assignment->responded_at = now();
        $assignment->save();

        if($request->action == 'accept'){
            $consultation->status = 'accepted';
            $consultation->lawyer_id = $lawyerId;
            $consultation->save();

            $meetingNumber = $consultation->id.rand(1000,9999);

            $consultation->zoom_meeting_id = $meetingNumber;
            $consultation->save();

            $signature = generateZoomSignature($meetingNumber, $lawyerId, 1);

            // $consultation->lawyer->update(['is_busy' => 1]);

            return response()->json([
                'status' => true,
                'message' => 'Call accepted, Zoom meeting initialized',
                'data'=> [
                    'consultation_id' => $consultation->id,
                    'meeting_number' => $meetingNumber,
                    'password'       => '',
                    'role'           => 1,
                    'sdk_key'        => config('services.zoom.sdk_key'),
                    'signature'      => $signature,
                ]],200);
        }

        $nextLawyer = findBestFitLawyer($consultation);
        if($nextLawyer){
            assignLawyer($consultation, $nextLawyer->id);
            return response()->json(['status'=> false, 'message'=>'Lawyer rejected, next lawyer assigned']);
        }else{
            $consultation->status = 'rejected';
            $consultation->save();
            return response()->json(['status'=> false, 'message'=> __('frontend.rejected_no_lawyer_available')]);
        }

        $consultation->status = 'rejected';
        $consultation->save();
        return response()->json(['status'=> false, 'message'=> __('frontend.rejected_no_lawyer_available')]);
    }

    public function checkUserConsultationStatus(Request $request)
    {
        $lang       = $request->header('lang') ?? env('APP_LOCALE','en');
        $user       = $request->user();
        $userId   = $user->id ?? null; 
        
        $consultationId = $request->consultation_id ?? null;

        $consultation = Consultation::where('id',$consultationId)->where('user_id', $userId)
                                        ->where('status', 'accepted')->first();
        $meetingNumber = $consultation->zoom_meeting_id ?? null;

        $signature = generateZoomSignature($meetingNumber, $userId, 0);

        if (!$consultation) {
            return response()->json([
                'status' => false,
                'message' => 'No consultation found',
            ], 200);
        }
        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => [
                'consultation_id' => $consultation->id ?? null,
                'status' => $consultation->status ?? null,
                'lawyer_id' => $consultation->lawyer_id ?? null,
                'meeting_number' => $meetingNumber,
                'password'       => '',
                'role'           => 0,
                'sdk_key'        => config('services.zoom.sdk_key'),
                'signature'      => $signature,
            ],
        ], 200);
    }


    public function handleZoomCompleted(Request $request)
    {
        $event = $request->event ?? null;

        if ($event === 'meeting.ended') {
            $meetingId = $request->input('payload.object.id');

            $consultation = Consultation::where('zoom_meeting_id', $meetingId)->first();
            if ($consultation) {
                $consultation->status = 'completed';
                $consultation->save();

                $consultation->lawyer->update(['is_busy' => 0]);
            }
        }
    }

    public function updateConsultationStatus(Request $request)
    {
        $lang       = $request->header('lang') ?? env('APP_LOCALE','en');
        $user       = $request->user();
        $userId   = $user->id ?? null; 
        $consultationId = $request->consultation_id;
        $consultation = Consultation::find($consultationId);
        if ($consultation) {
            $consultation->status = $request->status;
            $consultation->save();

            unreserveLawyer($consultation->lawyer_id);
            return response()->json(['status' => true,'message' => 'Success'],200);
        }

        return response()->json(['status' => false,'message' => 'Failed'], 200);
    }







    public function extendZoom(Request $request, $id)
    {
        $request->validate([
            'extra_minutes'=>'required|integer'
        ]);

        $consultation = Consultation::findOrFail($id);
        $this->extendZoomMeeting($consultation, $request->extra_minutes);

        return response()->json(['success'=>true]);
    }

    private function createZoomMeeting(Consultation $consultation)
    {
        $jwt = $this->getZoomJWT();
        $response = Http::withHeaders([
            'Authorization'=>"Bearer $jwt",
            'Content-Type'=>'application/json'
        ])->post('https://api.zoom.us/v2/users/me/meetings',[
            'topic'=>"Consultation #{$consultation->id}",
            'type'=>2,
            'start_time'=>now()->addMinutes(1)->toIso8601String(),
            'duration'=>$consultation->duration,
            'settings'=>['join_before_host'=>true]
        ]);

        $data = $response->json();
        return ['id'=>$data['id'],'join_url'=>$data['join_url']];
    }

    private function extendZoomMeeting(Consultation $consultation, $extraMinutes)
    {
        $jwt = $this->getZoomJWT();
        Http::withHeaders([
            'Authorization'=>"Bearer $jwt",
            'Content-Type'=>'application/json'
        ])->patch("https://api.zoom.us/v2/meetings/{$consultation->zoom_meeting_id}",[
            'duration'=>$consultation->duration + $extraMinutes
        ]);

        $consultation->update(['duration'=>$consultation->duration + $extraMinutes]);
    }

}
