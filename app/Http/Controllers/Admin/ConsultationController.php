<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Lawyer;
use App\Models\Dropdown;
use App\Models\Vendor;
use App\Exports\ConsultationsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        $request->session()->put('last_page_consultations', url()->full());

        $conQuery = Consultation::with(['user', 'lawyer', 'emirate']);
            
        if($request->filled('lawyer_id')) {
            $conQuery->where('lawyer_id', $request->lawyer_id);
        }

        if($request->filled('lawfirm_id')) {
            $conQuery->whereHas('lawyer', function ($q) use ($request) {
                $q->where('lawfirm_id', $request->lawfirm_id);
            });
        }

        if($request->filled('consultation_type')) {
            $conQuery->where('consultant_type', $request->consultation_type);
        }

        if($request->filled('specialities')) {
            $conQuery->where('case_type', $request->specialities);
        }

        if($request->filled('language')) {
            $conQuery->where('language', $request->language);
        }

        if($request->filled('status')) {
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

        if($request->filled('keyword')) {
            $keyword = $request->keyword;
            $conQuery->where(function ($q) use ($keyword){
                $q->where('ref_code', 'like', "%{$keyword}%");
                $q->orWhereHas('user', function ($userQuery) use ($keyword) {
                    $userQuery->where('name', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%")
                            ->orWhere('phone', 'like', "%{$keyword}%");
                });
            });
        }

        $consultations = $conQuery->where('request_success', 1)->orderBy('id', 'desc')
                            ->paginate(15);

        $lawyers = Lawyer::select('id', 'full_name')->get();

        $dropdowns = Dropdown::with(['options.translations' => function ($q) {
            $q->where('language_code', 'en');
        }])->whereIn('slug', ['specialities', 'case_stage','languages'])->get()->keyBy('slug');

        $lawfirms = Vendor::select('id', 'law_firm_name')->get();

        return view('admin.consultations.index', compact('consultations', 'lawyers', 'dropdowns', 'lawfirms'));
    }

    public function show($id)
    {
        $consultation = Consultation::with([
            'user', 'lawyer', 'emirate', 
            'caseType', 'caseStage',
            'assignments.lawyer', 
            'payments'
        ])->find($id);

        return view('admin.consultations.view', compact('consultation'));
    }

    public function export(Request $request)
    {
        $filters = $request->all();
        
        $fileName = 'consultations_' . now()->format('Y_m_d_His') . '.xlsx';

        $canViewSales = auth()->user()->can('service_request_sales_view');
        return Excel::download(new ConsultationsExport($filters, $canViewSales), $fileName);
    }
}