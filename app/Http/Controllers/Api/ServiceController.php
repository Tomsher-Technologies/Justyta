<?php

namespace App\Http\Controllers\Api;

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
use App\Notifications\ServiceRequestSubmitted;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class ServiceController extends Controller
{
    public function getCourtCaseFormData(Request $request){
        $lang       = $request->header('lang') ?? env('APP_LOCALE','en'); // default to English 
        
        $dropdowns  = Dropdown::with([
                        'options' => function ($q) {
                            $q->where('status', 'active')->orderBy('sort_order');
                        },
                        'options.translations' => function ($q) use ($lang) {
                            $q->whereIn('language_code', [$lang, 'en']);
                        }
                    ])->whereIn('slug', ['case_type', 'you_represent'])->get()->keyBy('slug');
       
        // Transform each dropdown
        $response   = [];
        $emirates   = Emirate::where('status',1)->orderBy('id')->get();

        $response['emirates'] = $emirates->map(function ($emirate) use($lang) {
                return [
                    'id'    => $emirate->id,
                    'value' => $emirate->getTranslation('name',$lang),
                ];
        });

        foreach ($dropdowns as $slug => $dropdown) {
            $response[$slug] = $dropdown->options->map(function ($option) use ($lang){
                return [
                    'id'    => $option->id,
                    'value' => $option->getTranslation('name',$lang),
                ];
            });
        }
        $response['payment'] = [];
        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $response,
        ], 200);
    }

    public function getCriminalComplaintFormData(Request $request){
        $lang       = $request->header('lang') ?? env('APP_LOCALE','en'); // default to English 
        
        $dropdowns  = Dropdown::with([
                        'options' => function ($q) {
                            $q->where('status', 'active')->orderBy('sort_order');
                        },
                        'options.translations' => function ($q) use ($lang) {
                            $q->whereIn('language_code', [$lang, 'en']);
                        }
                    ])->whereIn('slug', ['case_type', 'you_represent'])->get()->keyBy('slug');
       
        // Transform each dropdown
        $response   = [];
        $emirates   = Emirate::where('status',1)->orderBy('id')->get();

        $response['emirates'] = $emirates->map(function ($emirate) use($lang) {
                return [
                    'id'    => $emirate->id,
                    'value' => $emirate->getTranslation('name',$lang),
                ];
        });

        foreach ($dropdowns as $slug => $dropdown) {
            $response[$slug] = $dropdown->options->map(function ($option) use ($lang){
                return [
                    'id'    => $option->id,
                    'value' => $option->getTranslation('name',$lang),
                ];
            });
        }
        $response['payment'] = [];
        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $response,
        ], 200);
    }

    public function getPowerOfAttorneyFormData(Request $request){
        $lang       = $request->header('lang') ?? env('APP_LOCALE','en'); // default to English 
        
        $dropdowns  = Dropdown::with([
                        'options' => function ($q) {
                            $q->where('status', 'active')->orderBy('sort_order');
                        },
                        'options.translations' => function ($q) use ($lang) {
                            $q->whereIn('language_code', [$lang, 'en']);
                        }
                    ])->whereIn('slug', ['poa_type', 'poa_relationships'])->get()->keyBy('slug');
       
        // Transform each dropdown
        $response   = [];
        $emirates   = Emirate::where('status',1)->orderBy('id')->get();

        $response['emirates'] = $emirates->map(function ($emirate) use($lang) {
                return [
                    'id'    => $emirate->id,
                    'value' => $emirate->getTranslation('name',$lang),
                ];
        });

        foreach ($dropdowns as $slug => $dropdown) {
            $response[$slug] = $dropdown->options->map(function ($option) use ($lang){
                return [
                    'id'    => $option->id,
                    'value' => $option->getTranslation('name',$lang),
                ];
            });
        }
        $response['payment'] = [];
        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $response,
        ], 200);
    }

    public function getLastWillFormData(Request $request){
        $lang       = $request->header('lang') ?? env('APP_LOCALE','en'); // default to English 
        
        $dropdowns  = Dropdown::with([
                        'options' => function ($q) {
                            $q->where('status', 'active')->orderBy('sort_order');
                        },
                        'options.translations' => function ($q) use ($lang) {
                            $q->whereIn('language_code', [$lang, 'en']);
                        }
                    ])->whereIn('slug', ['you_represent','religion'])->get()->keyBy('slug');
       
        // Transform each dropdown
        $response   = [];
        $emirates   = Emirate::where('status',1)->orderBy('id')->get();

        $response['emirates'] = $emirates->map(function ($emirate) use($lang) {
                return [
                    'id'    => $emirate->id,
                    'value' => $emirate->getTranslation('name',$lang),
                ];
        });

        foreach ($dropdowns as $slug => $dropdown) {
            $response[$slug] = $dropdown->options->map(function ($option) use ($lang){
                return [
                    'id'    => $option->id,
                    'value' => $option->getTranslation('name',$lang),
                ];
            });
        }

        $countries = Country::where('status',1)->orderBy('id')->get();

        $response['nationality'] = $countries->map(function ($country) use($lang) {
                return [
                    'id'    => $country->id,
                    'value' => $country->getTranslation('name',$lang),
                ];
        });
        $response['payment'] = [];
        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $response,
        ], 200);
    }

    public function getMemoWritingFormData(Request $request){
        $lang       = $request->header('lang') ?? env('APP_LOCALE','en'); // default to English 
        
        $dropdowns  = Dropdown::with([
                        'options' => function ($q) {
                            $q->where('status', 'active')->orderBy('sort_order');
                        },
                        'options.translations' => function ($q) use ($lang) {
                            $q->whereIn('language_code', [$lang, 'en']);
                        }
                    ])->whereIn('slug', ['case_type', 'you_represent'])->get()->keyBy('slug');
       
        // Transform each dropdown
        $response   = [];
        $emirates   = Emirate::where('status',1)->orderBy('id')->get();

        $response['emirates'] = $emirates->map(function ($emirate) use($lang) {
                return [
                    'id'    => $emirate->id,
                    'value' => $emirate->getTranslation('name',$lang),
                ];
        });

        foreach ($dropdowns as $slug => $dropdown) {
            $response[$slug] = $dropdown->options->map(function ($option) use ($lang){
                return [
                    'id'    => $option->id,
                    'value' => $option->getTranslation('name',$lang),
                ];
            });
        }
        $response['payment'] = [];
        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $response,
        ], 200);
    }

    public function getExpertReportsFormData(Request $request){
        $lang       = $request->header('lang') ?? env('APP_LOCALE','en'); // default to English 
        
        $dropdowns  = Dropdown::with([
                        'options' => function ($q) {
                            $q->where('status', 'active')->orderBy('sort_order');
                        },
                        'options.translations' => function ($q) use ($lang) {
                            $q->whereIn('language_code', [$lang, 'en']);
                        }
                    ])->whereIn('slug', ['expert_report_type', 'expert_report_languages'])->get()->keyBy('slug');
       
        // Transform each dropdown
        $response   = [];
        $emirates   = Emirate::where('status',1)->orderBy('id')->get();

        $response['emirates'] = $emirates->map(function ($emirate) use($lang) {
                return [
                    'id'    => $emirate->id,
                    'value' => $emirate->getTranslation('name',$lang),
                ];
        });

        foreach ($dropdowns as $slug => $dropdown) {
            $response[$slug] = $dropdown->options->map(function ($option) use ($lang){
                return [
                    'id'    => $option->id,
                    'value' => $option->getTranslation('name',$lang),
                ];
            });
        }

        $service    = Service::where('slug', 'expert-report')->firstOrFail();

        $response['payment'] = [
            'service_fee'       => $service->service_fee ?? 0,
            'govt_fee'          => $service->govt_fee ?? 0,
            'tax'               => $service->tax ?? 0,
            'total_amount'      => $service->total_amount ?? 0
        ];
        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $response,
        ], 200);
    }

    public function getContractsDraftingFormData(Request $request){
        $lang       = $request->header('lang') ?? env('APP_LOCALE','en'); // default to English 
        
        $dropdowns  = Dropdown::with([
                        'options' => function ($q) {
                            $q->where('status', 'active')->orderBy('sort_order');
                        },
                        'options.translations' => function ($q) use ($lang) {
                            $q->whereIn('language_code', [$lang, 'en']);
                        }
                    ])->whereIn('slug', ['contract_languages', 'industries'])->get()->keyBy('slug');
       
        // Transform each dropdown
        $response   = [];
        $emirates   = Emirate::where('status',1)->orderBy('id')->get();

        $response['emirates'] = $emirates->map(function ($emirate) use($lang) {
                return [
                    'id'    => $emirate->id,
                    'value' => $emirate->getTranslation('name',$lang),
                ];
        });

        $contractTypes = ContractType::where('status',1)->whereNull('parent_id')->orderBy('sort_order')->get();

        $response['contract_type'] = $contractTypes->map(function ($ctype) use($lang) {
                return [
                    'id'    => $ctype->id,
                    'value' => $ctype->getTranslation('name',$lang),
                ];
        });

        foreach ($dropdowns as $slug => $dropdown) {
            $response[$slug] = $dropdown->options->map(function ($option) use ($lang){
                return [
                    'id'    => $option->id,
                    'value' => $option->getTranslation('name',$lang),
                ];
            });
        }
        $response['payment'] = [];
        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $response,
        ], 200);
    }

    public function getEscrowAccountsFormData(Request $request){
        $lang       = $request->header('lang') ?? env('APP_LOCALE','en'); // default to English 
        
        $dropdowns  = Dropdown::with([
                        'options' => function ($q) {
                            $q->where('status', 'active')->orderBy('sort_order');
                        },
                        'options.translations' => function ($q) use ($lang) {
                            $q->whereIn('language_code', [$lang, 'en']);
                        }
                    ])->whereIn('slug', ['industries'])->get()->keyBy('slug');
       
        // Transform each dropdown
        $response = [];
       
        foreach ($dropdowns as $slug => $dropdown) {
            $response['company_activity'] = $dropdown->options->map(function ($option) use ($lang){
                return [
                    'id'    => $option->id,
                    'value' => $option->getTranslation('name',$lang),
                ];
            });
        }

        $countries = Country::where('status',1)->orderBy('id')->get();

        $response['company_origin'] = $countries->map(function ($country) use($lang) {
                return [
                    'id'    => $country->id,
                    'value' => $country->getTranslation('name',$lang),
                ];
        });

        $response['payment'] = [];

        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $response,
        ], 200);
    }
    
    public function getDebtsCollectionFormData(Request $request){
        $lang       = $request->header('lang') ?? env('APP_LOCALE','en'); // default to English 
        
        $dropdowns  = Dropdown::with([
                        'options' => function ($q) {
                            $q->where('status', 'active')->orderBy('sort_order');
                        },
                        'options.translations' => function ($q) use ($lang) {
                            $q->whereIn('language_code', [$lang, 'en']);
                        }
                    ])->whereIn('slug', ['debt_type','debt_category'])->get()->keyBy('slug');
       
        // Transform each dropdown
        $response   = [];
        $emirates   = Emirate::where('status',1)->orderBy('id')->get();

        $response['emirates'] = $emirates->map(function ($emirate) use($lang) {
                return [
                    'id'    => $emirate->id,
                    'value' => $emirate->getTranslation('name',$lang),
                ];
        });
       
        foreach ($dropdowns as $slug => $dropdown) {
            $response[$slug] = $dropdown->options->map(function ($option) use ($lang){
                return [
                    'id'    => $option->id,
                    'value' => $option->getTranslation('name',$lang),
                ];
            });
        }
        $response['payment'] = [];
        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $response,
        ], 200);
    }

    public function getCompanySetupFormData(Request $request){
        $lang       = $request->header('lang') ?? env('APP_LOCALE','en'); // default to English 
        
        $dropdowns  = Dropdown::with([
                        'options' => function ($q) {
                            $q->where('status', 'active')->orderBy('sort_order');
                        },
                        'options.translations' => function ($q) use ($lang) {
                            $q->whereIn('language_code', [$lang, 'en']);
                        }
                    ])->whereIn('slug', ['company_type','industries'])->get()->keyBy('slug');
       
        // Transform each dropdown
        $response   = [];
        $emirates   = Emirate::where('status',1)->orderBy('id')->get();

        $response['emirates'] = $emirates->map(function ($emirate) use($lang) {
                return [
                    'id'    => $emirate->id,
                    'value' => $emirate->getTranslation('name',$lang),
                ];
        });

        $licenseTypes = LicenseType::where('status',1)->whereNull('parent_id')->orderBy('sort_order')->get();

        $response['license_type'] = $licenseTypes->map(function ($ctype) use($lang) {
                return [
                    'id'    => $ctype->id,
                    'value' => $ctype->getTranslation('name',$lang),
                ];
        });
       
        foreach ($dropdowns as $slug => $dropdown) {
            $response[$slug] = $dropdown->options->map(function ($option) use ($lang){
                return [
                    'id'    => $option->id,
                    'value' => $option->getTranslation('name',$lang),
                ];
            });
        }
        $response['payment'] = [];
        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $response,
        ], 200);
    }
    
    public function getSubContractTypes(Request $request){
        $lang       = $request->header('lang') ?? env('APP_LOCALE', 'en');
        $parentId   = $request->contract_id ?? NULL;
        $subTypes   = ContractType::where('status', 1)
                        ->where('parent_id', $parentId)
                        ->orderBy('sort_order')
                        ->get();

        $response   = $subTypes->map(function ($subType) use ($lang) {
            return [
                'id'        => $subType->id,
                'parent_id' => $subType->parent_id,
                'value'     => $subType->getTranslation('name', $lang),
            ];
        });

        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $response,
        ], 200);
    }

    public function getZones(Request $request){
        $lang       = $request->header('lang') ?? env('APP_LOCALE', 'en');

        $parentId   = $request->emirate_id ?? NULL;
        $subTypes   = FreeZone::where('status', 1)
                            ->where('emirate_id', $parentId)
                            ->orderBy('sort_order')
                            ->get();

        $response   = $subTypes->map(function ($subType) use ($lang) {
            return [
                'id'            => $subType->id,
                'emirate_id'    => $subType->emirate_id,
                'value'         => $subType->getTranslation('name', $lang),
            ];
        });

        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $response,
        ], 200);
    }

    public function getLicenseActivities(Request $request){
        $lang       = $request->header('lang') ?? env('APP_LOCALE', 'en');

        $parentId   = $request->license_type_id ?? NULL;
        $subTypes   = LicenseType::where('status', 1)
                            ->where('parent_id', $parentId)
                            ->orderBy('sort_order')
                            ->get();

        $response   = $subTypes->map(function ($subType) use ($lang) {
            return [
                'id'        => $subType->id,
                'parent_id' => $subType->parent_id,
                'value'     => $subType->getTranslation('name', $lang),
            ];
        });

        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $response,
        ], 200);
    }
    
    public function getOnlineConsultationFormData(Request $request){
        $lang       = $request->header('lang') ?? env('APP_LOCALE','en'); // default to English 
        
        $dropdowns  = Dropdown::with([
                        'options' => function ($q) {
                            $q->where('status', 'active')->orderBy('sort_order');
                        },
                        'options.translations' => function ($q) use ($lang) {
                            $q->whereIn('language_code', [$lang, 'en']);
                        }
                    ])->whereIn('slug', ['case_type', 'case_stage', 'you_represent','languages'])->get()->keyBy('slug');
       
        // Transform each dropdown
        $response   = [];
        $emirates   = Emirate::where('status',1)->orderBy('id')->get();

        $response['emirates'] = $emirates->map(function ($emirate) use($lang) {
                return [
                    'id'    => $emirate->id,
                    'value' => $emirate->getTranslation('name',$lang),
                ];
        });

        foreach ($dropdowns as $slug => $dropdown) {
            $response[$slug] = $dropdown->options->map(function ($option) use ($lang){
                return [
                    'id'    => $option->id,
                    'value' => $option->getTranslation('name',$lang),
                ];
            });
        }

        $timeslots = ConsultationDuration::where('status',1)->where('type','normal')->orderBy('id')->get();

        $response['timeslots'] = $timeslots->map(function ($timeslot) use($lang) {
                return [
                    'duration'  => $timeslot->duration,
                    'value'     => $timeslot->getTranslation('name',$lang),
                ];
        });

        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $response,
        ], 200);
    }

    public function getRequestSubmissionFormData(Request $request){
        $lang       = $request->header('lang') ?? env('APP_LOCALE','en'); // default to English 
        
        $dropdowns  = Dropdown::with([
                        'options' => function ($q) {
                            $q->where('status', 'active')->orderBy('sort_order');
                        },
                        'options.translations' => function ($q) use ($lang) {
                            $q->whereIn('language_code', [$lang, 'en']);
                        }
                    ])->whereIn('slug', ['case_type'])->get()->keyBy('slug');
       
        // Transform each dropdown
        $response = [];
        $emirates = Emirate::where('status',1)->orderBy('id')->get();

        $response['emirates'] = $emirates->map(function ($emirate) use($lang) {
                return [
                    'id'    => $emirate->id,
                    'value' => $emirate->getTranslation('name',$lang),
                ];
        });

        foreach ($dropdowns as $slug => $dropdown) {
            $response[$slug] = $dropdown->options->map(function ($option) use ($lang){
                return [
                    'id'    => $option->id,
                    'value' => $option->getTranslation('name',$lang),
                ];
            });
        }

        $form_info = Page::with('translations')->where('slug','request_submission_forminfo')->first();

        $response['form_info'] = $form_info->getTranslation('content',$lang);

        $service    = Service::where('slug', 'request-submission')->firstOrFail();

        $response['payment'] = [
            'service_fee'       => $service->service_fee ?? 0,
            'govt_fee'          => $service->govt_fee ?? 0,
            'tax'               => $service->tax ?? 0,
            'total_amount'      => $service->total_amount ?? 0
        ];

        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $response,
        ], 200);
    }

    public function getRequestTypes(Request $request){
        $lang               = $request->header('lang') ?? env('APP_LOCALE', 'en');
        $litigation_place   = $request->litigation_place ?? NULL;
        $requestTypes       = [];
        if(trim(strtolower($litigation_place)) === 'court'){
            $requestTypes = CourtRequest::with('translations')->where('status', 1)
                            ->whereNull('parent_id')
                            ->orderBy('sort_order')
                            ->get();
        }elseif(trim(strtolower($litigation_place)) === 'public_prosecution'){
            $requestTypes = PublicProsecution::with('translations')->where('status', 1)
                            ->whereNull('parent_id')
                            ->orderBy('sort_order')
                            ->get();
        }

        $response = [];
        if(!empty($requestTypes)){
            $response = $requestTypes->map(function ($type) use ($lang, $litigation_place) {    
                return [
                    'id'                => $type->id,
                    'litigation_place'  => $litigation_place,
                    'value'             => $type->getTranslation('name', $lang),
                ];
            });
        }

        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $response,
        ], 200);
    }
    
    public function getRequestTitles(Request $request){
        $lang               = $request->header('lang') ?? env('APP_LOCALE', 'en');
        $litigation_place   = $request->litigation_place ?? NULL;
        $request_type       = $request->request_type ?? NULL;

        $requestTypes       = [];
        if(trim(strtolower($litigation_place)) === 'court'){
            $requestTypes = CourtRequest::with('translations')->where('status', 1)
                            ->where('parent_id', $request_type)
                            ->orderBy('sort_order')
                            ->get();
        }elseif(trim(strtolower($litigation_place)) === 'public_prosecution'){
            $requestTypes = PublicProsecution::with('translations')->where('status', 1)
                            ->where('parent_id', $request_type)
                            ->orderBy('sort_order')
                            ->get();
        }

        $response = [];
        if(!empty($requestTypes)){
            $response = $requestTypes->map(function ($type) use ($lang, $litigation_place) {    
                return [
                    'id'                => $type->id,
                    'litigation_place'  => $litigation_place,
                    'value'             => $type->getTranslation('name', $lang),
                ];
            });
        }

        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $response,
        ], 200);
    }

    public function getLegalTranslationFormData(Request $request){
        $lang           = $request->header('lang') ?? env('APP_LOCALE','en'); // default to English 
        
        $response       = [];
        $transLanguages = TranslationLanguage::where('status',1)->orderBy('sort_order')->get();

        $response['document_language'] = $transLanguages->map(function ($tlang) use($lang) {
                return [
                    'id'    => $tlang->id,
                    'value' => $tlang->getTranslation('name',$lang),
                ];
        });

        // Filter only 'en' and 'ar' for translation_language
        $translationLanguages = $transLanguages->filter(function ($lang) {
            return in_array($lang->lang_code, ['en', 'ar']);
        });

        $response['translation_language'] = $translationLanguages->map(function ($tlang) use ($lang) {
                return [
                    'id'    => $tlang->id,
                    'value' => $tlang->getTranslation('name', $lang),
                ];
        })->values();

        $documentTypes = DocumentType::with('translations')->where('status', 1)
                            ->whereNull('parent_id')
                            ->orderBy('sort_order')
                            ->get();

        $response['document_type'] = $documentTypes->map(function ($doc) use($lang) {
                return [
                    'id'    => $doc->id,
                    'value' => $doc->getTranslation('name',$lang),
                ];
        });

        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $response,
        ], 200);
    }

    public function getSubDocumentTypes(Request $request){
        $lang       = $request->header('lang') ?? env('APP_LOCALE', 'en');

        $doc_type   = $request->document_type ?? NULL;

        $docTypes   = [];
        if($doc_type){
            $docTypes = DocumentType::with('translations')->where('status', 1)
                            ->where('parent_id', $doc_type)
                            ->orderBy('sort_order')
                            ->get();
        }

        $response = [];
        if(!empty($docTypes)){
            $response = $docTypes->map(function ($type) use ($lang) {    
                return [
                    'id'            => $type->id, 
                    'document_type' => $type->parent_id,
                    'value'         => $type->getTranslation('name', $lang),
                ];
            });
        }

        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $response,
        ], 200);
    }
    
    public function getImmigrationRequestFormData(Request $request){
        $lang = $request->header('lang') ?? env('APP_LOCALE','en'); // default to English 
        
        $dropdowns = Dropdown::with([
                        'options' => function ($q) {
                            $q->where('status', 'active')->orderBy('sort_order');
                        },
                        'options.translations' => function ($q) use ($lang) {
                            $q->whereIn('language_code', [$lang, 'en']);
                        }
                    ])->whereIn('slug', ['positions','residency_status','immigration_type'])->get()->keyBy('slug');
       
        // Transform each dropdown
        $response = [];
       
        foreach ($dropdowns as $slug => $dropdown) {
            $response[$slug] = $dropdown->options->map(function ($option) use ($lang){
                return [
                    'id'    => $option->id,
                    'value' => $option->getTranslation('name',$lang),
                ];
            });
        }

        $countries = Country::where('status',1)->orderBy('id')->get();

        $response['nationality'] = $countries->map(function ($country) use($lang) {
                return [
                    'id'    => $country->id,
                    'value' => $country->getTranslation('name',$lang),
                ];
        });

        $response['preffered_country'] = $response['nationality'];
        $response['application_type'] = $response['immigration_type'];
        unset($response['immigration_type']);
        $service    = Service::where('slug', 'immigration-requests')->firstOrFail();

        $response['payment'] = [
            'service_fee'       => $service->service_fee ?? 0,
            'govt_fee'          => $service->govt_fee ?? 0,
            'tax'               => $service->tax ?? 0,
            'total_amount'      => $service->total_amount ?? 0
        ];
        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $response,
        ], 200);
    }

    public function getAnnualAgreementFormData(Request $request){
        $lang       = $request->header('lang') ?? env('APP_LOCALE','en'); // default to English 
        
        $dropdowns  = Dropdown::with([
                        'options' => function ($q) {
                            $q->where('status', 'active')->orderBy('sort_order');
                        },
                        'options.translations' => function ($q) use ($lang) {
                            $q->whereIn('language_code', [$lang, 'en']);
                        }
                    ])->whereIn('slug', ['industries','no_of_employees','case_type'])->get()->keyBy('slug');
       
        // Transform each dropdown
        $response = [];
       
        foreach ($dropdowns as $slug => $dropdown) {
            $response[$slug] = $dropdown->options->map(function ($option) use ($lang){
                return [
                    'id'    => $option->id,
                    'value' => $option->getTranslation('name',$lang),
                ];
            });
        }

        $emirates   = Emirate::where('status',1)->orderBy('id')->get();

        $response['emirates'] = $emirates->map(function ($emirate) use($lang) {
                return [
                    'id'    => $emirate->id,
                    'value' => $emirate->getTranslation('name',$lang),
                ];
        });

        $licenseTypes = LicenseType::where('status',1)->whereNull('parent_id')->orderBy('sort_order')->get();

        $response['license_type'] = $licenseTypes->map(function ($ctype) use($lang) {
                return [
                    'id'    => $ctype->id,
                    'value' => $ctype->getTranslation('name',$lang),
                ];
        });

        $response['calls']          = [1,2,3,4,5];
        $response['visits']         = [0,1,2,3,4];
        $response['installments']   = [1, 2, 4];

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

        $response['law_firms'] = $lawFirms->map(function ($lawfirm) use($lang) {
            return [
                'id'    => $lawfirm->id,
                'value' => $lawfirm->getTranslation('law_firm_name',$lang),
            ];
        });

        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $response,
        ], 200);
    }

    public function getAnnualAgreementPrice(Request $request){
        $lang           = $request->header('lang') ?? env('APP_LOCALE','en'); // default to English 
        
        $calls          = $request->query('calls');
        $visits         = $request->query('visits');
        $installments   = $request->query('installments');

        if (!in_array($calls, [1,2,3,4,5]) || !in_array($visits, [0,1,2,3,4]) || !in_array($installments, [1,2,4])) {
            return response()->json([
                'status'    => false,
                'message'   => __('messages.invalid_combination'),
            ], 200);
        }

        $base = AnnualRetainerBaseFee::where('calls_per_month', $calls)
                                    ->where('visits_per_year', $visits)
                                    ->first();

        if (!$base) {
             return response()->json([
                'status'    => false,
                'message'   => __('messages.combination_not_found'),
            ], 200);
        }

        $installment = $base->installments()->where('installments', $installments)->first();

        if (!$installment) {
             return response()->json([
                'status'    => false,
                'message'   => __('messages.installment_not_found'),
            ], 200);
        }

        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => [
                            'calls_per_month'   => $calls,
                            'visits_per_year'   => $visits,
                            'installments'      => $installments,
                            'service_fee'       => $base->service_fee,
                            'govt_fee'          => $base->govt_fee,
                            'tax'               => $base->tax,
                            'base_total'        => $base->base_total,
                            'extra_percent'     => $installment->extra_percent,
                            'final_total'       => $installment->final_total,
                        ]
        ], 200);
    }
    // Request submission

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
            $message = implode(' ', $validator->errors()->all());

            return response()->json([
                'status'    => false,
                'message'   => $message,
            ], 200);
        }

        $lang       = $request->header('lang') ?? env('APP_LOCALE','en');
        $user       = $request->user();
        $service    = Service::where('slug', 'court-case-submission')->firstOrFail();

        $referenceCode = ServiceRequest::generateReferenceCode($service);

        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'court-case-submission',
            'reference_code'    => $referenceCode,
            'source'            => 'mob',
            'submitted_at'      => date('Y-m-d H:i:s')
        ]);

        $courtCase = RequestCourtCase::create([
            'user_id'               => $user->id,
            'service_request_id'    => $service_request->id,
            'applicant_type'        => $request->input('applicant_type'),
            'litigation_type'       => $request->input('litigation_type'),
            'emirate_id'            => $request->input('emirate_id'),
            'case_type'             => $request->input('case_type'),
            'you_represent'         => $request->input('you_represent'),
            'about_case'            => $request->input('about_case'),
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

        $usersToNotify = getUsersWithPermissions(['view_service_requests','export_service_requests','change_request_status','manage_service_requests']);
        Notification::send($usersToNotify, new ServiceRequestSubmitted($service_request, true));

        $pageData = getPageDynamicContent('request_success',$lang);
        $response = [
            'reference' => $service_request->reference_code,
            'message'   => $pageData['content']
        ];
        return response()->json([
            'status'    => true,
            'message'   => __('messages.request_submit_success'),
            'data'      => $response,
        ], 200);
    }

    public function requestCriminalComplaints(Request $request){

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
            $message = implode(' ', $validator->errors()->all());

            return response()->json([
                'status'    => false,
                'message'   => $message,
            ], 200);
        }

        $lang       = $request->header('lang') ?? env('APP_LOCALE','en');
        $user       = $request->user();
        $service    = Service::where('slug', 'criminal-complaint')->firstOrFail();

        $referenceCode = ServiceRequest::generateReferenceCode($service);

        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'criminal-complaint',
            'reference_code'    => $referenceCode,
            'source'            => 'mob',
            'submitted_at'      => date('Y-m-d H:i:s')
        ]);

        $criminalComplaint = RequestCriminalComplaint::create([
            'user_id'               => $user->id,
            'service_request_id'    => $service_request->id,
            'applicant_type'        => $request->input('applicant_type'),
            'litigation_type'       => $request->input('litigation_type'),
            'emirate_id'            => $request->input('emirate_id'),
            'case_type'             => $request->input('case_type'),
            'you_represent'         => $request->input('you_represent'),
            'about_case'            => $request->input('about_case'),
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

        $usersToNotify = getUsersWithPermissions(['view_service_requests','export_service_requests','change_request_status','manage_service_requests']);
        Notification::send($usersToNotify, new ServiceRequestSubmitted($service_request, true));

        $pageData = getPageDynamicContent('request_success',$lang);
        $response = [
            'reference' => $service_request->reference_code,
            'message'   => $pageData['content']
        ];
        return response()->json([
            'status'    => true,
            'message'   => __('messages.request_submit_success'),
            'data'      => $response,
        ], 200);
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
            $message = implode(' ', $validator->errors()->all());

            return response()->json([
                'status'    => false,
                'message'   => $message,
            ], 200);
        }

        $lang       = $request->header('lang') ?? env('APP_LOCALE','en');
        $user       = $request->user();
        $service    = Service::where('slug', 'last-will-and-testament')->firstOrFail();

        $referenceCode = ServiceRequest::generateReferenceCode($service);

        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'last-will-and-testament',
            'reference_code'    => $referenceCode,
            'source'            => 'mob',
            'submitted_at'      => date('Y-m-d H:i:s')
        ]);

        $lastWill = RequestLastWill::create([
            'user_id'               => $user->id,
            'service_request_id'    => $service_request->id,
            'testament_place'       => $request->input('testament_place'),
            'nationality'           => $request->input('nationality'),
            'emirate_id'            => $request->input('emirate_id'),
            'religion'              => $request->input('religion'),
            'you_represent'         => $request->input('you_represent'),
            'about_case'            => $request->input('about_case'),
            'full_name'             => $request->input('full_name'),
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

        $usersToNotify = getUsersWithPermissions(['view_service_requests','export_service_requests','change_request_status','manage_service_requests']);
        Notification::send($usersToNotify, new ServiceRequestSubmitted($service_request, true));

        $pageData = getPageDynamicContent('request_success',$lang);
        $response = [
            'reference' => $service_request->reference_code,
            'message'   => $pageData['content']
        ];
        return response()->json([
            'status'    => true,
            'message'   => __('messages.request_submit_success'),
            'data'      => $response,
        ], 200);
    }

    public function requestPowerOfAttorney(Request $request){
        $validator = Validator::make($request->all(), [
            'applicant_type'        => 'required',
            'appointer_name'        => 'required',
            'id_number'             => 'required',
            'appointer_mobile'      => 'required',
            'emirate_id'            => 'required',
            'poa_type'              => 'required',
            'name_of_authorized'    => 'required',
            'authorized_mobile'     => 'required',
            'id_number_authorized'  => 'required',
            'authorized_address'    => 'required',
            'relationship'          => 'required',
            'authorized_passport'   => 'nullable|array',
            'appointer_id'          => 'required|array',
            'authorized_id'         => 'required|array',
            'authorized_passport.*' => 'file|mimes:pdf,jpg,jpeg,webp,png,svg|max:500',
            'appointer_id.*'        => 'file|mimes:pdf,jpg,jpeg,webp,png,svg|max:500',
            'authorized_id.*'       => 'file|mimes:pdf,jpg,jpeg,webp,png,svg|max:500',
        ], [
            'applicant_type.required'       => __('messages.applicant_type_required'),
            'appointer_name.required'       => __('messages.appointer_name_required'),
            'emirate_id.required'           => __('messages.emirate_required'),
            'id_number.required'            => __('messages.id_number_required'),
            'appointer_mobile.required'     => __('messages.appointer_mobile_required'),
            'poa_type.required'             => __('messages.poa_type_required'),
            'name_of_authorized.required'   => __('messages.name_of_authorized_required'),
            'authorized_mobile.required'    => __('messages.authorized_mobile_required'),
            'id_number_authorized.required' => __('messages.id_number_authorized_required'),
            'authorized_address.required'   => __('messages.authorized_address_required'),
            'relationship.required'         => __('messages.relationship_required'),
            'authorized_passport.*.file'    => __('messages.authorized_passport_invalid'),
            'authorized_passport.*.mimes'   => __('messages.authorized_passport_mimes'),
            'authorized_passport.*.max'     => __('messages.authorized_passport_max'),
            'appointer_id.required'         => __('messages.appointer_id_required'),
            'appointer_id.*.file'           => __('messages.appointer_id_invalid'),
            'appointer_id.*.mimes'          => __('messages.appointer_id_mimes'),
            'appointer_id.*.max'            => __('messages.appointer_id_max'),
            'authorized_id.required'        => __('messages.authorized_id_required'),
            'authorized_id.*.file'          => __('messages.authorized_id_invalid'),
            'authorized_id.*.mimes'         => __('messages.authorized_id_mimes'),
            'authorized_id.*.max'           => __('messages.authorized_id_max'),
        ]);

        if ($validator->fails()) {
            $message = implode(' ', $validator->errors()->all());

            return response()->json([
                'status'    => false,
                'message'   => $message,
            ], 200);
        }

        $lang       = $request->header('lang') ?? env('APP_LOCALE','en');
        $user       = $request->user();
        $service    = Service::where('slug', 'power-of-attorney')->firstOrFail();

        $referenceCode = ServiceRequest::generateReferenceCode($service);

        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'power-of-attorney',
            'reference_code'    => $referenceCode,
            'source'            => 'mob',
            'submitted_at'      => date('Y-m-d H:i:s')
        ]);

        $powerOA = RequestPowerOfAttorney::create([
            'user_id'               => $user->id,
            'service_request_id'    => $service_request->id,
            'applicant_type'        => $request->input('applicant_type'), 
            'appointer_name'        => $request->input('appointer_name'), 
            'id_number'             => $request->input('id_number'), 
            'appointer_mobile'      => $request->input('appointer_mobile'), 
            'emirate_id'            => $request->input('emirate_id'), 
            'poa_type'              => $request->input('poa_type'), 
            'name_of_authorized'    => $request->input('name_of_authorized'), 
            'authorized_mobile'     => $request->input('authorized_mobile'), 
            'id_number_authorized'  => $request->input('id_number_authorized'), 
            'authorized_address'    => $request->input('authorized_address'), 
            'relationship'          => $request->input('relationship'),
            'appointer_id'          => [],
            'authorized_id'         => [],
            'authorized_passport'   => [],
        ]);

        $requestFolder = "uploads/power_of_attorney/{$powerOA->id}/";

        $fileFields = [
            'appointer_id'          => 'appointer_id',
            'authorized_id'         => 'authorized_id',
            'authorized_passport'   => 'authorized_passport'
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

        $powerOA->update($filePaths);
        // Notify the user
        $request->user()->notify(new ServiceRequestSubmitted($service_request));

        $usersToNotify = getUsersWithPermissions(['view_service_requests','export_service_requests','change_request_status','manage_service_requests']);
        Notification::send($usersToNotify, new ServiceRequestSubmitted($service_request, true));

        $pageData = getPageDynamicContent('request_success',$lang);
        $response = [
            'reference' => $service_request->reference_code,
            'message'   => $pageData['content']
        ];
        return response()->json([
            'status'    => true,
            'message'   => __('messages.request_submit_success'),
            'data'      => $response,
        ], 200);
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
            $message = implode(' ', $validator->errors()->all());

            return response()->json([
                'status'    => false,
                'message'   => $message,
            ], 200);
        }

        $lang       = $request->header('lang') ?? env('APP_LOCALE','en');
        $user       = $request->user();
        $service    = Service::where('slug', 'memo-writing')->firstOrFail();

        $referenceCode = ServiceRequest::generateReferenceCode($service);

        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'memo-writing',
            'reference_code'    => $referenceCode,
            'source'            => 'mob',
            'submitted_at'      => date('Y-m-d H:i:s')
        ]);

        $memoWriting = RequestMemoWriting::create([
            'user_id'               => $user->id,
            'service_request_id'    => $service_request->id,
            'applicant_type'        => $request->input('applicant_type'),
            'litigation_type'       => $request->input('litigation_type'),
            'emirate_id'            => $request->input('emirate_id'),
            'case_type'             => $request->input('case_type'),
            'you_represent'         => $request->input('you_represent'),
            'full_name'             => $request->input('full_name'),
            'about_case'            => $request->input('about_case'),
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

        $usersToNotify = getUsersWithPermissions(['view_service_requests','export_service_requests','change_request_status','manage_service_requests']);
        Notification::send($usersToNotify, new ServiceRequestSubmitted($service_request, true));

        $pageData = getPageDynamicContent('request_success',$lang);
        $response = [
            'reference' => $service_request->reference_code,
            'message'   => $pageData['content']
        ];
        return response()->json([
            'status'    => true,
            'message'   => __('messages.request_submit_success'),
            'data'      => $response,
        ], 200);
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
            $message = implode(' ', $validator->errors()->all());

            return response()->json([
                'status'    => false,
                'message'   => $message,
            ], 200);
        }

        $lang       = $request->header('lang') ?? env('APP_LOCALE','en');
        $user       = $request->user();
        $service    = Service::where('slug', 'escrow-accounts')->firstOrFail();

        $referenceCode = ServiceRequest::generateReferenceCode($service);

        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'escrow-accounts',
            'reference_code'    => $referenceCode,
            'source'            => 'mob',
            'submitted_at'      => date('Y-m-d H:i:s')
        ]);

        $escrowAccounts = RequestEscrowAccount::create([
            'user_id'               => $user->id,
            'service_request_id'    => $service_request->id,
            'applicant_type'        => $request->input('applicant_type'),
            'company_name'          => $request->input('company_name'),
            'company_activity'      => $request->input('company_activity'),
            'company_origin'        => $request->input('company_origin'),
            'amount'                => $request->input('amount'),
            'about_deal'            => $request->input('about_deal')
        ]);

        // Notify the user
        $request->user()->notify(new ServiceRequestSubmitted($service_request));

        $usersToNotify = getUsersWithPermissions(['view_service_requests','export_service_requests','change_request_status','manage_service_requests']);
        Notification::send($usersToNotify, new ServiceRequestSubmitted($service_request, true));

        $pageData = getPageDynamicContent('request_success',$lang);

        $response = [
            'reference' => $service_request->reference_code,
            'message'   => $pageData['content']
        ];
        return response()->json([
            'status'    => true,
            'message'   => __('messages.request_submit_success'),
            'data'      => $response,
        ], 200);
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
            $message = implode(' ', $validator->errors()->all());

            return response()->json([
                'status'    => false,
                'message'   => $message,
            ], 200);
        }

       
        $lang       = $request->header('lang') ?? env('APP_LOCALE','en');
        $user       = $request->user();
        $service    = Service::where('slug', 'debts-collection')->firstOrFail();

        $referenceCode = ServiceRequest::generateReferenceCode($service);

        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'debts-collection',
            'reference_code'    => $referenceCode,
            'source'            => 'mob',
            'submitted_at'      => date('Y-m-d H:i:s')
        ]);

        $debtCollection = RequestDebtCollection::create([
            'user_id'               => $user->id,
            'service_request_id'    => $service_request->id,
            'applicant_type'        => $request->input('applicant_type'),
            'emirate_id'            => $request->input('emirate_id'),
            'debt_type'             => $request->input('debt_type'),
            'debt_amount'           => $request->input('debt_amount'),
            'debt_category'         => $request->input('debt_category'),
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

        $usersToNotify = getUsersWithPermissions(['view_service_requests','export_service_requests','change_request_status','manage_service_requests']);
        Notification::send($usersToNotify, new ServiceRequestSubmitted($service_request, true));

        $pageData = getPageDynamicContent('request_success',$lang);
        $response = [
            'reference' => $service_request->reference_code,
            'message'   => $pageData['content']
        ];
        return response()->json([
            'status'    => true,
            'message'   => __('messages.request_submit_success'),
            'data'      => $response,
        ], 200);
    }

    public function requestCompanySetup(Request $request){

        $validator = Validator::make($request->all(), [
            'applicant_type'    => 'required',
            'emirate_id'        => 'required',
            'zone'              => 'required',
            'license_type'      => 'required',
            'license_activity'  => 'required',
            'company_type'      => 'required',
            'industry'          => 'required',
            'company_name'      => 'required',
            'mobile'            => 'required',
            'email'             => 'required',
            'documents'         => 'nullable|array',
            'documents.*'       => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:1024',
        ], [
            'applicant_type.required'   => __('messages.applicant_type_required'),
            'zone.required'             => __('messages.zone_required'),
            'emirate_id.required'       => __('messages.emirate_required'),
            'license_type.required'     => __('messages.license_type_required'),
            'license_activity.required' => __('messages.license_activity_required'),
            'company_type.required'     => __('messages.company_type_required'),
            'industry.required'         => __('messages.industry_required'),
            'company_name.required'     => __('messages.company_person_name_required'),
            'mobile.required'           => __('messages.mobile_required'),
            'email.required'            => __('messages.email_required'),
            'documents.*.file'          => __('messages.document_file_invalid'),
            'documents.*.mimes'         => __('messages.document_file_mimes'),
            'documents.*.max'           => __('messages.document_file_max'),
        ]);

        if ($validator->fails()) {
            $message = implode(' ', $validator->errors()->all());

            return response()->json([
                'status'    => false,
                'message'   => $message,
            ], 200);
        }

        $lang       = $request->header('lang') ?? env('APP_LOCALE','en');
        $user       = $request->user();
        $service    = Service::where('slug', 'company-setup')->firstOrFail();

        $referenceCode = ServiceRequest::generateReferenceCode($service);

        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'company-setup',
            'reference_code'    => $referenceCode,
            'source'            => 'mob',
            'submitted_at'      => date('Y-m-d H:i:s')
        ]);

        $companySetup = RequestCompanySetup::create([
            'user_id'               => $user->id,
            'service_request_id'    => $service_request->id,
            'applicant_type'        => $request->input('applicant_type'),
            'emirate_id'            => $request->input('emirate_id'),
            'zone'                  => $request->input('zone'),
            'license_type'          => $request->input('license_type'),
            'license_activity'      => $request->input('license_activity'),
            'company_type'          => $request->input('company_type'),
            'industry'              => $request->input('industry'),
            'company_name'          => $request->input('company_name'),
            'mobile'                => $request->input('mobile'),
            'email'                 => $request->input('email'),
            'documents'             => []
        ]);

        $requestFolder = "uploads/company_setup/{$companySetup->id}/";

        $fileFields = [
            'documents'     => 'documents'
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

        $companySetup->update($filePaths);
        // Notify the user
        $request->user()->notify(new ServiceRequestSubmitted($service_request));

        $usersToNotify = getUsersWithPermissions(['view_service_requests','export_service_requests','change_request_status','manage_service_requests']);
        Notification::send($usersToNotify, new ServiceRequestSubmitted($service_request, true));

        $pageData = getPageDynamicContent('request_success',$lang);
        $response = [
            'reference' => $service_request->reference_code,
            'message'   => $pageData['content']
        ];
        return response()->json([
            'status'    => true,
            'message'   => __('messages.request_submit_success'),
            'data'      => $response,
        ], 200);
    }
    
    public function requestContractDrafting(Request $request){

        $validator = Validator::make($request->all(), [
            'applicant_type'    => 'required',
            'contract_type'   => 'required',
            'emirate_id'        => 'required',
            'sub_contract_type'         => 'required',
            'contract_language'     => 'required',
            'company_name'     => 'required',
            'industry'     => 'required',
            'email'     => 'required',
            'priority'     => 'required',
            'documents'         => 'nullable|array',
            'eid'               => 'required|array',
            'trade_license'     => 'required|array',
            'documents.*'       => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:1024',
            'eid.*'             => 'file|mimes:pdf,jpg,jpeg,webp,png,svg|max:500',
            'trade_license.*'   => 'file|mimes:pdf,jpg,jpeg,webp,png,svg|max:500',
        ], [
            'applicant_type.required'       => __('messages.applicant_type_required'),
            'contract_type.required'        => __('messages.contract_type_required'),
            'emirate_id.required'           => __('messages.emirate_required'),
            'sub_contract_type.required'    => __('messages.sub_contract_type_required'),
            'contract_language.required'    => __('messages.contract_language_required'),
            'company_name.required'         => __('messages.company_person_name_required'),
            'industry.required'             => __('messages.industry_required'),
            'email.required'                => __('messages.email_required'),
            'priority.required'             => __('messages.priority_required'),
            'documents.*.file'              => __('messages.document_file_invalid'),
            'documents.*.mimes'             => __('messages.document_file_mimes'),
            'documents.*.max'               => __('messages.document_file_max'),
            'eid.required'                  => __('messages.eid_required'),
            'eid.*.file'                    => __('messages.eid_file_invalid'),
            'eid.*.mimes'                   => __('messages.eid_file_mimes'),
            'eid.*.max'                     => __('messages.eid_file_max'),
            'trade_license.required'        => __('messages.trade_license_required'),
            'trade_license.*.file'          => __('messages.trade_license_file_invalid'),
            'trade_license.*.mimes'         => __('messages.trade_license_file_mimes'),
            'trade_license.*.max'           => __('messages.trade_license_file_max'),
        ]);

        if ($validator->fails()) {
            $message = implode(' ', $validator->errors()->all());

            return response()->json([
                'status'    => false,
                'message'   => $message,
            ], 200);
        }

        $lang       = $request->header('lang') ?? env('APP_LOCALE','en');
        $user       = $request->user();
        $service    = Service::where('slug', 'contract-drafting')->firstOrFail();

        $referenceCode = ServiceRequest::generateReferenceCode($service);

        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'contract-drafting',
            'reference_code'    => $referenceCode,
            'source'            => 'mob',
            'submitted_at'      => date('Y-m-d H:i:s')
        ]);

        $contractDrafting = RequestContractDrafting::create([
            'user_id'               => $user->id,
            'service_request_id'    => $service_request->id,
            'applicant_type'        => $request->input('applicant_type'),
            'contract_type'         => $request->input('contract_type'),
            'emirate_id'            => $request->input('emirate_id'),
            'sub_contract_type'     => $request->input('sub_contract_type'),
            'contract_language'     => $request->input('contract_language'),
            'company_name'          => $request->input('company_name'),
            'industry'              => $request->input('industry'),
            'email'                 => $request->input('email'),
            'priority'              => $request->input('priority'),
            'documents'             => [],
            'eid'                   => [],
            'trade_license'         => [],
        ]);

        $requestFolder = "uploads/contract_drafting/{$contractDrafting->id}/";

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

        $contractDrafting->update($filePaths);
        // Notify the user
        $request->user()->notify(new ServiceRequestSubmitted($service_request));

        $usersToNotify = getUsersWithPermissions(['view_service_requests','export_service_requests','change_request_status','manage_service_requests']);
        Notification::send($usersToNotify, new ServiceRequestSubmitted($service_request, true));

        $pageData = getPageDynamicContent('request_success',$lang);
        $response = [
            'reference' => $service_request->reference_code,
            'message'   => $pageData['content']
        ];
        return response()->json([
            'status'    => true,
            'message'   => __('messages.request_submit_success'),
            'data'      => $response,
        ], 200);
    }

    public function requestExpertReport(Request $request){

        $validator = Validator::make($request->all(), [
            'applicant_type'            => 'required',
            'applicant_place'           => 'required',
            'emirate_id'                => 'required',
            'expert_report_type'        => 'required',
            'expert_report_language'    => 'required',
            'documents'                 => 'required|array',
            'eid'                       => 'required|array',
            'trade_license'             => 'required|array',
            'documents.*'               => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:1024',
            'eid.*'                     => 'file|mimes:pdf,jpg,jpeg,webp,png,svg|max:500',
            'trade_license.*'           => 'file|mimes:pdf,jpg,jpeg,webp,png,svg|max:500',
        ], [
            'applicant_type.required'           => __('messages.applicant_type_required'),
            'applicant_place.required'          => __('messages.applicant_place_required'),
            'emirate_id.required'               => __('messages.emirate_required'),
            'expert_report_type.required'       => __('messages.expert_report_type_required'),
            'expert_report_language.required'   => __('messages.expert_report_language_required'),
            'documents.required'                => __('messages.document_required'),
            'documents.*.file'                  => __('messages.document_file_invalid'),
            'documents.*.mimes'                 => __('messages.document_file_mimes'),
            'documents.*.max'                   => __('messages.document_file_max'),
            'eid.required'                      => __('messages.eid_required'),
            'eid.*.file'                        => __('messages.eid_file_invalid'),
            'eid.*.mimes'                       => __('messages.eid_file_mimes'),
            'eid.*.max'                         => __('messages.eid_file_max'),
            'trade_license.required'            => __('messages.trade_license_required'),
            'trade_license.*.file'              => __('messages.trade_license_file_invalid'),
            'trade_license.*.mimes'             => __('messages.trade_license_file_mimes'),
            'trade_license.*.max'               => __('messages.trade_license_file_max'),
        ]);

        if ($validator->fails()) {
            $message = implode(' ', $validator->errors()->all());

            return response()->json([
                'status'    => false,
                'message'   => $message,
            ], 200);
        }

        $lang       = $request->header('lang') ?? env('APP_LOCALE','en');
        $user       = $request->user();
        $service    = Service::where('slug', 'expert-report')->firstOrFail();
        $referenceCode = ServiceRequest::generateReferenceCode($service);
        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'expert-report',
            'reference_code'    => $referenceCode,
            'source'            => 'mob',
            'submitted_at'      => date('Y-m-d H:i:s'),
            'payment_status'    => 'pending'
        ]);

        $expertReport = RequestExpertReport::create([
            'user_id'                   => $user->id,
            'service_request_id'        => $service_request->id,
            'applicant_type'            => $request->input('applicant_type'),
            'applicant_place'           => $request->input('applicant_place'),
            'emirate_id'                => $request->input('emirate_id'),
            'expert_report_type'        => $request->input('expert_report_type'),
            'expert_report_language'    => $request->input('expert_report_language'),
            'about_case'                => $request->input('about_case'),
            'documents'                 => [],
            'eid'                       => [],
            'trade_license'             => [],
        ]);

        $requestFolder = "uploads/expert_report/{$expertReport->id}/";

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

        $expertReport->update($filePaths);

        $pageData = getPageDynamicContent('request_payment_success',$lang);
        $response = [
            'reference' => $service_request->reference_code,
            'message'   => $pageData['content']
        ];
        return response()->json([
            'status'    => true,
            'message'   => __('messages.request_submit_success'),
            'data'      => $response,
        ], 200);
    }

    public function requestImmigration(Request $request){

        $validator = Validator::make($request->all(), [
            'preferred_country'     => 'required',
            'position'              => 'required',
            'age'                   => 'required',
            'nationality'           => 'required',
            'years_of_experience'   => 'required',
            'address'               => 'required',
            'residency_status'      => 'required',
            'current_salary'        => 'required',
            'application_type'      => 'required',
            'cv'                    => 'required|array',
            'certificates'          => 'required|array',
            'passport'              => 'required|array',
            'photo'                 => 'required|array',
            'account_statement'     => 'required|array',
            'cv.*'                  => 'file|mimes:pdf,jpg,jpeg,webp,png,svg|max:500',
            'certificates.*'        => 'file|mimes:pdf,jpg,jpeg,webp,png,svg|max:500',
            'account_statement.*'   => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:1024',
            'passport.*'            => 'file|mimes:pdf,jpg,jpeg,webp,png,svg|max:500',
            'photo.*'               => 'file|mimes:pdf,jpg,jpeg,webp,png,svg|max:500',
        ], [
            'preferred_country.required'    => __('messages.preferred_country_required'),
            'position.required'             => __('messages.position_required'),
            'age.required'                  => __('messages.age_required'),
            'nationality.required'          => __('messages.nationality_required'),
            'years_of_experience.required'  => __('messages.years_of_experience_required'),
            'address.required'              => __('messages.address_required'),
            'residency_status.required'     => __('messages.residency_status_required'),
            'current_salary.required'       => __('messages.current_salary_required'),
            'application_type.required'     => __('messages.application_type_required'),
            'cv.required'                   => __('messages.cv_required'),
            'certificates.required'         => __('messages.certificates_required'),
            'account_statement.required'    => __('messages.account_statement_required'),
            'passport.required'             => __('messages.passport_required'),
            'photo.required'                => __('messages.photo_required'),
            'cv.*.file'                     => __('messages.cv_invalid'),
            'cv.*.mimes'                    => __('messages.cv_mimes'),
            'cv.*.max'                      => __('messages.cv_max'),
            'certificates.*.file'           => __('messages.certificates_invalid'),
            'certificates.*.mimes'          => __('messages.certificates_mimes'),
            'certificates.*.max'            => __('messages.certificates_max'),
            'account_statement.*.file'      => __('messages.account_statement_invalid'),
            'account_statement.*.mimes'     => __('messages.account_statement_mimes'),
            'account_statement.*.max'       => __('messages.account_statement_max'),
            'passport.*.file'               => __('messages.passport_invalid'),
            'passport.*.mimes'              => __('messages.passport_mimes'),
            'passport.*.max'                => __('messages.passport_max'),
            'photo.*.file'                  => __('messages.photo_invalid'),
            'photo.*.mimes'                 => __('messages.photo_mimes'),
            'photo.*.max'                   => __('messages.photo_max'),
        ]);

        if ($validator->fails()) {
            $message = implode(' ', $validator->errors()->all());

            return response()->json([
                'status'    => false,
                'message'   => $message,
            ], 200);
        }

        $lang       = $request->header('lang') ?? env('APP_LOCALE','en');
        $user       = $request->user();
        $service    = Service::where('slug', 'immigration-requests')->firstOrFail();
        $referenceCode = ServiceRequest::generateReferenceCode($service);
        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'immigration-requests',
            'reference_code'    => $referenceCode,
            'source'            => 'mob',
            'submitted_at'      => date('Y-m-d H:i:s'),
            'payment_status'    => 'pending'
        ]);

        $immigration = RequestImmigration::create([
            'service_request_id'    => $service_request->id, 
            'user_id'               => $user->id, 
            'preferred_country'     => $request->input('preferred_country'),
            'position'              => $request->input('position'),
            'age'                   => $request->input('age'),
            'nationality'           => $request->input('nationality'),
            'years_of_experience'   => $request->input('years_of_experience'),
            'address'               => $request->input('address'),
            'residency_status'      => $request->input('residency_status'),
            'current_salary'        => $request->input('current_salary'),
            'application_type'      => $request->input('application_type'),
            'cv'                    => [],
            'certificates'          => [],
            'passport'              => [],
            'photo'                 => [],
            'account_statement'     => [],
        ]);

        $requestFolder = "uploads/immigration/{$immigration->id}/";

        $fileFields = [
            'cv'                => 'cv',
            'certificates'      => 'certificates',
            'passport'          => 'passport',
            'photo'             => 'photo',
            'account_statement' => 'account_statement'
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

        $immigration->update($filePaths);

        $pageData = getPageDynamicContent('request_payment_success',$lang);
        $response = [
            'reference' => $service_request->reference_code,
            'message'   => $pageData['content']
        ];
        return response()->json([
            'status'    => true,
            'message'   => __('messages.request_submit_success'),
            'data'      => $response,
        ], 200);
    }

    public function requestRequestSubmission(Request $request){

        $validator = Validator::make($request->all(), [
            'applicant_type'    => 'required',
            'litigation_type'   => 'required',
            'litigation_place'  => 'required',
            'emirate_id'        => 'required',
            'case_type'         => 'required',
            'request_type'     => 'required',
            'request_title'     => 'required',
            'case_number'     => 'required',
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
            'litigation_place.required' => __('messages.litigation_place_required'),
            'emirate_id.required'       => __('messages.emirate_required'),
            'case_type.required'        => __('messages.case_type_required'),
            'request_type.required'     => __('messages.request_type_required'),
            'request_title.required'    => __('messages.request_title_required'),
            'case_number.required'      => __('messages.case_number_required'),
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
            $message = implode(' ', $validator->errors()->all());

            return response()->json([
                'status'    => false,
                'message'   => $message,
            ], 200);
        }

        $lang       = $request->header('lang') ?? env('APP_LOCALE','en');
        $user       = $request->user();
        $service    = Service::where('slug', 'request-submission')->firstOrFail();

        $referenceCode = ServiceRequest::generateReferenceCode($service);

        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'request-submission',
            'reference_code'    => $referenceCode,
            'source'            => 'mob',
            'submitted_at'      => date('Y-m-d H:i:s'),
            'payment_status'    => 'pending'
        ]);

        $requestSubmission = RequestRequestSubmission::create([
            'user_id'               => $user->id,
            'service_request_id'    => $service_request->id,
            'applicant_type'        => $request->input('applicant_type'),
            'litigation_type'       => $request->input('litigation_type'),
            'litigation_place'      => $request->input('litigation_place'),
            'emirate_id'            => $request->input('emirate_id'),
            'case_type'             => $request->input('case_type'),
            'request_type'          => $request->input('request_type'),
            'request_title'         => $request->input('request_title'),
            'case_number'           => $request->input('case_number'),
            'memo'                  => [],
            'documents'             => [],
            'eid'                   => [],
            'trade_license'         => [],
        ]);

        $requestFolder = "uploads/request_submission/{$requestSubmission->id}/";

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

        $requestSubmission->update($filePaths);

        $pageData = getPageDynamicContent('request_payment_success',$lang);
        $response = [
            'reference' => $service_request->reference_code,
            'message'   => $pageData['content']
        ];
        return response()->json([
            'status'    => true,
            'message'   => __('messages.request_submit_success'),
            'data'      => $response,
        ], 200);
    }

    public function requestAnnualAgreement(Request $request){

        $validator = Validator::make($request->all(), [
            'company_name'      => 'required',
            'emirate_id'        => 'required',
            'license_type'      => 'required',
            'industry'          => 'required',
            'no_of_employees'   => 'required',
            'case_type'         => 'required',
            'no_of_calls'       => 'required',
            'no_of_visits'      => 'required',
            'no_of_installment' => 'required',
            'lawfirm'           => 'required',
        ], [
            'company_name.required'         => __('messages.company_name_required'),
            'emirate_id.required'           => __('messages.emirate_required'),
            'license_type.required'         => __('messages.license_type_required'),
            'industry.required'             => __('messages.industry_required'),
            'no_of_employees.required'      => __('messages.no_of_employees_required'),
            'case_type.required'            => __('messages.case_type_required'),
            'no_of_calls.required'          => __('messages.no_of_calls_required'),
            'no_of_visits.required'         => __('messages.no_of_visits_required'),
            'no_of_installment.required'    => __('messages.no_of_installment_required'),
            'lawfirm.required'              => __('messages.lawfirm_required'),
        ]);

        if ($validator->fails()) {
            $message = implode(' ', $validator->errors()->all());

            return response()->json([
                'status'    => false,
                'message'   => $message,
            ], 200);
        }
       
        $lang       = $request->header('lang') ?? env('APP_LOCALE','en');
        $user       = $request->user();
        $service    = Service::where('slug', 'annual-retainer-agreement')->firstOrFail();

        $referenceCode = ServiceRequest::generateReferenceCode($service);

        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'annual-retainer-agreement',
            'reference_code'    => $referenceCode,
            'source'            => 'mob',
            'submitted_at'      => date('Y-m-d H:i:s'),
            'payment_status'    => 'pending',
        ]);
        $casetype = explode(',',$request->input('case_type'));
        
        $annualAgreement = RequestAnnualAgreement::create([
            'user_id'               => $user->id,
            'service_request_id'    => $service_request->id,
            'company_name'          => $request->input('company_name'),
            'emirate_id'            => $request->input('emirate_id'),
            'license_type'          => $request->input('license_type'),
            'license_activity'      => $request->input('license_activity'),
            'industry'              => $request->input('industry'),
            'no_of_employees'       => $request->input('no_of_employees'),
            'case_type'             => $casetype,
            'no_of_calls'           => $request->input('no_of_calls'),
            'no_of_visits'          => $request->input('no_of_visits'),
            'no_of_installment'     => $request->input('no_of_installment'),
            'lawfirm'               => $request->input('lawfirm'),
        ]);

        $pageData = getPageDynamicContent('request_payment_success',$lang);
        $response = [
            'reference' => $service_request->reference_code,
            'message'   => $pageData['content']
        ];
        return response()->json([
            'status'    => true,
            'message'   => __('messages.request_submit_success'),
            'data'      => $response,
        ], 200);
    }

    public function requestLegalTranslation(Request $request){

        $validator = Validator::make($request->all(), [
            'priority_level'            => 'required',
            'document_language'         => 'required',
            'translation_language'      => 'required',
            'document_type'             => 'required',
            'document_sub_type'         => 'required',
            'receive_by'                => 'required',
            'no_of_pages'               => 'required',
            'memo'                      => 'nullable|array',
            'documents'                 => 'required|array',
            'additional_documents'      => 'nullable|array',
            'trade_license'             => 'nullable|array',
            'memo.*'                    => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:1024',
            'documents.*'               => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:2048',
            'additional_documents.*'    => 'file|mimes:pdf,jpg,jpeg,webp,png,svg|max:2048',
            'trade_license.*'           => 'file|mimes:pdf,jpg,jpeg,webp,png,svg|max:500',
        ], [
            'priority_level.required'       => __('messages.priority_level_required'),
            'document_language.required'    => __('messages.document_language_required'),
            'translation_language.required' => __('messages.translation_language_required'),
            'document_type.required'        => __('messages.document_type_required'),
            'document_sub_type.required'    => __('messages.document_sub_type_required'),
            'receive_by.required'           => __('messages.receive_by_required'),
            'no_of_pages.required'          => __('messages.no_of_pages_required'),
            'memo.*.file'                   => __('messages.memo_file_invalid'),
            'memo.*.mimes'                  => __('messages.memo_file_mimes'),
            'memo.*.max'                    => __('messages.memo_file_max'),
            'documents.*.file'              => __('messages.document_file_invalid'),
            'documents.*.mimes'             => __('messages.document_file_mimes'),
            'documents.*.max'               => __('messages.document_file_max'),
            'documents.required'            => __('messages.document_required'),
            'additional_documents.*.file'   => __('messages.additional_documents_invalid'),
            'additional_documents.*.mimes'  => __('messages.additional_documents_mimes'),
            'additional_documents.*.max'    => __('messages.additional_documents_max'),
            // 'trade_license.required'    => __('messages.trade_license_required'),
            'trade_license.*.file'          => __('messages.trade_license_file_invalid'),
            'trade_license.*.mimes'         => __('messages.trade_license_file_mimes'),
            'trade_license.*.max'           => __('messages.trade_license_file_max'),
        ]);

        if ($validator->fails()) {
            $message = implode(' ', $validator->errors()->all());

            return response()->json([
                'status'    => false,
                'message'   => $message,
            ], 200);
        }

        $lang       = $request->header('lang') ?? env('APP_LOCALE','en');
        $user       = $request->user();
        $service    = Service::where('slug', 'legal-translation')->firstOrFail();

        $referenceCode = ServiceRequest::generateReferenceCode($service);

        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'legal-translation',
            'reference_code'    => $referenceCode,
            'source'            => 'mob',
            'submitted_at'      => date('Y-m-d H:i:s'),
            'payment_status'    => 'pending',
        ]);

        $legalTranslation = RequestLegalTranslation::create([
            'user_id'               => $user->id,
            'service_request_id'    => $service_request->id,
            'priority_level'        => $request->input('priority_level'),
            'document_language'     => $request->input('document_language'),
            'translation_language'  => $request->input('translation_language'),
            'document_type'         => $request->input('document_type'),
            'document_sub_type'     => $request->input('document_sub_type'),
            'receive_by'            => $request->input('receive_by'),
            'no_of_pages'           => $request->input('no_of_pages'),
            'memo'                  => [],
            'documents'             => [],
            'additional_documents'  => [],
            'trade_license'         => [],
        ]);

        $requestFolder = "uploads/legal_translation/{$legalTranslation->id}/";

        $fileFields = [
            'memo'                  => 'memo',
            'documents'             => 'documents',
            'additional_documents'  => 'additional_documents',
            'trade_license'         => 'trade_license',
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

        $legalTranslation->update($filePaths);

        $from           = $request->input('document_language');
        $to             = $request->input('translation_language');
        $pages          = $request->input('no_of_pages') ?? 0;
        $totalAmount    = 0;

        $assignment = DefaultTranslatorAssignment::where([
                                    'from_language_id' => $from,
                                    'to_language_id'   => $to,
                                ])->first();

        if ($assignment) {
            $rate = TranslatorLanguageRate::where([
                                            'translator_id'     => $assignment->translator_id,
                                            'from_language_id'  => $from,
                                            'to_language_id'    => $to,
                                        ])->first();

            if ($rate) {
                $totalAmount = $rate->total_amount * $pages;
            }
        }

        if($totalAmount != 0){

        }

        $pageData = getPageDynamicContent('request_payment_success',$lang);
        $response = [
            'reference' => $service_request->reference_code,
            'message'   => $pageData['content']
        ];
        return response()->json([
            'status'    => true,
            'message'   => __('messages.request_submit_success'),
            'data'      => $response,
        ], 200);
    }

    public function calculateTranslationPrice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_language_id' => 'required|integer|exists:translation_languages,id',
            'to_language_id'   => 'required|integer|exists:translation_languages,id',
            'no_of_pages'      => 'required|integer|min:1',
        ],[
            'from_language_id.required' => __('messages.from_language_id_required'),
            'from_language_id.exists'   => __('messages.from_language_id_exists'),
            'to_language_id.required'   => __('messages.to_language_id_required'),
            'to_language_id.exists'     => __('messages.to_language_id_exists'),
            'no_of_pages.required'      => __('messages.no_of_pages_required'),
            'no_of_pages.integer'       => __('messages.no_of_pages_integer'),
            'no_of_pages.min'           => __('messages.no_of_pages_min'),
        ]);

        if ($validator->fails()) {
            $message = implode(' ', $validator->errors()->all());

            return response()->json([
                'status'    => false,
                'message'   => $message,
            ], 200);
        }

        $from   = $request->from_language_id;
        $to     = $request->to_language_id;
        $pages  = $request->no_of_pages;

        // Step 1: Get default translator
        $assignment = DefaultTranslatorAssignment::where([
            'from_language_id' => $from,
            'to_language_id'   => $to,
        ])->first();

        if (!$assignment) {
            return response()->json([
                'status'    => false,
                'message'   => __('messages.no_default_translators'),
            ], 200);
        }

        // Step 2: Get rate from translator_language_rates
        $rate = TranslatorLanguageRate::where([
            'translator_id'     => $assignment->translator_id,
            'from_language_id'  => $from,
            'to_language_id'    => $to,
        ])->first();

        if (!$rate) {
            return response()->json([
                'status'    => false,
                'message'   => __('messages.translator_rate_not_found'),
            ], 200);
        }

        // Step 3: Calculate
        $totalAmount = $rate->total_amount * $pages;
        $totalHours  = $rate->hours_per_page * $pages;

        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => [
                            'total_amount'      => $totalAmount,
                            'total_hours'       => $totalHours,
                            'amount_per_page'   => $rate->total_amount,
                            'hours_per_page'    => $rate->hours_per_page 
                        ]
        ],200);
    }

    public function paymentTest(){
        $amount = 200;
        $orderReference = '11--CCS-00001';
        $currency = env('APP_CURRENCY','AED');
        $customer = [
            'email' => 'jisha.tomsher@gmail.com',
            'name'  => 'Jisha P',
            'phone' => '971568650838'
        ];

        $order = createOrder($customer, $amount, $currency, $orderReference);
        if (!$order || !isset($order['reference'])) {
            return response()->json([
                'status' => false,
                'message' => 'Order creation failed'
            ], 200);
        }

        return response()->json([
            'status' => true,
            'message' => 'success',
            'data' => [
                'order_id' => $order['reference'], // Required by mobile SDK
                'merchant_reference' => $order['merchantOrderReference'] ?? null,
                'amount' => $order['amount']['value'] / 100, // AED format
                'currency' => $order['amount']['currencyCode'],
                // 'payment_url' => $order['_links']['payment']['href'] ?? null, // Optional
            ]
        ]);
    }

    public function confirmPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string'
        ]);

        $orderId = $request->order_id;
        $order = checkOrderStatus($orderId);

        if (!$order || ($order['state'] ?? null) !== 'CAPTURED') {
            return response()->json(['status' => false, 'message' => 'Payment not successful'], 200);
        }

        echo '<pre>';
        print_r($order);
        die;
        $paymentData = $order['_embedded']['payment'][0] ?? [];
        $amount = ($order['amount']['value'] ?? 0) / 100;

        // DB::beginTransaction();
        // try {
        //     $serviceRequest = ServiceRequest::create([
        //         'user_id' => auth()->id(),
        //         'service_id' => 1, // set accordingly
        //         'service_slug' => 'example-service',
        //         'reference_code' => Str::upper(Str::random(10)),
        //         'status' => 'pending',
        //         'payment_status' => 'paid',
        //         'payment_reference' => $paymentData['paymentReference'] ?? null,
        //         'amount' => $amount,
        //         'paid_at' => now(),
        //         'submitted_at' => now(),
        //         'source' => 'mobile',
        //     ]);

        //     DB::commit();

        //     return response()->json([
        //         'success' => true,
        //         'message' => 'Payment confirmed',
        //         'data' => $serviceRequest
        //     ]);
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     \Log::error('Service request creation failed', ['error' => $e->getMessage()]);
        //     return response()->json(['success' => false, 'message' => 'Something went wrong'], 500);
        // }
    }

    public function test(Request $request){

        print_r($request->all());

    }
}
