<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use App\Models\MembershipPlanTranslation;
use App\Models\MembershipPlanLanguageRate;
use App\Models\Language;
use App\Models\DocumentType;
use App\Models\TranslationLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class MembershipPlanController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
       
        $this->middleware('permission:manage_plan',  ['only' => ['index','destroy']]);
        $this->middleware('permission:add_plan',  ['only' => ['create','store']]);
        $this->middleware('permission:edit_plan',  ['only' => ['edit','update']]);
        $this->middleware('permission:view_plan_pricing',  ['only' => ['indexPricing']]);
        $this->middleware('permission:add_plan_pricing',  ['only' => ['createPricing','storePricing']]);
        $this->middleware('permission:edit_plan_pricing',  ['only' => ['editPricing','updatePricing']]);
        $this->middleware('permission:delete_plan_pricing',  ['only' => ['destroyPricing']]);
    }

    public function index(Request $request)
    {
        $request->session()->put('plan_last_url', url()->full());
        $plans = MembershipPlan::latest()->paginate(10);
        return view('admin.membership_plans.index', compact('plans'));
    }

    public function create()
    {
        $languages = Language::where('status', 1)->get();
        return view('admin.membership_plans.create', compact('languages'));
    }

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

    public function edit($id)
    {
        $plan = MembershipPlan::findOrFail($id);
        $plan->load('translations');
        $languages = Language::where('status', 1)->get();
        return view('admin.membership_plans.edit', compact('plan','languages'));
    }
    public function update(Request $request, $id)
    {
        $plan = MembershipPlan::findOrFail($id);

        $request->validate([
            'translations.en.title' => 'required|string|max:255',
            'icon' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'amount' => 'required|numeric',
            'member_count' => 'required|integer',
            // 'en_ar_price' => 'required|numeric',
            // 'for_ar_price' => 'required|numeric',
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
            'amount', 'member_count', 'job_post_count', 'annual_free_ad_days', 'welcome_gift', 'live_online', 'specific_law_firm_choice', 'annual_legal_contract', 'unlimited_training_applications', 'is_active',
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

    public function destroy($id)
    {
        $plan = MembershipPlan::findOrFail($id);

        if ($plan->icon && Storage::disk('public')->exists($plan->icon)) {
            Storage::disk('public')->delete($plan->icon);
        }

        $plan->delete();

        return redirect()->route('membership-plans.index')->with('success', 'Membership Plan deleted successfully.');
    }

     public function indexPricing(Request $request, $id)
    {
        $request->session()->put('plan_pricing_last_url', url()->full());
        $planId = base64_decode($id);
        $query = MembershipPlanLanguageRate::with(['membershipPlan','fromLanguage', 'toLanguage','documentType','documentSubType','deliveries'])->where('membership_plan_id', $planId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
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

        $planPricing = $query->orderBy('id', 'DESC')->paginate(15); 

        $languages = TranslationLanguage::where('status', 1)->get();
        $plan = MembershipPlan::find($planId);
        $documentTypes = DocumentType::with('translations')->where('status', 1)
                            ->whereNull('parent_id')
                            ->orderBy('sort_order')
                            ->get();
        return view('admin.membership_plans.index-pricing', compact('documentTypes','planPricing','plan', 'languages','planId'));
    }

    public function createPricing($id){
        $languages = TranslationLanguage::where('status', 1)->get();
        $documentTypes = DocumentType::with('translations')->where('status', 1)
                            ->whereNull('parent_id')
                            ->orderBy('sort_order')
                            ->get();
        $plan = MembershipPlan::find(base64_decode($id));
        return view('admin.membership_plans.create-pricing', compact('languages','documentTypes','id','plan'));
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
            'plan_amount_normal_email'    => 'required|numeric|min:0',
            'tax_amount_normal_email'           => 'required|numeric|min:0',
            'total_amount_normal_email'         => 'required|numeric|min:0',
            'physical_delivery_normal_physical' => 'required|numeric|min:0',
            'admin_amount_normal_physical'      => 'required|numeric|min:0',
            'plan_amount_normal_physical' => 'required|numeric|min:0',
            'tax_amount_normal_physical'        => 'required|numeric|min:0',
            'total_amount_normal_physical'      => 'required|numeric|min:0',
            'email_delivery_urgent_email'       => 'required|numeric|min:0',
            'admin_amount_urgent_email'         => 'required|numeric|min:0',
            'plan_amount_urgent_email'    => 'required|numeric|min:0',
            'tax_amount_urgent_email'           => 'required|numeric|min:0',
            'total_amount_urgent_email'         => 'required|numeric|min:0',
            'physical_delivery_urgent_physical' => 'required|numeric|min:0',
            'admin_amount_urgent_physical'      => 'required|numeric|min:0',
            'plan_amount_urgent_physical' => 'required|numeric|min:0',
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

        $planId = base64_decode($request->membership_plan_id);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $exists = MembershipPlanLanguageRate::where('membership_plan_id', $planId)
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

        $rate = MembershipPlanLanguageRate::create([
            'membership_plan_id'         => $planId,
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
                'translator_amount' => $request->plan_amount_normal_email,
                'tax'               => $request->tax_amount_normal_email,
                'total_amount'      => $request->total_amount_normal_email,
            ],
            [
                'priority_type'     => 'normal',
                'delivery_type'     => 'physical',
                'delivery_amount'   => $request->physical_delivery_normal_physical,
                'admin_amount'      => $request->admin_amount_normal_physical,
                'translator_amount' => $request->plan_amount_normal_physical,
                'tax'               => $request->tax_amount_normal_physical,
                'total_amount'      => $request->total_amount_normal_physical,
            ],
            [
                'priority_type'     => 'urgent',
                'delivery_type'     => 'email',
                'delivery_amount'   => $request->email_delivery_urgent_email,
                'admin_amount'      => $request->admin_amount_urgent_email,
                'translator_amount' => $request->plan_amount_urgent_email,
                'tax'               => $request->tax_amount_urgent_email,
                'total_amount'      => $request->total_amount_urgent_email,
            ],
            [
                'priority_type'     => 'urgent',
                'delivery_type'     => 'physical',
                'delivery_amount'   => $request->physical_delivery_urgent_physical,
                'admin_amount'      => $request->admin_amount_urgent_physical,
                'translator_amount' => $request->plan_amount_urgent_physical,
                'tax'               => $request->tax_amount_urgent_physical,
                'total_amount'      => $request->total_amount_urgent_physical,
            ],
        ];

        foreach ($deliveries as $data) {
            $rate->deliveries()->create($data);
        }
      
        session()->flash('success','Membership Plan pricing created successfully.');
       
        return redirect()->route('plan-pricing',['id' => $request->membership_plan_id]);
    }

    public function editPricing($id, $planId){
        $id = base64_decode($id);
        $pricing = MembershipPlanLanguageRate::find($id);
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
      
        $plan = MembershipPlan::find(base64_decode($planId));
        return view('admin.membership_plans.edit-pricing', compact('deliveryValues','languages','subdocTypes','documentTypes','pricing','planId','plan'));
    }

    public function updatePricing(Request $request, $id)
    {
        $pricing = MembershipPlanLanguageRate::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'from_language'                     => 'required|integer|different:to_language',
            'to_language'                       => 'required|integer',
            'doc_type'                          => 'required|integer',
            'sub_doc_type'                      => 'required|integer',
            'email_delivery_normal_email'       => 'required|numeric|min:0',
            'admin_amount_normal_email'         => 'required|numeric|min:0',
            'plan_amount_normal_email'    => 'required|numeric|min:0',
            'tax_amount_normal_email'           => 'required|numeric|min:0',
            'total_amount_normal_email'         => 'required|numeric|min:0',
            'physical_delivery_normal_physical' => 'required|numeric|min:0',
            'admin_amount_normal_physical'      => 'required|numeric|min:0',
            'plan_amount_normal_physical' => 'required|numeric|min:0',
            'tax_amount_normal_physical'        => 'required|numeric|min:0',
            'total_amount_normal_physical'      => 'required|numeric|min:0',
            'email_delivery_urgent_email'       => 'required|numeric|min:0',
            'admin_amount_urgent_email'         => 'required|numeric|min:0',
            'plan_amount_urgent_email'    => 'required|numeric|min:0',
            'tax_amount_urgent_email'           => 'required|numeric|min:0',
            'total_amount_urgent_email'         => 'required|numeric|min:0',
            'physical_delivery_urgent_physical' => 'required|numeric|min:0',
            'admin_amount_urgent_physical'      => 'required|numeric|min:0',
            'plan_amount_urgent_physical' => 'required|numeric|min:0',
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

        $exists = MembershipPlanLanguageRate::where('membership_plan_id', $pricing->membership_plan_id)
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
                'translator_amount' => $request->plan_amount_normal_email,     
                'tax'               => $request->tax_amount_normal_email,     
                'total_amount'      => $request->total_amount_normal_email
            ],
            [
                'priority_type'     => 'normal', 
                'delivery_type'     => 'physical', 
                'delivery_amount'   => $request->physical_delivery_normal_physical, 
                'admin_amount'      => $request->admin_amount_normal_physical, 
                'translator_amount' => $request->plan_amount_normal_physical, 
                'tax'               => $request->tax_amount_normal_physical, 
                'total_amount'      => $request->total_amount_normal_physical
            ],
            [
                'priority_type'     => 'urgent', 
                'delivery_type'     => 'email',    
                'delivery_amount'   => $request->email_delivery_urgent_email,      
                'admin_amount'      => $request->admin_amount_urgent_email,      
                'translator_amount' => $request->plan_amount_urgent_email,      
                'tax'               => $request->tax_amount_urgent_email,      
                'total_amount'      => $request->total_amount_urgent_email
            ],
            [
                'priority_type'     => 'urgent', 
                'delivery_type'     => 'physical', 
                'delivery_amount'   => $request->physical_delivery_urgent_physical, 
                'admin_amount'      => $request->admin_amount_urgent_physical, 
                'translator_amount' => $request->plan_amount_urgent_physical, 
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
        
        $url =  session()->has('plan_pricing_last_url') ? session()->get('plan_pricing_last_url') : route('plan-pricing', ['id' => $request->membership_plan_id]);
       
        return redirect($url)->with('success', 'Pricing updated successfully.');
    }

    public function updatePricingStatus(Request $request)
    {
        $price = MembershipPlanLanguageRate::findOrFail($request->id);
        
        $price->status = $request->status;
        $price->save();
       
        return 1;
    }

    public function destroyPricing($id)
    {
        $price = MembershipPlanLanguageRate::findOrFail($id);
        $price->delete();
        return redirect()->back()->with('success', 'Membership plan pricing deleted successfully.');
    }
}
