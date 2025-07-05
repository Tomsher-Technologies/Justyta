<?php

use App\Models\BusinessSetting;
use App\Utility\CategoryUtility;
use App\Models\EnquiryStatus;
use App\Models\Service;
use App\Models\Page;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

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
                    'title' => $data->getTranslation('title',$lang),
                    'description' => $data->getTranslation('description',$lang),
                    'content' => $data->getTranslation('content',$lang),
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
        'depts-collection'          => 'debtCollection',
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
                'emirate_id'            => $model->emirate->getTranslation('name',$lang) ?? NULL,
                'case_type'             => $model->caseType->getTranslation('name',$lang) ?? NULL,
                'request_type'          => $model->requestType->getTranslation('name',$lang) ?? NULL,
                'request_title'         => $model->requestTitle->getTranslation('name',$lang) ?? NULL,
                'case_number'           => $model->case_number,
                'memo'                  => formatFilePathsWithFullUrl($model->memo ?? []),
                'documents'             => formatFilePathsWithFullUrl($model->documents ?? []),
                'eid'                   => formatFilePathsWithFullUrl($model->eid ?? []),
                'trade_license'         => formatFilePathsWithFullUrl($model->trade_license ?? []),
            ];
        case 'legal-translation' :
            return [
                'priority_level'        => $model->priority_level,
                'document_language'     => $model->documentLanguage->getTranslation('name', $lang) ?? NULL,
                'translation_language'  => $model->translationLanguage->getTranslation('name', $lang) ?? NULL,
                'document_type'         => $model->documentType->getTranslation('name', $lang) ?? NULL,
                'document_sub_type'     => $model->documentSubType->getTranslation('name', $lang) ?? NULL,
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
                'emirate_id'            => $model->emirate->getTranslation('name',$lang) ?? NULL,
                'license_type'          => $model->licenseType->getTranslation('name',$lang) ?? NULL,
                'license_activity'      => $model->licenseActivity->getTranslation('name',$lang) ?? NULL,
                'industry'              => $model->industryOption->getTranslation('name',$lang) ?? NULL,
                'no_of_employees'       => $model->noOfEmployees->getTranslation('name',$lang) ?? NULL,
                'case_type'             => $model->case_type_names,
                'no_of_calls'           => $model->no_of_calls,
                'no_of_visits'          => $model->no_of_visits,
                'no_of_installment'     => $model->no_of_installment,
                'lawfirm'               => $model->lawFirm->getTranslation('law_firm_name',$lang) ?? NULL,
            ];
        case 'immigration-requests' :
            return [
                'preferred_country'     => $model->preferredCountry->getTranslation('name',$lang) ?? NULL,
                'position'              => $model->currentPosition->getTranslation('name',$lang) ?? NULL,
                'age'                   => $model->age,
                'nationality'           => $model->nationalityOption->getTranslation('name',$lang) ?? NULL,
                'years_of_experience'   => $model->years_of_experience,
                'address'               => $model->address,
                'residency_status'      => $model->residencyStatus->getTranslation('name',$lang) ?? NULL,
                'current_salary'        => $model->current_salary,
                'application_type'      => $model->applicationType->getTranslation('name',$lang) ?? NULL,
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
                'emirate_id'            => $model->emirate->getTranslation('name',$lang) ?? NULL,
                'case_type'             => $model->caseType->getTranslation('name',$lang) ?? NULL,
                'you_represent'         => $model->youRepresent->getTranslation('name',$lang) ?? NULL,
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
                'emirate_id'            => $model->emirate->getTranslation('name',$lang) ?? NULL,
                'case_type'             => $model->caseType->getTranslation('name',$lang) ?? NULL,
                'you_represent'         => $model->youRepresent->getTranslation('name',$lang) ?? NULL,
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
                'emirate_id'            => $model->emirate->getTranslation('name',$lang) ?? NULL,
                'poa_type'              => $model->powerOfAttorneyType->getTranslation('name',$lang) ?? NULL,
                'name_of_authorized'    => $model->name_of_authorized, 
                'authorized_mobile'     => $model->authorized_mobile, 
                'id_number_authorized'  => $model->id_number_authorized, 
                'authorized_address'    => $model->authorized_address, 
                'relationship'          => $model->relationshipOption->getTranslation('name',$lang) ?? NULL,
                'appointer_id'          => formatFilePathsWithFullUrl($model->appointer_id ?? []),
                'authorized_id'         => formatFilePathsWithFullUrl($model->authorized_id ?? []),
                'authorized_passport'   => formatFilePathsWithFullUrl($model->authorized_passport ?? []),
            ];
        case 'last-will-and-testament' :
            return [
                'testament_place'       => $model->testament_place,
                'nationality'           => $model->nationalityOption->getTranslation('name',$lang) ?? NULL,
                'emirate_id'            => $model->emirate->getTranslation('name',$lang) ?? NULL,
                'religion'              => $model->religionOption->getTranslation('name',$lang) ?? NULL,
                'you_represent'         => $model->youRepresent->getTranslation('name',$lang) ?? NULL,
                'about_case'            => $model->about_case,
                'eid'                   => formatFilePathsWithFullUrl($model->eid ?? []),
            ];
        case 'memo-writing' :
            return [
                'applicant_type'        => $model->applicant_type,
                'litigation_type'       => $model->litigation_type,
                'emirate_id'            => $model->emirate->getTranslation('name',$lang) ?? NULL,
                'case_type'             => $model->caseType->getTranslation('name',$lang) ?? NULL,
                'you_represent'         => $model->youRepresent->getTranslation('name',$lang) ?? NULL,
                'full_name'             => $model->full_name,
                'about_case'            => $model->about_case,
                'memo'                  => formatFilePathsWithFullUrl($model->memo ?? []),
                'documents'             => formatFilePathsWithFullUrl($model->documents ?? []),
                'eid'                   => formatFilePathsWithFullUrl($model->eid ?? []),
                'trade_license'         => formatFilePathsWithFullUrl($model->trade_license ?? []),
            ];
        case 'expert-report' :
            return [
                'applicant_type'            => $model->applicant_type,
                'applicant_place'           => $model->applicant_place,
                'emirate_id'                => $model->emirate->getTranslation('name',$lang) ?? NULL,
                'expert_report_type'        => $model->expertReportType->getTranslation('name',$lang) ?? NULL,
                'expert_report_language'    => $model->expertReportLanguage->getTranslation('name',$lang) ?? NULL,
                'about_case'                => $model->about_case,
                'documents'                 => formatFilePathsWithFullUrl($model->documents ?? []),
                'eid'                       => formatFilePathsWithFullUrl($model->eid ?? []),
                'trade_license'             => formatFilePathsWithFullUrl($model->trade_license ?? []),
            ];
        case 'contract-drafting':
            return [
                'applicant_type'        => $model->applicant_type,
                'contract_type'         => $model->contractType->getTranslation('name',$lang) ?? NULL,
                'emirate_id'            => $model->emirate->getTranslation('name',$lang) ?? NULL,
                'sub_contract_type'     => $model->subContractType->getTranslation('name',$lang) ?? NULL,
                'contract_language'     => $model->contractLanguage->getTranslation('name',$lang) ?? NULL,
                'company_name'          => $model->company_name,
                'industry'              => $model->industryOption->getTranslation('name',$lang) ?? NULL,
                'email'                 => $model->email,
                'priority'              => $model->priority,
                'documents'             => formatFilePathsWithFullUrl($model->documents ?? []),
                'eid'                   => formatFilePathsWithFullUrl($model->eid ?? []),
                'trade_license'         => formatFilePathsWithFullUrl($model->trade_license ?? []),
            ];
        case 'company-setup':
            return [
                'applicant_type'        => $model->applicant_type,
                'emirate_id'            => $model->emirate->getTranslation('name',$lang) ?? NULL,
                'zone'                  => $model->zoneOption->getTranslation('name',$lang) ?? NULL,
                'license_type'          => $model->licenseType->getTranslation('name',$lang) ?? NULL,
                'license_activity'      => $model->licenseActivity->getTranslation('name',$lang) ?? NULL,
                'company_type'          => $model->companyType->getTranslation('name',$lang) ?? NULL,
                'industry'              => $model->industryOption->getTranslation('name',$lang) ?? NULL,
                'company_name'          => $model->company_name,
                'mobile'                => $model->mobile,
                'email'                 => $model->email,
                'documents'             => formatFilePathsWithFullUrl($model->documents ?? []),
            ];
        case 'escrow-accounts':
            return [
                'applicant_type'        => $model->applicant_type,
                'company_name'          => $model->company_name,
                'company_activity'      => $model->companyActivity->getTranslation('name',$lang) ?? NULL,
                'company_origin'        => $model->companyOrigin->getTranslation('name',$lang) ?? NULL,
                'amount'                => $model->amount,
                'about_deal'            => $model->about_deal
            ];
        case 'depts-collection':
            return [
                'applicant_type'        => $model->applicant_type,
                'emirate_id'            => $model->emirate->getTranslation('name',$lang) ?? NULL,
                'debt_type'             => $model->debtType->getTranslation('name',$lang) ?? NULL,
                'debt_amount'           => $model->debt_amount,
                'debt_category'         => $model->debtCategory->getTranslation('name',$lang) ?? NULL,
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