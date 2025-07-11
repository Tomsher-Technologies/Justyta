<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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

                $dropdownData['emirates'] = $emirates;
                
                break;

            case 'criminal-complaint':
                
                break;

            case 'power-of-attorney':
                
                break;

            case 'last-will-and-testament':
                
                break;
    
            case 'memo-writing':
                
                break;
                    
            case 'expert-report':
                
                break;
                    
            case 'contract-drafting':
                
                break;

            case 'company-setup':
                
                break;

            case 'escrow-accounts':
                
                break;

            case 'debts-collection':
                
                break;

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

        return view('frontend.service-request.dynamic', [
            'service' => $service,
            'dropdownData' => $dropdownData,
        ]);
    }
}
