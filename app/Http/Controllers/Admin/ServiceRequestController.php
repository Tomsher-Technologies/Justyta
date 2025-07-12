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
        $this->middleware('permission:change_request_status',  ['only' => ['updateStatus']]);
        $this->middleware('permission:export_service_requests',  ['only' => ['index','show']]);
    }

    public function index (Request $request)
    {
        $request->session()->put('service_request_last_url', url()->full());

        $services = Service::whereNotIn('slug', ['online-live-consultancy','legal-translation','law-firm-services'])
                            ->where('status',1)->orderBy('name')->get();
        $query = ServiceRequest::with('service')->whereNotIn('service_slug',['legal-translation']); 

        // Filter by service_id
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

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by date range
        if ($request->filled('daterange')) {
            $dates = explode(' to ', $request->daterange);
            if (count($dates) === 2) {
                $query->whereBetween('submitted_at', [
                    Carbon::parse($dates[0])->startOfDay(),
                    Carbon::parse($dates[1])->endOfDay()
                ]);
            }
        }

        // Keyword search on reference_code
        if ($request->filled('keyword')) {
            $query->where('reference_code', 'like', '%' . $request->keyword . '%');
        }

        // Pagination
        $serviceRequests = $query->orderByDesc('id')->paginate(15);

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
            'service_details' => $translatedData,
        ];

        // echo '<pre>';
        // print_r($dataService);
        // die;

        return view('admin.service_requests.show', compact('dataService'));
    }

    public function updateRequestStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|in:pending,ongoing,completed,rejected'
        ]);

        $id = $request->id;

        $serviceRequest = ServiceRequest::findOrFail($id);
        $serviceRequest->status = $request->status;
        $serviceRequest->save();

        // 🔔 Send notification to the user who submitted the request
        $user = $serviceRequest->user; // assumes relation: serviceRequest belongsTo user
        $user->notify(new ServiceRequestStatusChanged($serviceRequest));

        // session()->flash('success', 'Service request status updated successfully.');

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

        // session()->flash('success', 'Service request payment status updated successfully.');

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

        // Eager load the related sub-model
        $query = ServiceRequest::with('user', 'service') // 'details' is dynamic
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

        // Dynamically attach the sub-model data into `details`
        foreach ($records as $record) {
            $details = $subModel::where('service_request_id', $record->id)->first();
            $record->details = $details;
        }
        $serviceName = $service->name ?? '';

        $filename = $serviceSlug . '_export_' . now()->format('Y_m_d_h_i_s') . '.xlsx';

        return Excel::download(new ServiceRequestExport($records, $serviceName, $serviceSlug, $fields), $filename);
    }
}
