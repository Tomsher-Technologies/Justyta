<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dropdown;
use App\Models\User;
use App\Models\Translator;
use App\Models\DocumentType;
use App\Models\TranslationLanguage;
use App\Models\TranslatorLanguageRate;
use App\Models\DefaultTranslatorHistory;
use App\Models\DefaultTranslatorAssignment;
use App\Models\DefaultTranslatorAssignmentHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Carbon\Carbon;


class TranslatorController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
       
        $this->middleware('permission:manage_translators',  ['only' => ['index']]);
        $this->middleware('permission:add_translator',  ['only' => ['create','store']]);
        $this->middleware('permission:edit_translator',  ['only' => ['edit','update']]);
        $this->middleware('permission:default_translator',  ['only' => ['showDefaultForm','setDefault']]);
        $this->middleware('permission:view_translator_pricing',  ['only' => ['indexPricing']]);
        $this->middleware('permission:add_translator_pricing',  ['only' => ['createPricing','storePricing']]);
        $this->middleware('permission:edit_translator_pricing',  ['only' => ['editPricing','updatePricing']]);
        $this->middleware('permission:delete_translator_pricing',  ['only' => ['destroyPricing']]);
    }

    public function index(Request $request)
    {
        $request->session()->put('translator_last_url', url()->full());
        $query = Translator::with(['user','languageRates.fromLanguage', 'languageRates.toLanguage']);

        if ($request->filled('language_id')) {
            $selectedLangId = $request->language_id;
            $query->whereHas('languageRates', function ($q) use ($selectedLangId) {
                $q->where('from_language_id', $selectedLangId);
            });
        }

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword){
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%")
                    ->orWhere('company_name', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('status')) {
            $query->whereHas('user', function ($q) use ($request) {
                if ($request->status == 1) {
                    $q->where('banned', 0);
                } elseif ($request->status == 2) {
                    $q->where('banned', 1);
                }
            });
        }

        $translators = $query->orderBy('id', 'DESC')->paginate(15);

        $languages = TranslationLanguage::where('status', 1)->get();
        return view('admin.translators.index', compact('translators', 'languages'));
    }

    public function create()
    {
        $languages = TranslationLanguage::where('status', 1)->get();
        return view('admin.translators.create', compact('languages'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' =>  [
                    'required',
                    'email',
                    Rule::unique('users', 'email')
                        ->where('user_type', 'translator'),
                ],
            'phone' => 'required|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:200',
            'emirate_id' => 'required',
            'country' => 'required',
            'password' => 'required|string|min:6|confirmed',
            'trade_license' => 'nullable|file|mimes:jpg,jpeg,png,svg,pdf,webp|max:200',
            'trade_license_expiry' => 'nullable|date',
            'emirates_id_front' => 'required|file|mimes:jpg,jpeg,png,svg,pdf,webp|max:200',
            'emirates_id_back' => 'required|file|mimes:jpg,jpeg,png,svg,pdf,webp|max:200',
            'emirates_id_expiry' => 'required|date',
            'residence_visa' => 'nullable|file|mimes:jpg,jpeg,png,svg,pdf,webp|max:200',
            'residence_visa_expiry' => 'nullable|date',
            'passport' => 'required|file|mimes:jpg,jpeg,png,svg,pdf,webp|max:200',
            'passport_expiry' => 'required|date',
            'type' => 'required',
        ],[
            '*.required' => 'This field is required.'
        ]);

        if ($validator->fails()) {
            return redirect()->route('translators.create')->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'user_type' => 'translator',
        ]);

        $translator = new Translator([
            'type'                      => $request->type,
            'name'                      => $request->name, 
            'email'                     => $request->email, 
            'phone'                     => $request->phone, 
            'company_name'              => $request->company_name, 
            'emirate_id'                => $request->emirate_id, 
            'image'                     => $request->hasfile('logo') ? uploadImage('translators/'.$user->id, $request->logo, 'image_') : NULL,  
            'country'                   => $request->country, 
            'trade_license'             => $request->hasfile('trade_license') ? uploadImage('translators/'.$user->id, $request->trade_license, 'trade_license_') : NULL,
            'trade_license_expiry'      => $request->trade_license_expiry ? Carbon::parse($request->trade_license_expiry)->format('Y-m-d') : null,
            'emirates_id_front'         => $request->hasfile('emirates_id_front') ? uploadImage('translators/'.$user->id, $request->emirates_id_front, 'emirates_id_front_') : NULL,
            'emirates_id_back'          => $request->hasfile('emirates_id_back') ? uploadImage('translators/'.$user->id, $request->emirates_id_back, 'emirates_id_back_') : NULL,
            'emirates_id_expiry'        => $request->emirates_id_expiry ? Carbon::parse($request->emirates_id_expiry)->format('Y-m-d') : null,
            'residence_visa'            => $request->hasfile('residence_visa') ? uploadImage('translators/'.$user->id, $request->residence_visa, 'residence_visa_') : NULL,
            'residence_visa_expiry'     => $request->residence_visa_expiry ? Carbon::parse($request->residence_visa_expiry)->format('Y-m-d') : null,
            'passport'                  => $request->hasfile('passport') ? uploadImage('translators/'.$user->id, $request->passport, 'passport_') : NULL,
            'passport_expiry'           => $request->passport_expiry ? Carbon::parse($request->passport_expiry)->format('Y-m-d') : null,
            
        ]);
        $translator =  $user->translator()->save($translator);

        session()->flash('success','Translator created successfully.');
        return redirect()->route('translators.index');
    }

    public function edit($id)
    {
        $translator = Translator::with('user','languages.translations', 'emirate')->findOrFail($id);
         
        $languageIds = $translator->dropdownOptions()->wherePivot('type', 'languages')->pluck('dropdown_option_id')->toArray();

        $dropdowns = Dropdown::with(['options.translations' => function ($q) {
                                    $q->where('language_code', 'en');
                                }])->whereIn('slug', ['languages'])->get()->keyBy('slug');

        $languages = TranslationLanguage::where('status', 1)->get();

        return view('admin.translators.edit', compact('translator','dropdowns','languageIds','languages'));
    }

    public function update(Request $request, $id)
    {
        $translator = Translator::with(['user', 'languages.translations'])->findOrFail($id);
        $user = $translator->user;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' =>  [
                    'required',
                    'email',
                    Rule::unique('users', 'email')
                        ->ignore($user->id)
                        ->where('user_type', 'translator'),
                ],
            'phone' => 'required|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:200',
            'emirate_id' => 'required',
            'country' => 'required',
            'password' => 'nullable|string|min:6|confirmed',
            'trade_license' => 'nullable|file|mimes:jpg,jpeg,png,svg,pdf,webp|max:200',
            'trade_license_expiry' => 'nullable|date',
            'emirates_id_front' => 'nullable|file|mimes:jpg,jpeg,png,svg,pdf,webp|max:200',
            'emirates_id_back' => 'nullable|file|mimes:jpg,jpeg,png,svg,pdf,webp|max:200',
            'emirates_id_expiry' => 'required|date',
            'residence_visa' => 'nullable|file|mimes:jpg,jpeg,png,svg,pdf,webp|max:200',
            'residence_visa_expiry' => 'nullable|date',
            'passport' => 'nullable|file|mimes:jpg,jpeg,png,svg,pdf,webp|max:200',
            'passport_expiry' => 'required|date',
            'type' => 'required',
        ],[
            '*.required' => 'This field is required.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->filled('password') ? Hash::make($request->password) : $user->password,
        ]);

        $uploadPath = 'translators/' . $user->id;

        $translator->update([
            'type'                      => $request->type,
            'name'                      => $request->name, 
            'email'                     => $request->email, 
            'phone'                     => $request->phone, 
            'company_name'              => $request->company_name, 
            'emirate_id'                => $request->emirate_id, 
            'image'                     => $this->replaceFile($request, 'image', $translator, $uploadPath, 'profile_'), 
            'country'                   => $request->country, 
            'trade_license'             => $this->replaceFile($request, 'trade_license', $translator, $uploadPath, 'trade_license_'), 

            'trade_license_expiry'      => $request->trade_license_expiry ? Carbon::parse($request->trade_license_expiry)->format('Y-m-d') : $translator->trade_license_expiry,
            'emirates_id_front'         => $this->replaceFile($request, 'emirates_id_front', $translator, $uploadPath, 'emirates_id_front_'),
            'emirates_id_back'          => $this->replaceFile($request, 'emirates_id_back', $translator, $uploadPath, 'emirates_id_back_'), 
            'emirates_id_expiry'        => $request->emirates_id_expiry ? Carbon::parse($request->emirates_id_expiry)->format('Y-m-d') : $translator->emirates_id_expiry,
            'residence_visa'            => $this->replaceFile($request, 'residence_visa', $translator, $uploadPath, 'residence_visa_'), 
        
            'residence_visa_expiry'     => $request->residence_visa_expiry ? Carbon::parse($request->residence_visa_expiry)->format('Y-m-d') : $translator->residence_visa_expiry,
            'passport'                  => $this->replaceFile($request, 'passport', $translator, $uploadPath, 'passport_'), 
            'passport_expiry'           => $request->passport_expiry ? Carbon::parse($request->passport_expiry)->format('Y-m-d') : $translator->passport_expiry,

        ]);

        $translator = $user->translator()->save($translator);

        $dropdowns = collect([
            'languages' => $request->languages
        ]);

        foreach ($dropdowns as $type => $optionIds) {
            $translator->dropdownOptions()
                ->wherePivot('type', $type)
                ->detach();
            if (!empty($optionIds)) {
                $attachData = [];
                foreach ($optionIds as $optionId) {
                    $attachData[$optionId] = ['type' => $type];
                }
                $translator->dropdownOptions()->attach($attachData);
            }
        }

        session()->flash('success', 'Translator details updated successfully.');

        $url =  session()->has('translator_last_url') ? session()->get('translator_last_url') : route('translators.index');
       
        return redirect($url);
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

    public function setDefault(Request $request)
    {
        $request->validate([
            'translator_id' => 'required|exists:translators,id',
        ],[
            'translator_id.required' => 'This field is required.'
        ]);

        $newTranslatorId = $request->translator_id;

        $current = Translator::where('is_default', 1)->first();

       
        if ($current && $current->id != $newTranslatorId) {
            $current->update(['is_default' => 0]);
            DefaultTranslatorHistory::where('translator_id', $current->id)->whereNull('ended_at')->update([
                'ended_at' => Carbon::now(),
            ]);
        }

        $newTranslator = Translator::findOrFail($newTranslatorId);
        $newTranslator->update(['is_default' => 1]);

        $existingHistory = DefaultTranslatorHistory::where('translator_id', $newTranslatorId)
            ->whereNull('ended_at')->first();

        if (!$existingHistory) {
            DefaultTranslatorHistory::create([
                'translator_id' => $newTranslatorId,
                'started_at' => Carbon::now(),
            ]);
        }
        session()->flash('success', 'Default translator updated.');
        return redirect()->route('translators.default');
    }

    public function showDefaultForm()
    {
        $combinations = TranslationLanguage::all()->flatMap(function ($from) {
            return TranslationLanguage::whereIn('id', [1, 3]) 
                ->where('id', '!=', $from->id)
                ->get()
                ->map(function ($to) use ($from) {
                    return (object)[
                        'from' => $from,
                        'to' => $to,
                        'eligible_translators' => Translator::active()->whereHas('languageRates', function ($q) use ($from, $to) {
                            $q->where('from_language_id', $from->id)->where('to_language_id', $to->id);
                        })->get(),
                        'current_default' => DefaultTranslatorAssignment::where([
                            ['from_language_id', $from->id],
                            ['to_language_id', $to->id],
                        ])->first()
                    ];
                });
        });

        return view('admin.translators.default', compact('combinations'));
    }

    public function assign(Request $request)
    {
        $validated = $request->validate([
            'from_language_id' => 'required|exists:translation_languages,id',
            'to_language_id' => 'required|exists:translation_languages,id|different:from_language_id',
            'translator_id' => 'required|exists:translators,id',
        ]);

        $adminId = auth()->id();

        DefaultTranslatorAssignmentHistory::create([
            'from_language_id' => $validated['from_language_id'],
            'to_language_id' => $validated['to_language_id'],
            'translator_id' => $validated['translator_id'],
            'assigned_by' => $adminId,
        ]);

        DefaultTranslatorAssignment::updateOrCreate(
            [
                'from_language_id' => $validated['from_language_id'],
                'to_language_id' => $validated['to_language_id'],
            ],
            [
                'translator_id' => $validated['translator_id'],
                'assigned_by' => $adminId,
                'assigned_at' => now(),
            ]
        );

        session()->flash('success', 'Default translator updated successfully.');
        return redirect()->route('translators.default');
    }

    public function historyForPair($fromLangId, $toLangId)
    {
        $fromLang = TranslationLanguage::findOrFail($fromLangId);
        $toLang = TranslationLanguage::findOrFail($toLangId);

        $histories = DefaultTranslatorAssignmentHistory::with(['translator', 'assignedBy'])
            ->where('from_language_id', $fromLangId)
            ->where('to_language_id', $toLangId)
            ->latest('assigned_at')
            ->paginate(20);

        return view('admin.translators.history-single', compact('fromLang', 'toLang', 'histories'));
    }


    public function indexPricing(Request $request, $id)
    {
        $request->session()->put('translator_pricing_last_url', url()->full());
        $translatorId = base64_decode($id);
        $query = TranslatorLanguageRate::with(['translator','fromLanguage', 'toLanguage','documentType','documentSubType','deliveries'])
                    ->where('translator_id', $translatorId);

        if ($request->filled('status')) {
            if ($request->status == 1) {
                $query->where('status', 1);
            } elseif ($request->status == 2) {
                $query->where('status', 0);
            }
        }

        if ($request->filled('from_language_id')) {
            $query->where('from_language_id', $request->from_language_id);
        }

        if ($request->filled('to_language_id')) {
            $query->where('to_language_id', $request->to_language_id);
        }

        if ($request->filled('doc_type_id')) {
            $query->where('doc_type_id', $request->doc_type_id);
        }

        if ($request->filled('doc_subtype_id')) {
            $query->where('doc_subtype_id', $request->doc_subtype_id);
        }

        $translatorPricing = $query->orderBy('id', 'DESC')->paginate(15); 

        $languages = TranslationLanguage::where('status', 1)->get();
        $translator = Translator::find($translatorId);
        $documentTypes = DocumentType::with('translations')->where('status', 1)
                            ->whereNull('parent_id')
                            ->orderBy('sort_order')
                            ->get();
        return view('admin.translators.index-pricing', compact('documentTypes','translatorPricing','translator', 'languages','translatorId'));
    }

    public function createPricing($id){
        $languages = TranslationLanguage::where('status', 1)->get();
        $documentTypes = DocumentType::with('translations')->where('status', 1)
                            ->whereNull('parent_id')
                            ->orderBy('sort_order')
                            ->get();
        return view('admin.translators.create-pricing', compact('languages','documentTypes','id'));
    }

    public function storePricing(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'from_language'                     => 'required|integer|different:to_language',
            'to_language'                       => 'required|integer',
            'doc_type'                          => 'required|integer',
            'sub_doc_type'                      => 'required|integer',
            'email_delivery_normal_email'       => 'required|numeric|min:0',
            'admin_amount_normal_email'         => 'required|numeric|min:0',
            'translator_amount_normal_email'    => 'required|numeric|min:0',
            'tax_amount_normal_email'           => 'required|numeric|min:0',
            'total_amount_normal_email'         => 'required|numeric|min:0',
            'physical_delivery_normal_physical' => 'required|numeric|min:0',
            'admin_amount_normal_physical'      => 'required|numeric|min:0',
            'translator_amount_normal_physical' => 'required|numeric|min:0',
            'tax_amount_normal_physical'        => 'required|numeric|min:0',
            'total_amount_normal_physical'      => 'required|numeric|min:0',
            'email_delivery_urgent_email'       => 'required|numeric|min:0',
            'admin_amount_urgent_email'         => 'required|numeric|min:0',
            'translator_amount_urgent_email'    => 'required|numeric|min:0',
            'tax_amount_urgent_email'           => 'required|numeric|min:0',
            'total_amount_urgent_email'         => 'required|numeric|min:0',
            'physical_delivery_urgent_physical' => 'required|numeric|min:0',
            'admin_amount_urgent_physical'      => 'required|numeric|min:0',
            'translator_amount_urgent_physical' => 'required|numeric|min:0',
            'tax_amount_urgent_physical'        => 'required|numeric|min:0',
            'total_amount_urgent_physical'      => 'required|numeric|min:0',
            'normal_hours_1_10'                 => 'required|numeric|min:0',
            'normal_hours_11_20'                => 'required|numeric|min:0',
            'normal_hours_21_30'                => 'required|numeric|min:0',
            'normal_hours_31_50'                => 'required|numeric|min:0',
            'normal_hours_above_50'             => 'required|numeric|min:0',
            'urgent_hours_1_10'                 => 'required|numeric|min:0',
            'urgent_hours_11_20'                => 'required|numeric|min:0',
            'urgent_hours_21_30'                => 'required|numeric|min:0',
            'urgent_hours_31_50'                => 'required|numeric|min:0',
            'urgent_hours_above_50'             => 'required|numeric|min:0',
        ],[
            '*.required'                        => 'This field is required.',
            'from_language.different'           => 'From Language and To Language must be different.',
        ]);

        $translatorId = base64_decode($request->translator_id);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $exists = TranslatorLanguageRate::where('translator_id', $translatorId)
                                ->where('from_language_id', $request->from_language)
                                ->where('to_language_id', $request->to_language)
                                ->where('doc_type_id', $request->doc_type)
                                ->where('doc_subtype_id', $request->sub_doc_type)
                                ->exists();

        if ($exists) {
            return redirect()->back()->withInput()->withErrors([
                'from_language' => 'Pricing for this language and document combination already exists.'
            ]);
        }

        $rate = TranslatorLanguageRate::create([
            'translator_id'         => $translatorId,
            'from_language_id'      => $request->from_language,
            'to_language_id'        => $request->to_language,
            'doc_type_id'           => $request->doc_type,
            'doc_subtype_id'        => $request->sub_doc_type,
            'normal_hours_1_10'     => $request->normal_hours_1_10,
            'normal_hours_11_20'    => $request->normal_hours_11_20,
            'normal_hours_21_30'    => $request->normal_hours_21_30,
            'normal_hours_31_50'    => $request->normal_hours_31_50,
            'normal_hours_above_50' => $request->normal_hours_above_50,
            'urgent_hours_1_10'     => $request->urgent_hours_1_10,
            'urgent_hours_11_20'    => $request->urgent_hours_11_20,
            'urgent_hours_21_30'    => $request->urgent_hours_21_30,
            'urgent_hours_31_50'    => $request->urgent_hours_31_50,
            'urgent_hours_above_50' => $request->urgent_hours_above_50,
        ]);

        $deliveries = [
            [
                'priority_type'     => 'normal',
                'delivery_type'     => 'email',
                'delivery_amount'   => $request->email_delivery_normal_email,
                'admin_amount'      => $request->admin_amount_normal_email,
                'translator_amount' => $request->translator_amount_normal_email,
                'tax'               => $request->tax_amount_normal_email,
                'total_amount'      => $request->total_amount_normal_email,
            ],
            [
                'priority_type'     => 'normal',
                'delivery_type'     => 'physical',
                'delivery_amount'   => $request->physical_delivery_normal_physical,
                'admin_amount'      => $request->admin_amount_normal_physical,
                'translator_amount' => $request->translator_amount_normal_physical,
                'tax'               => $request->tax_amount_normal_physical,
                'total_amount'      => $request->total_amount_normal_physical,
            ],
            [
                'priority_type'     => 'urgent',
                'delivery_type'     => 'email',
                'delivery_amount'   => $request->email_delivery_urgent_email,
                'admin_amount'      => $request->admin_amount_urgent_email,
                'translator_amount' => $request->translator_amount_urgent_email,
                'tax'               => $request->tax_amount_urgent_email,
                'total_amount'      => $request->total_amount_urgent_email,
            ],
            [
                'priority_type'     => 'urgent',
                'delivery_type'     => 'physical',
                'delivery_amount'   => $request->physical_delivery_urgent_physical,
                'admin_amount'      => $request->admin_amount_urgent_physical,
                'translator_amount' => $request->translator_amount_urgent_physical,
                'tax'               => $request->tax_amount_urgent_physical,
                'total_amount'      => $request->total_amount_urgent_physical,
            ],
        ];

        foreach ($deliveries as $data) {
            $rate->deliveries()->create($data);
        }
      
        session()->flash('success','Translator pricing created successfully.');
       
        return redirect()->route('translator-pricing',['id' => $request->translator_id]);
    }

    public function editPricing($id, $transId){
        $id = base64_decode($id);
        $pricing = TranslatorLanguageRate::find($id);
        $languages = TranslationLanguage::where('status', 1)->get();
        $documentTypes = DocumentType::with('translations')->where('status', 1)
                            ->whereNull('parent_id')
                            ->orderBy('sort_order')
                            ->get();

        $subdocTypes = DocumentType::with('translations')->where('status', 1)
                            ->where('parent_id', $pricing->doc_type_id)
                            ->orderBy('sort_order')
                            ->get();

        $deliveryValues = [];

        foreach ($pricing->deliveries as $delivery) {
            $priority = $delivery->priority_type; // 'normal' or 'urgent'
            $type = $delivery->delivery_type;     // 'email' or 'physical'

            $deliveryValues[$priority][$type] = $delivery;
        }
      
        return view('admin.translators.edit-pricing', compact('deliveryValues','languages','subdocTypes','documentTypes','pricing','transId'));
    }

    public function updatePricing(Request $request, $id)
    {
        $pricing = TranslatorLanguageRate::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'from_language'                     => 'required|integer|different:to_language',
            'to_language'                       => 'required|integer',
            'doc_type'                          => 'required|integer',
            'sub_doc_type'                      => 'required|integer',
            'email_delivery_normal_email'       => 'required|numeric|min:0',
            'admin_amount_normal_email'         => 'required|numeric|min:0',
            'translator_amount_normal_email'    => 'required|numeric|min:0',
            'tax_amount_normal_email'           => 'required|numeric|min:0',
            'total_amount_normal_email'         => 'required|numeric|min:0',
            'physical_delivery_normal_physical' => 'required|numeric|min:0',
            'admin_amount_normal_physical'      => 'required|numeric|min:0',
            'translator_amount_normal_physical' => 'required|numeric|min:0',
            'tax_amount_normal_physical'        => 'required|numeric|min:0',
            'total_amount_normal_physical'      => 'required|numeric|min:0',
            'email_delivery_urgent_email'       => 'required|numeric|min:0',
            'admin_amount_urgent_email'         => 'required|numeric|min:0',
            'translator_amount_urgent_email'    => 'required|numeric|min:0',
            'tax_amount_urgent_email'           => 'required|numeric|min:0',
            'total_amount_urgent_email'         => 'required|numeric|min:0',
            'physical_delivery_urgent_physical' => 'required|numeric|min:0',
            'admin_amount_urgent_physical'      => 'required|numeric|min:0',
            'translator_amount_urgent_physical' => 'required|numeric|min:0',
            'tax_amount_urgent_physical'        => 'required|numeric|min:0',
            'total_amount_urgent_physical'      => 'required|numeric|min:0',
            'normal_hours_1_10'                 => 'required|numeric|min:0',
            'normal_hours_11_20'                => 'required|numeric|min:0',
            'normal_hours_21_30'                => 'required|numeric|min:0',
            'normal_hours_31_50'                => 'required|numeric|min:0',
            'normal_hours_above_50'             => 'required|numeric|min:0',
            'urgent_hours_1_10'                 => 'required|numeric|min:0',
            'urgent_hours_11_20'                => 'required|numeric|min:0',
            'urgent_hours_21_30'                => 'required|numeric|min:0',
            'urgent_hours_31_50'                => 'required|numeric|min:0',
            'urgent_hours_above_50'             => 'required|numeric|min:0',
        ],[
            '*.required' => 'This field is required.',
            'from_language.different' => 'From Language and To Language must be different.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $exists = TranslatorLanguageRate::where('translator_id', $pricing->translator_id)
            ->where('from_language_id', $request->from_language)
            ->where('to_language_id', $request->to_language)
            ->where('doc_type_id', $request->doc_type)
            ->where('doc_subtype_id', $request->sub_doc_type)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->back()->withInput()->withErrors([
                'from_language' => 'Pricing for this language and document combination already exists.'
            ]);
        }

        $pricing->update([
            'from_language_id'      => $request->from_language ?? NULL,
            'to_language_id'        => $request->to_language ?? NULL,
            'doc_type_id'           => $request->doc_type ?? NULL,
            'doc_subtype_id'        => $request->sub_doc_type ?? NULL,
            'normal_hours_1_10'     => $request->normal_hours_1_10,
            'normal_hours_11_20'    => $request->normal_hours_11_20,
            'normal_hours_21_30'    => $request->normal_hours_21_30,
            'normal_hours_31_50'    => $request->normal_hours_31_50,
            'normal_hours_above_50' => $request->normal_hours_above_50,
            'urgent_hours_1_10'     => $request->urgent_hours_1_10,
            'urgent_hours_11_20'    => $request->urgent_hours_11_20,
            'urgent_hours_21_30'    => $request->urgent_hours_21_30,
            'urgent_hours_31_50'    => $request->urgent_hours_31_50,
            'urgent_hours_above_50' => $request->urgent_hours_above_50,
            'hours_above_50'        => $request->hours_above_50 ?? 0,
            'status'                => $request->status
        ]);

        $deliveries = [
            [
                'priority_type'     => 'normal', 
                'delivery_type'     => 'email',    
                'delivery_amount'   => $request->email_delivery_normal_email,     
                'admin_amount'      => $request->admin_amount_normal_email,     
                'translator_amount' => $request->translator_amount_normal_email,     
                'tax'               => $request->tax_amount_normal_email,     
                'total_amount'      => $request->total_amount_normal_email
            ],
            [
                'priority_type'     => 'normal', 
                'delivery_type'     => 'physical', 
                'delivery_amount'   => $request->physical_delivery_normal_physical, 
                'admin_amount'      => $request->admin_amount_normal_physical, 
                'translator_amount' => $request->translator_amount_normal_physical, 
                'tax'               => $request->tax_amount_normal_physical, 
                'total_amount'      => $request->total_amount_normal_physical
            ],
            [
                'priority_type'     => 'urgent', 
                'delivery_type'     => 'email',    
                'delivery_amount'   => $request->email_delivery_urgent_email,      
                'admin_amount'      => $request->admin_amount_urgent_email,      
                'translator_amount' => $request->translator_amount_urgent_email,      
                'tax'               => $request->tax_amount_urgent_email,      
                'total_amount'      => $request->total_amount_urgent_email
            ],
            [
                'priority_type'     => 'urgent', 
                'delivery_type'     => 'physical', 
                'delivery_amount'   => $request->physical_delivery_urgent_physical, 
                'admin_amount'      => $request->admin_amount_urgent_physical, 
                'translator_amount' => $request->translator_amount_urgent_physical, 
                'tax'               => $request->tax_amount_urgent_physical, 
                'total_amount'      => $request->total_amount_urgent_physical],
        ];

        foreach ($deliveries as $data) {
            $pricing->deliveries()
                ->updateOrCreate(
                    [
                        'priority_type' => $data['priority_type'],
                        'delivery_type' => $data['delivery_type']
                    ],
                    $data
                );
        }
        
        $url =  session()->has('translator_pricing_last_url') ? session()->get('translator_pricing_last_url') : route('translator-pricing', ['id' => $request->translator_id]);
       
        return redirect($url)->with('success', 'Pricing updated successfully.');
    }

    public function getSubDocTypes($docTypeId){
        $lang = env('APP_LOCALE','en');
        $docTypes   = [];
        
        if($docTypeId){
            $docTypes = DocumentType::with('translations')->where('status', 1)
                            ->where('parent_id', $docTypeId)
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

    public function updatePricingStatus(Request $request)
    {
        $price = TranslatorLanguageRate::findOrFail($request->id);
        
        $price->status = $request->status;
        $price->save();
       
        return 1;
    }

    public function destroyPricing($id)
    {
        $price = TranslatorLanguageRate::findOrFail($id);
        $price->delete();
        return redirect()->back()->with('success', 'Translator pricing deleted successfully.');
    }
}
