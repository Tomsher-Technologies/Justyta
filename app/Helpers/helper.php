<?php

use App\Models\BusinessSetting;
use App\Utility\CategoryUtility;
use App\Models\EnquiryStatus;
use App\Models\Service;
use App\Models\Page;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

function uploadImage($type, $imageUrl, $filename = null){
    $data_url = '';
    $ext = $imageUrl->getClientOriginalExtension();
    
    $path = $type.'/';
    
    $filename = $path . $filename.'_'.time().'_'.rand(10, 9999) . '.' . $ext;

    $imageContents = file_get_contents($imageUrl);

    // Save the original image in the storage folder
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

function getServiceId($slug){
    $service =  Service::where('slug', $slug)->pluck('id');

    return $service[0] ?? NULL;
}

function getPageDynamicContent($slug,$lang = 'en'){
    $data = Page::with('translations')->where('slug', $slug)->first();

    $response =  [
                    'title' => $data?->getTranslation('title',$lang),
                    'description' => $data?->getTranslation('description',$lang),
                    'content' => $data?->getTranslation('content',$lang),
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

function serviceModelFieldsMap(){
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
                'lawfirm'           => 'Lawfirm',
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
        case 'request-submission' :
            return [
                'applicant_type'        => $model->applicant_type,
                'litigation_type'       => $model->litigation_type,
                'litigation_place'      => $model->litigation_place,
                'emirate_id'            => $model->emirate?->getTranslation('name',$lang) ?? NULL,
                'case_type'             => $model->caseType?->getTranslation('name',$lang) ?? NULL,
                'request_type'          => $model->requestType?->getTranslation('name',$lang) ?? NULL,
                'request_title'         => $model->requestTitle?->getTranslation('name',$lang) ?? NULL,
                'case_number'           => $model->case_number,
                'memo'                  => formatFilePathsWithFullUrl($model->memo ?? []),
                'documents'             => formatFilePathsWithFullUrl($model->documents ?? []),
                'eid'                   => formatFilePathsWithFullUrl($model->eid ?? []),
                'trade_license'         => formatFilePathsWithFullUrl($model->trade_license ?? []),
            ];
        case 'legal-translation' :
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
                'eid'                   => formatFilePathsWithFullUrl($model->eid ?? []),
                'trade_license'         => formatFilePathsWithFullUrl($model->trade_license ?? []),
            ];
        case 'annual-retainer-agreement' :
            return [
                'company_name'          => $model->company_name,
                'emirate_id'            => $model->emirate?->getTranslation('name',$lang) ?? NULL,
                'license_type'          => $model->licenseType?->getTranslation('name',$lang) ?? NULL,
                'license_activity'      => $model->licenseActivity?->getTranslation('name',$lang) ?? NULL,
                'industry'              => $model->industryOption?->getTranslation('name',$lang) ?? NULL,
                'no_of_employees'       => $model->noOfEmployees?->getTranslation('name',$lang) ?? NULL,
                'case_type'             => $model->case_type_names,
                'no_of_calls'           => $model->no_of_calls,
                'no_of_visits'          => $model->no_of_visits,
                'no_of_installment'     => $model->no_of_installment,
                'lawfirm'               => $model->lawFirm?->getTranslation('law_firm_name',$lang) ?? NULL,
            ];
        case 'immigration-requests' :
            return [
                'preferred_country'     => $model->preferredCountry?->getTranslation('name',$lang) ?? NULL,
                'position'              => $model->currentPosition?->getTranslation('name',$lang) ?? NULL,
                'age'                   => $model->age,
                'nationality'           => $model->nationalityOption?->getTranslation('name',$lang) ?? NULL,
                'years_of_experience'   => $model->years_of_experience,
                'address'               => $model->address,
                'residency_status'      => $model->residencyStatus?->getTranslation('name',$lang) ?? NULL,
                'current_salary'        => $model->current_salary,
                'application_type'      => $model->applicationType?->getTranslation('name',$lang) ?? NULL,
                'cv'                    => formatFilePathsWithFullUrl($model->cv ?? []),
                'certificates'          => formatFilePathsWithFullUrl($model->certificates ?? []),
                'passport'              => formatFilePathsWithFullUrl($model->passport ?? []),
                'photo'                 => formatFilePathsWithFullUrl($model->photo ?? []),
                'account_statement'     => formatFilePathsWithFullUrl($model->account_statement ?? []),
            ];
        case 'court-case-submission' :
            return [
                'applicant_type'        => $model->applicant_type,
                'litigation_type'       => $model->litigation_type,
                'emirate_id'            => $model->emirate?->getTranslation('name',$lang) ?? NULL,
                'case_type'             => $model->caseType?->getTranslation('name',$lang) ?? NULL,
                'you_represent'         => $model->youRepresent?->getTranslation('name',$lang) ?? NULL,
                'about_case'            => $model->about_case,
                'memo'                  => formatFilePathsWithFullUrl($model->memo ?? []),
                'documents'             => formatFilePathsWithFullUrl($model->documents ?? []),
                'eid'                   => formatFilePathsWithFullUrl($model->eid ?? []),
                'trade_license'         => formatFilePathsWithFullUrl($model->trade_license ?? []),
            ];
        case 'criminal-complaint' :
            return [
                'applicant_type'        => $model->applicant_type,
                'litigation_type'       => $model->litigation_type,
                'emirate_id'            => $model->emirate?->getTranslation('name',$lang) ?? NULL,
                'case_type'             => $model->caseType?->getTranslation('name',$lang) ?? NULL,
                'you_represent'         => $model->youRepresent?->getTranslation('name',$lang) ?? NULL,
                'about_case'            => $model->about_case,
                'memo'                  => formatFilePathsWithFullUrl($model->memo ?? []),
                'documents'             => formatFilePathsWithFullUrl($model->documents ?? []),
                'eid'                   => formatFilePathsWithFullUrl($model->eid ?? []),
                'trade_license'         => formatFilePathsWithFullUrl($model->trade_license ?? []),
            ];
        case 'power-of-attorney' :
            return [
                'applicant_type'        => $model->applicant_type, 
                'appointer_name'        => $model->appointer_name, 
                'id_number'             => $model->id_number, 
                'appointer_mobile'      => $model->appointer_mobile, 
                'emirate_id'            => $model->emirate?->getTranslation('name',$lang) ?? NULL,
                'poa_type'              => $model->powerOfAttorneyType?->getTranslation('name',$lang) ?? NULL,
                'name_of_authorized'    => $model->name_of_authorized, 
                'authorized_mobile'     => $model->authorized_mobile, 
                'id_number_authorized'  => $model->id_number_authorized, 
                'authorized_address'    => $model->authorized_address, 
                'relationship'          => $model->relationshipOption?->getTranslation('name',$lang) ?? NULL,
                'appointer_id'          => formatFilePathsWithFullUrl($model->appointer_id ?? []),
                'authorized_id'         => formatFilePathsWithFullUrl($model->authorized_id ?? []),
                'authorized_passport'   => formatFilePathsWithFullUrl($model->authorized_passport ?? []),
            ];
        case 'last-will-and-testament' :
            return [
                'testament_place'       => $model->testament_place,
                'nationality'           => $model->nationalityOption?->getTranslation('name',$lang) ?? NULL,
                'emirate_id'            => $model->emirate?->getTranslation('name',$lang) ?? NULL,
                'religion'              => $model->religionOption?->getTranslation('name',$lang) ?? NULL,
                'you_represent'         => $model->youRepresent?->getTranslation('name',$lang) ?? NULL,
                'about_case'            => $model->about_case,
                'eid'                   => formatFilePathsWithFullUrl($model->eid ?? []),
            ];
        case 'memo-writing' :
            return [
                'applicant_type'        => $model->applicant_type,
                'litigation_type'       => $model->litigation_type,
                'emirate_id'            => $model->emirate?->getTranslation('name',$lang) ?? NULL,
                'case_type'             => $model->caseType?->getTranslation('name',$lang) ?? NULL,
                'you_represent'         => $model->youRepresent?->getTranslation('name',$lang) ?? NULL,
                'full_name'             => $model->full_name,
                'about_case'            => $model->about_case,
                'documents'             => formatFilePathsWithFullUrl($model->documents ?? []),
                'eid'                   => formatFilePathsWithFullUrl($model->eid ?? []),
                'trade_license'         => formatFilePathsWithFullUrl($model->trade_license ?? []),
            ];
        case 'expert-report' :
            return [
                'applicant_type'            => $model->applicant_type,
                'applicant_place'           => $model->applicant_place,
                'emirate_id'                => $model->emirate?->getTranslation('name',$lang) ?? NULL,
                'expert_report_type'        => $model->expertReportType?->getTranslation('name',$lang) ?? NULL,
                'expert_report_language'    => $model->expertReportLanguage?->getTranslation('name',$lang) ?? NULL,
                'about_case'                => $model->about_case,
                'documents'                 => formatFilePathsWithFullUrl($model->documents ?? []),
                'eid'                       => formatFilePathsWithFullUrl($model->eid ?? []),
                'trade_license'             => formatFilePathsWithFullUrl($model->trade_license ?? []),
            ];
        case 'contract-drafting':
            return [
                'applicant_type'        => $model->applicant_type,
                'contract_type'         => $model->contractType?->getTranslation('name',$lang) ?? NULL,
                'emirate_id'            => $model->emirate?->getTranslation('name',$lang) ?? NULL,
                'sub_contract_type'     => $model->subContractType?->getTranslation('name',$lang) ?? NULL,
                'contract_language'     => $model->contractLanguage?->getTranslation('name',$lang) ?? NULL,
                'company_name'          => $model->company_name,
                'industry'              => $model->industryOption?->getTranslation('name',$lang) ?? NULL,
                'email'                 => $model->email,
                'priority'              => $model->priority,
                'documents'             => formatFilePathsWithFullUrl($model->documents ?? []),
                'eid'                   => formatFilePathsWithFullUrl($model->eid ?? []),
                'trade_license'         => formatFilePathsWithFullUrl($model->trade_license ?? []),
            ];
        case 'company-setup':
            return [
                'applicant_type'        => $model->applicant_type,
                'emirate_id'            => $model->emirate?->getTranslation('name',$lang) ?? NULL,
                'zone'                  => $model->zoneOption?->getTranslation('name',$lang) ?? NULL,
                'license_type'          => $model->licenseType?->getTranslation('name',$lang) ?? NULL,
                'license_activity'      => $model->licenseActivity?->getTranslation('name',$lang) ?? NULL,
                'company_type'          => $model->companyType?->getTranslation('name',$lang) ?? NULL,
                'industry'              => $model->industryOption?->getTranslation('name',$lang) ?? NULL,
                'company_name'          => $model->company_name,
                'mobile'                => $model->mobile,
                'email'                 => $model->email,
                'documents'             => formatFilePathsWithFullUrl($model->documents ?? []),
            ];
        case 'escrow-accounts':
            return [
                'applicant_type'        => $model->applicant_type,
                'company_name'          => $model->company_name,
                'company_activity'      => $model->companyActivity?->getTranslation('name',$lang) ?? NULL,
                'company_origin'        => $model->companyOrigin?->getTranslation('name',$lang) ?? NULL,
                'amount'                => $model->amount,
                'about_deal'            => $model->about_deal
            ];
        case 'debts-collection':
            return [
                'applicant_type'        => $model->applicant_type,
                'emirate_id'            => $model->emirate?->getTranslation('name',$lang) ?? NULL,
                'debt_type'             => $model->debtType?->getTranslation('name',$lang) ?? NULL,
                'debt_amount'           => $model->debt_amount,
                'debt_category'         => $model->debtCategory?->getTranslation('name',$lang) ?? NULL,
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

// function ngenius_create_payment($amount, $orderId)
// {
//     $token = getAccessToken();

//     $baseUrl = config('services.ngenius.base_url');
//     $outletRef = config('services.ngenius.outlet_ref');
   
//     $response = Http::withToken($token)
//         ->withHeaders(['Content-Type' => 'application/vnd.ni-payment.v2+json'])
//         ->post("{$baseUrl}/transactions/outlets/{$outletRef}/orders", [
//             "action" => "PURCHASE",
//             "amount" => [
//                 "currencyCode" => "AED",
//                 "value" => $amount * 100 // in fils
//             ],
//             "merchantAttributes" => [
//                 "redirectUrl" => route('user.ngenius.callback', ['order_id' => $orderId])
//             ]
//         ]);

//     return $response->json();
// }

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
    // $details = json_decode($response->getBody(), true);

    // echo '<pre>';
    // print_r($details);
    // die;
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