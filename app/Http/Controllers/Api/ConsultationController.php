<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Consultation;
use App\Models\Lawyer;
use App\Models\ConsultationLawyerAttempt;
use Illuminate\Support\Facades\Http;

class ConsultationController extends Controller
{

    public function store(Request $request)
    {
        $data = $request->validate([
            'applicant_type' => 'required|in:company,individual',
            'litigation_type'=> 'required|in:local,federal',
            'consultant_type'=> 'required|in:normal,vip',
            'emirate_id'     => 'required|exists:emirates,id',
            'you_represent'  => 'required',
            'case_type'      => 'required',
            'case_stage'     => 'required',
            'language'       => 'required',
            'duration'       => 'required|numeric',
            'amount'         => 'required|numeric',
            'lawyer_id'      => 'nullable|exists:lawyers,id'
        ]);

        $consultation = Consultation::create([
            'user_id'=> auth()->id(),
            ...$data
        ]);

        return response()->json(['success'=>true, 'consultation_id'=>$consultation->id]);
    }

    public function paymentSuccess($id)
    {
        $consultation = Consultation::findOrFail($id);
        $consultation->update(['status'=>'waiting_lawyer']);

        if($consultation->consultant_type == 'vip') {
            ConsultationLawyerAttempt::create([
                'consultation_id'=> $consultation->id,
                'lawyer_id' => $consultation->lawyer_id
            ]);
        } else {
            $lawyers = Lawyer::where('emirate_id',$consultation->emirate_id)
                ->whereJsonContains('languages', $consultation->language)
                ->take(10)->get();

            foreach($lawyers as $lawyer){
                ConsultationLawyerAttempt::create([
                    'consultation_id'=> $consultation->id,
                    'lawyer_id'=> $lawyer->id
                ]);
            }
        }

        return response()->json(['success'=>true]);
    }

    public function lawyerPending(Request $request)
    {
        $lawyerId = auth()->user()->lawyer->id;
        $pending = ConsultationLawyerAttempt::with('consultation')
                    ->where('lawyer_id',$lawyerId)
                    ->where('status','pending')
                    ->get()
                    ->map(fn($a)=>$a->consultation);

        return response()->json($pending);
    }

    public function accept($id, Request $request)
    {
        $lawyerId = auth()->user()->lawyer->id;
        $attempt = ConsultationLawyerAttempt::where('consultation_id',$id)
                    ->where('lawyer_id',$lawyerId)
                    ->firstOrFail();

        if($attempt->status != 'pending'){
            return response()->json(['error'=>'Already responded'],400);
        }

        DB::transaction(function() use ($attempt){
            $attempt->update(['status'=>'accepted']);
            $consultation = $attempt->consultation;
            $consultation->update(['status'=>'accepted','lawyer_id'=>$attempt->lawyer_id]);

            ConsultationLawyerAttempt::where('consultation_id',$consultation->id)
                ->where('id','!=',$attempt->id)
                ->update(['status'=>'rejected']);

            $zoom = $this->createZoomMeeting($consultation);
            $consultation->update([
                'zoom_meeting_id'=>$zoom['id'],
                'zoom_join_url'=>$zoom['join_url']
            ]);
        });

        return response()->json(['success'=>true]);
    }

    public function reject($id)
    {
        $lawyerId = auth()->user()->lawyer->id;
        $attempt = ConsultationLawyerAttempt::where('consultation_id',$id)
                    ->where('lawyer_id',$lawyerId)
                    ->firstOrFail();

        $attempt->update(['status'=>'rejected']);

        $next = ConsultationLawyerAttempt::where('consultation_id',$id)
                ->where('status','pending')->first();

        if($next){
            // Notify next lawyer in app
        }

        return response()->json(['success'=>true]);
    }

    public function show($id)
    {
        return Consultation::with('attempts.lawyer')->findOrFail($id);
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

    private function getZoomJWT()
    {
        $key = env('ZOOM_API_KEY');
        $secret = env('ZOOM_API_SECRET');
        return \Firebase\JWT\JWT::encode([
            "iss"=>$key,
            "exp"=>time()+60
        ],$secret,'HS256');
    }
}
