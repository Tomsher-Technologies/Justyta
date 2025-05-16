<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use App\Models\MembershipPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::with('user', 'currentSubscription.plan');

        // Filter by membership plan
        if ($request->filled('plan_id')) {
            $query->whereHas('currentSubscription', function ($q) use ($request) {
                $q->where('membership_plan_id', $request->plan_id);
            });
        }

        // Filter by keyword in name, email or phone
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->whereHas('user', function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                ->orWhere('email', 'like', "%{$keyword}%")
                ->orWhere('phone', 'like', "%{$keyword}%");
            });
        }

        $vendors = $query->paginate(10); // or ->get() if you don’t want pagination

        // Optional: to populate dropdowns
        $plans = MembershipPlan::get();

        return view('admin.vendors.index', compact('vendors', 'plans'));
    }

    public function create()
    {
        $plans = MembershipPlan::where('is_active', 1)->get();
        return view('admin.vendors.create', compact('plans'));
    }

    public function store(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'office_address' => 'nullable|string',
            'city_emirate' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'subscription_plan_id' => 'required',
            'password' => 'required|string|min:6|confirmed',
            'trade_license' => 'required|file|mimes:jpg,jpeg,png,svg,pdf|max:2048',
            'trade_license_expiry' => 'nullable|date',
            'emirates_id_front' => 'required|file|mimes:jpg,jpeg,png,svg,pdf|max:2048',
            'emirates_id_back' => 'required|file|mimes:jpg,jpeg,png,svg,pdf|max:2048',
            'emirates_id_expiry' => 'nullable|date',
            'residence_visa' => 'required|file|mimes:jpg,jpeg,png,svg,pdf|max:2048',
            'residence_visa_expiry' => 'nullable|date',
            'passport' => 'required|file|mimes:jpg,jpeg,png,svg,pdf|max:2048',
            'passport_expiry' => 'nullable|date',
            'card_of_law' => 'required|file|mimes:jpg,jpeg,png,svg,pdf|max:2048',
            'card_of_law_expiry' => 'nullable|date',
        ]);


        if ($validator->fails()) {
            return redirect()->route('vendors.create')->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'user_type' => 'vendor',
        ]);

        $vendor = new Vendor([
            'logo'                  => $request->hasfile('logo') ? uploadImage('vendors/'.$user->id, $request->logo, 'image_') : NULL,
            'office_address'        => $request->office_address,
            'city'                  => $request->city,
            'country'               => $request->country,
            'trade_license'         => $request->hasfile('trade_license') ? uploadImage('vendors/'.$user->id, $request->trade_license, 'image_') : NULL,
            'trade_license_expiry'  => $request->trade_license_expiry ? Carbon::parse($request->trade_license_expiry)->format('Y-m-d') : null,
            'emirates_id_front'     => $request->hasfile('emirates_id_front') ? uploadImage('vendors/'.$user->id, $request->emirates_id_front, 'image_') : NULL,
            'emirates_id_back'      => $request->hasfile('emirates_id_back') ? uploadImage('vendors/'.$user->id, $request->emirates_id_back, 'image_') : NULL,
            'emirates_id_expiry'    => $request->emirates_id_expiry ? Carbon::parse($request->emirates_id_expiry)->format('Y-m-d') : null,
            'residence_visa'        => $request->hasfile('residence_visa') ? uploadImage('vendors/'.$user->id, $request->residence_visa, 'image_') : NULL,
            'residence_visa_expiry' => $request->residence_visa_expiry ? Carbon::parse($request->residence_visa_expiry)->format('Y-m-d') : null,
            'passport'              => $request->hasfile('passport') ? uploadImage('vendors/'.$user->id, $request->passport, 'image_') : NULL,
            'passport_expiry'       => $request->passport_expiry ? Carbon::parse($request->passport_expiry)->format('Y-m-d') : null,
            'card_of_law'           => $request->hasfile('card_of_law') ? uploadImage('vendors/'.$user->id, $request->card_of_law, 'image_') : NULL,
            'card_of_law_expiry'    => $request->card_of_law_expiry ? Carbon::parse($request->card_of_law_expiry)->format('Y-m-d') : null,
        ]);

        $user->vendor()->save($vendor);
        $plan = MembershipPlan::findOrFail($request->subscription_plan_id);
        // Now store subscription with a snapshot
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
            'subscription_end'                  => now()->addYear(), // or based on plan duration
            'status'                            => 'active',
        ]);

        session()->flash('success', 'Law Firm created successfully.');
        return redirect()->route('vendors.index');
    }

    public function edit($id)
    {
        $vendor = Vendor::with('user', 'currentSubscription.plan')->findOrFail($id);
        $plans = MembershipPlan::where('is_active', 1)->get();
        return view('admin.vendors.edit', compact('vendor','plans'));
    }

    public function update(Request $request, $id)
    {
        $vendor = Vendor::with(['user', 'currentSubscription'])->findOrFail($id);
        $user = $vendor->user;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg,webp|max:2048',
            'office_address' => 'nullable|string',
            'city_emirate' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'subscription_plan_id' => 'required',
            'password' => 'nullable|string|min:6|confirmed',

            'trade_license' => 'nullable|file|mimes:jpg,jpeg,png,svg,webp,pdf|max:2048',
            'trade_license_expiry' => 'nullable|date',
            'emirates_id_front' => 'nullable|file|mimes:jpg,jpeg,png,svg,webp,pdf|max:2048',
            'emirates_id_back' => 'nullable|file|mimes:jpg,jpeg,png,svg,webp,pdf|max:2048',
            'emirates_id_expiry' => 'nullable|date',
            'residence_visa' => 'nullable|file|mimes:jpg,jpeg,png,svg,webp,pdf|max:2048',
            'residence_visa_expiry' => 'nullable|date',
            'passport' => 'nullable|file|mimes:jpg,jpeg,png,svg,webp,pdf|max:2048',
            'passport_expiry' => 'nullable|date',
            'card_of_law' => 'nullable|file|mimes:jpg,jpeg,png,svg,webp,pdf|max:2048',
            'card_of_law_expiry' => 'nullable|date',
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

        $uploadPath = 'vendors/' . $user->id;

        $vendor->update([
            'logo'                  => $this->replaceFile($request, 'logo', $vendor, $uploadPath),
            'office_address'        => $request->office_address,
            'city'                  => $request->city,
            'country'               => $request->country,
            'trade_license'         => $this->replaceFile($request, 'trade_license', $vendor, $uploadPath),
            'trade_license_expiry'  => $request->trade_license_expiry ? Carbon::parse($request->trade_license_expiry)->format('Y-m-d') : $vendor->trade_license_expiry,
            'emirates_id_front'     => $this->replaceFile($request, 'emirates_id_front', $vendor, $uploadPath),
            'emirates_id_back'      => $this->replaceFile($request, 'emirates_id_back', $vendor, $uploadPath),
            'emirates_id_expiry'    => $request->emirates_id_expiry ? Carbon::parse($request->emirates_id_expiry)->format('Y-m-d') : $vendor->emirates_id_expiry,
            'residence_visa'        => $this->replaceFile($request, 'residence_visa', $vendor, $uploadPath),
            'residence_visa_expiry' => $request->residence_visa_expiry ? Carbon::parse($request->residence_visa_expiry)->format('Y-m-d') : $vendor->residence_visa_expiry,
            'passport'              => $this->replaceFile($request, 'passport', $vendor, $uploadPath),
            'passport_expiry'       => $request->passport_expiry ? Carbon::parse($request->passport_expiry)->format('Y-m-d') : $vendor->passport_expiry,
            'card_of_law'           => $this->replaceFile($request, 'card_of_law', $vendor, $uploadPath),
            'card_of_law_expiry'    => $request->card_of_law_expiry ? Carbon::parse($request->card_of_law_expiry)->format('Y-m-d') : $vendor->card_of_law_expiry,
        ]);


        $user->vendor()->save($vendor);
        $plan = MembershipPlan::findOrFail($request->subscription_plan_id);

        // Check if the plan has changed
        if (!$vendor->currentSubscription || $vendor->currentSubscription->membership_plan_id != $plan->id) {
            
            // Expire the current subscription if exists
            if ($vendor->currentSubscription) {
                $vendor->currentSubscription->update([
                    'status' => 'expired',
                    'subscription_end' => now(), // Mark end time
                ]);
            }

            // Create new subscription
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
                'subscription_end'                  => now()->addYear(), // or use $plan->duration if dynamic
                'status'                            => 'active',
            ]);
        }

        session()->flash('success', 'Law Firm created successfully.');
        return redirect()->route('vendors.index');
    }

    function replaceFile($request, $fieldName, $vendor, $uploadPath) {
        if ($request->hasFile($fieldName)) {
            if (!empty($vendor->$fieldName)) {
                $pathToDelete = str_replace('/storage/', '', $vendor->$fieldName);
                if (Storage::disk('public')->exists($pathToDelete)) {
                    Storage::disk('public')->delete($pathToDelete);
                }
            }
            return uploadImage($uploadPath, $request->file($fieldName), 'image_');
        }
        return $vendor->$fieldName;
    }

    private function upload($file)
    {
        if (!$file) return null;
        return $file->store('vendors', 'public');
    }
}
