<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Emirate;
use App\Models\Lawyer;
use App\Models\Language;
use App\Models\Dropdown;
use App\Models\DropdownOption;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LawyerController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
       
        $this->middleware('permission:manage_lawyers',  ['only' => ['index','destroy']]);
        $this->middleware('permission:add_lawyer',  ['only' => ['create','store']]);
        $this->middleware('permission:edit_lawyer',  ['only' => ['edit','update']]);
    }

    public function index(Request $request){
        $query = Lawyer::with('lawfirm', 'emirate');

        if ($request->filled('lawfirm_id')) {
            $query->whereHas('lawfirm', function ($q) use ($request) {
                $q->where('id', $request->lawfirm_id);
            });
        }

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

        $lawyers = $query->orderBy('id', 'DESC')->paginate(15);

        // $emirates = Emirate::orderBy('name','ASC')->get();
        $lawfirms = Vendor::orderBy('law_firm_name','ASC')->get();
        return view('admin.lawyers.index', compact('lawyers', 'lawfirms'));
    }

    public function create()
    {
        $dropdowns = Dropdown::with(['options.translations' => function ($q) {
            $q->where('language_code', 'en');
        }])->whereIn('slug', ['specialities', 'languages', 'years_experience'])->get()->keyBy('slug');

        $lawfirms = Vendor::orderBy('law_firm_name', 'ASC')->get();
        $languages = Language::where('status', 1)->get();
        return view('admin.lawyers.create', compact('dropdowns','lawfirms','languages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'law_firm' => 'required',
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
            'practicing_lawyer_card_expiry' => 'required|date',
            'password' => 'required|string|min:6|confirmed',
            'emirates_id_front' => 'required|file|mimes:jpg,jpeg,webp,png,svg,pdf|max:500',
            'emirates_id_back' => 'required|file|mimes:jpg,jpeg,webp,png,svg,pdf|max:500',
            'passport' => 'required|file|mimes:jpg,jpeg,png,svg,pdf,webp|max:500',
            'bar_card' => 'required|file|mimes:jpg,jpeg,png,svg,pdf,webp|max:500',
            'practicing_lawyer_card' => 'required|file|mimes:jpg,jpeg,png,svg,pdf,webp|max:500',
            'photo' => 'nullable|file|mimes:jpg,jpeg,png,svg,webp|max:500'
        ],[
            '*.required' => 'This field is required.',
            'translations.en.name.required' => 'The lawyer name in english is required.',
            'translations.en.name.max' => 'The lawyer name in english may not be greater than 255 characters.',
            'translations.en.name.string' => 'The lawyer name in english must be a valid text string.',
        ]);

        DB::transaction(function () use ($request, $validated) {
            $user = User::create([
                'name' => $request->translations['en']['name'],
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'user_type' => 'lawyer',
            ]);

            $lawyer = new Lawyer([
                'lawfirm_id'                        => $request->law_firm,
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
                'practicing_lawyer_card'            => $request->hasfile('practicing_lawyer_card') ? uploadImage('lawyers/'.$user->id, $request->practicing_lawyer_card, 'lawyer_card') : NULL,
                'practicing_lawyer_card_expiry'     => $request->practicing_lawyer_card_expiry ? Carbon::parse($request->practicing_lawyer_card_expiry)->format('Y-m-d') : null,
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
        session()->flash('success','Lawyer created successfully.');
        return redirect()->route('lawyers.index');
    }

    public function edit($id)
    {
        $lawyer = Lawyer::with('lawfirm', 'emirate')->findOrFail($id);
         
        $specialityIds = $lawyer->dropdownOptions()->wherePivot('type', 'specialities')->pluck('dropdown_option_id')->toArray();
        $languageIds = $lawyer->dropdownOptions()->wherePivot('type', 'languages')->pluck('dropdown_option_id')->toArray();

        $dropdowns = Dropdown::with(['options.translations' => function ($q) {
            $q->where('language_code', 'en');
        }])->whereIn('slug', ['specialities', 'languages', 'years_experience'])->get()->keyBy('slug');

        $lawfirms = Vendor::orderBy('law_firm_name', 'ASC')->get();
        $languages = Language::where('status', 1)->get();
        return view('admin.lawyers.edit', compact('lawyer','dropdowns','lawfirms','languages','specialityIds','languageIds'));
    }

    public function update(Request $request, $id)
    {
        $lawyer = Lawyer::with(['lawfirm', 'emirate'])->findOrFail($id);
        $user = $lawyer->user;

        $validated = $request->validate([
            'law_firm' => 'required',
            'translations.en.name' => 'required|string|max:255',
            'email' => [
                    'required',
                    'email',
                    Rule::unique('users', 'email')
                        ->ignore($user->id)
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
            'practicing_lawyer_card_expiry' => 'required|date',
            'emirate_id_front' => 'nullable|file|mimes:jpg,jpeg,webp,png,svg,pdf|max:500',
            'emirate_id_back' => 'nullable|file|mimes:jpg,jpeg,webp,png,svg,pdf|max:500',
            'passport' => 'nullable|file|mimes:jpg,jpeg,png,svg,pdf,webp|max:500',
            'bar_card' => 'nullable|file|mimes:jpg,jpeg,png,svg,pdf,webp|max:500',
            'practicing_lawyer_card' => 'nullable|file|mimes:jpg,jpeg,png,svg,pdf,webp|max:500',
            'profile_photo' => 'nullable|file|mimes:jpg,jpeg,png,svg,webp|max:500'
        ],[
            '*.required' => 'This field is required.',
            'translations.en.name.required' => 'The lawyer name in english is required.',
            'translations.en.name.max' => 'The lawyer name in english may not be greater than 255 characters.',
            'translations.en.name.string' => 'The lawyer name in english must be a valid text string.',
        ]);

        $user->update([
            'name' => $request->translations['en']['name'],
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->filled('password') ? Hash::make($request->password) : $user->password
        ]);

        $uploadPath = 'lawyers/' . $user->id;

        $lawyer->update([
            'lawfirm_id'                        => $request->law_firm,
            'full_name'                         => $request->translations['en']['name'], 
            'email'                             => $request->email,
            'phone'                             => $request->phone, 
            'gender'                            => $request->gender, 
            'date_of_birth'                     => $request->dob ? Carbon::parse($request->dob)->format('Y-m-d') : $lawyer->date_of_birth,
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
            'practicing_lawyer_card_expiry'     => $request->practicing_lawyer_card_expiry ? Carbon::parse($request->practicing_lawyer_card_expiry)->format('Y-m-d') : $lawyer->practicing_lawyer_card_expiry
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

        session()->flash('success', 'Lawyer details updated successfully.');
        return redirect()->route('lawyers.index');
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
}