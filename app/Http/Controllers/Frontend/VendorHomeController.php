<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Models\Service;
use App\Models\Dropdown;
use App\Models\Vendor;
use App\Models\Language;
use App\Models\User;
use App\Models\Emirate;
use App\Models\Lawyer;
use App\Models\TrainingRequest;
use App\Models\Contacts;
use App\Models\VendorSubscription;
use App\Models\DropdownOption;
use App\Models\TranslationLanguage;
use App\Models\DocumentType;
use App\Models\ServiceRequest;
use App\Models\DefaultTranslatorAssignment;
use App\Models\TranslatorLanguageRate;
use App\Models\MembershipPlanLanguageRate;
use App\Models\RequestLegalTranslation;
use App\Models\TranslationAssignmentHistory;
use App\Models\ServiceRequestTimeline;
use App\Models\Translator;
use App\Models\JobPost;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use App\Notifications\ServiceRequestSubmitted;
use App\Services\ServiceRequestFileService;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Carbon\Carbon;

class VendorHomeController extends Controller
{
 
     public function dashboard()
    {
        $translatorId = Auth::id();
        $lawfirmId = Auth::guard('frontend')->user()->vendor?->id;

        $lang = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $services = \App\Models\Service::with('translations')->get();

        $totalLawyers = Lawyer::where('lawfirm_id',$lawfirmId)->count();

        $legalTranslationRequests = RequestLegalTranslation::where('assigned_translator_id', Auth::guard('frontend')->user()->translator?->id)
            ->with(['serviceRequest', 'documentLanguage', 'translationLanguage'])
            ->get();

        $totalTranslations = $legalTranslationRequests->count();

        $completedTranslations = $legalTranslationRequests->filter(function ($item) {
            return $item->serviceRequest && in_array($item->serviceRequest->status, ['completed']);
        })->count();

        $pendingTranslations = $legalTranslationRequests->filter(function ($item) {
            return $item->serviceRequest && in_array($item->serviceRequest->status, ['pending']);
        })->count();

        $inProgressTranslations = $legalTranslationRequests->filter(function ($item) {
            return $item->serviceRequest && in_array($item->serviceRequest->status, ['under_review', 'ongoing']);
        })->count();

        $currentMonthIncome = $legalTranslationRequests->filter(function ($item) {
            return $item->serviceRequest &&
                $item->serviceRequest->paid_at &&
                Carbon::parse($item->serviceRequest->paid_at)->isCurrentMonth()
                && in_array($item->serviceRequest->status, ['completed']);
        })->sum('translator_amount');

        $totalIncome = $legalTranslationRequests->filter(function ($item) {
            return $item->serviceRequest &&
                $item->serviceRequest->paid_at
                && in_array($item->serviceRequest->status, ['completed']);
        })->sum('translator_amount');

        $currentYear = Carbon::now()->year;
        $year = request()->get('consultation_year', $currentYear);

        $monthlyTranslations = RequestLegalTranslation::where('assigned_translator_id', Auth::guard('frontend')->user()->translator?->id)
            ->whereHas('serviceRequest', function ($query) use ($year) {
                $query->whereYear('created_at', $year);
            })
            ->with(['serviceRequest'])
            ->get()
            ->groupBy(function ($item) {
                return $item->serviceRequest ? Carbon::parse($item->serviceRequest->created_at)->format('m') : null;
            })
            ->map(function ($group) {
                return $group->count();
            })
            ->toArray();


        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $month = str_pad($i, 2, '0', STR_PAD_LEFT);
            $monthlyData[$month] = $monthlyTranslations[$month] ?? 0;
        }


        $serviceRequests = $legalTranslationRequests
            ->sortByDesc(function ($item) {
                return $item->serviceRequest ? $item->serviceRequest->created_at : $item->created_at;
            })
            ->take(10)
            ->map(function ($item) {
                $serviceRequest = $item->serviceRequest;
                return [
                    'reference_code' => $serviceRequest ? $serviceRequest->reference_code : 'N/A',
                    'date_time' => $serviceRequest ? $serviceRequest->created_at->format('Y-m-d H:i A') : $item->created_at->format('Y-m-d H:i A'),
                    'document_language' => $item->documentLanguage ? $item->documentLanguage->name : 'N/A',
                    'translation_language' => $item->translationLanguage ? $item->translationLanguage->name : 'N/A',
                    'no_of_pages' => $item->no_of_pages ?? 'N/A',
                    'status' => $serviceRequest ? $serviceRequest->status : 'N/A',
                    'service_request_id' => $serviceRequest ? $serviceRequest->id : null
                ];
            });

        $notificationsResult = $result = $this->getTranslatorNotifications();
        $notifications = $notificationsResult['notifications'];

        $totalTranslations = RequestLegalTranslation::where('user_id', Auth::guard('frontend')->user()?->id)->count();
        $totalJobs = JobPost::where('user_id', Auth::guard('frontend')->user()?->id)->count();

        return view('frontend.vendor.dashboard', compact('totalLawyers','totalJobs',
            'totalTranslations',
            'completedTranslations',
            'pendingTranslations',
            'inProgressTranslations',
            'currentMonthIncome',
            'serviceRequests',
            'totalIncome',
            'notifications',
            'monthlyData',
            'year'
        ));
    }

     public function getTranslatorNotifications()
    {
        $lang       = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $services   = \App\Models\Service::with('translations')->get();

        $serviceMap = [];

        foreach ($services as $service) {
            foreach ($service->translations as $translation) {
                $serviceMap[$service->slug][$translation->lang] = $translation->title;
            }
        }

        $allNotifications =  Auth::guard('frontend')->user()->notifications();

        $paginatedNot = (clone $allNotifications)
            ->orderByDesc('created_at')
            ->paginate(10);

        $notifications = collect($paginatedNot->items())
            ->map(function ($notification) use ($lang, $serviceMap) {
                $data = $notification->data;
                $slug = $data['service'] ?? null;

                $serviceName =  $slug && isset($serviceMap[$slug]) ? ($serviceMap[$slug][$lang] ?? $serviceMap[$slug][env('APP_LOCALE', 'en')] ?? $slug) : '';

                return [
                    'id'   => $notification->id,
                    'message'   => __($notification->data['message'], [
                        'service'   => $serviceName,
                        'reference' => $data['reference_code'],
                    ]),
                    'time'      => $notification->created_at->format('d M, Y h:i A'),
                ];
            });

        return [
            'notifications' => $notifications,
            'paginatedNot'  => $paginatedNot,
        ];
    }

    public function lawyers(Request $request){
        $lang = app()->getLocale() ?? env('APP_LOCALE','en'); 
        
        $request->session()->put('lawyers_last_url', url()->full());

        $lawfirmId = Auth::guard('frontend')->user()->vendor?->id;
    
        $query = Lawyer::with('lawfirm', 'emirate')->where('lawfirm_id', $lawfirmId);

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword){
                $q->where('full_name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%")
                    ->orWhere('ref_no', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('status')) {
            // 1 = active, 2 = inactive; 
            $query->whereHas('user', function ($q) use ($request) {
                 if ($request->status == 1) {
                    $q->where('banned', 0);
                } elseif ($request->status == 2) {
                    $q->where('banned', 1);
                }
            });
        }

        if ($request->filled('specialities')) {
            $query->whereHas('specialities', function ($q) use ($request) {
                $q->whereIn('dropdown_option_id', (array) $request->specialities);
            });
        }

        $lawyers = $query->orderBy('id', 'DESC')->paginate(12);
        $dropdowns = Dropdown::with(['options.translations'])->whereIn('slug', ['specialities'])->get()->keyBy('slug');
        return view('frontend.vendor.lawyers.index', compact('lang','lawyers','dropdowns'));
    }

    public function createLawyer(){
        $lang = app()->getLocale() ?? env('APP_LOCALE','en'); 
        
        $dropdowns = Dropdown::with(['options.translations'])->whereIn('slug', ['specialities', 'languages', 'years_experience'])->get()->keyBy('slug');

        $languages = Language::where('status', 1)->get();

        if(checkLawyerLimit()){
            return view('frontend.vendor.lawyers.create', compact('lang', 'dropdowns', 'languages'));
        }else{
            session()->flash('error', __('frontend.lawyer_limit_reached'));
            return redirect()->route('vendor.lawyers');
        }
    }

    public function storeLawyer(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'translations.en.name' => 'required|string|max:255',
            'email' => [
                    'required',
                    'email',
                    Rule::unique('users', 'email')
                        ->where('user_type', 'lawyer'),
                ],
            'phone' => 'required|string|max:20',
            'emirate_id' => 'required|string',
            'gender' => 'required|in:male,female',
            'dob' => 'nullable|date',
            'country' => 'required',
            'experience' => 'required',
            'specialities' => 'required|array',
            'languages' => 'required|array',
            'emirates_id_expiry' => 'required|date',
            'passport_expiry' => 'required|date',
            'bar_card_expiry' => 'required|date',
            'ministry_of_justice_card_expiry' => 'required|date',
            'password' => 'required|string|min:6|confirmed',
            'emirates_id_front' => 'required|file|mimes:jpg,jpeg,webp,png,svg,pdf',
            'emirates_id_back' => 'required|file|mimes:jpg,jpeg,webp,png,svg,pdf',
            'passport' => 'required|file|mimes:jpg,jpeg,png,svg,pdf,webp',
            'bar_card' => 'required|file|mimes:jpg,jpeg,png,svg,pdf,webp',
            'ministry_of_justice_card' => 'required|file|mimes:jpg,jpeg,png,svg,pdf,webp',
            'photo' => 'nullable|file|mimes:jpg,jpeg,png,svg,webp'
        ],[
            '*.required' => __('frontend.this_field_required'),
        ]);

        if ($validated->fails()) {
            return redirect()->back()->withErrors($validated)->withInput();
        }

        DB::transaction(function () use ($request, $validated) {
            $user = User::create([
                'name' => $request->translations['en']['name'],
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'user_type' => 'lawyer',
            ]);

            $lawfirmId = Auth::guard('frontend')->user()->vendor?->id;

            $lawyer = new Lawyer([
                'lawfirm_id'                        => $lawfirmId,
                'full_name'                         => $request->translations['en']['name'], 
                'email'                             => $request->email,
                'phone'                             => $request->phone, 
                'gender'                            => $request->gender, 
                'date_of_birth'                     => $request->dob ? Carbon::parse($request->dob)->format('Y-m-d') : null,
                'emirate_id'                        => $request->emirate_id, 
                'nationality'                       => $request->country, 
                'years_of_experience'               => $request->experience, 
                'working_hours'                     => $request->working_hours,     
                'profile_photo'                     => $request->hasfile('photo') ? uploadImage('lawyers/'.$user->id, $request->photo, 'lawyer') : NULL,
                'emirate_id_front'                  => $request->hasfile('emirates_id_front') ? uploadImage('lawyers/'.$user->id, $request->emirates_id_front, 'emirate_id_front') : NULL,
                'emirate_id_back'                   => $request->hasfile('emirates_id_back') ? uploadImage('lawyers/'.$user->id, $request->emirates_id_back, 'emirate_id_back') : NULL,
                'emirate_id_expiry'                 => $request->emirates_id_expiry ? Carbon::parse($request->emirates_id_expiry)->format('Y-m-d') : null,
                'passport'                          => $request->hasfile('passport') ? uploadImage('lawyers/'.$user->id, $request->passport, 'passport') : NULL,
                'passport_expiry'                   => $request->passport_expiry ? Carbon::parse($request->passport_expiry)->format('Y-m-d') : null,
                'residence_visa'                    => $request->hasfile('residence_visa') ? uploadImage('lawyers/'.$user->id, $request->residence_visa, 'residence_visa') : NULL,
                'residence_visa_expiry'             => $request->residence_visa_expiry ? Carbon::parse($request->residence_visa_expiry)->format('Y-m-d') : null,
                'bar_card'                          => $request->hasfile('bar_card') ? uploadImage('lawyers/'.$user->id, $request->bar_card, 'bar_card') : NULL,
                'bar_card_expiry'                   => $request->bar_card_expiry ? Carbon::parse($request->bar_card_expiry)->format('Y-m-d') : null,
                'practicing_lawyer_card'            => $request->hasfile('ministry_of_justice_card') ? uploadImage('lawyers/'.$user->id, $request->ministry_of_justice_card, 'lawyer_card') : NULL,
                'practicing_lawyer_card_expiry'     => $request->ministry_of_justice_card_expiry ? Carbon::parse($request->ministry_of_justice_card_expiry)->format('Y-m-d') : null,
            ]);
            
            $user->lawyer()->save($lawyer);

            foreach ($request->translations as $lang => $fields) {
                if (!empty($fields['name'])) {
                    $lawyer->translations()->create([
                        'lang' => $lang,
                        'full_name' => $fields['name']
                    ]);
                }
            }

            $dropdowns = collect([
                'specialities' => $request->specialities,
                'languages' => $request->languages,
            ]);

            foreach ($dropdowns as $slug => $optionIds) {
                if (!empty($optionIds)) {
                    $attachData = [];
                    foreach ($optionIds as $optionId) {
                        $attachData[$optionId] = ['type' => $slug];
                    }
                    $lawyer->dropdownOptions()->attach($attachData);
                }
            }
        });
        session()->flash('success',__('frontend.lawyer_created_successfully'));
        return redirect()->route('vendor.lawyers');
    }

    public function editLawyer($id){
        $id = base64_decode($id);
        $lang = app()->getLocale() ?? env('APP_LOCALE','en'); 
        
        $dropdowns = Dropdown::with(['options.translations'])->whereIn('slug', ['specialities', 'languages', 'years_experience'])->get()->keyBy('slug');

        $languages = Language::where('status', 1)->get();

        $lawyer = Lawyer::with('lawfirm', 'emirate')->findOrFail($id);

        $specialityIds = $lawyer->dropdownOptions()->wherePivot('type', 'specialities')->pluck('dropdown_option_id')->toArray();
        $languageIds = $lawyer->dropdownOptions()->wherePivot('type', 'languages')->pluck('dropdown_option_id')->toArray();

        return view('frontend.vendor.lawyers.edit', compact('lang', 'dropdowns', 'languages','lawyer','specialityIds','languageIds'));
    }

    public function updateLawyer(Request $request, $id){
        $lawyer = Lawyer::with(['lawfirm', 'emirate'])->findOrFail($id);
        $user = $lawyer->user;

        $validated = Validator::make($request->all(), [
            'translations.en.name' => 'required|string|max:255',
            // 'email' => [
            //         'required',
            //         'email',
            //         Rule::unique('users', 'email')
            //             ->ignore($user->id)
            //             ->where('user_type', 'lawyer'),
            //     ],
            'phone' => 'required|string|max:20',
            'emirate_id' => 'required|string',
            'gender' => 'required|in:male,female',
            'dob' => 'nullable|date',
            'country' => 'required',
            'experience' => 'required',
            'specialities' => 'required|array',
            'languages' => 'required|array',
            'emirates_id_expiry' => 'required|date',
            'passport_expiry' => 'required|date',
            'bar_card_expiry' => 'required|date',
            'ministry_of_justice_card_expiry' => 'required|date',
            'password' => 'nullable|string|min:6|confirmed',
            'emirates_id_front' => 'nullable|file|mimes:jpg,jpeg,webp,png,svg,pdf',
            'emirates_id_back' => 'nullable|file|mimes:jpg,jpeg,webp,png,svg,pdf',
            'passport' => 'nullable|file|mimes:jpg,jpeg,png,svg,pdf,webp',
            'bar_card' => 'nullable|file|mimes:jpg,jpeg,png,svg,pdf,webp',
            'practicing_lawyer_card' => 'nullable|file|mimes:jpg,jpeg,png,svg,pdf,webp',
            'profile_photo' => 'nullable|file|mimes:jpg,jpeg,png,svg,webp'
        ],[
            '*.required' => __('frontend.this_field_required'),
        ]);

        if ($validated->fails()) {
            return redirect()->back()->withErrors($validated)->withInput();
        }

        $user->update([
            'name' => $request->translations['en']['name'],
            // 'email' => $request->email,
            'phone' => $request->phone,
            'banned' => $request->status ?? $user->banned,
            'password' => $request->filled('password') ? Hash::make($request->password) : $user->password
        ]);

        $uploadPath = 'lawyers/' . $user->id;

        $lawyer->update([
            'full_name'                         => $request->translations['en']['name'], 
            // 'email'                             => $request->email,
            'phone'                             => $request->phone, 
            'gender'                            => $request->gender, 
            'date_of_birth'                     => $request->dob ? Carbon::parse($request->dob)->format('Y-m-d') : null,
            'emirate_id'                        => $request->emirate_id, 
            'nationality'                       => $request->country, 
            'years_of_experience'               => $request->experience, 
            'working_hours'                     => $request->working_hours,  
            'profile_photo'                     => $this->replaceFile($request, 'profile_photo', $lawyer, $uploadPath, 'lawyer'),
            'emirate_id_front'                  => $this->replaceFile($request, 'emirate_id_front', $lawyer, $uploadPath, 'emirate_id_front'),
            'emirate_id_back'                   => $this->replaceFile($request, 'emirate_id_back', $lawyer, $uploadPath, 'emirate_id_back'),
            'emirate_id_expiry'                 => $request->emirates_id_expiry ? Carbon::parse($request->emirates_id_expiry)->format('Y-m-d') : $lawyer->emirate_id_expiry, 
            'passport'                          => $this->replaceFile($request, 'passport', $lawyer, $uploadPath, 'passport'),
            'passport_expiry'                   => $request->passport_expiry ? Carbon::parse($request->passport_expiry)->format('Y-m-d') : $lawyer->passport_expiry,
            'residence_visa'                    => $this->replaceFile($request, 'residence_visa', $lawyer, $uploadPath, 'residence_visa'),
            'residence_visa_expiry'             => $request->residence_visa_expiry ? Carbon::parse($request->residence_visa_expiry)->format('Y-m-d') : $lawyer->residence_visa_expiry,
            'bar_card'                          => $this->replaceFile($request, 'bar_card', $lawyer, $uploadPath, 'bar_card'),
            'bar_card_expiry'                   => $request->bar_card_expiry ? Carbon::parse($request->bar_card_expiry)->format('Y-m-d') : $lawyer->bar_card_expiry,
            'practicing_lawyer_card'            => $this->replaceFile($request, 'practicing_lawyer_card', $lawyer, $uploadPath, 'lawyer_card'),
            'practicing_lawyer_card_expiry'     => $request->ministry_of_justice_card_expiry ? Carbon::parse($request->ministry_of_justice_card_expiry)->format('Y-m-d') : $lawyer->ministry_of_justice_card_expiry  
        ]);

        $user->lawyer()->save($lawyer);

        foreach ($request->translations as $lang => $fields) {
            if (!empty($fields['name'])) {
                $lawyer->translations()->updateOrCreate(
                    ['lang' => $lang],
                    ['full_name' => $fields['name']]
                );
            }
        }

        $dropdowns = collect([
            'specialities' => $request->specialities,
            'languages' => $request->languages,
        ]);

        foreach ($dropdowns as $type => $optionIds) {
            $lawyer->dropdownOptions()
                ->wherePivot('type', $type)
                ->detach();

            if (!empty($optionIds)) {
                $attachData = [];
                foreach ($optionIds as $optionId) {
                    $attachData[$optionId] = ['type' => $type];
                }
                $lawyer->dropdownOptions()->attach($attachData);
            }
        }

        session()->flash('success',__('frontend.lawyer_updated_successfully'));
        return redirect()->route('vendor.lawyers');
    }

    function replaceFile($request, $fieldName, $lawyer, $uploadPath, $fileName = 'image_') {
        
        if ($request->hasFile($fieldName)) {
            if (!empty($lawyer->$fieldName)) {
                $pathToDelete = str_replace('/storage/', '', $lawyer->$fieldName);
                
                if (Storage::disk('public')->exists($pathToDelete)) {
                    Storage::disk('public')->delete($pathToDelete);
                }
            }
            return uploadImage($uploadPath, $request->file($fieldName), $fileName);
        }
        return $lawyer->$fieldName;
    }

    public function viewLawyer($id){
        $id = base64_decode($id);
        $lang = app()->getLocale() ?? env('APP_LOCALE','en'); 
       
        $lawyer = Lawyer::with('lawfirm', 'emirate')->findOrFail($id);
    
        $specialityIds = $lawyer->dropdownOptions()->wherePivot('type', 'specialities')->pluck('dropdown_option_id')->toArray();
        $languageIds = $lawyer->dropdownOptions()->wherePivot('type', 'languages')->pluck('dropdown_option_id')->toArray();

        return view('frontend.vendor.lawyers.show', compact('lang', 'lawyer','specialityIds','languageIds'));
    }

    public function trainingRequests(Request $request){
        $lang = env('APP_LOCALE', 'en');
        $query = TrainingRequest::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('emirate_id')) {
            $query->where('emirate_id', $request->emirate_id);
        }

        if ($request->filled('residency_status')) {
            $query->where('residency_status', $request->residency_status);
        }

        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(10);

        $dropdowns  = Dropdown::with([
                        'options' => function ($q) {
                            $q->where('status', 'active')->orderBy('sort_order');
                        },
                        'options.translations' => function ($q) use ($lang) {
                            $q->whereIn('language_code', [$lang, 'en']);
                        }
                    ])->whereIn('slug', ['training_positions','residency_status'])->get()->keyBy('slug');

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

        return view('frontend.vendor.training-requests', compact('requests', 'response'));
    }

    public function translationRequests (Request $request){
        $lang = app()->getLocale() ?? env('APP_LOCALE', 'en');

        $query = RequestLegalTranslation::where('user_id', Auth::guard('frontend')->user()?->id)
            ->with(['serviceRequest', 'documentLanguage', 'translationLanguage']);

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($q) use ($search) {
                $q->where('no_of_pages', 'LIKE', "%{$search}%");

                $q->orWhereHas('serviceRequest', function ($sq) use ($search) {
                    $sq->where('reference_code', 'LIKE', "%{$search}%")
                        ->orWhere('status', 'LIKE', "%{$search}%");
                });

                $q->orWhereHas('documentLanguage', function ($lq) use ($search) {
                    $lq->where('name', 'LIKE', "%{$search}%");
                });

                $q->orWhereHas('translationLanguage', function ($lq) use ($search) {
                    $lq->where('name', 'LIKE', "%{$search}%");
                });
            });
        }

        if ($request->filled('date_range') && !empty($request->date_range)) {
            $dateRange = $request->date_range;
            $dateRange = explode(' - ', $dateRange);

            if (count($dateRange) == 2) {
                $date1 = trim($dateRange[0]);
                $date2 = trim($dateRange[1]);

                $dateFromParsed = \Carbon\Carbon::createFromFormat('Y-m-d', $date1);
                $dateToParsed = \Carbon\Carbon::createFromFormat('Y-m-d', $date2);

                if ($dateFromParsed && $dateToParsed) {
                    $dateFrom = $dateFromParsed->startOfDay();
                    $dateTo = $dateToParsed->endOfDay();

                    $query->whereHas('serviceRequest', function ($q) use ($dateFrom, $dateTo) {
                        $q->whereBetween('created_at', [$dateFrom, $dateTo]);
                    });
                }
            }
        }

        if ($request->has('language_pair') && !empty($request->language_pair) && $request->language_pair !== 'all') {
            $languagePair = $request->language_pair;
            $parts = explode(' - ', $languagePair);
            if (count($parts) == 2) {
                $fromLanguage = $parts[0];
                $toLanguage = $parts[1];

                $query->whereHas('documentLanguage', function ($subQ) use ($fromLanguage) {
                    $subQ->where('name', 'LIKE', "%{$fromLanguage}%");
                })->whereHas('translationLanguage', function ($subQ) use ($toLanguage) {
                    $subQ->where('name', 'LIKE', "%{$toLanguage}%");
                });
            }
        }

        if ($request->has('status') && !empty($request->status) && $request->status !== 'all') {
            $status = $request->status;
            $query->whereHas('serviceRequest', function ($q) use ($status) {
                $q->where('status', $status);
            });
        }

        $serviceRequests = $query->paginate(20)->withQueryString();

        return view('frontend.vendor.translation.index', compact('serviceRequests'));
    }

    public function showTranslationRequest($id)
    {
        $lang           = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $user = Auth::guard('frontend')->user();
       
        $serviceRequest = ServiceRequest::with('service', 'statusHistories')->findOrFail($id);

        $relation = getServiceRelationName($serviceRequest->service_slug);

        if (!$relation || !$serviceRequest->relationLoaded($relation)) {
            $serviceRequest->load($relation);
        }

        $serviceDetails = $serviceRequest->$relation;

        if (!$serviceDetails) {
            return redirect()->back()->with('error', __('frontend.no_details_found'));
        }

        $timeline = getFullStatusHistory($serviceRequest);

        $translatedData = getServiceHistoryTranslatedFields($serviceRequest->service_slug, $serviceDetails, $lang);

        $details = [
            'id'                => $serviceRequest->id,
            'service_slug'      => $serviceRequest->service_slug,
            'service_name'      => $serviceRequest->service->getTranslation('title', $lang),
            'reference_code'    => $serviceRequest->reference_code,
            'status'            => $serviceRequest->status ?? "",
            'payment_status'    => $serviceRequest->payment_status,
            'payment_reference' => $serviceRequest->payment_reference,
            'amount'            => $serviceRequest->amount,
            'submitted_at'      => $serviceRequest->submitted_at,
            'created_at'        => ($serviceRequest?->created_at?->format('Y-m-d h:i A')) ?? 'N/A',
            'document_title'    => $serviceRequest?->title ?? 'N/A',
            'sub_document_type' => $serviceRequest?->documentSubType->name ?? 'N/A',
            'payment_status'    => $serviceRequest->payment_status,
            'amount'            => $serviceRequest->amount,
            'service_details'   => $translatedData,
            'timeline'          => $timeline,
        ];

        return view('frontend.vendor.translation.translation-details', compact(
            'details',
        ));
    }


    public function createTranslationRequest(Request $request){
        $lang           = app()->getLocale() ?? env('APP_LOCALE', 'en');

        $dropdownData   = [];

        $emirates   = Emirate::where('status', 1)->orderBy('id')->get()
                        ->map(fn($e) => [
                            'id' => $e->id,
                            'value' => $e->getTranslation('name', $lang)
                        ]);
                        
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

        return view('frontend.vendor.translation.create', compact('dropdownData', 'lang'));
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

    public function calculateTranslationPrice(Request $request)
    {
        $from           = $request->from_language_id;
        $to             = $request->to_language_id;
        $pages          = $request->no_of_pages;
        $priority       = $request->priority ?? null;
        $doc_type       = $request->doc_type;
        $subdoc_type    = $request->doc_sub_type;
        $receive_by     = $request->receive_by ?? null;

        $lawfirmId = Auth::guard('frontend')->user()->vendor?->id;

        $lawfirmPlan = VendorSubscription::where('vendor_id', $lawfirmId)
                        ->where('status', 'active')
                        ->first();
        if (!$lawfirmPlan) {
            return response()->json([
                'status'    => false,
                'message'   => __('messages.translation_not_available'),
            ], 200);
        }

        $rate = MembershipPlanLanguageRate::with(['deliveries' => function ($q) use ($priority, $receive_by) {
                                            $q->where('priority_type', $priority)
                                                ->where('delivery_type', $receive_by);
                                        }])
                                        ->where('membership_plan_id', $lawfirmPlan->membership_plan_id)
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

        $lawfirmId = Auth::guard('frontend')->user()->vendor?->id;

        $lawfirmPlan = VendorSubscription::where('vendor_id', $lawfirmId)
                        ->where('status', 'active')
                        ->first();
        $rate = MembershipPlanLanguageRate::with(['deliveries' => function ($q) use ($priority, $receive_by) {
                                    $q->where('priority_type', $priority)
                                        ->where('delivery_type', $receive_by);
                                }])
                                ->where('membership_plan_id', $lawfirmPlan->membership_plan_id)
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

        $total_amount = $totalAmount ?? 0;
        $currency = env('APP_CURRENCY', 'AED');
        $payment = [];

        if ($total_amount != 0) {
            $customer = [
                'email' => $user->email,
                'name'  => $user->name,
                'phone' => $user->phone,
                'address' => $user->address
            ];

            $orderReference = $service_request->id . '--' . $service_request->reference_code;

            $payment = createWebOrder($customer, $total_amount, env('APP_CURRENCY', 'AED'), $orderReference,'vendor');

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
            return redirect()->route('vendor.payment-request-success', ['reqid' => base64_encode($service_request->id)]);
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
            $serviceRequest->update([
                'payment_status' => 'success',
                'request_success'   => 1,
                'payment_response' => $data,
                'paid_at' => date('Y-m-d h:i:s')
            ]);

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

                $userToNotify = Translator::find($assignment->translator_id);

                if ($userToNotify) {
                    $userNot = User::find($userToNotify->user_id);
                    $userNot->notify(new ServiceRequestSubmitted($serviceRequest));
                }

                if ($assignment) {
                    $lawfirmId = Auth::guard('frontend')->user()->vendor?->id;

                    $lawfirmPlan = VendorSubscription::where('vendor_id', $lawfirmId)
                                    ->where('status', 'active')
                                    ->first();
                    $rate = MembershipPlanLanguageRate::with(['deliveries' => function ($q) use ($priority, $receive_by) {
                                                $q->where('priority_type', $priority)
                                        ->where('delivery_type', $receive_by);
                                }])
                                ->where('membership_plan_id', $lawfirmPlan->membership_plan_id)
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

            ServiceRequestTimeline::create([
                'service_request_id'    => $serviceRequest->id,
                'service_slug'  => $serviceRequest->service_slug,
                'status'             => "pending",
            ]);

            Auth::guard('frontend')->user()->notify(new ServiceRequestSubmitted($serviceRequest));

            $usersToNotify = getUsersWithPermissions(['view_service_requests', 'export_service_requests', 'change_request_status', 'manage_service_requests']);
            Notification::send($usersToNotify, new ServiceRequestSubmitted($serviceRequest, true));

            return redirect()->route('vendor.payment-request-success', ['reqid' => base64_encode($serviceRequest->id)]);
        } else {
            $pageData = getPageDynamicContent('request_payment_failed', $lang);

            $serviceSlug = $serviceRequest->service_slug;
            $requestId   = $serviceRequest->id;

            $serviceModelMap = [
                'legal-translation' => \App\Models\RequestLegalTranslation::class,
            ];
            $filePath = [
                'legal-translation' => 'legal_translation',
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
            return redirect()->route('vendor.payment-request-success', ['reqid' => base64_encode($serviceRequest->id)]);
        }

        return redirect()->route('vendor.dashboard')->with('error', 'Payment failed or cancelled.');
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
            ];
            $filePath = [
                'legal-translation' => 'legal_translation',
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

        return redirect()->route('vendor.dashboard')->with('error', __('frontend.request_cancelled'));
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

            return view('frontend.vendor.translation.request_success', ['data' => $response, 'lang' => $lang]);
        } else {
            $pageData = getPageDynamicContent('request_payment_failed', $lang);
            $response = [
                'reference' => '',
                'message'   => $pageData['content']
            ];

            return view('frontend.vendor.translation.request_failed', ['data' => $response, 'lang' => $lang]);
        }
    }
}
