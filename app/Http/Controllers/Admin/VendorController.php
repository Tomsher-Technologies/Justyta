<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use App\Models\MembershipPlan;
use App\Models\Language;
use App\Models\VendorTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use App\Mail\VendorStatusChanged;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;


class VendorController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
       
        $this->middleware('permission:manage_vendors',  ['only' => ['index','destroy']]);
        $this->middleware('permission:add_vendor',  ['only' => ['create','store']]);
        $this->middleware('permission:edit_vendor',  ['only' => ['edit','update']]);
        $this->middleware('permission:approve_vendor',  ['only' => ['updateStatus']]);
    }

    public function index(Request $request)
    {
        $query = Vendor::with('user', 'currentSubscription.plan');

        if ($request->filled('plan_id')) {
            $query->whereHas('currentSubscription', function ($q) use ($request) {
                $q->where('membership_plan_id', $request->plan_id);
            });
        }

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword){
                $q->where('law_firm_name', 'like', "%{$keyword}%")
                    ->orWhere('law_firm_email', 'like', "%{$keyword}%")
                    ->orWhere('law_firm_phone', 'like', "%{$keyword}%")
                    ->orWhere('owner_name', 'like', "%{$keyword}%")
                    ->orWhere('owner_email', 'like', "%{$keyword}%")
                    ->orWhere('owner_phone', 'like', "%{$keyword}%")
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

        if ($request->filled('approval_status')) {
            // 1 = approved, 2 = rejected, 3 = pending; 
            $query->whereHas('user', function ($q) use ($request) {
                 if ($request->approval_status == 1) {
                    $q->where('approved', 1);
                } elseif ($request->approval_status == 2) {
                    $q->where('approved', 2);
                } elseif ($request->approval_status == 3) {
                    $q->where('approved', 0);
                }
            });
        }

        $vendors = $query->orderBy('id', 'DESC')->paginate(15); 

        $plans = MembershipPlan::get();

        return view('admin.vendors.index', compact('vendors', 'plans'));
    }

    public function create()
    {
        $plans = MembershipPlan::where('is_active', 1)->get();
        $languages = Language::where('status', 1)->get();
        return view('admin.vendors.create', compact('plans','languages'));
    }

    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'translations.en.name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'owner_name' => 'required|string|max:255',
            'owner_email' => [
                    'required',
                    'email',
                    Rule::unique('users', 'email')
                        ->where('user_type', 'vendor'),
                ],
            'owner_phone' => 'required|string|max:20',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:200',
            'emirate_id' => 'required',
            'country' => 'nullable|string|max:255',
            'subscription_plan_id' => 'required',
            'password' => 'required|string|min:6|confirmed',
            'trade_license' => 'required|file|mimes:jpg,jpeg,png,svg,pdf,webp|max:200',
            'trade_license_expiry' => 'required|date',
            'emirates_id_front' => 'required|file|mimes:jpg,jpeg,png,svg,pdf,webp|max:200',
            'emirates_id_back' => 'required|file|mimes:jpg,jpeg,png,svg,pdf,webp|max:200',
            'emirates_id_expiry' => 'required|date',
            'residence_visa' => 'nullable|file|mimes:jpg,jpeg,png,svg,pdf,webp|max:200',
            'residence_visa_expiry' => 'nullable|date',
            'passport' => 'required|file|mimes:jpg,jpeg,png,svg,pdf,webp|max:200',
            'passport_expiry' => 'required|date',
            'card_of_law' => 'required|file|mimes:jpg,jpeg,png,svg,pdf,webp|max:200',
            'card_of_law_expiry' => 'required|date',
            'consultation_commission' => 'required'
        ],[
            '*.required' => 'This field is required.',
            'translations.en.name.required' => 'The law firm name in english is required.',
            'translations.en.name.max' => 'The law firm name in english may not be greater than 255 characters.',
            'translations.en.name.string' => 'The law firm name in english must be a valid text string.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('vendors.create')->withErrors($validator)->withInput();
        }
        
        $user = User::create([
            'name' => $request->translations['en']['name'],
            'email' => $request->owner_email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'user_type' => 'vendor',
        ]);

        $vendor = new Vendor([
            'consultation_commission'   => $request->consultation_commission,
            'law_firm_name'             => $request->translations['en']['name'], 
            'law_firm_email'            => $request->email, 
            'law_firm_phone'            => $request->phone, 
            'office_address'            => $request->office_address,
            'owner_name'                => $request->owner_name, 
            'owner_email'               => $request->owner_email,  
            'owner_phone'               => $request->owner_phone,  
            'emirate_id'                => $request->emirate_id, 
            'website_url'               => $request->website_url,
            'trn'                       => $request->trn, 
            'logo'                      => $request->hasfile('logo') ? uploadImage('vendors/'.$user->id, $request->logo, 'logo_') : NULL,  
            'country' => 'UAE', 
            'trade_license'             => $request->hasfile('trade_license') ? uploadImage('vendors/'.$user->id, $request->trade_license, 'trade_license_') : NULL,
            'trade_license_expiry'      => $request->trade_license_expiry ? Carbon::parse($request->trade_license_expiry)->format('Y-m-d') : null,
            'emirates_id_front'         => $request->hasfile('emirates_id_front') ? uploadImage('vendors/'.$user->id, $request->emirates_id_front, 'emirates_id_front_') : NULL,
            'emirates_id_back'          => $request->hasfile('emirates_id_back') ? uploadImage('vendors/'.$user->id, $request->emirates_id_back, 'emirates_id_back_') : NULL,
            'emirates_id_expiry'        => $request->emirates_id_expiry ? Carbon::parse($request->emirates_id_expiry)->format('Y-m-d') : null,
            'residence_visa'            => $request->hasfile('residence_visa') ? uploadImage('vendors/'.$user->id, $request->residence_visa, 'residence_visa_') : NULL,
            'residence_visa_expiry'     => $request->residence_visa_expiry ? Carbon::parse($request->residence_visa_expiry)->format('Y-m-d') : null,
            'passport'                  => $request->hasfile('passport') ? uploadImage('vendors/'.$user->id, $request->passport, 'passport_') : NULL,
            'passport_expiry'           => $request->passport_expiry ? Carbon::parse($request->passport_expiry)->format('Y-m-d') : null,
            'card_of_law'               => $request->hasfile('card_of_law') ? uploadImage('vendors/'.$user->id, $request->card_of_law, 'card_of_law_') : NULL,
            'card_of_law_expiry'        => $request->card_of_law_expiry ? Carbon::parse($request->card_of_law_expiry)->format('Y-m-d') : null,
        ]);

        $user->vendor()->save($vendor);
        $plan = MembershipPlan::findOrFail($request->subscription_plan_id);

        foreach ($request->translations as $lang => $fields) {
            if(!empty($fields['name']) || !empty($fields['about'])){
                VendorTranslation::updateOrCreate(
                    ['vendor_id' => $vendor->id, 'lang' => $lang],
                    [
                        'law_firm_name' => $fields['name'],
                        'about' => $fields['about']
                    ]
                );
            }
        }
        
        $vendor->subscriptions()->create([
            'membership_plan_id'                => $plan->id,
            'amount'                            => $plan->amount,
            'member_count'                      => $plan->member_count,
            'job_post_count'                    => $plan->job_post_count,
            'en_ar_price'                       => $plan->en_ar_price,
            'for_ar_price'                      => $plan->for_ar_price,
            'live_online'                       => $plan->live_online,
            'specific_law_firm_choice'          => $plan->specific_law_firm_choice,
            'annual_legal_contract'             => $plan->annual_legal_contract,
            'annual_free_ad_days'               => $plan->annual_free_ad_days,
            'unlimited_training_applications'   => $plan->unlimited_training_applications,
            'welcome_gift'                      => $plan->welcome_gift,
            'subscription_start'                => now(),
            'subscription_end'                  => now()->addYear(), 
            'status'                            => 'active',
        ]);

        session()->flash('success', 'Law Firm created successfully.');
        return redirect()->route('vendors.index');
    }

    public function edit($id)
    {
        $vendor = Vendor::with('user', 'currentSubscription.plan')->findOrFail($id);
        $plans = MembershipPlan::where('is_active', 1)->get();
        $languages = Language::where('status', 1)->get();
        return view('admin.vendors.edit', compact('vendor','plans','languages'));
    }

    public function update(Request $request, $id)
    {
        $vendor = Vendor::with(['user', 'currentSubscription'])->findOrFail($id);
        $user = $vendor->user;
        $validator = Validator::make($request->all(), [
            'translations.en.name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'owner_name' => 'required|string|max:255',
            'owner_email' => [
                    'required',
                    'email',
                    Rule::unique('users', 'email')
                        ->ignore($user->id)
                        ->where('user_type', 'vendor'),
                ],
            'owner_phone' => 'required|string|max:20',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg,webp|max:200',
            'emirate_id' => 'required',
            'country' => 'nullable|string|max:255',
            'subscription_plan_id' => 'required',
            'password' => 'nullable|string|min:6|confirmed',
            'trade_license' => 'nullable|file|mimes:jpg,jpeg,png,svg,webp,pdf|max:200',
            'trade_license_expiry' => 'required|date',
            'emirates_id_front' => 'nullable|file|mimes:jpg,jpeg,png,svg,webp,pdf|max:200',
            'emirates_id_back' => 'nullable|file|mimes:jpg,jpeg,png,svg,webp,pdf|max:200',
            'emirates_id_expiry' => 'required|date',
            // 'residence_visa' => 'nullable|file|mimes:jpg,jpeg,png,svg,webp,pdf|max:200',
            // 'residence_visa_expiry' => 'required|date',
            'passport' => 'nullable|file|mimes:jpg,jpeg,png,svg,webp,pdf|max:200',
            'passport_expiry' => 'required|date',
            'card_of_law' => 'nullable|file|mimes:jpg,jpeg,png,svg,webp,pdf|max:200',
            'card_of_law_expiry' => 'required|date',
            'consultation_commission' => 'required'
        ],[
            '*.required' => 'This field is required.',
            'translations.en.name.required' => 'The law firm name in english is required.',
            'translations.en.name.max' => 'The law firm name in english may not be greater than 255 characters.',
            'translations.en.name.string' => 'The law firm name in english must be a valid text string.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user->update([
            'name' => $request->translations['en']['name'],
            'email' => $request->owner_email,
            'phone' => $request->phone,
            'password' => $request->filled('password') ? Hash::make($request->password) : $user->password,
        ]);

        $uploadPath = 'vendors/' . $user->id;

        $vendor->update([
            'consultation_commission'   => $request->consultation_commission,
            'law_firm_name'             => $request->translations['en']['name'], 
            'law_firm_email'            => $request->email, 
            'law_firm_phone'            => $request->phone, 
            'office_address'            => $request->office_address,
            'owner_name'                => $request->owner_name, 
            'owner_email'               => $request->owner_email,  
            'owner_phone'               => $request->owner_phone,  
            'emirate_id'                => $request->emirate_id, 
            'trn'                       => $request->trn, 
            'website_url'               => $request->website_url,
            'logo'                      => $this->replaceFile($request, 'logo', $vendor, $uploadPath,'logo_'),
            'country' => 'UAE', 
            'trade_license'             => $this->replaceFile($request, 'trade_license', $vendor, $uploadPath,'trade_license_'),
            'trade_license_expiry'      => $request->trade_license_expiry ? Carbon::parse($request->trade_license_expiry)->format('Y-m-d') : $vendor->trade_license_expiry,
            'emirates_id_front'         => $this->replaceFile($request, 'emirates_id_front', $vendor, $uploadPath,'emirates_id_front_'),
            'emirates_id_back'          => $this->replaceFile($request, 'emirates_id_back', $vendor, $uploadPath,'emirates_id_back_'),
            'emirates_id_expiry'        => $request->emirates_id_expiry ? Carbon::parse($request->emirates_id_expiry)->format('Y-m-d') : $vendor->emirates_id_expiry,
            'residence_visa'            => $this->replaceFile($request, 'residence_visa', $vendor, $uploadPath,'residence_visa_'),
            'residence_visa_expiry'     => $request->residence_visa_expiry ? Carbon::parse($request->residence_visa_expiry)->format('Y-m-d') : $vendor->residence_visa_expiry,
            'passport'                  => $this->replaceFile($request, 'passport', $vendor, $uploadPath,'passport_'),
            'passport_expiry'           => $request->passport_expiry ? Carbon::parse($request->passport_expiry)->format('Y-m-d') : $vendor->passport_expiry,
            'card_of_law'               => $this->replaceFile($request, 'card_of_law', $vendor, $uploadPath,'card_of_law_'),
            'card_of_law_expiry'        => $request->card_of_law_expiry ? Carbon::parse($request->card_of_law_expiry)->format('Y-m-d') : $vendor->card_of_law_expiry,
        ]);

        $user->vendor()->save($vendor);

        foreach ($request->translations as $lang => $fields) {
            if(!empty($fields['name']) || !empty($fields['about'])){
                VendorTranslation::updateOrCreate(
                    ['vendor_id' => $vendor->id, 'lang' => $lang],
                    [
                        'law_firm_name' => $fields['name'],
                        'about' => $fields['about']
                    ]
                );
            }
        }

        $plan = MembershipPlan::findOrFail($request->subscription_plan_id);

        if (!$vendor->currentSubscription || $vendor->currentSubscription->membership_plan_id != $plan->id) {
            
            if ($vendor->currentSubscription) {
                $vendor->currentSubscription->update([
                    'status' => 'expired',
                    'subscription_end' => now(), 
                ]);
            }

            $vendor->subscriptions()->create([
                'membership_plan_id'                => $plan->id,
                'amount'                            => $plan->amount,
                'member_count'                      => $plan->member_count,
                'job_post_count'                    => $plan->job_post_count,
                'en_ar_price'                       => $plan->en_ar_price,
                'for_ar_price'                      => $plan->for_ar_price,
                'live_online'                       => $plan->live_online,
                'specific_law_firm_choice'          => $plan->specific_law_firm_choice,
                'annual_legal_contract'             => $plan->annual_legal_contract,
                'annual_free_ad_days'               => $plan->annual_free_ad_days,
                'unlimited_training_applications'   => $plan->unlimited_training_applications,
                'welcome_gift'                      => $plan->welcome_gift,
                'subscription_start'                => now(),
                'subscription_end'                  => now()->addYear(), 
                'status'                            => 'active',
            ]);
        }

        session()->flash('success', 'Law Firm updated successfully.');
        return redirect()->route('vendors.index');
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

    private function upload($file)
    {
        if (!$file) return null;
        return $file->store('vendors', 'public');
    }

    public function updateStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($request->action === 'approve') {
            $user->approved = 1;
        } elseif ($request->action === 'reject') {
            $user->approved = 2;
        }
        $user->save();

        $statusText = $user->approved == 1 ? 'Approved' : 'Rejected';

        Mail::to($user->email)->send(new VendorStatusChanged($user->name, strtolower($statusText)));

        return response()->json(['success' => true]);
    }
}
