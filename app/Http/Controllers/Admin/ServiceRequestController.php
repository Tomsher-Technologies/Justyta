<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Emirate;
use App\Models\Dropdown;
use App\Models\Country;
use App\Models\ContractType;
use App\Models\LicenseType;
use App\Models\FreeZone;
use App\Models\AnnualAgreementInstallment;
use App\Models\ConsultationDuration;
use App\Models\Vendor;
use App\Models\AnnualRetainerBaseFee;
use App\Models\User;
use App\Models\Page;
use App\Models\CourtRequest;
use App\Models\PublicProsecution;
use App\Models\TranslationLanguage;
use App\Models\DocumentType;
use App\Models\ServiceRequest;
use App\Models\RequestCourtCase;
use App\Models\RequestCriminalComplaint;
use App\Models\RequestPowerOfAttorney;
use App\Models\RequestMemoWriting;
use App\Models\RequestEscrowAccount;
use App\Models\RequestDebtCollection;
use App\Models\RequestCompanySetup;
use App\Models\RequestContractDrafting;
use App\Models\RequestExpertReport;
use App\Models\RequestImmigration;
use App\Models\RequestRequestSubmission;
use App\Models\RequestAnnualAgreement;
use App\Models\RequestLegalTranslation;
use App\Models\DefaultTranslatorAssignment;
use App\Models\TranslatorLanguageRate;
use App\Models\RequestLastWill;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Notifications\ServiceRequestStatusChanged;
use Illuminate\Support\Facades\Notification;
use App\Exports\ServiceRequestExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ServiceRequestController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
       
        $this->middleware('permission:manage_service_requests',  ['only' => ['index','destroy']]);
        $this->middleware('permission:view_service_requests',  ['only' => ['index','show']]);
        $this->middleware('permission:change_request_status',  ['only' => ['updateRequestStatus','updatePaymentStatus','updateInstallmentStatus','assignServiceLawfirm']]);
        $this->middleware('permission:export_service_requests',  ['only' => ['export']]);

        $this->middleware('permission:manage_translation_requests',  ['only' => ['indexTranslation']]);
        $this->middleware('permission:view_translation_requests',  ['only' => ['indexTranslation','showTranslationRequest']]);
        $this->middleware('permission:change_translation_request_status',  ['only' => ['updateRequestStatus','updatePaymentStatus']]);
        $this->middleware('permission:export_translation_requests',  ['only' => ['exportLegalTranslationRequests']]);
    }

    public function indexTranslation (Request $request)
    {
        $request->session()->put('translation_service_request_last_url', url()->full());

        $query = ServiceRequest::with(['service','legalTranslation'])
                    ->where('request_success', 1)
                    ->whereIn('service_slug',['legal-translation']); 

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('daterange')) {
            $dates = explode(' to ', $request->daterange);
            if (count($dates) === 2) {
                $query->whereBetween('submitted_at', [
                    Carbon::parse($dates[0])->startOfDay(),
                    Carbon::parse($dates[1])->endOfDay()
                ]);
            }
        }

        if ($request->filled('keyword')) {
            $query->where('reference_code', 'like', '%' . $request->keyword . '%');
        }

        $serviceRequests = $query->orderByDesc('id')->paginate(30);

        return view('admin.translation_requests.index', compact('serviceRequests'));
    }

    public function showTranslationRequest($id){
        $id = base64_decode($id);

        $serviceRequest = ServiceRequest::with('service')->findOrFail($id);

        $relation = getServiceRelationName($serviceRequest->service_slug);

        if (!$relation || !$serviceRequest->relationLoaded($relation)) {
            $serviceRequest->load($relation);
        }

        $serviceDetails = $serviceRequest->$relation;
        $translatedData = getServiceHistoryTranslatedFields($serviceRequest->service_slug, $serviceDetails, 'en');
        $dataService = [
            'id'                => $serviceRequest->id,
            'translator'        => $serviceRequest->legalTranslation?->assignedTranslator?->name ?? '',
            'service_slug'      => $serviceRequest->service_slug,
            'user_name'         => $serviceRequest->user?->name,
            'user_email'        => $serviceRequest->user?->email,
            'user_phone'        => $serviceRequest->user?->phone,
            'service_name'      => $serviceRequest->service->getTranslation('title','en'),
            'reference_code'    => $serviceRequest->reference_code,
            'status'            => $serviceRequest->status,
            'payment_status'    => $serviceRequest->payment_status,
            'payment_reference' => $serviceRequest->payment_reference,
            'amount'            => $serviceRequest->amount,
            'submitted_at'      => date('d, M Y h:i A', strtotime($serviceRequest->submitted_at)),
            'service_details'   => $translatedData,
        ];
        
        return view('admin.translation_requests.show', compact('dataService'));
    }

     public function exportLegalTranslationRequests(Request $request)
    {
        $serviceSlug = 'legal-translation';

        $service = Service::where('slug', $serviceSlug)->firstOrFail();

        $modelMap = serviceModelFieldsMap();

        if (!isset($modelMap[$serviceSlug])) {
            return back()->with('error', 'Export not supported for this service.');
        }

        $modelInfo = $modelMap[$serviceSlug];
        $subModel = $modelInfo['model'];
        $fields = $modelInfo['fields'];

        $query = ServiceRequest::with('user', 'service')
                ->where('request_success', 1)
                ->where('service_slug', $serviceSlug);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('keyword')) {
            $query->where('reference_code', 'like', '%' . $request->keyword . '%');
        }

        if ($request->filled('daterange')) {
            $dates = explode(' to ', $request->daterange);
            if (count($dates) === 2) {
                $query->whereBetween('submitted_at', [
                    Carbon::parse($dates[0])->startOfDay(),
                    Carbon::parse($dates[1])->endOfDay()
                ]);
            }
        }
    
        $records = $query->get();

        foreach ($records as $record) {
            $details = $subModel::where('service_request_id', $record->id)->first();
            $record->details = $details;
        }
        $serviceName = $service->name ?? '';

        $filename = $serviceSlug . '_export_' . now()->format('Y_m_d_h_i_s') . '.xlsx';

        return Excel::download(new ServiceRequestExport($records, $serviceName, $serviceSlug, $fields), $filename);
    }


    public function index (Request $request)
    {
        $request->session()->put('service_request_last_url', url()->full());

        $services = Service::whereNotIn('slug', ['online-live-consultancy','legal-translation','law-firm-services'])
                            ->where('status',1)->orderBy('name')->get();
        $query = ServiceRequest::with('service')
                    ->where('request_success', 1)
                    ->whereNotIn('service_slug',['legal-translation']); 

        if ($request->filled('service_id')) {
            $serviceSlug = $request->service_id;
            if($serviceSlug === 'law-firm-services'){
                $slugs = Service::whereHas('parent', function ($query) {
                    $query->where('slug', 'law-firm-services');
                })->pluck('slug');

                $query->whereIn('service_slug', $slugs);
            }else{
                $query->where('service_slug', $serviceSlug);
            }    
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('daterange')) {
            $dates = explode(' to ', $request->daterange);
            if (count($dates) === 2) {
                $query->whereBetween('submitted_at', [
                    Carbon::parse($dates[0])->startOfDay(),
                    Carbon::parse($dates[1])->endOfDay()
                ]);
            }
        }

        if ($request->filled('keyword')) {
            $query->where('reference_code', 'like', '%' . $request->keyword . '%');
        }

        $serviceRequests = $query->orderByDesc('id')->paginate(30);

        return view('admin.service_requests.index', compact('serviceRequests','services'));
    }

    public function show($id){
        $id = base64_decode($id);

        $serviceRequest = ServiceRequest::with('service')->findOrFail($id);

        $relation = getServiceRelationName($serviceRequest->service_slug);

        if (!$relation || !$serviceRequest->relationLoaded($relation)) {
            $serviceRequest->load($relation);
        }

        $serviceDetails = $serviceRequest->$relation;
        $translatedData = getServiceHistoryTranslatedFields($serviceRequest->service_slug, $serviceDetails, 'en');
        $dataService = [
            'id'                => $serviceRequest->id,
            'service_slug'      => $serviceRequest->service_slug,
            'user_name'         => $serviceRequest->user?->name,
            'user_email'        => $serviceRequest->user?->email,
            'user_phone'        => $serviceRequest->user?->phone,
            'service_name'      => $serviceRequest->service->getTranslation('title','en'),
            'reference_code'    => $serviceRequest->reference_code,
            'status'            => $serviceRequest->status,
            'payment_status'    => $serviceRequest->payment_status,
            'payment_reference' => $serviceRequest->payment_reference,
            'amount'            => $serviceRequest->amount,
            'submitted_at'      => date('d, M Y h:i A', strtotime($serviceRequest->submitted_at)),
            'service_details'   => $translatedData,
        ];

        if($serviceRequest->service_slug === 'annual-retainer-agreement'){
            $installmentAnnual = AnnualAgreementInstallment::where('service_request_id',$serviceRequest->id)->get();

            $installments = $installmentAnnual->map(function ($inst) {
                return [
                    'id' => $inst->id,
                    'installment_no' => $inst->installment_no,
                    'amount' => $inst->amount ?? 0,
                    'status' => $inst->status,
                ];
            });
            $dataService['installments'] = $installments;

            $lawFirms = Vendor::whereHas('subscriptions', function ($query) {
                                $query->where('status', 'active')
                                    ->whereDate('subscription_end', '>=', Carbon::today());
                            })
                            ->whereHas('user', function ($query) {
                                $query->where('banned', 0);
                            })
                            ->with(['subscriptions', 'user'])
                            ->orderBy('law_firm_name', 'ASC')
                            ->get();

            $dataService['law_firms'] = $lawFirms->map(function ($lawfirm) {
                return [
                    'id'    => $lawfirm->id,
                    'value' => $lawfirm->getTranslation('law_firm_name', 'en'),
                ];
            });
        }
        
        return view('admin.service_requests.show', compact('dataService'));
    }

    public function updateRequestStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|in:pending,ongoing,under_review,completed,rejected'
        ]);

        $id = $request->id;

        $serviceRequest = ServiceRequest::findOrFail($id);
        $serviceRequest->status = $request->status;
        $serviceRequest->save();

        $user = $serviceRequest->user; 
        $user->notify(new ServiceRequestStatusChanged($serviceRequest));
        return response()->json(['status' => true,'message' => 'Service request status updated successfully.']);
    }

    public function updatePaymentStatus(Request $request)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,success',
        ]);

        $id = $request->id;

        $serviceRequest = ServiceRequest::findOrFail($id);
        $serviceRequest->payment_status  = $request->payment_status ;
        $serviceRequest->save();

        return response()->json(['status' => true,'message' => 'Service request payment status updated successfully.']);
    }

    public function export(Request $request)
    {
        $serviceSlug = $request->service_id;

        $service = Service::where('slug', $serviceSlug)->firstOrFail();

        $modelMap = serviceModelFieldsMap();

        if (!isset($modelMap[$serviceSlug])) {
            return back()->with('error', 'Export not supported for this service.');
        }

        $modelInfo = $modelMap[$serviceSlug];
        $subModel = $modelInfo['model'];
        $fields = $modelInfo['fields'];

        $query = ServiceRequest::with('user', 'service')
                ->where('request_success', 1)
                ->where('service_slug', $serviceSlug);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('keyword')) {
            $query->where('reference_code', 'like', '%' . $request->keyword . '%');
        }

        if ($request->filled('daterange')) {
            $dates = explode(' to ', $request->daterange);
            if (count($dates) === 2) {
                $query->whereBetween('submitted_at', [
                    Carbon::parse($dates[0])->startOfDay(),
                    Carbon::parse($dates[1])->endOfDay()
                ]);
            }
        }
    
        $records = $query->get();

        foreach ($records as $record) {
            $details = $subModel::where('service_request_id', $record->id)->first();
            $record->details = $details;
        }
        $serviceName = $service->name ?? '';

        $filename = $serviceSlug . '_export_' . now()->format('Y_m_d_h_i_s') . '.xlsx';

        return Excel::download(new ServiceRequestExport($records, $serviceName, $serviceSlug, $fields), $filename);
    }

    public function updateInstallmentStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:annual_agreement_installments,id',
            'status' => 'required|in:paid,pending,failed',
        ]);

        $installment = AnnualAgreementInstallment::findOrFail($request->id);
        $installment->status = $request->status;
        $installment->updated_at = now();
        $installment->save();

        $serviceRequest = $installment->serviceRequest;

        if ($serviceRequest) {
            
            $allPaid = $serviceRequest->installments()->where('status', '!=', 'paid')->count() === 0;

            if ($allPaid) {
                $serviceRequest->payment_status = 'success';
                $serviceRequest->paid_at = now();
                $serviceRequest->save();
            }else{
                $serviceRequest->payment_status = 'partial';
                $serviceRequest->paid_at = now();
                $serviceRequest->save();
            }
        }
        return response()->json(['success' => true]);
    }

    public function assignServiceLawfirm(Request $request){
        $request->validate([
            'lawfirm' => 'required|exists:vendors,id',
        ]);

        $lawfirmid = $request->lawfirm;

        $serviceRequest = ServiceRequest::findOrFail($request->id);
        if($serviceRequest){
            $serRequest = RequestAnnualAgreement::where('service_request_id', $serviceRequest->id)->first();
            if($serRequest){
                $serRequest->lawfirm = $lawfirmid;
                $serRequest->save();
            }
        }
        return response()->json(['status' => true,'message' => 'Law firm assigned successfully.']);
    }
}
