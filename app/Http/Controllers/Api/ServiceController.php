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

class ServiceController extends Controller
{
    public function getCourtCaseFormData(Request $request){
        $lang = $request->header('lang') ?? env('APP_LOCALE','en'); // default to English 
        
        $dropdowns = Dropdown::with([
                        'options' => function ($q) {
                            $q->where('status', 'active')->orderBy('sort_order');
                        },
                        'options.translations' => function ($q) use ($lang) {
                            $q->whereIn('language_code', [$lang, 'en']);
                        }
                    ])->whereIn('slug', ['case_type', 'you_represent'])->get()->keyBy('slug');
       
        // Transform each dropdown
        $response = [];
        $emirates = Emirate::where('status',1)->orderBy('id')->get();

        $response['emirates'] = $emirates->map(function ($emirate) use($lang) {
                return [
                    'id' => $emirate->id,
                    'value' => $emirate->getTranslation('name',$lang),
                ];
        });

        foreach ($dropdowns as $slug => $dropdown) {
            $response[$slug] = $dropdown->options->map(function ($option) use ($lang){
                return [
                    'id' => $option->id,
                    'value' => $option->getTranslation('name',$lang),
                ];
            });
        }

        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $response,
        ]);
    }

    public function getCriminalComplaintFormData(Request $request){
        $lang = $request->header('lang') ?? env('APP_LOCALE','en'); // default to English 
        
        $dropdowns = Dropdown::with([
                        'options' => function ($q) {
                            $q->where('status', 'active')->orderBy('sort_order');
                        },
                        'options.translations' => function ($q) use ($lang) {
                            $q->whereIn('language_code', [$lang, 'en']);
                        }
                    ])->whereIn('slug', ['case_type', 'you_represent'])->get()->keyBy('slug');
       
        // Transform each dropdown
        $response = [];
        $emirates = Emirate::where('status',1)->orderBy('id')->get();

        $response['emirates'] = $emirates->map(function ($emirate) use($lang) {
                return [
                    'id' => $emirate->id,
                    'value' => $emirate->getTranslation('name',$lang),
                ];
        });

        foreach ($dropdowns as $slug => $dropdown) {
            $response[$slug] = $dropdown->options->map(function ($option) use ($lang){
                return [
                    'id' => $option->id,
                    'value' => $option->getTranslation('name',$lang),
                ];
            });
        }

        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $response,
        ]);
    }

    public function getPowerOfAttorneyFormData(Request $request){
        $lang = $request->header('lang') ?? env('APP_LOCALE','en'); // default to English 
        
        $dropdowns = Dropdown::with([
                        'options' => function ($q) {
                            $q->where('status', 'active')->orderBy('sort_order');
                        },
                        'options.translations' => function ($q) use ($lang) {
                            $q->whereIn('language_code', [$lang, 'en']);
                        }
                    ])->whereIn('slug', ['poa_type', 'poa_relationships'])->get()->keyBy('slug');
       
        // Transform each dropdown
        $response = [];
        $emirates = Emirate::where('status',1)->orderBy('id')->get();

        $response['emirates'] = $emirates->map(function ($emirate) use($lang) {
                return [
                    'id' => $emirate->id,
                    'value' => $emirate->getTranslation('name',$lang),
                ];
        });

        foreach ($dropdowns as $slug => $dropdown) {
            $response[$slug] = $dropdown->options->map(function ($option) use ($lang){
                return [
                    'id' => $option->id,
                    'value' => $option->getTranslation('name',$lang),
                ];
            });
        }

        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $response,
        ]);
    }

    public function getLastWillFormData(Request $request){
        $lang = $request->header('lang') ?? env('APP_LOCALE','en'); // default to English 
        
        $dropdowns = Dropdown::with([
                        'options' => function ($q) {
                            $q->where('status', 'active')->orderBy('sort_order');
                        },
                        'options.translations' => function ($q) use ($lang) {
                            $q->whereIn('language_code', [$lang, 'en']);
                        }
                    ])->whereIn('slug', ['you_represent'])->get()->keyBy('slug');
       
        // Transform each dropdown
        $response = [];
        $emirates = Emirate::where('status',1)->orderBy('id')->get();

        $response['emirates'] = $emirates->map(function ($emirate) use($lang) {
                return [
                    'id' => $emirate->id,
                    'value' => $emirate->getTranslation('name',$lang),
                ];
        });

        foreach ($dropdowns as $slug => $dropdown) {
            $response[$slug] = $dropdown->options->map(function ($option) use ($lang){
                return [
                    'id' => $option->id,
                    'value' => $option->getTranslation('name',$lang),
                ];
            });
        }

        $countries = Country::where('status',1)->orderBy('id')->get();

        $response['nationality'] = $countries->map(function ($country) use($lang) {
                return [
                    'id' => $country->id,
                    'value' => $country->getTranslation('name',$lang),
                ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $response,
        ]);
    }

    public function getMemoWritingFormData(Request $request){
        $lang = $request->header('lang') ?? env('APP_LOCALE','en'); // default to English 
        
        $dropdowns = Dropdown::with([
                        'options' => function ($q) {
                            $q->where('status', 'active')->orderBy('sort_order');
                        },
                        'options.translations' => function ($q) use ($lang) {
                            $q->whereIn('language_code', [$lang, 'en']);
                        }
                    ])->whereIn('slug', ['case_type', 'you_represent'])->get()->keyBy('slug');
       
        // Transform each dropdown
        $response = [];
        $emirates = Emirate::where('status',1)->orderBy('id')->get();

        $response['emirates'] = $emirates->map(function ($emirate) use($lang) {
                return [
                    'id' => $emirate->id,
                    'value' => $emirate->getTranslation('name',$lang),
                ];
        });

        foreach ($dropdowns as $slug => $dropdown) {
            $response[$slug] = $dropdown->options->map(function ($option) use ($lang){
                return [
                    'id' => $option->id,
                    'value' => $option->getTranslation('name',$lang),
                ];
            });
        }

        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $response,
        ]);
    }

    public function getExpertReportsFormData(Request $request){
        $lang = $request->header('lang') ?? env('APP_LOCALE','en'); // default to English 
        
        $dropdowns = Dropdown::with([
                        'options' => function ($q) {
                            $q->where('status', 'active')->orderBy('sort_order');
                        },
                        'options.translations' => function ($q) use ($lang) {
                            $q->whereIn('language_code', [$lang, 'en']);
                        }
                    ])->whereIn('slug', ['expert_report_type', 'expert_report_languages'])->get()->keyBy('slug');
       
        // Transform each dropdown
        $response = [];
        $emirates = Emirate::where('status',1)->orderBy('id')->get();

        $response['emirates'] = $emirates->map(function ($emirate) use($lang) {
                return [
                    'id' => $emirate->id,
                    'value' => $emirate->getTranslation('name',$lang),
                ];
        });

        foreach ($dropdowns as $slug => $dropdown) {
            $response[$slug] = $dropdown->options->map(function ($option) use ($lang){
                return [
                    'id' => $option->id,
                    'value' => $option->getTranslation('name',$lang),
                ];
            });
        }

        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $response,
        ]);
    }

    public function getContractsDraftingFormData(Request $request){
        $lang = $request->header('lang') ?? env('APP_LOCALE','en'); // default to English 
        
        $dropdowns = Dropdown::with([
                        'options' => function ($q) {
                            $q->where('status', 'active')->orderBy('sort_order');
                        },
                        'options.translations' => function ($q) use ($lang) {
                            $q->whereIn('language_code', [$lang, 'en']);
                        }
                    ])->whereIn('slug', ['contract_languages', 'industries'])->get()->keyBy('slug');
       
        // Transform each dropdown
        $response = [];
        $emirates = Emirate::where('status',1)->orderBy('id')->get();

        $response['emirates'] = $emirates->map(function ($emirate) use($lang) {
                return [
                    'id' => $emirate->id,
                    'value' => $emirate->getTranslation('name',$lang),
                ];
        });

        $contractTypes = ContractType::where('status',1)->whereNull('parent_id')->orderBy('sort_order')->get();

        $response['contract_type'] = $contractTypes->map(function ($ctype) use($lang) {
                return [
                    'id' => $ctype->id,
                    'value' => $ctype->getTranslation('name',$lang),
                ];
        });

        foreach ($dropdowns as $slug => $dropdown) {
            $response[$slug] = $dropdown->options->map(function ($option) use ($lang){
                return [
                    'id' => $option->id,
                    'value' => $option->getTranslation('name',$lang),
                ];
            });
        }

        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $response,
        ]);
    }

    public function getEscrowAccountsFormData(Request $request){
        $lang = $request->header('lang') ?? env('APP_LOCALE','en'); // default to English 
        
        $dropdowns = Dropdown::with([
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
            $response[$slug] = $dropdown->options->map(function ($option) use ($lang){
                return [
                    'id' => $option->id,
                    'value' => $option->getTranslation('name',$lang),
                ];
            });
        }

        $countries = Country::where('status',1)->orderBy('id')->get();

        $response['company_origin'] = $countries->map(function ($country) use($lang) {
                return [
                    'id' => $country->id,
                    'value' => $country->getTranslation('name',$lang),
                ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $response,
        ]);
    }
    
    public function getDebtsCollectionFormData(Request $request){
        $lang = $request->header('lang') ?? env('APP_LOCALE','en'); // default to English 
        
        $dropdowns = Dropdown::with([
                        'options' => function ($q) {
                            $q->where('status', 'active')->orderBy('sort_order');
                        },
                        'options.translations' => function ($q) use ($lang) {
                            $q->whereIn('language_code', [$lang, 'en']);
                        }
                    ])->whereIn('slug', ['debt_type','debt_category'])->get()->keyBy('slug');
       
        // Transform each dropdown
        $response = [];
        $emirates = Emirate::where('status',1)->orderBy('id')->get();

        $response['emirates'] = $emirates->map(function ($emirate) use($lang) {
                return [
                    'id' => $emirate->id,
                    'value' => $emirate->getTranslation('name',$lang),
                ];
        });
       
        foreach ($dropdowns as $slug => $dropdown) {
            $response[$slug] = $dropdown->options->map(function ($option) use ($lang){
                return [
                    'id' => $option->id,
                    'value' => $option->getTranslation('name',$lang),
                ];
            });
        }

        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $response,
        ]);
    }

    public function getCompanySetupFormData(Request $request){
        $lang = $request->header('lang') ?? env('APP_LOCALE','en'); // default to English 
        
        $dropdowns = Dropdown::with([
                        'options' => function ($q) {
                            $q->where('status', 'active')->orderBy('sort_order');
                        },
                        'options.translations' => function ($q) use ($lang) {
                            $q->whereIn('language_code', [$lang, 'en']);
                        }
                    ])->whereIn('slug', ['company_type','industries'])->get()->keyBy('slug');
       
        // Transform each dropdown
        $response = [];
        $emirates = Emirate::where('status',1)->orderBy('id')->get();

        $response['emirates'] = $emirates->map(function ($emirate) use($lang) {
                return [
                    'id' => $emirate->id,
                    'value' => $emirate->getTranslation('name',$lang),
                ];
        });

        $licenseTypes = LicenseType::where('status',1)->whereNull('parent_id')->orderBy('sort_order')->get();

        $response['license_type'] = $licenseTypes->map(function ($ctype) use($lang) {
                return [
                    'id' => $ctype->id,
                    'value' => $ctype->getTranslation('name',$lang),
                ];
        });
       
        foreach ($dropdowns as $slug => $dropdown) {
            $response[$slug] = $dropdown->options->map(function ($option) use ($lang){
                return [
                    'id' => $option->id,
                    'value' => $option->getTranslation('name',$lang),
                ];
            });
        }

        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $response,
        ]);
    }
    
    public function getSubContractTypes(Request $request)
    {
        $lang = $request->header('lang') ?? env('APP_LOCALE', 'en');

        $parentId = $request->contract_id ?? NULL;
        $subTypes = ContractType::where('status', 1)
            ->where('parent_id', $parentId)
            ->orderBy('sort_order')
            ->get();

        $response = $subTypes->map(function ($subType) use ($lang) {
            return [
                'id' => $subType->id,
                'parent_id' => $subType->parent_id,
                'value' => $subType->getTranslation('name', $lang),
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $response,
        ]);
    }

    public function getZones(Request $request)
    {
        $lang = $request->header('lang') ?? env('APP_LOCALE', 'en');

        $parentId = $request->emirate_id ?? NULL;
        $subTypes = FreeZone::where('status', 1)
                            ->where('emirate_id', $parentId)
                            ->orderBy('sort_order')
                            ->get();

        $response = $subTypes->map(function ($subType) use ($lang) {
            return [
                'id' => $subType->id,
                'emirate_id' => $subType->emirate_id,
                'value' => $subType->getTranslation('name', $lang),
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $response,
        ]);
    }

    public function getLicenseActivities(Request $request)
    {
        $lang = $request->header('lang') ?? env('APP_LOCALE', 'en');

        $parentId = $request->license_type_id ?? NULL;
        $subTypes = LicenseType::where('status', 1)
                            ->where('parent_id', $parentId)
                            ->orderBy('sort_order')
                            ->get();

        $response = $subTypes->map(function ($subType) use ($lang) {
            return [
                'id' => $subType->id,
                'parent_id' => $subType->parent_id,
                'value' => $subType->getTranslation('name', $lang),
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $response,
        ]);
    }
    
}
