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
use App\Models\DropdownOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class VendorHomeController extends Controller
{
    public function dashboard(){
        $lang = app()->getLocale() ?? env('APP_LOCALE','en'); 
    
        return view('frontend.vendor.dashboard', compact('lang'));
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

        $lawyers = $query->paginate(12);
        $dropdowns = Dropdown::with(['options.translations'])->whereIn('slug', ['specialities'])->get()->keyBy('slug');
        return view('frontend.vendor.lawyers.index', compact('lang','lawyers','dropdowns'));
    }

    public function createLawyer(){
        $lang = app()->getLocale() ?? env('APP_LOCALE','en'); 
        
        $dropdowns = Dropdown::with(['options.translations'])->whereIn('slug', ['specialities', 'languages', 'years_experience'])->get()->keyBy('slug');

        $languages = Language::where('status', 1)->get();
        return view('frontend.vendor.lawyers.create', compact('lang', 'dropdowns', 'languages'));
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
                'profile_photo'                     => $request->hasfile('photo') ? uploadImage('lawyers/'.$user->id, $request->photo, 'lawyer_') : NULL,
                'emirate_id_front'                  => $request->hasfile('emirates_id_front') ? uploadImage('lawyers/'.$user->id, $request->emirates_id_front, 'emirate_id_') : NULL,
                'emirate_id_back'                   => $request->hasfile('emirates_id_back') ? uploadImage('lawyers/'.$user->id, $request->emirates_id_back, 'emirate_id_') : NULL,
                'emirate_id_expiry'                 => $request->emirates_id_expiry ? Carbon::parse($request->emirates_id_expiry)->format('Y-m-d') : null,
                'passport'                          => $request->hasfile('passport') ? uploadImage('lawyers/'.$user->id, $request->passport, 'passport_') : NULL,
                'passport_expiry'                   => $request->passport_expiry ? Carbon::parse($request->passport_expiry)->format('Y-m-d') : null,
                'residence_visa'                    => $request->hasfile('residence_visa') ? uploadImage('lawyers/'.$user->id, $request->residence_visa, 'residence_visa_') : NULL,
                'residence_visa_expiry'             => $request->residence_visa_expiry ? Carbon::parse($request->residence_visa_expiry)->format('Y-m-d') : null,
                'bar_card'                          => $request->hasfile('bar_card') ? uploadImage('lawyers/'.$user->id, $request->bar_card, 'bar_card_') : NULL,
                'bar_card_expiry'                   => $request->bar_card_expiry ? Carbon::parse($request->bar_card_expiry)->format('Y-m-d') : null,
                'practicing_lawyer_card'            => $request->hasfile('ministry_of_justice_card') ? uploadImage('lawyers/'.$user->id, $request->ministry_of_justice_card, 'lawyer_card_') : NULL,
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

}
