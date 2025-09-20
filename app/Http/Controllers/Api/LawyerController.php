<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lawyer;
use App\Models\Consultation;
use App\Models\ConsultationAssignment;
use DB;

class LawyerController extends Controller
{
    public function dashboard(Request $request)
    {
        $lang       = $request->header('lang') ?? env('APP_LOCALE','en');
        $user       = $request->user();
        $userId   = $user->id ?? null;
        $year = $request->year ?? now()->year;

        $lawyer = Lawyer::where('user_id', $userId)->first();
        $lawyerId = $lawyer->id ?? null;

        $totalConsultations = Consultation::where('lawyer_id', $lawyerId)->where('status', 'completed')->count();

        $todaysConsultations = Consultation::where('lawyer_id', $lawyerId)
                                        ->where('status', 'completed')
                                        ->whereDate('created_at', today())
                                        ->count();

        $recentConsultations = Consultation::where('lawyer_id', $lawyerId)
                                            ->orderBy('created_at', 'desc')
                                            ->where('status', 'completed')
                                            ->take(5)
                                            ->get();
        $recentCons = $recentConsultations->map(function ($recent) use ($lang) {
                            return [
                                'id' => $recent->id,
                                'user_name' => $recent->user?->name,
                                'reference_number' => $recent->ref_code,
                                'applicant_type' => $recent->applicant_type,
                                'litigant_type' => $recent->litigation_type,
                                'consultant_type' => $recent->consultant_type,
                                'emirate' => $recent->emirate?->getTranslation('name', $lang),
                                'you_represent' => $recent->youRepresent?->getTranslation('name', $lang),
                                'case_type' => $recent->caseType?->getTranslation('name', $lang),
                                'case_stage' => $recent->caseStage?->getTranslation('name', $lang),
                                'language' => $recent->languageValue?->getTranslation('name', $lang),
                                'duration' => $recent->duration
                            ];
                        });  
                        
        $monthlyCounts = Consultation::where('lawyer_id', $lawyerId)
                                    ->whereYear('created_at', $year)
                                    ->select(
                                        DB::raw('MONTH(created_at) as month'),
                                        DB::raw('COUNT(*) as total')
                                    )
                                    ->groupBy(DB::raw('MONTH(created_at)'))
                                    ->orderBy('month')
                                    ->get();

        $monthNames = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec'];

        $months = collect(range(1, 12))->map(function($m) use ($monthlyCounts) {
            $count = $monthlyCounts->firstWhere('month', $m)->total ?? 0;
            return ['month' => $m, 'total' => $count];
            // return ['month' => $monthNames[$m], 'total' => $count];
        });

        $todayHours = getTodaysActiveHours($userId);
        
        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => [
                'today_hours' => $todayHours,
                'total_consultations' => $totalConsultations,
                'todays_consultations' => $todaysConsultations,
                'recent_consultations' => $recentCons,
                'year' => $year,
                'monthly_counts' => $months
            ]
        ]);
    }

    public function assignedConsultations(Request $request)
    {
        $lang       = $request->header('lang') ?? env('APP_LOCALE','en');
        $user       = $request->user();
        $userId   = $user->id ?? null;

        $lawyer = Lawyer::where('user_id', $userId)->first();
        $lawyerId = $lawyer->id ?? null;

        $status = null;
        $perPage = $request->get('limit', 10);

        if($request->has('status')) {
            if($request->status == 'completed') {
                $status = 'accepted';
            }else {
                $status = 'rejected';
            }
        }
        $query = ConsultationAssignment::with(['consultation.caseType', 'consultation.user'])
                                    ->when($status, function ($q) use ($status) {
                                        $q->where('status', $status);
                                    })
                                    ->when($request->case_type, function ($q) use ($request) {
                                        $q->whereHas('consultation', function ($c) use ($request) {
                                            $c->where('case_type', $request->case_type);
                                        });
                                    })
                                    ->when($request->from_date && $request->to_date, function ($q) use ($request) {
                                        $q->whereBetween('assigned_at', [$request->from_date, $request->to_date]);
                                    })
                                    ->where('lawyer_id', $lawyerId)
                                    ->orderByDesc('assigned_at','desc');

        $paginatedConsultations = $query->paginate($perPage);

        $consultations = collect($paginatedConsultations->items())
                    ->map(function ($con) use($lang) {
                        
                        return [
                            'id' => $con->id,
                            'user_name' => $con->consultation?->user?->name,
                            'reference_number' => $con->consultation?->ref_code,
                            'applicant_type' => $con->consultation?->applicant_type,
                            'litigant_type' => $con->consultation?->litigation_type,
                            'consultant_type' => $con->consultation?->consultant_type,
                            'emirate' => $con->consultation?->emirate?->getTranslation('name', $lang),
                            'you_represent' => $con->consultation?->youRepresent?->getTranslation('name', $lang),
                            'case_type' => $con->consultation?->caseType?->getTranslation('name', $lang),
                            'case_stage' => $con->consultation?->caseStage?->getTranslation('name', $lang),
                            'language' => $con->consultation?->languageValue?->getTranslation('name', $lang),
                            'duration' => $con->consultation?->duration
                        ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => $consultations,
        ]);
    }
}
