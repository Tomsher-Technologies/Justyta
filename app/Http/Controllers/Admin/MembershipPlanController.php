<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use App\Models\MembershipPlanTranslation;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MembershipPlanController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
       
        $this->middleware('permission:manage_plan',  ['only' => ['index','destroy']]);
        $this->middleware('permission:add_plan',  ['only' => ['create','store']]);
        $this->middleware('permission:edit_plan',  ['only' => ['edit','update']]);
    }

    // Show all membership plans
    public function index()
    {
        $plans = MembershipPlan::latest()->paginate(10);
        return view('admin.membership_plans.index', compact('plans'));
    }

    // Show create form
    public function create()
    {
        $languages = Language::where('status', 1)->get();
        return view('admin.membership_plans.create', compact('languages'));
    }

    // Store new plan
    public function store(Request $request)
    {
        $request->validate([
            'translations.en.title' => 'required|string|max:255',
            'icon' => 'required|image|mimes:png,jpg,jpeg,svg|max:2048',
            'amount' => 'required|numeric',
            'member_count' => 'required|integer',
            'en_ar_price' => 'required|numeric',
            'for_ar_price' => 'required|numeric',
            'job_post_count' => 'required|integer',
            'annual_free_ad_days' => 'required|integer',
            'welcome_gift' => 'required|in:no,special,premium',
            'live_online' => 'required|boolean',
            'specific_law_firm_choice' => 'required|boolean',
            'annual_legal_contract' => 'required|boolean',
            'unlimited_training_applications' => 'required|boolean',
            'is_active' => 'required|boolean',
        ],[
            'translations.en.title.required' => 'The english title field is required.',
            'translations.en.title.max' => 'The english title may not be greater than 255 characters.',
            'translations.en.title.string' => 'The english title must be a valid text string.',
        ]);

        $iconPath = '';
        if ($request->hasfile('icon')) {
            $iconPath = uploadImage('membership_icons', $request->icon, 'image_1');
        }
      
        $plan = MembershipPlan::create([
                    'icon' => $iconPath,
                    'amount' => $request->amount,
                    'member_count' => $request->member_count,
                    'en_ar_price' => $request->en_ar_price,
                    'for_ar_price' => $request->for_ar_price,
                    'job_post_count' => $request->job_post_count,
                    'annual_free_ad_days' => $request->annual_free_ad_days,
                    'welcome_gift' => $request->welcome_gift,
                    'live_online' => $request->live_online,
                    'specific_law_firm_choice' => $request->specific_law_firm_choice,
                    'annual_legal_contract' => $request->annual_legal_contract,
                    'unlimited_training_applications' => $request->unlimited_training_applications,
                    'is_active' => $request->is_active,
                ]);

        foreach ($request->translations as $lang => $trans) {
            if($lang == 'en'){
                $plan->title = $trans['title'];
                $plan->save();
            }
            if($trans['title'] != ''){
                MembershipPlanTranslation::updateOrCreate(
                    ['membership_plan_id' => $plan->id, 'lang' => $lang],
                    ['title' => $trans['title']]
                );
            }
        }

        session()->flash('success', 'Membership Plan created successfully.');
        return redirect()->route('membership-plans.index');
    }

    // Show edit form
    public function edit($id)
    {
        $plan = MembershipPlan::findOrFail($id);
        $plan->load('translations');
        $languages = Language::where('status', 1)->get();
        return view('admin.membership_plans.edit', compact('plan','languages'));
    }

    // Update existing plan
    public function update(Request $request, $id)
    {
        $plan = MembershipPlan::findOrFail($id);

        $request->validate([
            'translations.en.title' => 'required|string|max:255',
            'icon' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'amount' => 'required|numeric',
            'member_count' => 'required|integer',
            'en_ar_price' => 'required|numeric',
            'for_ar_price' => 'required|numeric',
            'job_post_count' => 'required|integer',
            'annual_free_ad_days' => 'required|integer',
            'welcome_gift' => 'required|in:no,special,premium',
            'live_online' => 'required|boolean',
            'specific_law_firm_choice' => 'required|boolean',
            'annual_legal_contract' => 'required|boolean',
            'unlimited_training_applications' => 'required|boolean',
            'is_active' => 'required|boolean',
        ],[
            'translations.en.title.required' => 'The english title field is required.',
            'translations.en.title.max' => 'The english title may not be greater than 255 characters.',
            'translations.en.title.string' => 'The english title must be a valid text string.',
        ]);

        $data = $request->only([
            'amount', 'member_count', 'en_ar_price', 'for_ar_price',
            'job_post_count', 'annual_free_ad_days', 'welcome_gift',
            'live_online', 'specific_law_firm_choice', 'annual_legal_contract',
            'unlimited_training_applications', 'is_active',
        ]);

        if ($request->hasFile('icon')) {
            $iconPath = str_replace('/storage/', '', $plan->icon);
            if ($iconPath && Storage::disk('public')->exists($iconPath)) {
                Storage::disk('public')->delete($iconPath);
            }
            
            $data['icon'] = uploadImage('membership_icons', $request->icon, 'image');
        }

        $plan->update($data);

        foreach ($request->translations as $lang => $trans) {
            if($lang == 'en'){
                $plan->title = $trans['title'];
                $plan->save();
            }
            MembershipPlanTranslation::updateOrCreate(
                ['membership_plan_id' => $plan->id, 'lang' => $lang],
                ['title' => $trans['title']]
            );
        }

        return redirect()->route('membership-plans.index')->with('success', 'Membership Plan updated successfully.');
    }

    // Delete plan
    public function destroy($id)
    {
        $plan = MembershipPlan::findOrFail($id);

        if ($plan->icon && Storage::disk('public')->exists($plan->icon)) {
            Storage::disk('public')->delete($plan->icon);
        }

        $plan->delete();

        return redirect()->route('membership-plans.index')->with('success', 'Membership Plan deleted successfully.');
    }
}
