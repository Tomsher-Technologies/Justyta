<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dropdown;
use App\Models\User;
use App\Models\Translator;
use App\Models\TranslationLanguage;
use App\Models\DefaultTranslatorHistory;
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
       
        $this->middleware('permission:manage_translators',  ['only' => ['index','destroy']]);
        $this->middleware('permission:add_translator',  ['only' => ['create','store']]);
        $this->middleware('permission:edit_translator',  ['only' => ['edit','update']]);
        $this->middleware('permission:default_translator',  ['only' => ['showDefaultForm','setDefault']]);
    }

    public function index(Request $request)
    {
        $query = Translator::with(['user','languageRates.fromLanguage', 'languageRates.toLanguage']);

        // Filter by membership plan
        if ($request->filled('language_id')) {
            $selectedLangId = $request->language_id;
            $query->whereHas('languageRates', function ($q) use ($selectedLangId) {
                $q->where('from_language_id', $selectedLangId);
            });
        }

        // Filter by keyword in name, email or phone
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword){
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%")
                    ->orWhere('company_name', 'like', "%{$keyword}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            // Assuming 1 = active, 2 = inactive; 
            $query->whereHas('user', function ($q) use ($request) {
                 if ($request->status == 1) {
                    $q->where('banned', 0);
                } elseif ($request->status == 2) {
                    $q->where('banned', 1);
                }
            });
        }

        $translators = $query->orderBy('id', 'DESC')->paginate(15); // or ->get() if you don’t want pagination

        // Optional: to populate dropdowns
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
            // 'languages' => 'required|array',
            'rates.*.from_language_id' => 'required|different:rates.*.to_language_id|exists:translation_languages,id',
            'rates.*.to_language_id' => 'required|in:1,3', // only Arabic or English
            'rates.*.hours_per_page' => 'required|numeric|min:0',
            'rates.*.admin_amount' => 'required|numeric|min:0',
            'rates.*.translator_amount' => 'required|numeric|min:0',
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

        foreach ($request->rates as $rate) {
            $translator->languageRates()->create($rate);
        }
         // Sync dropdowns (pivot table)
        // $dropdowns = collect([
        //     'languages' => $request->languages
        // ]);

        // foreach ($dropdowns as $type => $optionIds) {
        //     if (!empty($optionIds)) {
        //         $attachData = [];
        //         foreach ($optionIds as $optionId) {
        //             $attachData[$optionId] = ['type' => $type];
        //         }
        //         $translator->dropdownOptions()->attach($attachData);
        //     }
        // }


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
            // 'languages' => 'required|array',
            'rates.*.from_language_id' => 'required|different:rates.*.to_language_id|exists:translation_languages,id',
            'rates.*.to_language_id' => 'required|in:1,3',
            'rates.*.hours_per_page' => 'required|numeric|min:0',
            'rates.*.admin_amount' => 'required|numeric|min:0',
            'rates.*.translator_amount' => 'required|numeric|min:0',
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

        // Remove old rates and add new
        $translator->languageRates()->delete();

        foreach ($request->rates as $rate) {
            $translator->languageRates()->create($rate);
        }

         // Sync dropdowns (pivot table)
        $dropdowns = collect([
            'languages' => $request->languages
        ]);

        foreach ($dropdowns as $type => $optionIds) {
            // Delete existing entries of this type
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
        return redirect()->route('translators.index');
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


    public function showDefaultForm()
    {
        $translators = Translator::with('user')->whereHas('user', function ($q) {
                $q->where('banned', 0);
            })->orderBy('name', 'ASC')->get();
        $histories = DefaultTranslatorHistory::with('translator')->orderBy('id','desc')->paginate(20);

        return view('admin.translators.default', compact('translators', 'histories'));
    }

    public function setDefault(Request $request)
    {
        $request->validate([
            'translator_id' => 'required|exists:translators,id',
        ],[
            'translator_id.required' => 'This field is required.'
        ]);

        $newTranslatorId = $request->translator_id;

        // End the current default
        $current = Translator::where('is_default', 1)->first();

       
        if ($current && $current->id != $newTranslatorId) {
            $current->update(['is_default' => 0]);
            DefaultTranslatorHistory::where('translator_id', $current->id)->whereNull('ended_at')->update([
                'ended_at' => Carbon::now(),
            ]);
        }

        // Set new default
        $newTranslator = Translator::findOrFail($newTranslatorId);
        $newTranslator->update(['is_default' => 1]);

        // Create history if not already started
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
}
