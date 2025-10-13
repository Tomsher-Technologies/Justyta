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
use App\Models\ExpertReportPricing;
use App\Models\ConsultationDuration;
use App\Models\Vendor;
use App\Models\AnnualRetainerBaseFee;
use App\Models\AnnualAgreementInstallment;
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
use App\Models\RequestSubmissionPricing;
use App\Models\ServiceRequestTimeline;
use App\Models\TranslationAssignmentHistory;
use App\Models\Consultation;
use App\Models\ConsultationAssignment;
use App\Models\ConsultationPayment;
use App\Notifications\ServiceRequestStatusChanged;
use App\Models\Lawyer;
use Illuminate\Support\Facades\Http;
use App\Services\ZoomService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Notifications\ServiceRequestSubmitted;
use App\Services\ServiceRequestFileService;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Carbon\Carbon;

class ServiceRequestController extends Controller
{
    public function getEmirates(Request $request)
    {
        $lang           = $request->header('lang') ?? env('APP_LOCALE', 'en');

        $litigation_type   = $request->litigation_type ?? NULL;
        $litigation_place   = $request->litigation_place ?? NULL;
        $service            = $request->service ?? NULL;

        $emirates   = Emirate::whereHas('emirate_litigations', function ($q) use ($service, $litigation_type) {
            $q->where('slug', $service)->where('status', 1);
            if (in_array($service, ['court-case-submission', 'criminal-complaint', 'expert-report', 'memo-writing', 'online-live-consultancy', 'request-submission'])) {
                if ($litigation_type === 'federal') {
                    $q->where('is_federal', 1);
                } elseif ($litigation_type === 'local') {
                    $q->where('is_local', 1);
                }
            }
        })->get();

        $response['emirates'] = $emirates->map(function ($emirate) use ($lang) {
            return [
                'id'    => $emirate->id,
                'value' => $emirate->getTranslation('name', $lang),
            ];
        });

        if (in_array($service, ['court-case-submission', 'memo-writing', 'online-live-consultancy', 'annual-retainer-agreement'])) {
            $litigation_place = 'court';
        } elseif ($service == 'criminal-complaint') {
            $litigation_place = 'public_prosecution';
        }

        $response['caseTypes'] = [];
        if ($litigation_place) {
            $response['caseTypes'] = getCaseTypes($litigation_type, $litigation_place, $lang);
        }

        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $response,
        ], 200);
    }

    public function getCaseTypes(Request $request)
    {
        $lang           = $request->header('lang') ?? env('APP_LOCALE', 'en');

        $litigation_type   = $request->litigation_type ?? NULL;
        $litigation_place   = $request->litigation_place ?? NULL;
        $service            = $request->service ?? NULL;

        if (in_array($service, ['court-case-submission', 'memo-writing'])) {
            $litigation_place = 'court';
        } else {
            $litigation_place = 'public_prosecution';
        }

        $caseTypes = getCaseTypes($litigation_type, $litigation_place, $lang);

        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $caseTypes,
        ], 200);
    }

    public function getOnlineConsultationPrice(Request $request){
        $consultant_type    = $request->query('consultant_type') ?? NULL;
        $duration           = $request->query('duration') ?? NULL;

        if ($consultant_type === NULL || $duration === NULL) {
            return response()->json([
                'status'    => true,
                'message'   => 'Success',
                'data'      => [
                                'admin_fee' => 0,
                                'govt_fee' => 0,
                                'tax'       => 0,
                                'total'     => 0,
                            ]
            ], 200);
        }

        $base = ConsultationDuration::where('type', $consultant_type)
                                    ->where('duration', $duration)
                                    ->where('status', 1)
                                    ->first();
        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => [
                            'admin_fee' => 0,
                            'govt_fee' => 0,
                            'tax'       => 0,
                            'total'     => (float)($base->amount ?? 0),
                        ]
        ], 200);
    }
        
    public function getAnnualAgreementPrice(Request $request)
    {
        $lang           = $request->header('lang') ?? env('APP_LOCALE', 'en');

        $calls          = $request->query('calls');
        $visits         = $request->query('visits');
        $installments   = $request->query('installments');

        if (!in_array($calls, [1, 2, 3, 4, 5]) || !in_array($visits, [0, 1, 2, 3, 4]) || !in_array($installments, [1, 2, 4])) {
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

    public function getRequestTypes(Request $request)
    {
        $lang               = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $litigation_place   = $request->litigation_place ?? NULL;
        $litigation_type    = $request->litigation_type ?? NULL;

        if ($litigation_type === NULL || $litigation_place === NULL) {
            return response()->json([
                'status'    => false,
                'message'   => __('messages.fill_all_fields'),
                'data'      => [],
            ], 200);
        }

        $requestTypes = getRequestTypes($litigation_type, $litigation_place, $lang);

        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $requestTypes,
        ], 200);
    }

    public function getRequestTitles(Request $request)
    {
        $lang               = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $request_type       = $request->request_type ?? NULL;

        $requestTitles = getRequestTitles($request_type, $lang);

        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $requestTitles,
        ], 200);
    }

    public function getSubDocumentTypes(Request $request)
    {
        $lang       = app()->getLocale() ?? env('APP_LOCALE', 'en');

        $doc_type   = $request->document_type ?? NULL;

        $docTypes   = [];
        if ($doc_type) {
            $docTypes = DocumentType::with('translations')->where('status', 1)
                ->where('parent_id', $doc_type)
                ->orderBy('sort_order')
                ->get();
        }

        $response = [];
        if (!empty($docTypes)) {
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

    public function getSubContractTypes($id)
    {
        $lang       = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $parentId   = $id ?? NULL;
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

        return response()->json($response);
    }

    public function calculateTranslationPrice(Request $request)
    {
        $from           = $request->from_language_id;
        $to             = $request->to_language_id;
        $pages          = $request->no_of_pages;
        $priority       = $request->priority ?? null;
        $doc_type       = $request->doc_type;
        $subdoc_type    = $request->doc_sub_type;
        $receive_by     = $request->receive_by ?? null;

        $assignment = DefaultTranslatorAssignment::where([
            'from_language_id' => $from,
            'to_language_id'   => $to,
        ])->first();

        if (!$assignment) {
            return response()->json([
                'status'    => false,
                'message'   => __('messages.translation_not_available'),
            ], 200);
        }

        $rate = TranslatorLanguageRate::with(['deliveries' => function ($q) use ($priority, $receive_by) {
            $q->where('priority_type', $priority)
                ->where('delivery_type', $receive_by);
        }])
            ->where('translator_id', $assignment->translator_id)
            ->where('from_language_id', $from)
            ->where('to_language_id', $to)
            ->where('doc_type_id', $doc_type)
            ->where('doc_subtype_id', $subdoc_type)
            ->where('status', 1)
            ->first();

        if (!$rate) {
            return response()->json([
                'status'    => false,
                'message'   => __('messages.translation_not_available'),
            ], 200);
        }

        if ($priority === 'normal') {
            if ($pages <= 10) {
                $totalHours = $rate->normal_hours_1_10;
            } elseif ($pages <= 20) {
                $totalHours = $rate->normal_hours_11_20;
            } elseif ($pages <= 30) {
                $totalHours = $rate->normal_hours_21_30;
            } elseif ($pages <= 50) {
                $totalHours = $rate->normal_hours_31_50;
            } else {
                $totalHours = $rate->normal_hours_above_50;
            }
        } else {
            if ($pages <= 10) {
                $totalHours = $rate->urgent_hours_1_10;
            } elseif ($pages <= 20) {
                $totalHours = $rate->urgent_hours_11_20;
            } elseif ($pages <= 30) {
                $totalHours = $rate->urgent_hours_21_30;
            } elseif ($pages <= 50) {
                $totalHours = $rate->urgent_hours_31_50;
            } else {
                $totalHours = $rate->urgent_hours_above_50;
            }
        }

        $delivery = $rate->deliveries->first();

        $admin_amount = $delivery->admin_amount * $pages;
        $translator_amount = $delivery->translator_amount * $pages;

        $totalAmountNoTax = ($admin_amount + $translator_amount + $delivery->delivery_amount);

        $tax = ($totalAmountNoTax / 100) * 5;

        $totalAmount = $totalAmountNoTax + $tax;

        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => [
                'total_amount'      => $totalAmount,
                'total_hours'       => $totalHours
            ]
        ], 200);
    }

    public function getLicenseActivities($id)
    {
        $lang       = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $parentId   = $id ?? NULL;
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

        return response()->json($response);
    }

    public function getZones($id)
    {
        $lang       = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $parentId   = $id ?? NULL;
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

        return response()->json($response);
    }

    public function showForm($slug)
    {
        $lang           = app()->getLocale() ?? env('APP_LOCALE', 'en');
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
                ])->whereIn('slug', ['you_represent'])->get()->keyBy('slug');

                foreach ($dropdowns as $slug => $dropdown) {
                    $dropdownData[$slug] = $dropdown->options->map(function ($option) use ($lang) {
                        return [
                            'id'    => $option->id,
                            'value' => $option->getTranslation('name', $lang),
                        ];
                    });
                }

                return view('frontend.user.service-requests.court_case', ['service' => $service, 'dropdownData' => $dropdownData, 'lang' => $lang]);

            case 'criminal-complaint':
                $dropdowns  = Dropdown::with([
                    'options' => function ($q) {
                        $q->where('status', 'active')->orderBy('sort_order');
                    },
                    'options.translations' => function ($q) use ($lang) {
                        $q->whereIn('language_code', [$lang, 'en']);
                    }
                ])->whereIn('slug', ['you_represent'])->get()->keyBy('slug');

                foreach ($dropdowns as $slug => $dropdown) {
                    $dropdownData[$slug] = $dropdown->options->map(function ($option) use ($lang) {
                        return [
                            'id'    => $option->id,
                            'value' => $option->getTranslation('name', $lang),
                        ];
                    });
                }

                return view('frontend.user.service-requests.criminal_complaint', ['service' => $service, 'dropdownData' => $dropdownData, 'lang' => $lang]);

            case 'power-of-attorney':
                $dropdowns  = Dropdown::with([
                    'options' => function ($q) {
                        $q->where('status', 'active')->orderBy('sort_order');
                    },
                    'options.translations' => function ($q) use ($lang) {
                        $q->whereIn('language_code', [$lang, 'en']);
                    }
                ])->whereIn('slug', ['poa_type', 'poa_relationships'])->get()->keyBy('slug');

                foreach ($dropdowns as $slug => $dropdown) {
                    $dropdownData[$slug] = $dropdown->options->map(function ($option) use ($lang) {
                        return [
                            'id'    => $option->id,
                            'value' => $option->getTranslation('name', $lang),
                        ];
                    });
                }

                $emirates   = Emirate::whereHas('emirate_litigations', function ($q) {
                    $q->where('slug', 'power-of-attorney')->where('status', 1);
                })->get();

                $dropdownData['emirates'] = $emirates->map(function ($emirate) use ($lang) {
                    return [
                        'id'    => $emirate->id,
                        'value' => $emirate->getTranslation('name', $lang),
                    ];
                });

                return view('frontend.user.service-requests.power_of_attorney', ['service' => $service, 'dropdownData' => $dropdownData, 'lang' => $lang]);

            case 'last-will-and-testament':

                $dropdowns  = Dropdown::with([
                    'options' => function ($q) {
                        $q->where('status', 'active')->orderBy('sort_order');
                    },
                    'options.translations' => function ($q) use ($lang) {
                        $q->whereIn('language_code', [$lang, 'en']);
                    }
                ])->whereIn('slug', ['you_represent', 'religion'])->get()->keyBy('slug');

                foreach ($dropdowns as $slug => $dropdown) {
                    $dropdownData[$slug] = $dropdown->options->map(function ($option) use ($lang) {
                        return [
                            'id'    => $option->id,
                            'value' => $option->getTranslation('name', $lang),
                        ];
                    });
                }
                $emirates   = Emirate::whereHas('emirate_litigations', function ($q) {
                    $q->where('slug', 'last-will-and-testament')->where('status', 1);
                })->get();

                $dropdownData['emirates'] = $emirates->map(function ($emirate) use ($lang) {
                    return [
                        'id'    => $emirate->id,
                        'value' => $emirate->getTranslation('name', $lang),
                    ];
                });

                $countries = Country::where('status', 1)->orderBy('id')->get();

                $dropdownData['nationality'] = $countries->map(function ($country) use ($lang) {
                    return [
                        'id'    => $country->id,
                        'value' => $country->getTranslation('name', $lang),
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
                    $dropdownData[$slug] = $dropdown->options->map(function ($option) use ($lang) {
                        return [
                            'id'    => $option->id,
                            'value' => $option->getTranslation('name', $lang),
                        ];
                    });
                }

                $dropdownData['emirates'] = $emirates;

                return view('frontend.user.service-requests.memo_writing', ['service' => $service, 'dropdownData' => $dropdownData, 'lang' => $lang]);

            case 'expert-report':
                $dropdowns  = Dropdown::with([
                    'options' => function ($q) {
                        $q->where('status', 'active')->orderBy('sort_order');
                    },
                    'options.translations' => function ($q) use ($lang) {
                        $q->whereIn('language_code', [$lang, 'en']);
                    }
                ])->whereIn('slug', ['expert_report_type', 'expert_report_languages'])->get()->keyBy('slug');

                foreach ($dropdowns as $slug => $dropdown) {
                    $dropdownData[$slug] = $dropdown->options->map(function ($option) use ($lang) {
                        return [
                            'id'    => $option->id,
                            'value' => $option->getTranslation('name', $lang),
                        ];
                    });
                }

                $dropdownData['emirates'] = $emirates;

                $service    = Service::where('slug', 'expert-report')->firstOrFail();

                $dropdownData['payment'] = [
                    'service_fee'       => $service->service_fee ?? 0,
                    'govt_fee'          => $service->govt_fee ?? 0,
                    'tax'               => $service->tax ?? 0,
                    'total_amount'      => $service->total_amount ?? 0
                ];

                return view('frontend.user.service-requests.expert_report', ['service' => $service, 'dropdownData' => $dropdownData, 'lang' => $lang]);

            case 'contract-drafting':
                $dropdowns  = Dropdown::with([
                    'options' => function ($q) {
                        $q->where('status', 'active')->orderBy('sort_order');
                    },
                    'options.translations' => function ($q) use ($lang) {
                        $q->whereIn('language_code', [$lang, 'en']);
                    }
                ])->whereIn('slug', ['contract_languages', 'industries'])->get()->keyBy('slug');

                foreach ($dropdowns as $slug => $dropdown) {
                    $dropdownData[$slug] = $dropdown->options->map(function ($option) use ($lang) {
                        return [
                            'id'    => $option->id,
                            'value' => $option->getTranslation('name', $lang),
                        ];
                    });
                }

                $emirates   = Emirate::whereHas('emirate_litigations', function ($q) {
                    $q->where('slug', 'contract-drafting')->where('status', 1);
                })->get();

                $dropdownData['emirates'] = $emirates->map(function ($emirate) use ($lang) {
                    return [
                        'id'    => $emirate->id,
                        'value' => $emirate->getTranslation('name', $lang),
                    ];
                });

                $contractTypes = ContractType::where('status', 1)->whereNull('parent_id')->orderBy('sort_order')->get();

                $dropdownData['contract_type'] = $contractTypes->map(function ($ctype) use ($lang) {
                    return [
                        'id'    => $ctype->id,
                        'value' => $ctype->getTranslation('name', $lang),
                    ];
                });

                return view('frontend.user.service-requests.contract_drafting', ['service' => $service, 'dropdownData' => $dropdownData, 'lang' => $lang]);
            case 'company-setup':
                $dropdowns  = Dropdown::with([
                    'options' => function ($q) {
                        $q->where('status', 'active')->orderBy('sort_order');
                    },
                    'options.translations' => function ($q) use ($lang) {
                        $q->whereIn('language_code', [$lang, 'en']);
                    }
                ])->whereIn('slug', ['company_type', 'industries'])->get()->keyBy('slug');

                foreach ($dropdowns as $slug => $dropdown) {
                    $dropdownData[$slug] = $dropdown->options->map(function ($option) use ($lang) {
                        return [
                            'id'    => $option->id,
                            'value' => $option->getTranslation('name', $lang),
                        ];
                    });
                }

                $emirates   = Emirate::whereHas('emirate_litigations', function ($q) {
                    $q->where('slug', 'company-setup')->where('status', 1);
                })->get();

                $dropdownData['emirates'] = $emirates->map(function ($emirate) use ($lang) {
                    return [
                        'id'    => $emirate->id,
                        'value' => $emirate->getTranslation('name', $lang),
                    ];
                });

                $licenseTypes = LicenseType::where('status', 1)->whereNull('parent_id')->orderBy('sort_order')->get();

                $dropdownData['license_type'] = $licenseTypes->map(function ($ctype) use ($lang) {
                    return [
                        'id'    => $ctype->id,
                        'value' => $ctype->getTranslation('name', $lang),
                    ];
                });

                return view('frontend.user.service-requests.company_setup', ['service' => $service, 'dropdownData' => $dropdownData, 'lang' => $lang]);
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
                    $dropdownData[$slug] = $dropdown->options->map(function ($option) use ($lang) {
                        return [
                            'id'    => $option->id,
                            'value' => $option->getTranslation('name', $lang),
                        ];
                    });
                }

                $countries = Country::where('status', 1)->orderBy('id')->get();

                $dropdownData['company_origin'] = $countries->map(function ($country) use ($lang) {
                    return [
                        'id'    => $country->id,
                        'value' => $country->getTranslation('name', $lang),
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
                ])->whereIn('slug', ['debt_type', 'debt_category'])->get()->keyBy('slug');

                foreach ($dropdowns as $slug => $dropdown) {
                    $dropdownData[$slug] = $dropdown->options->map(function ($option) use ($lang) {
                        return [
                            'id'    => $option->id,
                            'value' => $option->getTranslation('name', $lang),
                        ];
                    });
                }

                $emirates   = Emirate::whereHas('emirate_litigations', function ($q) {
                    $q->where('slug', 'debts-collection')->where('status', 1);
                })->get();

                $dropdownData['emirates'] = $emirates->map(function ($emirate) use ($lang) {
                    return [
                        'id'    => $emirate->id,
                        'value' => $emirate->getTranslation('name', $lang),
                    ];
                });

                return view('frontend.user.service-requests.debts_collection', ['service' => $service, 'dropdownData' => $dropdownData, 'lang' => $lang]);

            case 'online-live-consultancy':

                break;

            case 'request-submission':
                $dropdowns  = Dropdown::with([
                    'options' => function ($q) {
                        $q->where('status', 'active')->orderBy('sort_order');
                    },
                    'options.translations' => function ($q) use ($lang) {
                        $q->whereIn('language_code', [$lang, 'en']);
                    }
                ])->whereIn('slug', ['case_type'])->get()->keyBy('slug');

                foreach ($dropdowns as $slug => $dropdown) {
                    $dropdownData[$slug] = $dropdown->options->map(function ($option) use ($lang) {
                        return [
                            'id'    => $option->id,
                            'value' => $option->getTranslation('name', $lang),
                        ];
                    });
                }

                $form_info = getPageDynamicContent('request_submission_forminfo', $lang);

                $dropdownData['form_info'] = $form_info;

                $service    = Service::where('slug', 'request-submission')->firstOrFail();

                return view('frontend.user.service-requests.request_submission', ['service' => $service, 'dropdownData' => $dropdownData, 'lang' => $lang]);

            case 'legal-translation':
                $transLanguages = TranslationLanguage::where('status', 1)->orderBy('sort_order')->get();

                $dropdownData['document_language'] = $transLanguages->map(function ($tlang) use ($lang) {
                    return [
                        'id'    => $tlang->id,
                        'value' => $tlang->getTranslation('name', $lang),
                    ];
                });

                $translationLanguages = $transLanguages->filter(function ($lang) {
                    return in_array($lang->lang_code, ['en', 'ar']);
                });

                $dropdownData['translation_language'] = $translationLanguages->map(function ($tlang) use ($lang) {
                    return [
                        'id'    => $tlang->id,
                        'value' => $tlang->getTranslation('name', $lang),
                    ];
                })->values();

                $documentTypes = DocumentType::with('translations')->where('status', 1)
                    ->whereNull('parent_id')
                    ->orderBy('sort_order')
                    ->get();

                $dropdownData['document_type'] = $documentTypes->map(function ($doc) use ($lang) {
                    return [
                        'id'    => $doc->id,
                        'value' => $doc->getTranslation('name', $lang),
                    ];
                });
                $form_info = getPageDynamicContent('translation_calculator_page', $lang);

                $dropdownData['form_info'] = $form_info;

                return view('frontend.user.service-requests.legal_translation', ['service' => $service, 'dropdownData' => $dropdownData, 'lang' => $lang]);

            case 'annual-retainer-agreement':
                $dropdowns  = Dropdown::with([
                    'options' => function ($q) {
                        $q->where('status', 'active')->orderBy('sort_order');
                    },
                    'options.translations' => function ($q) use ($lang) {
                        $q->whereIn('language_code', [$lang, 'en']);
                    }
                ])->whereIn('slug', ['industries', 'no_of_employees'])->get()->keyBy('slug');

                foreach ($dropdowns as $slug => $dropdown) {
                    $dropdownData[$slug] = $dropdown->options->map(function ($option) use ($lang) {
                        return [
                            'id'    => $option->id,
                            'value' => $option->getTranslation('name', $lang),
                        ];
                    });
                }

                $emirates   = Emirate::whereHas('emirate_litigations', function ($q) {
                    $q->where('slug', 'annual-retainer-agreement')->where('status', 1);
                })->get();

                $dropdownData['emirates'] = $emirates->map(function ($emirate) use ($lang) {
                    return [
                        'id'    => $emirate->id,
                        'value' => $emirate->getTranslation('name', $lang),
                    ];
                });

                $dropdownData['case_type'] = getCaseTypesValue('local', 'court', $lang);

                $licenseTypes = LicenseType::where('status', 1)->whereNull('parent_id')->orderBy('sort_order')->get();

                $dropdownData['license_type'] = $licenseTypes->map(function ($ctype) use ($lang) {
                    return [
                        'id'    => $ctype->id,
                        'value' => $ctype->getTranslation('name', $lang),
                    ];
                });

                $dropdownData['calls']          = [1, 2, 3, 4, 5];
                $dropdownData['visits']         = [0, 1, 2, 3, 4];
                $dropdownData['installments']   = [1, 2, 4];

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

                $dropdownData['law_firms'] = $lawFirms->map(function ($lawfirm) use ($lang) {
                    return [
                        'id'    => $lawfirm->id,
                        'value' => $lawfirm->getTranslation('law_firm_name', $lang),
                    ];
                });
                $form_info = getPageDynamicContent('company_retainership', $lang);

                $dropdownData['form_info'] = $form_info;

                return view('frontend.user.service-requests.annual_agreement', ['service' => $service, 'dropdownData' => $dropdownData, 'lang' => $lang]);

            case 'immigration-requests':
                $dropdowns  = Dropdown::with([
                    'options' => function ($q) {
                        $q->where('status', 'active')->orderBy('sort_order');
                    },
                    'options.translations' => function ($q) use ($lang) {
                        $q->whereIn('language_code', [$lang, 'en']);
                    }
                ])->whereIn('slug', ['immigration_positions', 'residency_status', 'immigration_type'])->get()->keyBy('slug');

                foreach ($dropdowns as $slug => $dropdown) {
                    $dropdownData[$slug] = $dropdown->options->map(function ($option) use ($lang) {
                        return [
                            'id'    => $option->id,
                            'value' => $option->getTranslation('name', $lang),
                        ];
                    });
                }

                $dropdownData['emirates'] = $emirates;

                $countries = Country::where('status', 1)->orderBy('id')->get();

                $dropdownData['nationality'] = $countries->map(function ($country) use ($lang) {
                    return [
                        'id'    => $country->id,
                        'value' => $country->getTranslation('name', $lang),
                    ];
                });

                $service    = Service::where('slug', 'immigration-requests')->firstOrFail();

                $dropdownData['payment'] = [
                    'service_fee'       => $service->service_fee ?? 0,
                    'govt_fee'          => $service->govt_fee ?? 0,
                    'tax'               => $service->tax ?? 0,
                    'total_amount'      => $service->total_amount ?? 0
                ];

                $form_info = getPageDynamicContent('immigration_page', $lang);

                $dropdownData['form_info'] = $form_info;

                return view('frontend.user.service-requests.immigration', ['service' => $service, 'dropdownData' => $dropdownData, 'lang' => $lang]);
        }

        abort(404);
    }

    public function showConsultationForm (Request $request){
        $lang           = app()->getLocale() ?? env('APP_LOCALE','en');

        $dropdowns  = Dropdown::with([
                        'options' => function ($q) {
                            $q->where('status', 'active')->orderBy('sort_order');
                        },
                        'options.translations' => function ($q) use ($lang) {
                            $q->whereIn('language_code', [$lang, 'en']);
                        }
                    ])->whereIn('slug', ['specialities', 'case_stage', 'you_represent','languages'])->get()->keyBy('slug');
       
        
        $dropdownData   = [];

        foreach ($dropdowns as $slug => $dropdown) {
            $dropdownData[$slug] = $dropdown->options->map(function ($option) use ($lang){
                return [
                    'id'    => $option->id,
                    'value' => $option->getTranslation('name',$lang),
                ];
            });
        }

        if(isset($dropdownData['specialities'])){
            $dropdownData['case_types'] = $dropdownData['specialities'];
            unset($dropdownData['specialities']);
        }

        $timeslots = ConsultationDuration::where('status',1)->where('type','normal')->orderBy('id')->get();

        $dropdownData['timeslots'] = $timeslots->map(function ($timeslot) use($lang) {
                return [
                    'duration'  => $timeslot->duration,
                    'value'     => $timeslot->getTranslation('name',$lang),
                ];
        });

        $service = Service::where('slug', 'online-live-consultancy')->first();
        return view('frontend.user.service-requests.online_consultation', ['service' => $service,'dropdownData' => $dropdownData, 'lang' => $lang]);
    }

    public function requestConsultation(Request $request)
    {
        $data = $request->validate([
            'applicant_type' => 'required|in:company,individual',
            'litigation_type'=> 'required|in:local,federal',
            'consultant_type'=> 'required|in:normal,vip',
            'emirate_id'     => 'required|integer',
            'you_represent'  => 'required',
            'case_type'      => 'required',
            'case_stage'     => 'required',
            'language'       => 'required',
            'duration'       => 'required|numeric',
            'lawyer_id'      => 'nullable|exists:lawyers,id'
        ]);

        $lang       = app()->getLocale() ?? env('APP_LOCALE','en');
        $user       = Auth::guard('frontend')->user();

        $consultation = Consultation::create([
            'user_id'=> $user->id,
            ...$data
        ]);

        $lawyer = findBestFitLawyer($consultation);
       
        if ($lawyer) {
            reserveLawyer($lawyer->id, $consultation->id);
        } else {
            $consultation->delete();
            return redirect()->back()->with('error', __('frontend.no_lawyer_available'));
        }
    
        $base = ConsultationDuration::where('type', $request->consultant_type)
                                    ->where('duration', $request->duration)
                                    ->where('status', 1)
                                    ->first();

        $total_amount = (float)($base->amount ?? 0);

        $consultation->update([
            'amount' => $total_amount
        ]);
        $currency = env('APP_CURRENCY','AED');
        $payment = [];

        $total_amount = 0;
        if($total_amount > 0) {
            $customer = [
                'email' => $user->email,
                'name'  => $user->name,
                'phone' => $user->phone
            ];
            $orderReference = $consultation->id .'--'.$consultation->ref_code;

            $payment = createConsultationWebOrder($customer, $total_amount, env('APP_CURRENCY','AED'), $orderReference);

            if (isset($payment['_links']['payment']['href'])) {
                $paymentData = ConsultationPayment::create([
                                'consultation_id' => $consultation->id,
                                'user_id' => $user->id,
                                'amount' => $total_amount,
                                'type' => 'initial',
                                'status' => 'pending',
                                'payment_reference' => $payment['reference'] ?? NULL
                            ]);
                return redirect()->away($payment['_links']['payment']['href']);
            }

            return redirect()->back()->with('error', __('frontend.consultation_request_submit_failed'));
        }else{
            $consultation->refresh();
           
            if ($consultation->lawyer_id) {
                assignLawyer($consultation, $consultation->lawyer_id);
                $consultation->status = 'waiting_lawyer';
                $consultation->save();
            } else {
                // Backup case: find a lawyer again if not reserved
                $lawyer = findBestFitLawyer($consultation);
                if ($lawyer) {
                    assignLawyer($consultation, $lawyer->id);
                    $consultation->status = 'waiting_lawyer';
                    $consultation->save();
                } else {
                    $consultation->status = 'no_lawyer_available';
                    $consultation->save();
                }
            }
            return redirect()->route('user.consultation-payment.success', ['id' => base64_encode($consultation->id)]);

        }
    }


    public function consultationPaymentSuccess(Request $request) 
    {
        $paymentReference = $request->query('ref') ?? NULL;
        $token = getAccessToken();

        $baseUrl = config('services.ngenius.base_url');
        $outletRef = config('services.ngenius.outlet_ref');

        $response = Http::withToken($token)->get("{$baseUrl}/transactions/outlets/" . $outletRef . "/orders/{$paymentReference}");
        $data = $response->json();
      
        $orderRef = $data['merchantOrderReference'] ?? NULL;
        $serviceData = explode('--', $orderRef);

        $consultationId = $serviceData[0];
        $serviceRequestCode = $serviceData[1];
        
        $status = $data['_embedded']['payment'][0]['state'] ?? null;
        $paid_amount = $data['_embedded']['payment'][0]['amount']['value'] ?? 0;

        $paidAmount = ($paid_amount != 0) ? $paid_amount/100 : 0;
        $lang       = app()->getLocale() ?? env('APP_LOCALE','en');

        if ($status === 'PURCHASED' || $status === 'CAPTURED') {
            $servicePayment = ConsultationPayment::where('payment_reference', $paymentReference)
                                                ->where('consultation_id', $consultationId)
                                                ->first();

            if ($servicePayment) {
                $servicePayment->update(['status' => 'completed']);
            }

            $consultation = Consultation::findOrFail($consultationId);

            if ($consultation->lawyer_id) {
                assignLawyer($consultation, $consultation->lawyer_id);
                $consultation->status = 'waiting_lawyer';
                $consultation->save();
            } else {
                // Backup case: find a lawyer again if not reserved
                $lawyer = findBestFitLawyer($consultation);
                if ($lawyer) {
                    assignLawyer($consultation, $lawyer->id);
                    $consultation->status = 'waiting_lawyer';
                    $consultation->save();
                } else {
                    $consultation->status = 'no_lawyer_available';
                    $consultation->save();
                }
            }

            return redirect()->route('user.consultation-payment.success', ['id' => base64_encode($consultation->id)]);
        }else{
            $consultation = Consultation::find($consultationId);
            unreserveLawyer($consultation->lawyer_id);
            $consultation->delete();
            return redirect()->route('user.payment-consultation-failed');
        }

        return redirect()->route('user.dashboard')->with('error', 'Payment failed or cancelled.');
    }

    public function consultationCancelPayment(Request $request){
        $ref = $request->get('ref'); 

        $servicePayment = ConsultationPayment::where('payment_reference', $ref)->first();

        if ($servicePayment) {
            $consultation = Consultation::find($servicePayment->consultation_id);
            unreserveLawyer($consultation->lawyer_id);
            $consultation->delete();
        }

        return redirect()->route('user.dashboard')->with('error', __('frontend.request_cancelled'));
    }

    public function consultationWaitingLawyer(Request $request, $id = NULL){
        $lang = app()->getLocale() ?? env('APP_LOCALE','en');

        $consultation = Consultation::find(base64_decode($id));
        $pageData = getPageDynamicContent('consultancy_waiting_page',$lang);

        $pageData = $pageData['content'] ?? '';
        return view('frontend.user.service-requests.consultation_waiting_lawyer', compact('pageData', 'consultation'));
    }

    public function consultationRequestFailed(){
        $lang       = app()->getLocale() ?? env('APP_LOCALE','en');
         $pageData = getPageDynamicContent('consultancy_payment_failed',$lang);
        return view('frontend.user.service-requests.consultation_failed', compact('pageData'));
    }
        
    public function requestCourtCase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'applicant_type'    => 'required',
            'litigation_type'   => 'required',
            'emirate_id'        => 'required',
            'case_type'         => 'required',
            'you_represent'     => 'required',
            'memo'              => 'nullable|array',
            'documents'         => 'nullable|array',
            'eid'               => 'required|array',
            'trade_license'     => 'required_if:applicant_type,company|array',
            'memo.*'            => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
            'documents.*'       => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
            'eid.*'             => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
            'trade_license.*'   => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
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

        $lang       = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $user       = Auth::guard('frontend')->user();
        $service    = Service::where('slug', 'court-case-submission')->firstOrFail();

        $referenceCode = ServiceRequest::generateReferenceCode($service);

        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'court-case-submission',
            'reference_code'    => $referenceCode,
            'source'            => 'web',
            'request_success'   => 1,
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
                    $uniqueName     = $inputName . '_' . uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filename       = $requestFolder . $uniqueName;
                    $fileContents   = file_get_contents($file);
                    Storage::disk('public')->put($filename, $fileContents);
                    $filePaths[$columnName][] = Storage::url($filename);
                }
            }
        }

        $courtCase->update($filePaths);


        Auth::guard('frontend')->user()->notify(new ServiceRequestSubmitted($service_request));

        $usersToNotify = getUsersWithPermissions(['view_service_requests', 'export_service_requests', 'change_request_status', 'manage_service_requests']);
        Notification::send($usersToNotify, new ServiceRequestSubmitted($service_request, true));

        return redirect()->route('user.request-success', ['reqid' => base64_encode($service_request->id)]);
    }

    public function requestCriminalComplaint(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'applicant_type'    => 'required',
            'litigation_type'   => 'required',
            'emirate_id'        => 'required',
            'case_type'         => 'required',
            'you_represent'     => 'required',
            'memo'              => 'nullable|array',
            'documents'         => 'required|array',
            'eid'               => 'required|array',
            'trade_license'     => 'required_if:applicant_type,company|array',
            'memo.*'            => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
            'documents.*'       => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
            'eid.*'             => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
            'trade_license.*'   => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
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

        $lang       = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $user       = Auth::guard('frontend')->user();
        $service    = Service::where('slug', 'criminal-complaint')->firstOrFail();

        $referenceCode = ServiceRequest::generateReferenceCode($service);

        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'criminal-complaint',
            'request_success'   => 1,
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
                    $uniqueName     = $inputName . '_' . uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filename       = $requestFolder . $uniqueName;
                    $fileContents   = file_get_contents($file);
                    Storage::disk('public')->put($filename, $fileContents);
                    $filePaths[$columnName][] = Storage::url($filename);
                }
            }
        }

        $criminalComplaint->update($filePaths);


        Auth::guard('frontend')->user()->notify(new ServiceRequestSubmitted($service_request));

        $usersToNotify = getUsersWithPermissions(['view_service_requests', 'export_service_requests', 'change_request_status', 'manage_service_requests']);
        Notification::send($usersToNotify, new ServiceRequestSubmitted($service_request, true));

        return redirect()->route('user.request-success', ['reqid' => base64_encode($service_request->id)]);
    }

    public function requestLastWill(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'testament_place'   => 'required',
            'nationality'       => 'required',
            'emirate_id'        => 'required',
            'religion'          => 'required',
            'you_represent'     => 'required',
            'full_name'         => 'required',
            'eid'               => 'required|array',
            'eid.*'             => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
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

        $lang       = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $user       = Auth::guard('frontend')->user();
        $service    = Service::where('slug', 'last-will-and-testament')->firstOrFail();

        $referenceCode = ServiceRequest::generateReferenceCode($service);

        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'last-will-and-testament',
            'request_success'   => 1,
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
                    $uniqueName     = $inputName . '_' . uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filename       = $requestFolder . $uniqueName;
                    $fileContents   = file_get_contents($file);
                    Storage::disk('public')->put($filename, $fileContents);
                    $filePaths[$columnName][] = Storage::url($filename);
                }
            }
        }

        $lastWill->update($filePaths);

        Auth::guard('frontend')->user()->notify(new ServiceRequestSubmitted($service_request));

        $usersToNotify = getUsersWithPermissions(['view_service_requests', 'export_service_requests', 'change_request_status', 'manage_service_requests']);
        Notification::send($usersToNotify, new ServiceRequestSubmitted($service_request, true));

        return redirect()->route('user.request-success', ['reqid' => base64_encode($service_request->id)]);
    }

    public function requestEscrowAccount(Request $request)
    {

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

        $lang       = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $user       = Auth::guard('frontend')->user();
        $service    = Service::where('slug', 'escrow-accounts')->firstOrFail();

        $referenceCode = ServiceRequest::generateReferenceCode($service);

        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'escrow-accounts',
            'request_success'   => 1,
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


        Auth::guard('frontend')->user()->notify(new ServiceRequestSubmitted($service_request));

        $usersToNotify = getUsersWithPermissions(['view_service_requests', 'export_service_requests', 'change_request_status', 'manage_service_requests']);
        Notification::send($usersToNotify, new ServiceRequestSubmitted($service_request, true));

        return redirect()->route('user.request-success', ['reqid' => base64_encode($service_request->id)]);
    }

    public function requestDebtsCollection(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'applicant_type'    => 'required',
            'emirate_id'        => 'required',
            'debt_type'         => 'required',
            'debt_amount'       => 'required',
            'debt_category'     => 'required',
            'documents'         => 'nullable|array',
            'eid'               => 'required|array',
            'trade_license'     => 'required_if:applicant_type,company|array',
            'documents.*'       => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
            'eid.*'             => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
            'trade_license.*'   => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
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

        $lang       = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $user       = Auth::guard('frontend')->user();
        $service    = Service::where('slug', 'debts-collection')->firstOrFail();

        $referenceCode = ServiceRequest::generateReferenceCode($service);

        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'debts-collection',
            'request_success'   => 1,
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
                    $uniqueName     = $inputName . '_' . uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filename       = $requestFolder . $uniqueName;
                    $fileContents   = file_get_contents($file);
                    Storage::disk('public')->put($filename, $fileContents);
                    $filePaths[$columnName][] = Storage::url($filename);
                }
            }
        }

        $debtCollection->update($filePaths);

        Auth::guard('frontend')->user()->notify(new ServiceRequestSubmitted($service_request));

        $usersToNotify = getUsersWithPermissions(['view_service_requests', 'export_service_requests', 'change_request_status', 'manage_service_requests']);
        Notification::send($usersToNotify, new ServiceRequestSubmitted($service_request, true));

        return redirect()->route('user.request-success', ['reqid' => base64_encode($service_request->id)]);
    }

    public function requestMemoWriting(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'applicant_type'    => 'required',
            'litigation_type'   => 'required',
            'emirate_id'        => 'required',
            'case_type'         => 'required',
            'you_represent'     => 'required',
            'full_name'         => 'required',
            'documents'         => 'required|array',
            'eid'               => 'required|array',
            'trade_license'     => 'required_if:applicant_type,company|array',
            'documents.*'       => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
            'eid.*'             => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
            'trade_license.*'   => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
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

        $lang       = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $user       = Auth::guard('frontend')->user();
        $service    = Service::where('slug', 'memo-writing')->firstOrFail();

        $referenceCode = ServiceRequest::generateReferenceCode($service);

        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'memo-writing',
            'request_success'   => 1,
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
                if (!is_array($files)) {
                    $files = [$files];
                }
                foreach ($files as $file) {
                    $uniqueName     = $inputName . '_' . uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filename       = $requestFolder . $uniqueName;
                    $fileContents   = file_get_contents($file);
                    Storage::disk('public')->put($filename, $fileContents);
                    $filePaths[$columnName][] = Storage::url($filename);
                }
            }
        }

        $memoWriting->update($filePaths);

        Auth::guard('frontend')->user()->notify(new ServiceRequestSubmitted($service_request));

        $usersToNotify = getUsersWithPermissions(['view_service_requests', 'export_service_requests', 'change_request_status', 'manage_service_requests']);
        Notification::send($usersToNotify, new ServiceRequestSubmitted($service_request, true));

        return redirect()->route('user.request-success', ['reqid' => base64_encode($service_request->id)]);
    }

    public function requestSuccess(Request $request, $reqid)
    {

        $lang = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $requestId = $reqid ? base64_decode($reqid) : '';

        $service = ServiceRequest::find($requestId);

        if (!empty($service)) {
            $pageData = getPageDynamicContent('request_success', $lang);
            $response = [
                'reference' => $service->reference_code ?? '',
                'message'   => $pageData['content']
            ];

            return view('frontend.user.service-requests.request_success', ['data' => $response, 'lang' => $lang]);
        } else {
            $pageData = getPageDynamicContent('request_failed', $lang);
            $response = [
                'reference' => '',
                'message'   => $pageData['content']
            ];

            return view('frontend.user.service-requests.request_failed', ['data' => $response, 'lang' => $lang]);
        }
    }

    public function requestPaymentSuccess(Request $request, $reqid)
    {

        $lang = app()->getLocale() ?? env('APP_LOCALE', 'en');

        $pageData = getPageDynamicContent('request_payment_success', $lang);

        $requestId = $reqid ? base64_decode($reqid) : '';

        $service = ServiceRequest::find($requestId);

        if (!empty($service)) {
            $pageData = getPageDynamicContent('request_payment_success', $lang);
            $response = [
                'reference' => $service->reference_code ?? '',
                'message'   => $pageData['content']
            ];

            return view('frontend.user.service-requests.request_success', ['data' => $response, 'lang' => $lang]);
        } else {
            $pageData = getPageDynamicContent('request_payment_failed', $lang);
            $response = [
                'reference' => '',
                'message'   => $pageData['content']
            ];

            return view('frontend.user.service-requests.request_failed', ['data' => $response, 'lang' => $lang]);
        }
    }

    public function requestPowerOfAttorney(Request $request)
    {
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
            'authorized_passport.*' => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
            'appointer_id.*'        => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
            'authorized_id.*'       => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
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
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $lang       = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $user       = Auth::guard('frontend')->user();
        $service    = Service::where('slug', 'power-of-attorney')->firstOrFail();

        $referenceCode = ServiceRequest::generateReferenceCode($service);

        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'power-of-attorney',
            'request_success'   => 1,
            'reference_code'    => $referenceCode,
            'source'            => 'web',
            'submitted_at'      => date('Y-m-d H:i:s')
        ]);

        $powerOA = RequestPowerOfAttorney::create([
            'user_id'               => $user->id,
            'service_request_id'    => $service_request->id,
            'applicant_type'        => $request->input('applicant_type') ?? NULL,
            'appointer_name'        => $request->input('appointer_name') ?? NULL,
            'id_number'             => $request->input('id_number') ?? NULL,
            'appointer_mobile'      => $request->input('appointer_mobile') ?? NULL,
            'emirate_id'            => $request->input('emirate_id') ?? NULL,
            'poa_type'              => $request->input('poa_type') ?? NULL,
            'name_of_authorized'    => $request->input('name_of_authorized') ?? NULL,
            'authorized_mobile'     => $request->input('authorized_mobile') ?? NULL,
            'id_number_authorized'  => $request->input('id_number_authorized') ?? NULL,
            'authorized_address'    => $request->input('authorized_address') ?? NULL,
            'relationship'          => $request->input('relationship') ?? NULL,
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


                if (!is_array($files)) {
                    $files = [$files];
                }

                foreach ($files as $file) {
                    $uniqueName     = $inputName . '_' . uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filename       = $requestFolder . $uniqueName;
                    $fileContents   = file_get_contents($file);
                    Storage::disk('public')->put($filename, $fileContents);
                    $filePaths[$columnName][] = Storage::url($filename);
                }
            }
        }

        $powerOA->update($filePaths);

        Auth::guard('frontend')->user()->notify(new ServiceRequestSubmitted($service_request));

        $usersToNotify = getUsersWithPermissions(['view_service_requests', 'export_service_requests', 'change_request_status', 'manage_service_requests']);
        Notification::send($usersToNotify, new ServiceRequestSubmitted($service_request, true));

        return redirect()->route('user.request-success', ['reqid' => base64_encode($service_request->id)]);
    }

    public function requestContractDrafting(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'applicant_type'    => 'required',
            'contract_type'     => 'required',
            'emirate_id'        => 'required',
            'sub_contract_type' => 'required',
            'contract_language' => 'required',
            'company_name'      => 'required',
            'industry'          => 'required',
            'email'             => 'required',
            'priority'          => 'required',
            'documents'         => 'nullable|array',
            'eid'               => 'required|array',
            'trade_license'     => 'required_if:applicant_type,company|array',
            'documents.*'       => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
            'eid.*'             => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
            'trade_license.*'   => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
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
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $lang       = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $user       = Auth::guard('frontend')->user();
        $service    = Service::where('slug', 'contract-drafting')->firstOrFail();

        $referenceCode = ServiceRequest::generateReferenceCode($service);

        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'contract-drafting',
            'request_success'   => 1,
            'reference_code'    => $referenceCode,
            'source'            => 'web',
            'submitted_at'      => date('Y-m-d H:i:s')
        ]);

        $contractDrafting = RequestContractDrafting::create([
            'user_id'               => $user->id,
            'service_request_id'    => $service_request->id,
            'applicant_type'        => $request->input('applicant_type') ?? NULL,
            'contract_type'         => $request->input('contract_type') ?? NULL,
            'emirate_id'            => $request->input('emirate_id') ?? NULL,
            'sub_contract_type'     => $request->input('sub_contract_type') ?? NULL,
            'contract_language'     => $request->input('contract_language') ?? NULL,
            'company_name'          => $request->input('company_name') ?? NULL,
            'industry'              => $request->input('industry') ?? NULL,
            'email'                 => $request->input('email') ?? NULL,
            'priority'              => $request->input('priority') ?? NULL,
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
                    $uniqueName     = $inputName . '_' . uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filename       = $requestFolder . $uniqueName;
                    $fileContents   = file_get_contents($file);
                    Storage::disk('public')->put($filename, $fileContents);
                    $filePaths[$columnName][] = Storage::url($filename);
                }
            }
        }

        $contractDrafting->update($filePaths);

        Auth::guard('frontend')->user()->notify(new ServiceRequestSubmitted($service_request));

        $usersToNotify = getUsersWithPermissions(['view_service_requests', 'export_service_requests', 'change_request_status', 'manage_service_requests']);
        Notification::send($usersToNotify, new ServiceRequestSubmitted($service_request, true));

        return redirect()->route('user.request-success', ['reqid' => base64_encode($service_request->id)]);
    }

    public function requestCompanySetup(Request $request)
    {

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
            'documents.*'       => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
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
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $lang       = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $user       = Auth::guard('frontend')->user();
        $service    = Service::where('slug', 'company-setup')->firstOrFail();

        $referenceCode = ServiceRequest::generateReferenceCode($service);

        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'company-setup',
            'request_success'   => 1,
            'reference_code'    => $referenceCode,
            'source'            => 'web',
            'submitted_at'      => date('Y-m-d H:i:s')
        ]);

        $companySetup = RequestCompanySetup::create([
            'user_id'               => $user->id,
            'service_request_id'    => $service_request->id,
            'applicant_type'        => $request->input('applicant_type') ?? NULL,
            'emirate_id'            => $request->input('emirate_id') ?? NULL,
            'zone'                  => $request->input('zone') ?? NULL,
            'license_type'          => $request->input('license_type') ?? NULL,
            'license_activity'      => $request->input('license_activity') ?? NULL,
            'company_type'          => $request->input('company_type') ?? NULL,
            'industry'              => $request->input('industry') ?? NULL,
            'company_name'          => $request->input('company_name') ?? NULL,
            'mobile'                => $request->input('mobile') ?? NULL,
            'email'                 => $request->input('email') ?? NULL,
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
                    $uniqueName     = $inputName . '_' . uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filename       = $requestFolder . $uniqueName;
                    $fileContents   = file_get_contents($file);
                    Storage::disk('public')->put($filename, $fileContents);
                    $filePaths[$columnName][] = Storage::url($filename);
                }
            }
        }

        $companySetup->update($filePaths);

        Auth::guard('frontend')->user()->notify(new ServiceRequestSubmitted($service_request));

        $usersToNotify = getUsersWithPermissions(['view_service_requests', 'export_service_requests', 'change_request_status', 'manage_service_requests']);
        Notification::send($usersToNotify, new ServiceRequestSubmitted($service_request, true));

        return redirect()->route('user.request-success', ['reqid' => base64_encode($service_request->id)]);
    }

    public function requestExpertReport(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'applicant_type'            => 'required',
            'applicant_place'           => 'required',
            'emirate_id'                => 'required',
            'expert_report_type'        => 'required',
            'expert_report_language'    => 'required',
            'documents'                 => 'required|array',
            'eid'                       => 'required|array',
            'trade_license'             => 'required_if:applicant_type,company|array',
            'documents.*'               => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
            'eid.*'                     => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
            'trade_license.*'           => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
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
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $lang       = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $user       = Auth::guard('frontend')->user();
        $service    = Service::where('slug', 'expert-report')->firstOrFail();
        $referenceCode = ServiceRequest::generateReferenceCode($service);
        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'expert-report',
            'reference_code'    => $referenceCode,
            'source'            => 'web',
            'submitted_at'      => date('Y-m-d H:i:s'),
            'payment_status'    => 'pending'
        ]);

        $expertReport = RequestExpertReport::create([
            'user_id'                   => $user->id,
            'service_request_id'        => $service_request->id,
            'applicant_type'            => $request->input('applicant_type') ?? NULL,
            'applicant_place'           => $request->input('applicant_place') ?? NULL,
            'emirate_id'                => $request->input('emirate_id') ?? NULL,
            'expert_report_type'        => $request->input('expert_report_type') ?? NULL,
            'expert_report_language'    => $request->input('expert_report_language') ?? NULL,
            'about_case'                => $request->input('about_case') ?? NULL,
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
                    $uniqueName     = $inputName . '_' . uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filename       = $requestFolder . $uniqueName;
                    $fileContents   = file_get_contents($file);
                    Storage::disk('public')->put($filename, $fileContents);
                    $filePaths[$columnName][] = Storage::url($filename);
                }
            }
        }

        $expertReport->update($filePaths);
        $base = ExpertReportPricing::where('litigation_type', $request->input('applicant_place'))
            ->where('expert_report_type_id', $request->input('expert_report_type'))
            ->where('language_id', $request->input('expert_report_language'))
            ->where('status', 1)
            ->first();

        $total_amount = (float)($base->total ?? 0);

        $currency = env('APP_CURRENCY', 'AED');
        $payment = [];
        if ($total_amount != 0) {
            $customer = [
                'email' => $user->email,
                'name'  => $user->name,
                'phone' => $user->phone
            ];

            $orderReference = $service_request->id . '--' . $service_request->reference_code;

            $payment = createWebOrder($customer, $total_amount, env('APP_CURRENCY', 'AED'), $orderReference);

            if (isset($payment['_links']['payment']['href'])) {
                $service_request->update([
                    'payment_reference' => $payment['reference'] ?? null,
                    'amount' => (float)($base->total ?? 0),
                    'service_fee' => (float)($base->admin_fee ?? 0),
                    'govt_fee' => 0,
                    'tax' => (float)($base->vat ?? 0),
                ]);
                return redirect()->away($payment['_links']['payment']['href']);
            }

            return redirect()->back()->with('error', 'Failed to initiate payment');
        } else {

            $serviceSlug = $service_request->service_slug;
            $requestId   = $service_request->id;

            $serviceReq = \App\Models\RequestExpertReport::where('service_request_id', $service_request->id)->first();
            $serviceReqId = $serviceReq->id;

            $serviceReq->delete();

            deleteRequestFolder('expert_report', $serviceReqId);
            $service_request->delete();

            return redirect()->route('user.payment-request-success', ['reqid' => base64_encode($service_request->id)]);
        }
    }

    public function requestImmigration(Request $request)
    {

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
            'cv.*'                  => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
            'certificates.*'        => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
            'account_statement.*'   => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
            'passport.*'            => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
            'photo.*'               => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
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
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $lang       = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $user       = Auth::guard('frontend')->user();

        $service    = Service::where('slug', 'immigration-requests')->firstOrFail();
        $referenceCode = ServiceRequest::generateReferenceCode($service);
        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'immigration-requests',
            'reference_code'    => $referenceCode,
            'source'            => 'web',
            'submitted_at'      => date('Y-m-d H:i:s'),
            'payment_status'    => 'pending'
        ]);

        $immigration = RequestImmigration::create([
            'service_request_id'    => $service_request->id,
            'user_id'               => $user->id,
            'preferred_country'     => $request->input('preferred_country') ?? NULL,
            'position'              => $request->input('position') ?? NULL,
            'age'                   => $request->input('age') ?? NULL,
            'nationality'           => $request->input('nationality') ?? NULL,
            'years_of_experience'   => $request->input('years_of_experience') ?? NULL,
            'address'               => $request->input('address') ?? NULL,
            'residency_status'      => $request->input('residency_status') ?? NULL,
            'current_salary'        => $request->input('current_salary') ?? NULL,
            'application_type'      => $request->input('application_type') ?? NULL,
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
                    $uniqueName     = $inputName . '_' . uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filename       = $requestFolder . $uniqueName;
                    $fileContents   = file_get_contents($file);
                    Storage::disk('public')->put($filename, $fileContents);
                    $filePaths[$columnName][] = Storage::url($filename);
                }
            }
        }

        $immigration->update($filePaths);

        $total_amount = $service->total_amount ?? 0;

        if ($total_amount != 0) {
            $customer = [
                'email' => $user->email,
                'name'  => $user->name,
                'phone' => $user->phone
            ];

            $orderReference = $service_request->id . '--' . $service_request->reference_code;

            $payment = createWebOrder($customer, $total_amount, env('APP_CURRENCY', 'AED'), $orderReference);

            if (isset($payment['_links']['payment']['href'])) {
                $service_request->update([
                    'payment_reference' => $payment['reference'] ?? null,
                    'amount' => $service->total_amount,
                    'service_fee' => $service->service_fee,
                    'govt_fee' => $service->govt_fee,
                    'tax' => $service->tax,
                ]);
                return redirect()->away($payment['_links']['payment']['href']);
            }

            return redirect()->back()->with('error', 'Failed to initiate payment');
        } else {

            $serviceSlug = $service_request->service_slug;
            $requestId   = $service_request->id;

            $serviceReq = \App\Models\RequestImmigration::where('service_request_id', $service_request->id)->first();
            $serviceReqId = $serviceReq->id;

            $serviceReq->delete();

            deleteRequestFolder('immigration', $serviceReqId);
            $service_request->delete();

            return redirect()->route('user.request-success', ['reqid' => base64_encode($service_request->id)]);
        }
    }

    public function requestRequestSubmission(Request $request)
    {

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
            'trade_license'     => 'required_if:applicant_type,company|array',
            'memo.*'            => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
            'documents.*'       => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
            'eid.*'             => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
            'trade_license.*'   => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
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
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $lang       = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $user       = Auth::guard('frontend')->user();

        $service    = Service::where('slug', 'request-submission')->firstOrFail();

        $referenceCode = ServiceRequest::generateReferenceCode($service);

        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'request-submission',
            'reference_code'    => $referenceCode,
            'source'            => 'web',
            'submitted_at'      => date('Y-m-d H:i:s'),
            'payment_status'    => 'pending'
        ]);

        $requestSubmission = RequestRequestSubmission::create([
            'user_id'               => $user->id,
            'service_request_id'    => $service_request->id,
            'applicant_type'        => $request->input('applicant_type') ?? NULL,
            'litigation_type'       => $request->input('litigation_type') ?? NULL,
            'litigation_place'      => $request->input('litigation_place') ?? NULL,
            'emirate_id'            => $request->input('emirate_id') ?? NULL,
            'case_type'             => $request->input('case_type') ?? NULL,
            'request_type'          => $request->input('request_type') ?? NULL,
            'request_title'         => $request->input('request_title') ?? NULL,
            'case_number'           => $request->input('case_number') ?? NULL,
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
                    $uniqueName     = $inputName . '_' . uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filename       = $requestFolder . $uniqueName;
                    $fileContents   = file_get_contents($file);
                    Storage::disk('public')->put($filename, $fileContents);
                    $filePaths[$columnName][] = Storage::url($filename);
                }
            }
        }

        $requestSubmission->update($filePaths);

        $base = RequestSubmissionPricing::where('litigation_type', $request->input('litigation_type'))
            ->where('litigation_place', $request->input('litigation_place'))
            ->where('case_type_id', $request->input('case_type'))
            ->where('request_type_id', $request->input('request_type'))
            ->where('request_title_id', $request->input('request_title'))
            ->where('status', 1)
            ->first();

        $total_amount = (float)($base->total_amount ?? 0);

        $currency = env('APP_CURRENCY', 'AED');
        $payment = [];

        if ($total_amount != 0) {
            $customer = [
                'email' => $user->email,
                'name'  => $user->name,
                'phone' => $user->phone
            ];

            $orderReference = $service_request->id . '--' . $service_request->reference_code;

            $payment = createWebOrder($customer, $total_amount, env('APP_CURRENCY', 'AED'), $orderReference);

            if (isset($payment['_links']['payment']['href'])) {
                $service_request->update([
                    'payment_reference' => $payment['reference'] ?? null,
                    'amount' => (float)($base->total_amount ?? 0),
                    'service_fee' => (float)($base->admin_fee ?? 0),
                    'govt_fee' => (float)($base->govt_fee ?? 0),
                    'tax' => (float)($base->vat ?? 0),
                ]);
                return redirect()->away($payment['_links']['payment']['href']);
            }

            return redirect()->back()->with('error', 'Failed to initiate payment');
        } else {

            $serviceSlug = $service_request->service_slug;
            $requestId   = $service_request->id;

            $serviceReq = \App\Models\RequestRequestSubmission::where('service_request_id', $service_request->id)->first();
            $serviceReqId = $serviceReq->id;

            $serviceReq->delete();

            deleteRequestFolder('request_submission', $serviceReqId);
            $service_request->delete();
            return redirect()->route('user.payment-request-success', ['reqid' => base64_encode($service_request->id)]);
        }
    }

    public function requestLegalTranslation(Request $request)
    {

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
            'memo.*'                    => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
            'documents.*'               => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
            'additional_documents.*'    => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
            'trade_license.*'           => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:102400',
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
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $lang       = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $user       = Auth::guard('frontend')->user();

        $service    = Service::where('slug', 'legal-translation')->firstOrFail();

        $referenceCode = ServiceRequest::generateReferenceCode($service);

        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'legal-translation',
            'reference_code'    => $referenceCode,
            'source'            => 'web',
            'submitted_at'      => date('Y-m-d H:i:s'),
            'payment_status'    => 'pending',
        ]);

        $legalTranslation = RequestLegalTranslation::create([
            'user_id'               => $user->id,
            'service_request_id'    => $service_request->id,
            'priority_level'        => $request->input('priority_level') ?? NULL,
            'document_language'     => $request->input('document_language') ?? NULL,
            'translation_language'  => $request->input('translation_language') ?? NULL,
            'document_type'         => $request->input('document_type') ?? NULL,
            'document_sub_type'     => $request->input('document_sub_type') ?? NULL,
            'receive_by'            => $request->input('receive_by') ?? NULL,
            'no_of_pages'           => $request->input('no_of_pages') ?? NULL,
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
                    $uniqueName     = $inputName . '_' . uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filename       = $requestFolder . $uniqueName;
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
        $priority       = $request->priority_level ?? null;
        $doc_type       = $request->document_type;
        $subdoc_type    = $request->document_sub_type;
        $receive_by     = $request->receive_by ?? null;
        $totalAmount  = $totalHours  = 0;

        $assignment = DefaultTranslatorAssignment::where([
            'from_language_id' => $from,
            'to_language_id'   => $to,
        ])->first();

        if ($assignment) {
            $rate = TranslatorLanguageRate::with(['deliveries' => function ($q) use ($priority, $receive_by) {
                $q->where('priority_type', $priority)
                    ->where('delivery_type', $receive_by);
            }])
                ->where('translator_id', $assignment->translator_id)
                ->where('from_language_id', $from)
                ->where('to_language_id', $to)
                ->where('doc_type_id', $doc_type)
                ->where('doc_subtype_id', $subdoc_type)
                ->where('status', 1)
                ->first();

            if ($rate) {
                if ($priority === 'normal') {
                    if ($pages <= 10) {
                        $totalHours = $rate->normal_hours_1_10;
                    } elseif ($pages <= 20) {
                        $totalHours = $rate->normal_hours_11_20;
                    } elseif ($pages <= 30) {
                        $totalHours = $rate->normal_hours_21_30;
                    } elseif ($pages <= 50) {
                        $totalHours = $rate->normal_hours_31_50;
                    } else {
                        $totalHours = $rate->normal_hours_above_50;
                    }
                } else {
                    if ($pages <= 10) {
                        $totalHours = $rate->urgent_hours_1_10;
                    } elseif ($pages <= 20) {
                        $totalHours = $rate->urgent_hours_11_20;
                    } elseif ($pages <= 30) {
                        $totalHours = $rate->urgent_hours_21_30;
                    } elseif ($pages <= 50) {
                        $totalHours = $rate->urgent_hours_31_50;
                    } else {
                        $totalHours = $rate->urgent_hours_above_50;
                    }
                }

                $delivery = $rate->deliveries->first();
                $admin_amount = $delivery->admin_amount * $pages;
                $translator_amount = $delivery->translator_amount * $pages;

                $totalAmountNoTax = ($admin_amount + $translator_amount + $delivery->delivery_amount);

                $tax = ($totalAmountNoTax / 100) * 5;

                $totalAmount = $totalAmountNoTax + $tax;


                $legalTranslation->update([
                    'admin_amount' => $admin_amount,
                    'translator_amount' => $translator_amount,
                    'delivery_amount' => $delivery->delivery_amount,
                    'tax' => $tax,
                    'total_amount' => $totalAmount,
                    'hours_per_page' => $totalHours
                ]);
            }
        }
        $total_amount = $totalAmount ?? 0;
        $currency = env('APP_CURRENCY', 'AED');
        $payment = [];

        if ($total_amount != 0) {
            $customer = [
                'email' => $user->email,
                'name'  => $user->name,
                'phone' => $user->phone
            ];

            $orderReference = $service_request->id . '--' . $service_request->reference_code;

            $payment = createWebOrder($customer, $total_amount, env('APP_CURRENCY', 'AED'), $orderReference);

            if (isset($payment['_links']['payment']['href'])) {
                $service_request->update([
                    'payment_reference' => $payment['reference'] ?? null,
                    'amount' => $total_amount,
                    'service_fee' => $total_amount,
                    'govt_fee' => 0,
                    'tax' => 0,
                ]);
                return redirect()->away($payment['_links']['payment']['href']);
            }

            return redirect()->back()->with('error', 'Failed to initiate payment');
        } else {

            $serviceSlug = $service_request->service_slug;
            $requestId   = $service_request->id;

            $serviceReq = \App\Models\RequestLegalTranslation::where('service_request_id', $service_request->id)->first();
            $serviceReqId = $serviceReq->id;

            $serviceReq->delete();

            deleteRequestFolder('legal_translation', $serviceReqId);
            $service_request->delete();
            return redirect()->route('user.payment-request-success', ['reqid' => base64_encode($service_request->id)]);
        }
    }

    public function requestAnnualAgreement(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'company_name'      => 'required',
            'emirate_id'        => 'required',
            'license_type'      => 'required',
            'license_activity'  => 'required',
            'industry'          => 'required',
            'no_of_employees'   => 'required',
            'case_type'         => 'required',
            'no_of_calls'       => 'required',
            'no_of_visits'      => 'required',
            'no_of_installment' => 'required',
            // 'lawfirm'           => 'required',
        ], [
            'company_name.required'         => __('messages.company_name_required'),
            'emirate_id.required'           => __('messages.emirate_required'),
            'license_type.required'         => __('messages.license_type_required'),
            'license_activity.required'     => __('messages.license_activity_required'),
            'industry.required'             => __('messages.industry_required'),
            'no_of_employees.required'      => __('messages.no_of_employees_required'),
            'case_type.required'            => __('messages.case_type_required'),
            'no_of_calls.required'          => __('messages.no_of_calls_required'),
            'no_of_visits.required'         => __('messages.no_of_visits_required'),
            'no_of_installment.required'    => __('messages.no_of_installment_required'),
            // 'lawfirm.required'              => __('messages.lawfirm_required'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $lang       = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $user       = Auth::guard('frontend')->user();

        $service    = Service::where('slug', 'annual-retainer-agreement')->firstOrFail();

        $referenceCode = ServiceRequest::generateReferenceCode($service);

        $service_request = ServiceRequest::create([
            'user_id'           => $user->id,
            'service_id'        => $service->id,
            'service_slug'      => 'annual-retainer-agreement',
            'reference_code'    => $referenceCode,
            'source'            => 'web',
            'submitted_at'      => date('Y-m-d H:i:s'),
            'payment_status'    => 'pending',
        ]);
        $casetype = $request->input('case_type');

        $amountToBePaidNow = 0;

        $calls          = $request->input('no_of_calls');
        $visits         = $request->input('no_of_visits');
        $installments   = $request->input('no_of_installment') ?? 1;

        $base = AnnualRetainerBaseFee::where('calls_per_month', $calls)
            ->where('visits_per_year', $visits)
            ->first();


        if ($base) {
            $installment = $base->installments()->where('installments', $installments)->first();
        }

        $finalTotal          = $installment?->final_total ?? 0;
        $amountToBePaidNow   = $installments > 0 ? $finalTotal / $installments : $finalTotal;

        $annualAgreement = RequestAnnualAgreement::create([
            'user_id'               => $user->id,
            'service_request_id'    => $service_request->id,
            'company_name'          => $request->input('company_name') ?? NULL,
            'emirate_id'            => $request->input('emirate_id') ?? NULL,
            'license_type'          => $request->input('license_type') ?? NULL,
            'license_activity'      => $request->input('license_activity') ?? NULL,
            'industry'              => $request->input('industry') ?? NULL,
            'no_of_employees'       => $request->input('no_of_employees') ?? NULL,
            'case_type'             => $casetype,
            'no_of_calls'           => $request->input('no_of_calls') ?? NULL,
            'no_of_visits'          => $request->input('no_of_visits') ?? NULL,
            'no_of_installment'     => $request->input('no_of_installment') ?? NULL,
            // 'lawfirm'               => $request->input('lawfirm') ?? NULL,
            'final_total'           => $finalTotal
        ]);

        if ($amountToBePaidNow != 0) {

            for ($i = 1; $i <= $installments; $i++) {
                AnnualAgreementInstallment::create([
                    'service_request_id' => $service_request->id,
                    'installment_no'     => $i,
                    'amount'             => round($finalTotal / $installments, 2),
                    'status'             => 'pending',
                    'due_date'           => now()->addMonths($i - 1),
                ]);
            }

            $customer = [
                'email' => $user->email,
                'name'  => $user->name,
                'phone' => $user->phone
            ];

            $orderReference = $service_request->id . '--' . $service_request->reference_code;

            $payment = createWebOrder($customer, $amountToBePaidNow, env('APP_CURRENCY', 'AED'), $orderReference);

            if (isset($payment['_links']['payment']['href'])) {
                $service_request->update([
                    'payment_reference' => $payment['reference'] ?? null,
                    'amount' => $finalTotal,
                    'service_fee' => $finalTotal,
                    'govt_fee' => 0,
                    'tax' => 0,
                ]);
                return redirect()->away($payment['_links']['payment']['href']);
            }

            return redirect()->back()->with('error', 'Failed to initiate payment');
        } else {

            $serviceSlug = $service_request->service_slug;
            $requestId   = $service_request->id;

            $serviceReq = \App\Models\RequestAnnualAgreement::where('service_request_id', $service_request->id)->first();
            $serviceReqId = $serviceReq->id;

            $serviceReq->delete();

            deleteRequestFolder('annual_retainer_agreement', $serviceReqId);
            $service_request->delete();

            return redirect()->route('user.request-success', ['reqid' => base64_encode($service_request->id)]);
        }
    }

    public function paymentSuccess(Request $request)
    {
        $paymentReference = $request->query('ref') ?? NULL;
        $token = getAccessToken();

        $baseUrl = config('services.ngenius.base_url');
        $outletRef = config('services.ngenius.outlet_ref');

        $response = Http::withToken($token)->get("{$baseUrl}/transactions/outlets/" . $outletRef . "/orders/{$paymentReference}");
        $data = $response->json();

        $orderRef = $data['merchantOrderReference'] ?? NULL;
        $serviceData = explode('--', $orderRef);

        $serviceRequestId = $serviceData[0];
        $serviceRequestCode = $serviceData[1];

        $status = $data['_embedded']['payment'][0]['state'] ?? null;
        $paid_amount = $data['_embedded']['payment'][0]['amount']['value'] ?? 0;

        $paidAmount = ($paid_amount != 0) ? $paid_amount / 100 : 0;
        $serviceRequest = ServiceRequest::findOrFail($serviceRequestId);

        if ($status === 'PURCHASED' || $status === 'CAPTURED') {
            if ($serviceRequest->service_slug === 'annual-retainer-agreement') {
                $annualRequest = RequestAnnualAgreement::where('service_request_id', $serviceRequest->id)->first();
                if ($annualRequest->no_of_installment != 1) {
                    $serviceRequest->update([
                        'payment_status' => 'partial',
                        'payment_response' => $data,
                        'request_success'   => 1,
                        'paid_at' => date('Y-m-d h:i:s')
                    ]);
                } else {
                    $serviceRequest->update([
                        'payment_status' => 'success',
                        'request_success'   => 1,
                        'payment_response' => $data,
                        'paid_at' => date('Y-m-d h:i:s')
                    ]);
                }
            } else {
                $serviceRequest->update([
                    'payment_status' => 'success',
                    'request_success'   => 1,
                    'payment_response' => $data,
                    'paid_at' => date('Y-m-d h:i:s')
                ]);
            }

            if ($serviceRequest->service_slug === 'legal-translation') {
                $legalTranslation = requestLegalTranslation::where('service_request_id', $serviceRequest->id)->first();

                $from = $legalTranslation->document_language;
                $to = $legalTranslation->translation_language;
                $pages = $legalTranslation->no_of_pages;
                $priority = $legalTranslation->priority_level;
                $receive_by = $legalTranslation->receive_by;
                $doc_type = $legalTranslation->document_type;
                $subdoc_type = $legalTranslation->document_sub_type;

                $assignment = DefaultTranslatorAssignment::where([
                    'from_language_id' => $from,
                    'to_language_id'   => $to,
                ])->first();

                $userToNotify = User::find()->where('translator_id', $assignment->translator_id);

                if ($userToNotify) {
                    $userToNotify->notify(new ServiceRequestSubmitted($serviceRequest));
                }

                if ($assignment) {
                    $rate = TranslatorLanguageRate::with(['deliveries' => function ($q) use ($priority, $receive_by) {
                        $q->where('priority_type', $priority)
                            ->where('delivery_type', $receive_by);
                    }])
                        ->where('translator_id', $assignment->translator_id)
                        ->where('from_language_id', $from)
                        ->where('to_language_id', $to)
                        ->where('doc_type_id', $doc_type)
                        ->where('doc_subtype_id', $subdoc_type)
                        ->where('status', 1)
                        ->first();

                    if ($rate) {
                        $totalHours = 0;
                        if ($priority === 'normal') {
                            if ($pages <= 10) {
                                $totalHours = $rate->normal_hours_1_10;
                            } elseif ($pages <= 20) {
                                $totalHours = $rate->normal_hours_11_20;
                            } elseif ($pages <= 30) {
                                $totalHours = $rate->normal_hours_21_30;
                            } elseif ($pages <= 50) {
                                $totalHours = $rate->normal_hours_31_50;
                            } else {
                                $totalHours = $rate->normal_hours_above_50;
                            }
                        } else {
                            if ($pages <= 10) {
                                $totalHours = $rate->urgent_hours_1_10;
                            } elseif ($pages <= 20) {
                                $totalHours = $rate->urgent_hours_11_20;
                            } elseif ($pages <= 30) {
                                $totalHours = $rate->urgent_hours_21_30;
                            } elseif ($pages <= 50) {
                                $totalHours = $rate->urgent_hours_31_50;
                            } else {
                                $totalHours = $rate->urgent_hours_above_50;
                            }
                        }

                        $delivery = $rate->deliveries->first();
                        $admin_amount = $delivery->admin_amount * $pages;
                        $translator_amount = $delivery->translator_amount * $pages;

                        $totalAmountNoTax = ($admin_amount + $translator_amount + $delivery->delivery_amount);

                        $tax = ($totalAmountNoTax / 100) * 5;

                        $totalAmount = $totalAmountNoTax + $tax;

                        $legalTranslation->update([
                            'assigned_translator_id'    => $assignment->translator_id
                        ]);

                        TranslationAssignmentHistory::create([
                            'request_id'         => $legalTranslation->id,
                            'translator_id'      => $assignment->translator_id,
                            'assigned_by'        => NULL,
                            'hours_per_page'     => $totalHours ?? 0,
                            'admin_amount'       => $admin_amount ?? 0,
                            'translator_amount'  => $translator_amount ?? 0,
                            'delivery_amount'    => $delivery->delivery_amount ?? 0,
                            'tax'               => $tax ?? 0,
                            'total_amount'       => $totalAmount ?? 0,
                        ]);
                    }
                }
            }

            if ($serviceRequest->service_slug === 'annual-retainer-agreement') {
                $annualRequest = RequestAnnualAgreement::where('service_request_id', $serviceRequest->id)->first();
                $annualRequest->amount_paid = $paidAmount;
                $annualRequest->save();

                $installment = AnnualAgreementInstallment::where('service_request_id', $serviceRequest->id)
                    ->where('installment_no', 1)->first();
                $installment->status = 'paid';
                $installment->save();
            }

            ServiceRequestTimeline::create([
                'service_request_id'    => $serviceRequest->id,
                'service_slug'  => $serviceRequest->service_slug,
                'status'             => "pending",
            ]);

            Auth::guard('frontend')->user()->notify(new ServiceRequestSubmitted($serviceRequest));

            $usersToNotify = getUsersWithPermissions(['view_service_requests', 'export_service_requests', 'change_request_status', 'manage_service_requests']);
            Notification::send($usersToNotify, new ServiceRequestSubmitted($serviceRequest, true));

            return redirect()->route('user.payment-request-success', ['reqid' => base64_encode($serviceRequest->id)]);
        } else {
            $pageData = getPageDynamicContent('request_payment_failed', $lang);

            if ($serviceRequest->service_slug === 'expert-report') {
                $serviceRequest->update([
                    'payment_status' => 'failed',
                    'request_success'   => 1,
                    'payment_response' => $data,
                ]);
                $referenceCode = $serviceRequest->reference_code;

                Auth::guard('frontend')->user()->notify(new ServiceRequestSubmitted($serviceRequest));

                $usersToNotify = getUsersWithPermissions(['view_service_requests', 'export_service_requests', 'change_request_status', 'manage_service_requests']);
                Notification::send($usersToNotify, new ServiceRequestSubmitted($serviceRequest, true));
            } else {

                $serviceSlug = $serviceRequest->service_slug;
                $requestId   = $serviceRequest->id;

                $serviceModelMap = [
                    'legal-translation' => \App\Models\RequestLegalTranslation::class,
                    'expert-report'     => \App\Models\RequestExpertReport::class,
                    'request-submission' => \App\Models\RequestRequestSubmission::class,
                    'annual-retainer-agreement' => \App\Models\RequestAnnualAgreement::class,
                    'immigration-requests' => \App\Models\RequestImmigration::class
                ];
                $filePath = [
                    'legal-translation' => 'legal_translation',
                    'expert-report'     => 'expert_report',
                    'request-submission' => 'request_submission',
                    'annual-retainer-agreement' => 'annual_retainer_agreement',
                    'immigration-requests' => 'immigration'
                ];

                if (isset($serviceModelMap[$serviceSlug])) {
                    $modelClass = $serviceModelMap[$serviceSlug];
                    $serviceReq = $modelClass::where('service_request_id', $serviceRequest->id)->first();
                    $serviceReqId = $serviceReq->id;

                    $serviceReq->delete();

                    deleteRequestFolder($filePath[$serviceSlug], $serviceReqId);
                }
                $serviceRequest->delete();

                $referenceCode = '';
            }
            return redirect()->route('user.payment-request-success', ['reqid' => base64_encode($serviceRequest->id)]);
        }

        return redirect()->route('user.dashboard')->with('error', 'Payment failed or cancelled.');
    }

    public function paymentCancel(Request $request)
    {
        $ref = $request->get('ref');

        $serviceRequest = ServiceRequest::where('payment_reference', $ref)->first();

        if ($serviceRequest) {
            $serviceSlug = $serviceRequest->service_slug;
            $requestId   = $serviceRequest->id;

            $serviceModelMap = [
                'legal-translation' => \App\Models\RequestLegalTranslation::class,
                'expert-report'     => \App\Models\RequestExpertReport::class,
                'request-submission' => \App\Models\RequestRequestSubmission::class,
                'annual-retainer-agreement' => \App\Models\RequestAnnualAgreement::class,
                'immigration-requests' => \App\Models\RequestImmigration::class
            ];
            $filePath = [
                'legal-translation' => 'legal_translation',
                'expert-report'     => 'expert_report',
                'request-submission' => 'request_submission',
                'annual-retainer-agreement' => 'annual_retainer_agreement',
                'immigration-requests' => 'immigration'
            ];

            if (isset($serviceModelMap[$serviceSlug])) {
                $modelClass = $serviceModelMap[$serviceSlug];
                $serviceReq = $modelClass::where('service_request_id', $serviceRequest->id)->first();
                $serviceReqId = $serviceReq->id;

                $serviceReq->delete();
                deleteRequestFolder($filePath[$serviceSlug], $serviceReqId);
            }
            $serviceRequest->delete();
        }

        return redirect()->route('user.dashboard')->with('error', __('frontend.request_cancelled'));
    }

    public function getRequestSubmissionPrice(Request $request)
    {
        $lang       = app()->getLocale() ?? env('APP_LOCALE', 'en');

        $litigation_type    = $request->query('litigation_type') ?? NULL;
        $litigation_place   = $request->query('litigation_place') ?? NULL;
        $case_type          = $request->query('case_type') ?? NULL;
        $request_type       = $request->query('request_type') ?? NULL;
        $request_title      = $request->query('request_title') ?? NULL;

        if ($litigation_type === NULL || $litigation_place === NULL || $case_type === NULL || $request_type === NULL || $request_title === NULL) {
            return response()->json([
                'status'    => true,
                'message'   => 'Success',
                'data'      => [
                    'admin_fee' => 0,
                    'govt_fee' => 0,
                    'tax'       => 0,
                    'total'     => 0,
                ]
            ], 200);
        }

        $base = RequestSubmissionPricing::where('litigation_type', $litigation_type)
            ->where('litigation_place', $litigation_place)
            ->where('case_type_id', $case_type)
            ->where('request_type_id', $request_type)
            ->where('request_title_id', $request_title)
            ->where('status', 1)
            ->first();
        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => [
                'admin_fee' => (float)($base->admin_fee ?? 0),
                'govt_fee' => (float)($base->govt_fee ?? 0),
                'tax'       => (float)($base->vat ?? 0),
                'total'     => (float)($base->total_amount ?? 0),
            ]
        ], 200);
    }

    public function getExpertReportPrice(Request $request)
    {
        $lang       = app()->getLocale() ?? env('APP_LOCALE', 'en');

        $litigation_type    = $request->query('litigation_type') ?? NULL;
        $report_type        = $request->query('report_type') ?? NULL;
        $report_language    = $request->query('report_language') ?? NULL;

        if ($litigation_type === NULL || $report_type === NULL || $report_language === NULL) {
            return response()->json([
                'status'    => true,
                'message'   => 'Success',
                'data'      => [
                    'admin_fee' => 0,
                    'tax'       => 0,
                    'total'     => 0,
                ]
            ], 200);
        }

        $base = ExpertReportPricing::where('litigation_type', $litigation_type)
            ->where('expert_report_type_id', $report_type)
            ->where('language_id', $report_language)
            ->where('status', 1)
            ->first();
        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => [
                'admin_fee' => (float)($base->admin_fee ?? 0),
                'tax'       => (float)($base->vat ?? 0),
                'total'     => (float)($base->total ?? 0),
            ]
        ], 200);
    }

    public function reUploadAfterRejection(Request $request, $id)
    {
        $user = Auth::guard('frontend')->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => __('frontend.unauthorized')], 403);
        }

        $serviceRequest = ServiceRequest::with('statusHistories')->findOrFail($id);

        if ($serviceRequest->user_id != $user->id) {
            return response()->json(['success' => false, 'message' => __('frontend.unauthorized')], 403);
        }

        if ($serviceRequest->status !== 'rejected') {
            return response()->json(['success' => false, 'message' => __('status_not_rejected')], 400);
        }

        $rejectionDetails = $serviceRequest->getLatestRejectionDetails();
        $rejectionMeta = $rejectionDetails ? $rejectionDetails->meta : [];

        $requiredFiles = [];
        $validationRules = [];
        $customMessages = [];
        $needsAtLeastOneFile = false;

        if (isset($rejectionMeta['rejection_details'])) {
            $rejectionDetailsMeta = $rejectionMeta['rejection_details'];

            $supportingDocsRequired = isset($rejectionDetailsMeta['supporting_docs']) && $rejectionDetailsMeta['supporting_docs'];
            $supportingDocsAnyRequired = isset($rejectionDetailsMeta['supporting_docs_any']) && $rejectionDetailsMeta['supporting_docs_any'];

            if ($supportingDocsRequired || $supportingDocsAnyRequired) {
                $needsAtLeastOneFile = true;

                if ($supportingDocsRequired) {
                    $requiredFiles[] = 'supporting_docs';
                }
                if ($supportingDocsAnyRequired) {
                    $requiredFiles[] = 'supporting_docs_any';
                }

                $validationRules['supporting_docs'] = 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240';
                $validationRules['supporting_docs_any'] = 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240';

                $customMessages['supporting_docs.file'] = __('frontend.supporting_docs_must_be_file');
                $customMessages['supporting_docs.mimes'] = __('frontend.supporting_docs_invalid_type');
                $customMessages['supporting_docs.max'] = __('frontend.supporting_docs_too_large');

                $customMessages['supporting_docs_any.file'] = __('frontend.supporting_docs_any_must_be_file');
                $customMessages['supporting_docs_any.mimes'] = __('frontend.supporting_docs_any_invalid_type');
                $customMessages['supporting_docs_any.max'] = __('frontend.supporting_docs_any_too_large');
            }
        }

        try {
            $validatedData = $request->validate($validationRules, $customMessages);

            if ($needsAtLeastOneFile) {
                if (!$request->hasFile('supporting_docs') && !$request->hasFile('supporting_docs_any')) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'files' => [__('frontend.at_least_one_file_required')]
                    ]);
                }
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => __('frontend.validation_failed'),
                'errors' => $e->errors(),
                'required_files' => $requiredFiles
            ], 422);
        }


        $relation = getServiceRelationName($serviceRequest->service_slug);
        $serviceDetails = null;

        if ($relation) {
            $serviceDetails = $serviceRequest->$relation;
        }

        if (!$serviceDetails) {
            return response()->json(['success' => false, 'message' => 'Service details not found'], 404);
        }

        $requestFolder = "uploads/{$serviceRequest->service_slug}/{$serviceDetails->id}/";

        foreach ($requiredFiles as $fileName) {
            if ($request->hasFile($fileName)) {
                $file = $request->file($fileName);

                $uniqueName = $fileName . '_' . uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                $filename = $requestFolder . $uniqueName;

                $fileContents = file_get_contents($file);
                Storage::disk('public')->put($filename, $fileContents);
                $fileUrl = Storage::url($filename);

                $fieldMapping = [
                    'supporting_docs' => 'documents',
                    'supporting_docs_any' => 'additional_documents'
                ];

                if (isset($fieldMapping[$fileName])) {
                    $field = $fieldMapping[$fileName];
                    $updatedFiles = [$fileUrl];
                    $serviceDetails->update([$field => $updatedFiles]);
                }
            }
        }

        $serviceRequest->update(['status' => 'pending']);

        ServiceRequestTimeline::create([
            'service_request_id' => $serviceRequest->id,
            'service_slug' => $serviceRequest->service_slug,
            'status' => 'pending',
            'note' => 'Files re-uploaded after rejection',
            'changed_by' => $user->id,
            'meta' => [
                'action' => 'reupload_after_rejection',
                'files_uploaded' => $requiredFiles
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Files uploaded successfully and status changed to pending',
        ]);
    }
}
