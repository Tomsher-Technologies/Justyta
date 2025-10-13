<?php

use App\Models\BusinessSetting;
use App\Utility\CategoryUtility;
use App\Models\EnquiryStatus;
use App\Models\Service;
use App\Models\Page;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorSubscription;
use App\Models\Lawyer;
use App\Models\CaseType;
use App\Models\JobPost;
use App\Models\RequestType;
use App\Models\RequestTitle;
use App\Models\UserOnlineLog;
use App\Models\ConsultationAssignment;
use App\Models\ServiceRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Firebase\JWT\JWT;
use Carbon\Carbon;

function generateZoomSignature($meetingNumber, $userId, $role = 0)
{
    $sdkKey    = config('services.zoom.sdk_key');
    $sdkSecret = config('services.zoom.sdk_secret');

    // $iat = time();
    // $exp = $iat + 2 * 60; // valid for 2 minutes

    // $payload = [
    //     'sdkKey'   => $sdkKey,
    //     'mn'       => (string)$meetingNumber,
    //     'role'     => $role,
    //     'iat'      => $iat,
    //     'exp'      => $exp,
    //     'tokenExp' => $exp,
    // ];

    $payload = [
        "app_key" => $sdkKey,
        "tpc" => $meetingNumber,          // session name (e.g. Consultation-67, must not contain spaces)
        "role_type" => $role,       // 1 = host (lawyer), 0 = participant (user)
        "user_identity" => (string)$userId,
        "iat" => time(),
        "exp" => time() + 60 * 60,      // valid for 1 hour
        "version" => 1
    ];


    return JWT::encode($payload, $sdkSecret, 'HS256');
}

function getTodaysActiveHours($userId)
{
    $tz = config('app.timezone');
    $todayStart = Carbon::now($tz)->startOfDay();
    $todayEnd   = Carbon::now($tz)->endOfDay();

    $logs = UserOnlineLog::where('user_id', $userId)
        ->whereBetween('created_at', [$todayStart, $todayEnd])
        ->orderBy('created_at', 'asc')
        ->get();


    $totalSeconds = 0;
    $onlineAt = null;

    foreach ($logs as $key => $log) {
        $logTime = Carbon::parse($log->created_at, $tz);

        if ($log->status == 1) {
            if (!$onlineAt) {
                $onlineAt = $logTime;
            }
        } else {
            if ($onlineAt) {
                $diff = $onlineAt->diffInSeconds($logTime);
                $totalSeconds += max($diff, 0);
                $onlineAt = null;
            }
        }
    }

    if ($onlineAt) {
        $diff = $onlineAt->diffInSeconds(Carbon::now($tz));
        $totalSeconds += max($diff, 0);
    }

    $hours = round($totalSeconds / 3600, 2);

    return $hours;
}

function getCaseTypes($litigation_type, $litigation_place, $lang = 'en')
{
    $caseTypes = CaseType::where('litigation_type', $litigation_type)
        ->where('litigation_place', $litigation_place)
        ->where('status', 1)
        ->orderBy('sort_order')
        ->get();

    $caseTypes = $caseTypes->map(function ($caseType) use ($lang) {
        return [
            'id' => $caseType->id,
            'title' => $caseType->getTranslation('title', $lang),
        ];
    });

    return $caseTypes;
}

function getCaseTypesValue($litigation_type, $litigation_place, $lang = 'en')
{
    $caseTypes = CaseType::where('litigation_type', $litigation_type)
        ->where('litigation_place', $litigation_place)
        ->where('status', 1)
        ->orderBy('sort_order')
        ->get();

    $caseTypes = $caseTypes->map(function ($caseType) use ($lang) {
        return [
            'id' => $caseType->id,
            'value' => $caseType->getTranslation('title', $lang),
        ];
    });

    return $caseTypes;
}

function getRequestTypes($litigation_type, $litigation_place, $lang = 'en')
{
    $requestTypes = RequestType::where('litigation_type', $litigation_type)
        ->where('litigation_place', $litigation_place)
        ->where('status', 1)
        ->orderBy('sort_order')
        ->get();

    $requestTypes = $requestTypes->map(function ($requestType) use ($lang) {
        return [
            'id' => $requestType->id,
            'title' => $requestType->getTranslation('title', $lang),
        ];
    });

    return $requestTypes;
}

function getRequestTitles($request_type_id, $lang = 'en')
{
    $requestTitles = RequestTitle::where('request_type_id', $request_type_id)
        ->where('status', 1)
        ->orderBy('sort_order')
        ->get();

    $requestTitles = $requestTitles->map(function ($requestTitle) use ($lang) {
        return [
            'id' => $requestTitle->id,
            'title' => $requestTitle->getTranslation('title', $lang),
        ];
    });

    return $requestTitles;
}

if (!function_exists('getBaseURL')) {
    function getBaseURL()
    {
        $root = '//' . $_SERVER['HTTP_HOST'];
        $root .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

        return $root;
    }
}


//highlights the selected navigation on admin panel
if (!function_exists('areActiveRoutes')) {
    function areActiveRoutes(array $routes, $output = "active open")
    {
        foreach ($routes as $route) {
            if (Route::currentRouteName() == $route) return $output;
        }
    }
}

//highlights the selected navigation on frontend
if (!function_exists('areActiveWebRoutes')) {
    function areActiveWebRoutes(array $routes, $output = "side-active")
    {
        foreach ($routes as $route) {
            if (Route::currentRouteName() == $route) return $output;
        }
    }
}

function getActiveLanguage()
{
    if (Session::exists('locale')) {
        return Session::get('locale');
    }
    return 'en';
}


function checkLawyerLimit()
{
    $lawfirmId = Auth::guard('frontend')->user()->vendor?->id;

    $lawfirmPlan = VendorSubscription::where('vendor_id', $lawfirmId)
        ->where('status', 'active')
        ->first();

    $lawyerCount = Lawyer::where('lawfirm_id', $lawfirmId)->count();

    if ($lawfirmPlan && $lawyerCount < $lawfirmPlan->member_count) {
        return true;
    }

    return false;
}

function uploadImage($type, $imageUrl, $filename = null)
{
    $data_url = '';
    $ext = $imageUrl->getClientOriginalExtension();

    $path = $type . '/';

    $filename = $path . $filename . '_' . time() . '_' . rand(10, 9999) . '.' . $ext;

    $imageContents = file_get_contents($imageUrl);

    Storage::disk('public')->put($filename, $imageContents);
    $data_url = Storage::url($filename);

    return $data_url;
}

function getUploadedImage(?string $path, string $default = 'assets/img/default_image.png'): string
{
    if ($path) {
        $relativePath = str_replace('/storage/', '', $path);
        if (Storage::disk('public')->exists($relativePath)) {
            return asset($path);
        }
    }

    return asset($default);
}

function getUploadedUserImage(?string $path, string $default = 'assets/img/default_user.png'): string
{
    if ($path) {
        $relativePath = str_replace('/storage/', '', $path);
        if (Storage::disk('public')->exists($relativePath)) {
            return asset($path);
        }
    }

    return asset($default);
}

function getUploadedFile(?string $path): string
{
    if ($path) {
        $relativePath = str_replace('/storage/', '', $path);
        if (Storage::disk('public')->exists($relativePath)) {
            return asset($path);
        }
    }

    return NULL;
}

function getServiceId($slug)
{
    $service =  Service::where('slug', $slug)->pluck('id');

    return $service[0] ?? NULL;
}

function getPageDynamicContent($slug, $lang = 'en')
{
    $data = Page::with('translations')->where('slug', $slug)->first();

    $response =  [
        'title' => $data?->getTranslation('title', $lang),
        'description' => $data?->getTranslation('description', $lang),
        'content' => $data?->getTranslation('content', $lang),
    ];
    return $response;
}

function getServiceRelationName($slug)
{
    $map = [
        'request-submission'        => 'requestSubmission',
        'legal-translation'         => 'legalTranslation',
        'annual-retainer-agreement' => 'annualAgreement',
        'immigration-requests'      => 'immigrationRequest',
        'court-case-submission'     => 'courtCase',
        'criminal-complaint'        => 'criminalComplaint',
        'power-of-attorney'         => 'powerOfAttorney',
        'last-will-and-testament'   => 'lastWill',
        'memo-writing'              => 'memoWriting',
        'expert-report'             => 'expertReport',
        'contract-drafting'         => 'contractDrafting',
        'company-setup'             => 'companySetup',
        'escrow-accounts'           => 'escrowAccount',
        'debts-collection'          => 'debtCollection',
    ];

    return $map[$slug] ?? null;
}

function formatFilePathsWithFullUrl(array $files): array
{
    return array_values(array_filter(array_map(function ($path) {
        // Strip starting slash to match disk paths
        $cleanPath = ltrim($path, '/');

        // Check existence in storage
        if (Storage::disk('public')->exists(str_replace('storage/', '', $cleanPath))) {
            return asset($path); // Or use asset($path)
        }

        return null;
    }, $files)));
}

function serviceModelFieldsMap()
{
    $data = [
        'request-submission' => [
            'model' => \App\Models\RequestRequestSubmission::class,
            'fields' => [
                'applicant_type'    => 'Applicant Type',
                'litigation_type'   => 'Litigation Type',
                'litigation_place'  => 'Litigation Place',
                'emirate_id'        => 'Emirate',
                'case_type'         => 'Case Type',
                'request_type'      => 'Request Type',
                'request_title'     => 'Request Title',
                'case_number'       => 'Case Number',
                'memo'              => 'Memo',
                'documents'         => 'Documents',
                'eid'               => 'Emirates ID',
                'trade_license'     => 'Trade License',
            ],
        ],
        'annual-retainer-agreement' => [
            'model' => \App\Models\RequestAnnualAgreement::class,
            'fields' => [
                'company_name'      => 'Company Name',
                'emirate_id'        => 'Emirate',
                'license_type'      => 'License Type',
                'license_activity'  => 'License Activity',
                'industry'          => 'Industry',
                'no_of_employees'   => 'No of Employees',
                'case_type'         => 'Case Type',
                'no_of_calls'       => 'No of Calls',
                'no_of_visits'      => 'No of Visits',
                'no_of_installment' => 'No of Installments',
                'lawfirm'           => 'Law firm',
            ],
        ],
        'company-setup' => [
            'model' => \App\Models\RequestCompanySetup::class,
            'fields' => [
                'applicant_type'    => 'Applicant Type',
                'emirate_id'        => 'Emirate',
                'zone'              => 'Zone',
                'license_type'      => 'Licence Type',
                'license_activity'  => 'Licence Activity',
                'company_type'      => 'Company Type',
                'industry'          => 'Industry',
                'company_name'      => 'Company Name',
                'mobile'            => 'Mobile',
                'email'             => 'Email',
                'documents'         => 'Documents',
            ],
        ],
        'contract-drafting' => [
            'model' => \App\Models\RequestContractDrafting::class,
            'fields' => [
                'applicant_type'    => 'Applicant Type',
                'contract_type'     => 'Contract Type',
                'emirate_id'        => 'Emirate',
                'sub_contract_type' => 'Subcontract Type',
                'contract_language' => 'Contract Language',
                'company_name'      => 'Company Name',
                'industry'          => 'Industry',
                'email'             => 'Email',
                'priority'          => 'Priority',
                'documents'         => 'Documents',
                'eid'               => 'Emirates ID',
                'trade_license'     => 'Trade License',
            ],
        ],
        'court-case-submission' => [
            'model' => \App\Models\RequestCourtCase::class,
            'fields' => [
                'applicant_type'    => 'Applicant Type',
                'litigation_type'   => 'Litigation Type',
                'emirate_id'        => 'Emirate',
                'case_type'         => 'Case Type',
                'you_represent'     => 'You Represent',
                'about_case'        => 'About Case',
                'memo'              => 'Memo',
                'documents'         => 'Documents',
                'eid'               => 'Emirates ID',
                'trade_license'     => 'Trade License',
            ],
        ],
        'criminal-complaint' => [
            'model' => \App\Models\RequestCriminalComplaint::class,
            'fields' => [
                'applicant_type'    => 'Applicant Type',
                'litigation_type'   => 'Litigation Type',
                'emirate_id'        => 'Emirate',
                'case_type'         => 'Case Type',
                'you_represent'     => 'You Represent',
                'about_case'        => 'About Case',
                'memo'              => 'Memo',
                'documents'         => 'Documents',
                'eid'               => 'Emirates ID',
                'trade_license'     => 'Trade License',
            ],
        ],
        'debts-collection' => [
            'model' => \App\Models\RequestDebtCollection::class,
            'fields' => [
                'applicant_type'    => 'Applicant Type',
                'emirate_id'        => 'Emirate',
                'debt_type'         => 'Debt Type',
                'debt_amount'       => 'Debt Amount',
                'debt_category'     => 'Debt Category',
                'documents'         => 'Documents',
                'eid'               => 'Emirates ID',
                'trade_license'     => 'Trade License',
            ],
        ],
        'escrow-accounts' => [
            'model' => \App\Models\RequestEscrowAccount::class,
            'fields' => [
                'applicant_type'    => 'Applicant Type',
                'company_name'      => 'Company Name',
                'company_activity'  => 'Company Activity',
                'company_origin'    => 'Company Origin',
                'amount'            => 'Amount',
                'about_deal'        => 'About Deal'
            ],
        ],
        'expert-report' => [
            'model' => \App\Models\RequestExpertReport::class,
            'fields' => [
                'applicant_type'            => 'Applicant Type',
                'applicant_place'           => 'Applicant Place',
                'emirate_id'                => 'Emirate',
                'expert_report_type'        => 'Expert Report Type',
                'expert_report_language'    => 'Expert Report Language',
                'about_case'                => 'About Case',
                'documents'                 => 'Documents',
                'eid'                       => 'Emirates ID',
                'trade_license'             => 'Trade License',
            ],
        ],
        'immigration-requests' => [
            'model' => \App\Models\RequestImmigration::class,
            'fields' => [
                'preferred_country'     => 'Preferred Country',
                'position'              => 'Position',
                'age'                   => 'Age',
                'nationality'           => 'Nationality',
                'years_of_experience'   => 'Years Of Experience',
                'address'               => 'Address',
                'residency_status'      => 'Residency Status',
                'current_salary'        => 'Current Salary',
                'application_type'      => 'Application Type',
                'cv'                    => 'CV',
                'certificates'          => 'Certificates',
                'passport'              => 'Passport',
                'photo'                 => 'Photo',
                'account_statement'     => 'Account Statement',
            ],
        ],
        'last-will-and-testament' => [
            'model' => \App\Models\RequestLastWill::class,
            'fields' => [
                'testament_place'   => 'Testament Place',
                'nationality'       => 'Nationality',
                'emirate_id'        => 'Emirate',
                'religion'          => 'Religion',
                'you_represent'     => 'You Represent',
                'about_case'        => 'About Case',
                'eid'               => 'Emirates ID'
            ],
        ],
        'legal-translation' => [
            'model' => \App\Models\RequestLegalTranslation::class,
            'fields' => [
                'priority_level'        => 'Priority Level',
                'document_language'     => 'Document Language',
                'translation_language'  => 'Translation Language',
                'document_type'         => 'Document Type',
                'document_sub_type'     => 'Document Subtype',
                'receive_by'            => 'Receive By',
                'no_of_pages'           => 'No Of Pages',
                'memo'                  => 'Memo',
                'documents'             => 'Documents',
                'eid'                   => 'Emirates ID',
                'trade_license'         => 'Trade License',
            ],
        ],
        'memo-writing' => [
            'model' => \App\Models\RequestMemoWriting::class,
            'fields' => [
                'applicant_type'        => 'Applicant Type',
                'litigation_type'       => 'Litigation Type',
                'emirate_id'            => 'Emirate',
                'case_type'             => 'Case Type',
                'you_represent'         => 'You Represent',
                'full_name'             => 'Full Name',
                'about_case'            => 'About Case',
                'documents'             => 'Documents',
                'eid'                   => 'Emirates ID',
                'trade_license'         => 'Trade License',
            ],
        ],
        'power-of-attorney' => [
            'model' => \App\Models\RequestPowerOfAttorney::class,
            'fields' => [
                'applicant_type'        => 'Applicant Type',
                'appointer_name'        => 'Appointer Name',
                'id_number'             => 'ID Number',
                'appointer_mobile'      => 'Appointer Mobile',
                'emirate_id'            => 'Emirate',
                'poa_type'              => 'Power Of Attorney Type',
                'name_of_authorized'    => 'Name Of Authorized',
                'authorized_mobile'     => 'Authorized Mobile',
                'id_number_authorized'  => 'ID Number Of Authorized',
                'authorized_address'    => 'Authorized Address',
                'relationship'          => 'Relationship',
                'appointer_id'          => 'Appointer ID',
                'authorized_id'         => 'Authorized ID',
                'authorized_passport'   => 'Authorized Passport'
            ],
        ],
    ];

    return $data;
}

function getServiceHistoryTranslatedFields($slug, $model, $lang)
{
    // Example: Common service fields with translations
    if ($model->relationLoaded('translations') || method_exists($model, 'translations')) {
        $translation = $model->translations->where('lang', $lang)->first();
    }
    switch ($slug) {
        case 'request-submission':
            return [
                'applicant_type'        => $model->applicant_type,
                'litigation_type'       => $model->litigation_type,
                'litigation_place'      => $model->litigation_place,
                'emirate_id'            => $model->emirate?->getTranslation('name', $lang) ?? NULL,
                'case_type'             => $model->caseType?->getTranslation('title', $lang) ?? NULL,
                'request_type'          => $model->requestType?->getTranslation('title', $lang) ?? NULL,
                'request_title'         => $model->requestTitle?->getTranslation('title', $lang) ?? NULL,
                'case_number'           => $model->case_number,
                'memo'                  => formatFilePathsWithFullUrl($model->memo ?? []),
                'documents'             => formatFilePathsWithFullUrl($model->documents ?? []),
                'eid'                   => formatFilePathsWithFullUrl($model->eid ?? []),
                'trade_license'         => formatFilePathsWithFullUrl($model->trade_license ?? []),
            ];
        case 'legal-translation':
            return [
                'priority_level'        => $model->priority_level,
                'document_language'     => $model->documentLanguage?->getTranslation('name', $lang) ?? NULL,
                'translation_language'  => $model->translationLanguage?->getTranslation('name', $lang) ?? NULL,
                'document_type'         => $model->documentType?->getTranslation('name', $lang) ?? NULL,
                'document_sub_type'     => $model->documentSubType?->getTranslation('name', $lang) ?? NULL,
                'receive_by'            => $model->receive_by,
                'no_of_pages'           => $model->no_of_pages,
                'memo'                  => formatFilePathsWithFullUrl($model->memo ?? []),
                'documents'             => formatFilePathsWithFullUrl($model->documents ?? []),
                'additional_documents'  => formatFilePathsWithFullUrl($model->additional_documents ?? []),
                'trade_license'         => formatFilePathsWithFullUrl($model->trade_license ?? []),
            ];
        case 'annual-retainer-agreement':
            return [
                'company_name'          => $model->company_name,
                'emirate_id'            => $model->emirate?->getTranslation('name', $lang) ?? NULL,
                'license_type'          => $model->licenseType?->getTranslation('name', $lang) ?? NULL,
                'license_activity'      => $model->licenseActivity?->getTranslation('name', $lang) ?? NULL,
                'industry'              => $model->industryOption?->getTranslation('name', $lang) ?? NULL,
                'no_of_employees'       => $model->noOfEmployees?->getTranslation('name', $lang) ?? NULL,
                'case_type'             => $model->case_type_names,
                'no_of_calls'           => $model->no_of_calls,
                'no_of_visits'          => $model->no_of_visits,
                'no_of_installment'     => $model->no_of_installment,
                'lawfirm'               => $model->lawFirm?->getTranslation('law_firm_name', $lang) ?? NULL,
                'lawfirm_id'            => $model->lawfirm
            ];
        case 'immigration-requests':
            return [
                'preferred_country'     => $model->preferredCountry?->getTranslation('name', $lang) ?? NULL,
                'position'              => $model->currentPosition?->getTranslation('name', $lang) ?? NULL,
                'age'                   => $model->age,
                'nationality'           => $model->nationalityOption?->getTranslation('name', $lang) ?? NULL,
                'years_of_experience'   => $model->years_of_experience,
                'address'               => $model->address,
                'residency_status'      => $model->residencyStatus?->getTranslation('name', $lang) ?? NULL,
                'current_salary'        => $model->current_salary,
                'application_type'      => $model->applicationType?->getTranslation('name', $lang) ?? NULL,
                'cv'                    => formatFilePathsWithFullUrl($model->cv ?? []),
                'certificates'          => formatFilePathsWithFullUrl($model->certificates ?? []),
                'passport'              => formatFilePathsWithFullUrl($model->passport ?? []),
                'photo'                 => formatFilePathsWithFullUrl($model->photo ?? []),
                'account_statement'     => formatFilePathsWithFullUrl($model->account_statement ?? []),
            ];
        case 'court-case-submission':
            return [
                'applicant_type'        => $model->applicant_type,
                'litigation_type'       => $model->litigation_type,
                'emirate_id'            => $model->emirate?->getTranslation('name', $lang) ?? NULL,
                'case_type'             => $model->caseType?->getTranslation('title', $lang) ?? NULL,
                'you_represent'         => $model->youRepresent?->getTranslation('name', $lang) ?? NULL,
                'about_case'            => $model->about_case,
                'memo'                  => formatFilePathsWithFullUrl($model->memo ?? []),
                'documents'             => formatFilePathsWithFullUrl($model->documents ?? []),
                'eid'                   => formatFilePathsWithFullUrl($model->eid ?? []),
                'trade_license'         => formatFilePathsWithFullUrl($model->trade_license ?? []),
            ];
        case 'criminal-complaint':
            return [
                'applicant_type'        => $model->applicant_type,
                'litigation_type'       => $model->litigation_type,
                'emirate_id'            => $model->emirate?->getTranslation('name', $lang) ?? NULL,
                'case_type'             => $model->caseType?->getTranslation('title', $lang) ?? NULL,
                'you_represent'         => $model->youRepresent?->getTranslation('name', $lang) ?? NULL,
                'about_case'            => $model->about_case,
                'memo'                  => formatFilePathsWithFullUrl($model->memo ?? []),
                'documents'             => formatFilePathsWithFullUrl($model->documents ?? []),
                'eid'                   => formatFilePathsWithFullUrl($model->eid ?? []),
                'trade_license'         => formatFilePathsWithFullUrl($model->trade_license ?? []),
            ];
        case 'power-of-attorney':
            return [
                'applicant_type'        => $model->applicant_type,
                'appointer_name'        => $model->appointer_name,
                'id_number'             => $model->id_number,
                'appointer_mobile'      => $model->appointer_mobile,
                'emirate_id'            => $model->emirate?->getTranslation('name', $lang) ?? NULL,
                'poa_type'              => $model->powerOfAttorneyType?->getTranslation('name', $lang) ?? NULL,
                'name_of_authorized'    => $model->name_of_authorized,
                'authorized_mobile'     => $model->authorized_mobile,
                'id_number_authorized'  => $model->id_number_authorized,
                'authorized_address'    => $model->authorized_address,
                'relationship'          => $model->relationshipOption?->getTranslation('name', $lang) ?? NULL,
                'appointer_id'          => formatFilePathsWithFullUrl($model->appointer_id ?? []),
                'authorized_id'         => formatFilePathsWithFullUrl($model->authorized_id ?? []),
                'authorized_passport'   => formatFilePathsWithFullUrl($model->authorized_passport ?? []),
            ];
        case 'last-will-and-testament':
            return [
                'testament_place'       => $model->testament_place,
                'nationality'           => $model->nationalityOption?->getTranslation('name', $lang) ?? NULL,
                'emirate_id'            => $model->emirate?->getTranslation('name', $lang) ?? NULL,
                'full_name'             => $model->full_name,
                'religion'              => $model->religionOption?->getTranslation('name', $lang) ?? NULL,
                'you_represent'         => $model->youRepresent?->getTranslation('name', $lang) ?? NULL,
                'about_case'            => $model->about_case,
                'eid'                   => formatFilePathsWithFullUrl($model->eid ?? []),
            ];
        case 'memo-writing':
            return [
                'applicant_type'        => $model->applicant_type,
                'litigation_type'       => $model->litigation_type,
                'emirate_id'            => $model->emirate?->getTranslation('name', $lang) ?? NULL,
                'case_type'             => $model->caseType?->getTranslation('title', $lang) ?? NULL,
                'you_represent'         => $model->youRepresent?->getTranslation('name', $lang) ?? NULL,
                'full_name'             => $model->full_name,
                'about_case'            => $model->about_case,
                'documents'             => formatFilePathsWithFullUrl($model->document ?? []),
                'eid'                   => formatFilePathsWithFullUrl($model->eid ?? []),
                'trade_license'         => formatFilePathsWithFullUrl($model->trade_license ?? []),
            ];
        case 'expert-report':
            return [
                'applicant_type'            => $model->applicant_type,
                'applicant_place'           => $model->applicant_place,
                'emirate_id'                => $model->emirate?->getTranslation('name', $lang) ?? NULL,
                'expert_report_type'        => $model->expertReportType?->getTranslation('name', $lang) ?? NULL,
                'expert_report_language'    => $model->expertReportLanguage?->getTranslation('name', $lang) ?? NULL,
                'about_case'                => $model->about_case,
                'documents'                 => formatFilePathsWithFullUrl($model->documents ?? []),
                'eid'                       => formatFilePathsWithFullUrl($model->eid ?? []),
                'trade_license'             => formatFilePathsWithFullUrl($model->trade_license ?? []),
            ];
        case 'contract-drafting':
            return [
                'applicant_type'        => $model->applicant_type,
                'emirate_id'            => $model->emirate?->getTranslation('name', $lang) ?? NULL,
                'contract_type'         => $model->contractType?->getTranslation('name', $lang) ?? NULL,
                'sub_contract_type'     => $model->subContractType?->getTranslation('name', $lang) ?? NULL,
                'contract_language'     => $model->contractLanguage?->getTranslation('name', $lang) ?? NULL,
                'company_name'          => $model->company_name,
                'industry'              => $model->industryOption?->getTranslation('name', $lang) ?? NULL,
                'email'                 => $model->email,
                'priority'              => $model->priority,
                'documents'             => formatFilePathsWithFullUrl($model->documents ?? []),
                'eid'                   => formatFilePathsWithFullUrl($model->eid ?? []),
                'trade_license'         => formatFilePathsWithFullUrl($model->trade_license ?? []),
            ];
        case 'company-setup':
            return [
                'applicant_type'        => $model->applicant_type,
                'emirate_id'            => $model->emirate?->getTranslation('name', $lang) ?? NULL,
                'zone'                  => $model->zoneOption?->getTranslation('name', $lang) ?? NULL,
                'license_type'          => $model->licenseType?->getTranslation('name', $lang) ?? NULL,
                'license_activity'      => $model->licenseActivity?->getTranslation('name', $lang) ?? NULL,
                'company_type'          => $model->companyType?->getTranslation('name', $lang) ?? NULL,
                'industry'              => $model->industryOption?->getTranslation('name', $lang) ?? NULL,
                'company_name'          => $model->company_name,
                'mobile'                => $model->mobile,
                'email'                 => $model->email,
                'documents'             => formatFilePathsWithFullUrl($model->documents ?? []),
            ];
        case 'escrow-accounts':
            return [
                'applicant_type'        => $model->applicant_type,
                'company_name'          => $model->company_name,
                'company_activity'      => $model->companyActivity?->getTranslation('name', $lang) ?? NULL,
                'company_origin'        => $model->companyOrigin?->getTranslation('name', $lang) ?? NULL,
                'amount'                => $model->amount,
                'about_deal'            => $model->about_deal
            ];
        case 'debts-collection':
            return [
                'applicant_type'        => $model->applicant_type,
                'emirate_id'            => $model->emirate?->getTranslation('name', $lang) ?? NULL,
                'debt_type'             => $model->debtType?->getTranslation('name', $lang) ?? NULL,
                'debt_amount'           => $model->debt_amount,
                'debt_category'         => $model->debtCategory?->getTranslation('name', $lang) ?? NULL,
                'documents'             => formatFilePathsWithFullUrl($model->documents ?? []),
                'eid'                   => formatFilePathsWithFullUrl($model->eid ?? []),
                'trade_license'         => formatFilePathsWithFullUrl($model->trade_license ?? []),
            ];
        default:
            return $model->toArray(); // fallback
    }
}

function getAccessToken()
{
    $client = new Client([
        'base_uri' => config('services.ngenius.base_url'),
        'headers' => [
            'Authorization' => 'Basic ' . config('services.ngenius.api_key'),
            'Accept' => 'application/vnd.ni-identity.v1+json',
        ],
    ]);

    try {
        $response = $client->request('POST', '/identity/auth/access-token');

        $data = json_decode($response->getBody(), true);
        return $data['access_token'] ?? null;
    } catch (\Exception $e) {
        \Log::error('N-Genius token request failed', [
            'error' => $e->getMessage(),
        ]);
        return null;
    }
}

function createOrder($customer, float $amount, string $currency = 'AED', ?string $orderReference = null)
{

    $accessToken = getAccessToken();
    if (!$accessToken) return null;

    $baseUrl = config('services.ngenius.base_url');
    $outletRef = config('services.ngenius.outlet_ref');

    $payload = [
        'action' => 'SALE',
        'amount' => [
            'currencyCode' => $currency,
            'value' => intval($amount * 100), // AED 10.00 => 1000
        ],
        'merchantOrderReference' => $orderReference,
        'redirectUrl' => route('payment.callback'),
        'cancelUrl' => route('payment.cancel'),
        'emailAddress' => $customer['email']
    ];

    //  'merchantDetails' => [
    //         'email' => $customer['email'],
    //         'name' => $customer['name'],
    //         'mobile' => $customer['phone'],
    //     ],
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $accessToken,
        'Accept' => 'application/vnd.ni-payment.v2+json',
        'Content-Type' => 'application/vnd.ni-payment.v2+json',
    ])->post("{$baseUrl}/transactions/outlets/{$outletRef}/orders", $payload);

    if (!$response->successful()) {
        Log::error('N-Genius: Order create failed', ['response' => $response->body()]);
        return null;
    }
    // $details = json_decode($response->getBody(), true);

    return $response->json(); // returns _id, reference, _links etc.
}

function checkOrderStatus(string $orderId)
{
    $accessToken = getAccessToken();
    if (!$accessToken) return null;

    $baseUrl = config('services.ngenius.base_url');
    $outletRef = config('services.ngenius.outlet_ref');

    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $accessToken,
    ])->get("{$baseUrl}/transactions/outlets/{$outletRef}/orders/{$orderId}");

    if (!$response->successful()) {
        Log::error('N-Genius: Status check failed', ['order_id' => $orderId, 'response' => $response->body()]);
        return null;
    }

    return $response->json(); // contains state, _embedded.payment[0].paymentReference
}

function getUnreadNotifications()
{
    if (!Auth::check()) {
        return collect(); // return empty collection if not logged in
    }

    return Auth::user()->unreadNotifications;
}

function createMobOrder($customer, float $amount, string $currency = 'AED', ?string $orderReference = null)
{

    $accessToken = getAccessToken();
    if (!$accessToken) return null;

    $baseUrl = config('services.ngenius.base_url');
    $outletRef = config('services.ngenius.outlet_ref');

    $payload = [
        'action' => 'PURCHASE',
        'amount' => [
            'currencyCode' => $currency,
            'value' => intval($amount * 100), // AED 10.00 => 1000
        ],
        'merchantOrderReference' => $orderReference,
        'merchantAttributes' => [
            'merchantOrderReference' => $orderReference,
            'redirectUrl' => route('payment.callback'),
            'cancelUrl'   => route('payment.cancel')
        ],
        'emailAddress' => $customer['email'],

    ];

    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $accessToken,
        'Accept' => 'application/vnd.ni-payment.v2+json',
        'Content-Type' => 'application/vnd.ni-payment.v2+json',
    ])->post("{$baseUrl}/transactions/outlets/{$outletRef}/orders", $payload);

    if (!$response->successful()) {
        Log::error('N-Genius: Order create failed', ['response' => $response->body()]);
        return null;
    }
    return $response->json(); // returns _id, reference, _links etc.
}

function createWebOrder($customer, float $amount, string $currency = 'AED', ?string $orderReference = null)
{

    $accessToken = getAccessToken();
    if (!$accessToken) return null;

    $baseUrl = config('services.ngenius.base_url');
    $outletRef = config('services.ngenius.outlet_ref');

    $payload = [
        'action' => 'PURCHASE',
        'amount' => [
            'currencyCode' => $currency,
            'value' => intval($amount * 100), // AED 10.00 => 1000
        ],
        'merchantOrderReference' => $orderReference,
        'merchantAttributes' => [
            'merchantOrderReference' => $orderReference,
            'redirectUrl' => route('successPayment'),
            'cancelUrl'   => route('cancelPayment')
        ],
        'emailAddress' => $customer['email'],

    ];

    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $accessToken,
        'Accept' => 'application/vnd.ni-payment.v2+json',
        'Content-Type' => 'application/vnd.ni-payment.v2+json',
    ])->post("{$baseUrl}/transactions/outlets/{$outletRef}/orders", $payload);

    if (!$response->successful()) {
        Log::error('N-Genius: Order create failed', ['response' => $response->body()]);
        return null;
    }
    return $response->json(); // returns _id, reference, _links etc.
}

function createConsultationWebOrder($customer, float $amount, string $currency = 'AED', ?string $orderReference = null)
{
    
    $accessToken = getAccessToken();
    if (!$accessToken) return null;

    $baseUrl = config('services.ngenius.base_url');
    $outletRef = config('services.ngenius.outlet_ref');

    $payload = [
        'action' => 'PURCHASE',
        'amount' => [
            'currencyCode' => $currency,
            'value' => intval($amount * 100), // AED 10.00 => 1000
        ],
        'merchantOrderReference' => $orderReference,
        'merchantAttributes' => [
            'merchantOrderReference' => $orderReference,
            'redirectUrl' => route('consultationSuccessPayment'),
            'cancelUrl'   => route('consultationCancelPayment')
        ],
        'emailAddress' => $customer['email'],
        
    ];

    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $accessToken,
        'Accept' => 'application/vnd.ni-payment.v2+json',
        'Content-Type' => 'application/vnd.ni-payment.v2+json',
    ])->post("{$baseUrl}/transactions/outlets/{$outletRef}/orders", $payload);

    if (!$response->successful()) {
        Log::error('N-Genius: Order create failed', ['response' => $response->body()]);
        return null;
    }
    return $response->json(); // returns _id, reference, _links etc.
}

function deleteRequestFolder(string $serviceSlug, int $requestId): void
{
    $folderPath = "uploads/{$serviceSlug}/{$requestId}";

    if (Storage::disk('public')->exists($folderPath)) {
        Storage::disk('public')->deleteDirectory($folderPath);
    }
}

function getUsersWithPermissions(array $permissions, string $guard = 'web')
{
    $users =  User::where(function ($query) use ($permissions, $guard) {
        $query->whereHas('permissions', function ($q) use ($permissions, $guard) {
            $q->whereIn('name', $permissions)->where('guard_name', $guard);
        })->orWhereHas('roles.permissions', function ($q) use ($permissions, $guard) {
            $q->whereIn('name', $permissions)->where('guard_name', $guard);
        });
    })->get();

    return $users;
}

function getUnreadNotificationCount()
{
    $user = Auth::guard('frontend')->user();

    $count = $user->unreadNotifications()->count();

    return $count;
}

function getActiveAd($slug = null, $device = null)
{
    $page = \App\Models\AdsPage::where('slug', $slug)->first();

    if (!$page) return null;

    return $page->ads()
        ->where('status', 1)
        ->whereDate('start_date', '<=', now())
        ->whereDate('end_date', '>=', now())
        ->with(['files' => function ($query) use ($device) {
            $query->where('device', $device)->orderBy('id', 'asc')->limit(1);
        }])
        ->latest('start_date')
        ->first();
}

function determineFileType($extension)
{
    $extension = strtolower($extension);
    if (in_array($extension, ['mp4', 'mov', 'avi'])) {
        return 'video';
    } elseif ($extension === 'gif') {
        return 'gif';
    } else {
        return 'image';
    }
}

function createWebPlanOrder($customer, float $amount, string $currency = 'AED', ?string $orderReference = null)
{

    $accessToken = getAccessToken();
    if (!$accessToken) return null;

    $baseUrl = config('services.ngenius.base_url');
    $outletRef = config('services.ngenius.outlet_ref');

    $payload = [
        'action' => 'PURCHASE',
        'amount' => [
            'currencyCode' => $currency,
            'value' => intval($amount * 100), // AED 10.00 => 1000
        ],
        'merchantOrderReference' => $orderReference,
        'merchantAttributes' => [
            'merchantOrderReference' => $orderReference,
            'redirectUrl' => route('purchase-success'),
            'cancelUrl'   => route('purchase-cancel')
        ],
        'emailAddress' => $customer['email'],

    ];

    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $accessToken,
        'Accept' => 'application/vnd.ni-payment.v2+json',
        'Content-Type' => 'application/vnd.ni-payment.v2+json',
    ])->post("{$baseUrl}/transactions/outlets/{$outletRef}/orders", $payload);

    if (!$response->successful()) {
        Log::error('N-Genius: Order create failed', ['response' => $response->body()]);
        return null;
    }
    return $response->json(); // returns _id, reference, _links etc.
}


function assignLawyer($consultation, $lawyerId)
{
    ConsultationAssignment::create([
        'consultation_id' => $consultation->id,
        'lawyer_id' => $lawyerId,
        'status' => 'assigned'
    ]);

    $consultation->lawyer_id = $lawyerId;
    $consultation->save();

    // Notify lawyer via notification system (placeholder)
    // Notification::send($lawyer, new ConsultationRequest($consultation));
}


function findBestFitLawyer($consultation)
{
    $languages   = (array) $consultation->language;
    $caseType    = $consultation->case_type;
    // $emirateId   = $consultation->emirate_id;

    // Already rejected lawyers
    $lawyerIdsAlreadyRejected = ConsultationAssignment::where('consultation_id', $consultation->id)
        ->where('status', 'rejected')
        ->pluck('lawyer_id');

    $countLanguages = count($languages);

    $lawyerId =  DB::table('lawyers as l')
        ->join('users as u', 'u.id', '=', 'l.user_id')
        ->join('lawyer_dropdown_options as ld_speciality', function ($join) use ($caseType) {
            $join->on('ld_speciality.lawyer_id', '=', 'l.id')
                ->where('ld_speciality.type', 'specialities')
                ->where('ld_speciality.dropdown_option_id', $caseType);
        })

        ->join('lawyer_dropdown_options as ld_lang', function ($join) use ($languages) {
            $join->on('ld_lang.lawyer_id', '=', 'l.id')
                ->where('ld_lang.type', 'languages')
                ->whereIn('ld_lang.dropdown_option_id', $languages);
        })

        // ->when($emirateId, function ($q) use ($emirateId) {
        //     $q->where('l.emirate_id', $emirateId);
        // })

        ->where('u.is_online', 1)
        ->where('l.is_busy', 0)
        ->whereNotIn('l.id', $lawyerIdsAlreadyRejected)
        ->groupBy('l.id')
        ->havingRaw('COUNT(DISTINCT ld_lang.dropdown_option_id) = ?', [$countLanguages])
        ->pluck('l.id')
        ->first();

    $lawyer = $lawyerId ? \App\Models\Lawyer::find($lawyerId) : null;

    return $lawyer;
}

function getFormattedTimeline(ServiceRequest $serviceRequest, ?array $labels = null): array
{
    $ordered = [
        'pending' => 'Pending',
        'under_review' => 'Under Review',
        'ongoing' => 'Ongoing',
        'completed' => 'Completed',
        'rejected' => 'Rejected',
    ];

    if (is_array($labels)) {
        foreach ($labels as $k => $v) {
            if (array_key_exists($k, $ordered)) {
                $ordered[$k] = $v;
            }
        }
    }

    $latestByStatus = [];
    foreach ($serviceRequest->statusHistories as $h) {
        $key = is_string($h->status) ? $h->status : ($h->status->value ?? (string)$h->status);
        if (!isset($latestByStatus[$key])) {
            $latestByStatus[$key] = $h;
        }
    }

    $timeline = [];
    foreach ($ordered as $key => $label) {
        $row = $latestByStatus[$key] ?? null;
        $timeline[] = [
            'key' => $key,
            'label' => $label,
            'completed' => (bool) $row,
            'date' => $row?->created_at?->format('M d, Y'),
            'note' => $row?->note,
            'changed_by' => $row?->modifiedBy?->name,
        ];
    }

    return $timeline;
}


function getFullStatusHistory(ServiceRequest $serviceRequest): array
{
    $histories = $serviceRequest->statusHistories
        ->sortBy('created_at')
        ->values();

    return $histories->map(function ($h) {
        $key = is_string($h->status)
            ? $h->status
            : ($h->status->value ?? (string) $h->status);

        $label = $h->label
            ?? ucfirst(str_replace('_', ' ', (string) $key));

        return [
            ...$h->toArray(),
            'key'        => $key,
            'label'      => $label,
            'date'       => $h?->created_at?->format('M d, Y H:i'),
            'note'       => $h?->note,
            'changed_by' => $h?->modifiedBy?->name,
            'id'         => $h?->id,
            'raw_date'   => $h?->created_at,
        ];
        
    })->all();
}

 function reserveLawyer($lawyerId, $consultationId)
    {
        $lawyer = \App\Models\Lawyer::find($lawyerId);
        if ($lawyer) {
            $lawyer->update(['is_busy' => 1]);

            $consultation = \App\Models\Consultation::find($consultationId);
            if ($consultation) {
                $consultation->update(['lawyer_id' => $lawyerId, 'status' => 'reserved']);
            }
        }
    }

    function unreserveLawyer($lawyerId)
    {
        if ($lawyerId) {
            $lawyer = \App\Models\Lawyer::find($lawyerId);
            if ($lawyer) {
                $lawyer->update(['is_busy' => 0]);
            }
        }
    }


    function isVendorCanCreateLawyers()
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        $vendor = Vendor::with('currentSubscription')
            ->where('user_id', $user->id)
            ->first();

        if (!$vendor) {
            return false;
        }

        $subscription = $vendor->currentSubscription;

        if (!$subscription || !$subscription->member_count) {
            return false;
        }

        if ($subscription->subscription_end && now()->gt($subscription->subscription_end)) {
            return false;
        }

        $lawyerCount = Lawyer::where('lawfirm_id', $vendor->id)->count();

        return $lawyerCount < $subscription->member_count;
    }

    function isVendorCanCreateJobs()
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        $vendor = Vendor::with('currentSubscription')
            ->where('user_id', $user->id)
            ->first();

        if (!$vendor) {
            return false;
        }

        $subscription = $vendor->currentSubscription;

        if (!$subscription || !$subscription->job_post_count) {
            return false;
        }

        if ($subscription->subscription_end && now()->gt($subscription->subscription_end)) {
            return false;
        }

        $jobCount = JobPost::where('user_id', $vendor->id)->count();

        return $jobCount < $subscription->job_post_count;
    }