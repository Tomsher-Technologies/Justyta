<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dropdown;
use App\Models\Service;
use App\Models\Emirate;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Notifications\ServiceRequestSubmitted;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class ServiceRequestController extends Controller
{
     public function showForm($slug)
    {
        $lang           = app()->getLocale() ?? env('APP_LOCALE','en');
        $service        = Service::where('slug', $slug)->firstOrFail();

        $dropdownData   = [];

        $emirates   = Emirate::where('status', 1)->orderBy('id')->get()
                            ->map(fn($e) => [
                                'id' => $e->id,
                                'value' => $e->getTranslation('name', $lang)
                            ]);

        switch ($slug) {
            case 'court-case-submission':
                $dropdowns  = Dropdown::with([
                                    'options' => function ($q) {
                                        $q->where('status', 'active')->orderBy('sort_order');
                                    },
                                    'options.translations' => function ($q) use ($lang) {
                                        $q->whereIn('language_code', [$lang, 'en']);
                                    }
                                ])->whereIn('slug', ['case_type', 'you_represent'])->get()->keyBy('slug');

                foreach ($dropdowns as $slug => $dropdown) {
                    $dropdownData[$slug] = $dropdown->options->map(function ($option) use ($lang){
                        return [
                            'id'    => $option->id,
                            'value' => $option->getTranslation('name',$lang),
                        ];
                    });
                }

                $dropdownData['emirates'] = $emirates;

                return view('frontend.user.service-requests.court_case', ['service' => $service, 'dropdownData' => $dropdownData, 'lang' => $lang]);

            case 'criminal-complaint':
                $dropdowns  = Dropdown::with([
                                    'options' => function ($q) {
                                        $q->where('status', 'active')->orderBy('sort_order');
                                    },
                                    'options.translations' => function ($q) use ($lang) {
                                        $q->whereIn('language_code', [$lang, 'en']);
                                    }
                                ])->whereIn('slug', ['case_type', 'you_represent'])->get()->keyBy('slug');

                foreach ($dropdowns as $slug => $dropdown) {
                    $dropdownData[$slug] = $dropdown->options->map(function ($option) use ($lang){
                        return [
                            'id'    => $option->id,
                            'value' => $option->getTranslation('name',$lang),
                        ];
                    });
                }
                $dropdownData['emirates'] = $emirates;

                return view('frontend.user.service-requests.criminal_complaint', ['service' => $service, 'dropdownData' => $dropdownData, 'lang' => $lang]);

            case 'power-of-attorney':
                
                break;

            case 'last-will-and-testament':

                $dropdowns  = Dropdown::with([
                                'options' => function ($q) {
                                    $q->where('status', 'active')->orderBy('sort_order');
                                },
                                'options.translations' => function ($q) use ($lang) {
                                    $q->whereIn('language_code', [$lang, 'en']);
                                }
                            ])->whereIn('slug', ['you_represent','religion'])->get()->keyBy('slug');
                
                foreach ($dropdowns as $slug => $dropdown) {
                    $dropdownData[$slug] = $dropdown->options->map(function ($option) use ($lang){
                        return [
                            'id'    => $option->id,
                            'value' => $option->getTranslation('name',$lang),
                        ];
                    });
                }
                $dropdownData['emirates'] = $emirates;

                $countries = Country::where('status',1)->orderBy('id')->get();

                $dropdownData['nationality'] = $countries->map(function ($country) use($lang) {
                        return [
                            'id'    => $country->id,
                            'value' => $country->getTranslation('name',$lang),
                        ];
                });

                return view('frontend.user.service-requests.last_will', ['service' => $service, 'dropdownData' => $dropdownData, 'lang' => $lang]);
    
            case 'memo-writing':
                $dropdowns  = Dropdown::with([
                                    'options' => function ($q) {
                                        $q->where('status', 'active')->orderBy('sort_order');
                                    },
                                    'options.translations' => function ($q) use ($lang) {
                                        $q->whereIn('language_code', [$lang, 'en']);
                                    }
                                ])->whereIn('slug', ['case_type', 'you_represent'])->get()->keyBy('slug');

                foreach ($dropdowns as $slug => $dropdown) {
                    $dropdownData[$slug] = $dropdown->options->map(function ($option) use ($lang){
                        return [
                            'id'    => $option->id,
                            'value' => $option->getTranslation('name',$lang),
                        ];
                    });
                }

                $dropdownData['emirates'] = $emirates;

                return view('frontend.user.service-requests.memo_writing', ['service' => $service, 'dropdownData' => $dropdownData, 'lang' => $lang]);
                    
            case 'expert-report':
                
                break;
                    
            case 'contract-drafting':
                
                break;

            case 'company-setup':
                
                break;

            case 'escrow-accounts':
                $dropdowns  = Dropdown::with([
                                'options' => function ($q) {
                                    $q->where('status', 'active')->orderBy('sort_order');
                                },
                                'options.translations' => function ($q) use ($lang) {
                                    $q->whereIn('language_code', [$lang, 'en']);
                                }
                            ])->whereIn('slug', ['industries'])->get()->keyBy('slug');
                
                foreach ($dropdowns as $slug => $dropdown) {
                    $dropdownData[$slug] = $dropdown->options->map(function ($option) use ($lang){
                        return [
                            'id'    => $option->id,
                            'value' => $option->getTranslation('name',$lang),
                        ];
                    });
                }

                $countries = Country::where('status',1)->orderBy('id')->get();

                $dropdownData['company_origin'] = $countries->map(function ($country) use($lang) {
                        return [
                            'id'    => $country->id,
                            'value' => $country->getTranslation('name',$lang),
                        ];
                });

                return view('frontend.user.service-requests.escrow_account', ['service' => $service, 'dropdownData' => $dropdownData, 'lang' => $lang]);

            case 'debts-collection':
                $dropdowns  = Dropdown::with([
                                'options' => function ($q) {
                                    $q->where('status', 'active')->orderBy('sort_order');
                                },
                                'options.translations' => function ($q) use ($lang) {
                                    $q->whereIn('language_code', [$lang, 'en']);
                                }
                            ])->whereIn('slug', ['debt_type','debt_category'])->get()->keyBy('slug');
                
                foreach ($dropdowns as $slug => $dropdown) {
                    $dropdownData[$slug] = $dropdown->options->map(function ($option) use ($lang){
                        return [
                            'id'    => $option->id,
                            'value' => $option->getTranslation('name',$lang),
                        ];
                    });
                }

                $dropdownData['emirates'] = $emirates;

                return view('frontend.user.service-requests.debts_collection', ['service' => $service, 'dropdownData' => $dropdownData, 'lang' => $lang]);
                
            case 'online-live-consultancy':
                
                break;

            case 'request-submission':
                
                break;

            case 'legal-translation':
                
                break;

            case 'annual-retainer-agreement':
                
                break;

            case 'immigration-requests':
                
                break;


        }

        abort(404);
    }

    public function requestCourtCase(Request $request){
        $validator = Validator::make($request->all(), [
            'applicant_type'    => 'required',
            'litigation_type'   => 'required',
            'emirate_id'        => 'required',
            'case_type'         => 'required',
            'you_represent'     => 'required',
            'memo'              => 'nullable|array',
            'documents'         => 'nullable|array',
            'eid'               => 'required|array',
            'trade_license'     => 'required|array',
            'memo.*'            => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:1024',
            'documents.*'       => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:1024',
            'eid.*'             => 'file|mimes:pdf,jpg,jpeg,webp,png,svg|max:500',
            'trade_license.*'   => 'file|mimes:pdf,jpg,jpeg,webp,png,svg|max:500',
        ], [
            'applicant_type.required'   => __('messages.applicant_type_required'),
            'litigation_type.required'  => __('messages.litigation_type_required'),
            'emirate_id.required'       => __('messages.emirate_required'),
            'case_type.required'        => __('messages.case_type_required'),
            'you_represent.required'    => __('messages.you_represent_required'),
            'memo.*.file'               => __('messages.memo_file_invalid'),
            'memo.*.mimes'              => __('messages.memo_file_mimes'),
            'memo.*.max'                => __('messages.memo_file_max'),
            'documents.*.file'          => __('messages.document_file_invalid'),
            'documents.*.mimes'         => __('messages.document_file_mimes'),
            'documents.*.max'           => __('messages.document_file_max'),
            'eid.required'              => __('messages.eid_required'),
            'eid.*.file'                => __('messages.eid_file_invalid'),
            'eid.*.mimes'               => __('messages.eid_file_mimes'),
            'eid.*.max'                 => __('messages.eid_file_max'),
            'trade_license.required'    => __('messages.trade_license_required'),
            'trade_license.*.file'      => __('messages.trade_license_file_invalid'),
            'trade_license.*.mimes'     => __('messages.trade_license_file_mimes'),
            'trade_license.*.max'       => __('messages.trade_license_file_max'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $lang       = app()->getLocale() ?? env('APP_LOCALE','en'); 
        $user       = Auth::guard('frontend')->user();
        $service    = Service::where('slug', 'court-case-submission')->firstOrFail();

        $referenceCode = ServiceRequest::generateReferenceCode($service);

        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'court-case-submission',
            'reference_code'    => $referenceCode,
            'source'            => 'web',
            'submitted_at'      => date('Y-m-d H:i:s')
        ]);

        $courtCase = RequestCourtCase::create([
            'user_id'               => $user->id,
            'service_request_id'    => $service_request->id,
            'applicant_type'        => $request->input('applicant_type') ?? NULL,
            'litigation_type'       => $request->input('litigation_type') ?? NULL,
            'emirate_id'            => $request->input('emirate_id') ?? NULL,
            'case_type'             => $request->input('case_type') ?? NULL,
            'you_represent'         => $request->input('you_represent') ?? NULL,
            'about_case'            => $request->input('about_case') ?? NULL,
            'memo'                  => [],
            'documents'             => [],
            'eid'                   => [],
            'trade_license'         => [],
        ]);

        $requestFolder = "uploads/court_cases/{$courtCase->id}/";

        $fileFields = [
            'memo'          => 'memo',
            'documents'     => 'documents',
            'eid'           => 'eid',
            'trade_license' => 'trade_license',
        ];

        $filePaths = [];

        foreach ($fileFields as $inputName => $columnName) {
            $filePaths[$columnName] = [];
            if ($request->hasFile($inputName)) {
                $files = $request->file($inputName);
                if (!is_array($files)) {
                    $files = [$files];
                }
                foreach ($files as $file) {
                    $uniqueName     = $inputName.'_'.uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filename       = $requestFolder.$uniqueName;
                    $fileContents   = file_get_contents($file);
                    Storage::disk('public')->put($filename, $fileContents);
                    $filePaths[$columnName][] = Storage::url($filename);
                }
            }
        }

        $courtCase->update($filePaths);

        // Notify the user
        $request->user()->notify(new ServiceRequestSubmitted($service_request));

        // Notify the admin (single or multiple)
        $admins = User::where('user_type', 'admin')->get();
        Notification::send($admins, new ServiceRequestSubmitted($service_request, true));

        return redirect()->route('user.request-success',['reqid' => base64_encode($service_request->id)]);
    }

    public function requestCriminalComplaint(Request $request){
        $validator = Validator::make($request->all(), [
            'applicant_type'    => 'required',
            'litigation_type'   => 'required',
            'emirate_id'        => 'required',
            'case_type'         => 'required',
            'you_represent'     => 'required',
            'memo'              => 'nullable|array',
            'documents'         => 'required|array',
            'eid'               => 'required|array',
            'trade_license'     => 'required|array',
            'memo.*'            => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:1024',
            'documents.*'       => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:1024',
            'eid.*'             => 'file|mimes:pdf,jpg,jpeg,webp,png,svg|max:500',
            'trade_license.*'   => 'file|mimes:pdf,jpg,jpeg,webp,png,svg|max:500',
        ], [
            'applicant_type.required'   => __('messages.applicant_type_required'),
            'litigation_type.required'  => __('messages.litigation_type_required'),
            'emirate_id.required'       => __('messages.emirate_required'),
            'case_type.required'        => __('messages.case_type_required'),
            'you_represent.required'    => __('messages.you_represent_required'),
            'memo.*.file'               => __('messages.memo_file_invalid'),
            'memo.*.mimes'              => __('messages.memo_file_mimes'),
            'memo.*.max'                => __('messages.memo_file_max'),
            'documents.required'        => __('messages.document_required'),
            'documents.*.file'          => __('messages.document_file_invalid'),
            'documents.*.mimes'         => __('messages.document_file_mimes'),
            'documents.*.max'           => __('messages.document_file_max'),
            'eid.required'              => __('messages.eid_required'),
            'eid.*.file'                => __('messages.eid_file_invalid'),
            'eid.*.mimes'               => __('messages.eid_file_mimes'),
            'eid.*.max'                 => __('messages.eid_file_max'),
            'trade_license.required'    => __('messages.trade_license_required'),
            'trade_license.*.file'      => __('messages.trade_license_file_invalid'),
            'trade_license.*.mimes'     => __('messages.trade_license_file_mimes'),
            'trade_license.*.max'       => __('messages.trade_license_file_max'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $lang       = app()->getLocale() ?? env('APP_LOCALE','en'); 
        $user       = Auth::guard('frontend')->user();
        $service    = Service::where('slug', 'criminal-complaint')->firstOrFail();

        $referenceCode = ServiceRequest::generateReferenceCode($service);

        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'criminal-complaint',
            'reference_code'    => $referenceCode,
            'source'            => 'web',
            'submitted_at'      => date('Y-m-d H:i:s')
        ]);

        $criminalComplaint = RequestCriminalComplaint::create([
            'user_id'               => $user->id,
            'service_request_id'    => $service_request->id,
            'applicant_type'        => $request->input('applicant_type') ?? NULL,
            'litigation_type'       => $request->input('litigation_type') ?? NULL,
            'emirate_id'            => $request->input('emirate_id') ?? NULL,
            'case_type'             => $request->input('case_type') ?? NULL,
            'you_represent'         => $request->input('you_represent') ?? NULL,
            'about_case'            => $request->input('about_case') ?? NULL,
            'memo'                  => [],
            'documents'             => [],
            'eid'                   => [],
            'trade_license'         => [],
        ]);

        $requestFolder = "uploads/criminal_complaints/{$criminalComplaint->id}/";

        $fileFields = [
            'memo'          => 'memo',
            'documents'     => 'documents',
            'eid'           => 'eid',
            'trade_license' => 'trade_license',
        ];

        $filePaths = [];

        foreach ($fileFields as $inputName => $columnName) {
            $filePaths[$columnName] = [];
            if ($request->hasFile($inputName)) {
                $files = $request->file($inputName);
                if (!is_array($files)) {
                    $files = [$files];
                }
                foreach ($files as $file) {
                    $uniqueName     = $inputName.'_'.uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filename       = $requestFolder.$uniqueName;
                    $fileContents   = file_get_contents($file);
                    Storage::disk('public')->put($filename, $fileContents);
                    $filePaths[$columnName][] = Storage::url($filename);
                }
            }
        }

        $criminalComplaint->update($filePaths);

        // Notify the user
        $request->user()->notify(new ServiceRequestSubmitted($service_request));

        // Notify the admin (single or multiple)
        $admins = User::where('user_type', 'admin')->get();
        Notification::send($admins, new ServiceRequestSubmitted($service_request, true));

        return redirect()->route('user.request-success',['reqid' => base64_encode($service_request->id)]);
    }

    public function requestLastWill(Request $request){

        $validator = Validator::make($request->all(), [
            'testament_place'   => 'required',
            'nationality'       => 'required',
            'emirate_id'        => 'required',
            'religion'          => 'required',
            'you_represent'     => 'required',
            'full_name'         => 'required',
            'eid'               => 'required|array',
            'eid.*'             => 'file|mimes:pdf,jpg,jpeg,webp,png,svg|max:500',
        ], [
            'testament_place.required'  => __('messages.testament_place_required'),
            'nationality.required'      => __('messages.nationality_required'),
            'emirate_id.required'       => __('messages.emirate_required'),
            'religion.required'         => __('messages.religion_required'),
            'full_name.required'        => __('messages.full_name_required'),
            'you_represent.required'    => __('messages.you_represent_required'),
            'eid.required'              => __('messages.eid_required'),
            'eid.*.file'                => __('messages.eid_file_invalid'),
            'eid.*.mimes'               => __('messages.eid_file_mimes'),
            'eid.*.max'                 => __('messages.eid_file_max'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $lang       = app()->getLocale() ?? env('APP_LOCALE','en'); 
        $user       = Auth::guard('frontend')->user();
        $service    = Service::where('slug', 'last-will-and-testament')->firstOrFail();

        $referenceCode = ServiceRequest::generateReferenceCode($service);

        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'last-will-and-testament',
            'reference_code'    => $referenceCode,
            'source'            => 'web',
            'submitted_at'      => date('Y-m-d H:i:s')
        ]);

        $lastWill = RequestLastWill::create([
            'user_id'               => $user->id,
            'service_request_id'    => $service_request->id,
            'testament_place'       => $request->input('testament_place') ?? NULL,
            'nationality'           => $request->input('nationality') ?? NULL,
            'emirate_id'            => $request->input('emirate_id') ?? NULL,
            'religion'              => $request->input('religion') ?? NULL,
            'you_represent'         => $request->input('you_represent') ?? NULL,
            'about_case'            => $request->input('about_case') ?? NULL,
            'full_name'             => $request->input('full_name') ?? NULL,
            'eid'                   => [],
        ]);

        $requestFolder = "uploads/last_will/{$lastWill->id}/";

        $fileFields = [
            'eid' => 'eid'
        ];

        $filePaths = [];

        foreach ($fileFields as $inputName => $columnName) {
            $filePaths[$columnName] = [];
            if ($request->hasFile($inputName)) {
                $files = $request->file($inputName);
                if (!is_array($files)) {
                    $files = [$files];
                }
                foreach ($files as $file) {
                    $uniqueName     = $inputName.'_'.uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filename       = $requestFolder.$uniqueName;
                    $fileContents   = file_get_contents($file);
                    Storage::disk('public')->put($filename, $fileContents);
                    $filePaths[$columnName][] = Storage::url($filename);
                }
            }
        }

        $lastWill->update($filePaths);
        // Notify the user
        $request->user()->notify(new ServiceRequestSubmitted($service_request));

        // Notify the admin (single or multiple)
        $admins = User::where('user_type', 'admin')->get();
        Notification::send($admins, new ServiceRequestSubmitted($service_request, true));

        return redirect()->route('user.request-success',['reqid' => base64_encode($service_request->id)]);
    }

    public function requestEscrowAccount(Request $request){

        $validator = Validator::make($request->all(), [
            'applicant_type'    => 'required',
            'company_name'      => 'required',
            'company_activity'  => 'required',
            'company_origin'    => 'required',
            'amount'            => 'required',
            'about_deal'        => 'required',
        ], [
            'applicant_type.required'   => __('messages.applicant_type_required'),
            'company_name.required'     => __('messages.company_name_required'),
            'company_activity.required' => __('messages.company_activity_required'),
            'company_origin.required'   => __('messages.company_origin_required'),
            'amount.required'           => __('messages.amount_required'),
            'about_deal.required'       => __('messages.about_deal_required'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $lang       = app()->getLocale() ?? env('APP_LOCALE','en'); 
        $user       = Auth::guard('frontend')->user();
        $service    = Service::where('slug', 'escrow-accounts')->firstOrFail();

        $referenceCode = ServiceRequest::generateReferenceCode($service);

        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'escrow-accounts',
            'reference_code'    => $referenceCode,
            'source'            => 'web',
            'submitted_at'      => date('Y-m-d H:i:s')
        ]);

        $escrowAccounts = RequestEscrowAccount::create([
            'user_id'               => $user->id,
            'service_request_id'    => $service_request->id,
            'applicant_type'        => $request->input('applicant_type') ?? NULL,
            'company_name'          => $request->input('company_name') ?? NULL,
            'company_activity'      => $request->input('company_activity') ?? NULL,
            'company_origin'        => $request->input('company_origin') ?? NULL,
            'amount'                => $request->input('amount') ?? NULL,
            'about_deal'            => $request->input('about_deal') ?? NULL
        ]);

        // Notify the user
        $request->user()->notify(new ServiceRequestSubmitted($service_request));

        // Notify the admin (single or multiple)
        $admins = User::where('user_type', 'admin')->get();
        Notification::send($admins, new ServiceRequestSubmitted($service_request, true));

        return redirect()->route('user.request-success',['reqid' => base64_encode($service_request->id)]);
    }

    public function requestDebtsCollection(Request $request){

        $validator = Validator::make($request->all(), [
            'applicant_type'    => 'required',
            'emirate_id'        => 'required',
            'debt_type'         => 'required',
            'debt_amount'       => 'required',
            'debt_category'     => 'required',
            'documents'         => 'nullable|array',
            'eid'               => 'required|array',
            'trade_license'     => 'required|array',
            'documents.*'       => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:1024',
            'eid.*'             => 'file|mimes:pdf,jpg,jpeg,webp,png,svg|max:500',
            'trade_license.*'   => 'file|mimes:pdf,jpg,jpeg,webp,png,svg|max:500',
        ], [
            'applicant_type.required'   => __('messages.applicant_type_required'),
            'debt_type.required'        => __('messages.debt_type_required'),
            'emirate_id.required'       => __('messages.emirate_required'),
            'debt_amount.required'      => __('messages.debt_amount_required'),
            'debt_category.required'    => __('messages.debt_category_required'),
            'documents.*.file'          => __('messages.document_file_invalid'),
            'documents.*.mimes'         => __('messages.document_file_mimes'),
            'documents.*.max'           => __('messages.document_file_max'),
            'eid.required'              => __('messages.eid_required'),
            'eid.*.file'                => __('messages.eid_file_invalid'),
            'eid.*.mimes'               => __('messages.eid_file_mimes'),
            'eid.*.max'                 => __('messages.eid_file_max'),
            'trade_license.required'    => __('messages.trade_license_required'),
            'trade_license.*.file'      => __('messages.trade_license_file_invalid'),
            'trade_license.*.mimes'     => __('messages.trade_license_file_mimes'),
            'trade_license.*.max'       => __('messages.trade_license_file_max'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $lang       = app()->getLocale() ?? env('APP_LOCALE','en'); 
        $user       = Auth::guard('frontend')->user();
        $service    = Service::where('slug', 'debts-collection')->firstOrFail();

        $referenceCode = ServiceRequest::generateReferenceCode($service);

        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'debts-collection',
            'reference_code'    => $referenceCode,
            'source'            => 'web',
            'submitted_at'      => date('Y-m-d H:i:s')
        ]);

        $debtCollection = RequestDebtCollection::create([
            'user_id'               => $user->id,
            'service_request_id'    => $service_request->id,
            'applicant_type'        => $request->input('applicant_type') ?? NULL,
            'emirate_id'            => $request->input('emirate_id') ?? NULL,
            'debt_type'             => $request->input('debt_type') ?? NULL,
            'debt_amount'           => $request->input('debt_amount') ?? NULL,
            'debt_category'         => $request->input('debt_category') ?? NULL,
            'documents'             => [],
            'eid'                   => [],
            'trade_license'         => [],
        ]);

        $requestFolder = "uploads/debt_collection/{$debtCollection->id}/";

        $fileFields = [
            'documents'     => 'documents',
            'eid'           => 'eid',
            'trade_license' => 'trade_license',
        ];

        $filePaths = [];

        foreach ($fileFields as $inputName => $columnName) {
            $filePaths[$columnName] = [];
            if ($request->hasFile($inputName)) {
                $files = $request->file($inputName);
                if (!is_array($files)) {
                    $files = [$files];
                }
                foreach ($files as $file) {
                    $uniqueName     = $inputName.'_'.uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filename       = $requestFolder.$uniqueName;
                    $fileContents   = file_get_contents($file);
                    Storage::disk('public')->put($filename, $fileContents);
                    $filePaths[$columnName][] = Storage::url($filename);
                }
            }
        }

        $debtCollection->update($filePaths);
        // Notify the user
        $request->user()->notify(new ServiceRequestSubmitted($service_request));

        // Notify the admin (single or multiple)
        $admins = User::where('user_type', 'admin')->get();
        Notification::send($admins, new ServiceRequestSubmitted($service_request, true));

        return redirect()->route('user.request-success',['reqid' => base64_encode($service_request->id)]);
    }

    public function requestMemoWriting(Request $request){

        $validator = Validator::make($request->all(), [
            'applicant_type'    => 'required',
            'litigation_type'   => 'required',
            'emirate_id'        => 'required',
            'case_type'         => 'required',
            'you_represent'     => 'required',
            'full_name'         => 'required',
            'documents'         => 'required|array',
            'eid'               => 'required|array',
            'trade_license'     => 'required|array',
            'documents.*'       => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:1024',
            'eid.*'             => 'file|mimes:pdf,jpg,jpeg,webp,png,svg|max:500',
            'trade_license.*'   => 'file|mimes:pdf,jpg,jpeg,webp,png,svg|max:500',
        ], [
            'applicant_type.required'   => __('messages.applicant_type_required'),
            'litigation_type.required'  => __('messages.litigation_type_required'),
            'emirate_id.required'       => __('messages.emirate_required'),
            'case_type.required'        => __('messages.case_type_required'),
            'you_represent.required'    => __('messages.you_represent_required'),
            'full_name.required'        => __('messages.full_name_required'),
            'documents.required'        => __('messages.document_required'),
            'documents.*.file'          => __('messages.document_file_invalid'),
            'documents.*.mimes'         => __('messages.document_file_mimes'),
            'documents.*.max'           => __('messages.document_file_max'),
            'eid.required'              => __('messages.eid_required'),
            'eid.*.file'                => __('messages.eid_file_invalid'),
            'eid.*.mimes'               => __('messages.eid_file_mimes'),
            'eid.*.max'                 => __('messages.eid_file_max'),
            'trade_license.required'    => __('messages.trade_license_required'),
            'trade_license.*.file'      => __('messages.trade_license_file_invalid'),
            'trade_license.*.mimes'     => __('messages.trade_license_file_mimes'),
            'trade_license.*.max'       => __('messages.trade_license_file_max'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $lang       = app()->getLocale() ?? env('APP_LOCALE','en'); 
        $user       = Auth::guard('frontend')->user();
        $service    = Service::where('slug', 'memo-writing')->firstOrFail();

        $referenceCode = ServiceRequest::generateReferenceCode($service);

        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'memo-writing',
            'reference_code'    => $referenceCode,
            'source'            => 'web',
            'submitted_at'      => date('Y-m-d H:i:s')
        ]);

        $memoWriting = RequestMemoWriting::create([
            'user_id'               => $user->id,
            'service_request_id'    => $service_request->id,
            'applicant_type'        => $request->input('applicant_type') ?? NULL,
            'litigation_type'       => $request->input('litigation_type') ?? NULL,
            'emirate_id'            => $request->input('emirate_id') ?? NULL,
            'case_type'             => $request->input('case_type') ?? NULL,
            'you_represent'         => $request->input('you_represent') ?? NULL,
            'full_name'             => $request->input('full_name') ?? NULL,
            'about_case'            => $request->input('about_case') ?? NULL,
            'memo'                  => [],
            'documents'             => [],
            'eid'                   => [],
            'trade_license'         => [],
        ]);

        $requestFolder = "uploads/memo_writing/{$memoWriting->id}/";

        $fileFields = [
            'documents'     => 'document',
            'eid'           => 'eid',
            'trade_license' => 'trade_license',
        ];

        $filePaths = [];

        foreach ($fileFields as $inputName => $columnName) {
            $filePaths[$columnName] = [];
            
            if ($request->hasFile($inputName)) {
                $files = $request->file($inputName);

                // If it's a single file, wrap it as an array
                if (!is_array($files)) {
                    $files = [$files];
                }

                foreach ($files as $file) {
                    $uniqueName     = $inputName.'_'.uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filename       = $requestFolder.$uniqueName;
                    $fileContents   = file_get_contents($file);
                    Storage::disk('public')->put($filename, $fileContents);
                    $filePaths[$columnName][] = Storage::url($filename);
                }
            }
        }

        $memoWriting->update($filePaths);
        // Notify the user
        $request->user()->notify(new ServiceRequestSubmitted($service_request));

        // Notify the admin (single or multiple)
        $admins = User::where('user_type', 'admin')->get();
        Notification::send($admins, new ServiceRequestSubmitted($service_request, true));

        return redirect()->route('user.request-success',['reqid' => base64_encode($service_request->id)]);
    }

    public function requestSuccess(Request $request, $reqid){

        $lang = app()->getLocale() ?? env('APP_LOCALE','en'); 

        $pageData = getPageDynamicContent('request_success',$lang);

        $requestId = $reqid ? base64_decode($reqid) : '';

        $service = ServiceRequest::find($requestId);

        $response = [
            'reference' => $service->reference_code ?? '',
            'message'   => $pageData['content']
        ];
        
        return view('frontend.user.service-requests.request_success', ['data' => $response, 'lang' => $lang]);
    }
}
