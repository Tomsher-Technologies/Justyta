<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Language;
use App\Models\Dropdown;
use App\Models\ExpertReportPricing;
use App\Models\ServiceTranslation;
use App\Models\ConsultationDuration;
use App\Models\AnnualRetainerBaseFee;
use App\Models\AnnualRetainerInstallment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ServiceController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
       
        $this->middleware('permission:manage_service',  ['only' => ['index','destroy','indexExpertPricing']]);
        $this->middleware('permission:view_service',  ['only' => ['index','indexExpertPricing']]);
        $this->middleware('permission:edit_service',  ['only' => ['edit','update','updateStatus','createExpertPricing','storeExpertPricing', 'editExpertPricing','updateExpertPricing','destroyExpertPricing']]);
    }

    public function index(Request $request)
    {
        $services = Service::with([
            'translations',
            'children.translations'
        ])->orderBy('id');
        
        if ($request->filled('status')) {
            // 1 = active, 2 = inactive; 
            if ($request->status == 1) {
                $services->where('status', 1);
            } elseif ($request->status == 2) {
                $services->where('status', 0);
            }
        }
        $services = $services->get();

        $defaultLangId = Language::where('code', 'en')->first()->id ?? 1;

        return view('admin.services.index', compact('services', 'defaultLangId'));
    }

    public function edit($id)
    {
        $service = Service::with('translations')->findOrFail($id);
        $languages = Language::where('status', 1)->get();
        $consultationDurations = $fees = [];

        if ($service->slug === 'online-live-consultancy') {
            $consultationDurations = ConsultationDuration::where('status',1)->orderBy('type')->orderBy('duration')->get();
        }elseif($service->slug === 'annual-retainer-agreement'){
            $fees = AnnualRetainerBaseFee::with('installments')->orderBy('calls_per_month')->orderBy('visits_per_year')->get();
        }

        return view('admin.services.edit', compact('service', 'languages','consultationDurations','fees'));
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);
    
        $request->validate([
            'icon' => 'nullable|image|mimes:png|max:150',
            'sort_order' => 'nullable|integer',
            'status' => 'required|boolean',
            'translations.en.description' => 'required|string',
        ],[
            'translations.en.description.required' => 'The english description field is required.',
        ]);

        $iconPath = $service->icon;
        if ($request->hasfile('icon')) {
            $icon = str_replace('/storage/', '', $service->icon);
            if ($icon && Storage::disk('public')->exists($icon)) {
                Storage::disk('public')->delete($icon);
            }
            $iconPath = uploadImage('services', $request->icon, 'image');
        }

        $service->update([
            'icon' => $iconPath,
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->status,
            // 'payment_active' => $request->payment_active ?? 0, 
            'service_fee' => $request->service_fee ?? 0,
            'tax' => $request->tax_total ?? 0,
            'total_amount' => $request->total_amount ?? 0
        ]);

        foreach ($request->translations as $langId => $transData) {
            ServiceTranslation::updateOrCreate(
                ['service_id' => $service->id, 'lang' => $langId],
                ['description' => $transData['description'] ?? null,'info' => $transData['info'] ?? null]
            );
        }

        if ($service->slug === 'online-live-consultancy' && $request->has('durations')) {
            foreach ($request->durations as $durationId => $amount) {
                ConsultationDuration::where('id', $durationId)->update([
                    'amount' => $amount
                ]);
            }
        }

        if ($service->slug === 'annual-retainer-agreement' && $request->has('fees')) {
            foreach ($request->fees as $id => $data) {
                $base = AnnualRetainerBaseFee::find($id);
                if (!$base) continue;

                $service = floatval($data['service_fee']);
                
                $tax = ($service) * 0.05;
                $baseTotal = $service + $tax;

                $base->update([
                    'service_fee' => $service,
                    'tax' => $tax,
                    'base_total' => $baseTotal,
                ]);

                foreach ($data['installments'] as $installmentId => $instData) {
                    $percent = floatval($instData['extra_percent']);
                    $final = $baseTotal + ($baseTotal * ($percent / 100));

                    AnnualRetainerInstallment::where('id', $installmentId)->update([
                        'extra_percent' => $percent,
                        'final_total' => $final,
                    ]);
                }
            }
        }
        session()->flash('success', 'Service updated successfully.');
        return redirect()->route('services.index');
    }

    public function updateStatus(Request $request)
    {
        $newStatus = $request->status;
        $service = Service::findOrFail($request->id);
        $service->status = $newStatus;
        $service->save();

        if ($service->parent_id === null) {
            Service::where('parent_id', $service->id)
                ->update(['status' => $newStatus]);
        } else {
            $parent = Service::find($service->parent_id);

            if ($newStatus == 1 && $parent && $parent->status == 0) {
                $parent->status = 1;
                $parent->save();
            }

            if ($newStatus == 0 && $parent) {
                $allSiblingsInactive = Service::where('parent_id', $parent->id)
                    ->where('status', 1)
                    ->exists() === false;

                if ($allSiblingsInactive) {
                    $parent->status = 0;
                    $parent->save();
                }
            }
        }


        return 1;
    }


     public function indexExpertPricing(Request $request)
    {
        $request->session()->put('expert_pricing_last_url', url()->full());

        $query = ExpertReportPricing::with(['reportType','language']);
                
        if ($request->filled('status')) {
            if ($request->status == 1) {
                $query->where('status', 1);
            } elseif ($request->status == 2) {
                $query->where('status', 0);
            }
        }

        if ($request->filled('report_lang')) {
            $query->where('language_id', $request->report_lang);
        }

        if ($request->filled('report_type')) {
            $query->where('expert_report_type_id', $request->report_type);
        }

        if ($request->filled('litigation_type')) {
            $query->where('litigation_type', $request->litigation_type);
        }

        $expertPricing = $query->orderBy('id', 'DESC')->paginate(20); 

        $dropdowns = Dropdown::with(['options.translations' => function ($q) {
                                    $q->where('language_code', 'en');
                                }])->whereIn('slug', ['expert_report_type','expert_report_languages'])->get()->keyBy('slug');

        return view('admin.services.index-report-pricing', compact('dropdowns','expertPricing'));
    }

    public function createExpertPricing(){
        $dropdowns = Dropdown::with(['options.translations' => function ($q) {
                                    $q->where('language_code', 'en');
                                }])->whereIn('slug', ['expert_report_type','expert_report_languages'])->get()->keyBy('slug');
        return view('admin.services.create-report-pricing', compact('dropdowns'));
    }

    public function storeExpertPricing(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'litigation_type'           => 'required',
            'expert_report_type'        => 'required',
            'expert_report_language'    => 'required',
            'admin_amount'              => 'required|numeric|min:0',
            'tax_amount'                => 'required|numeric|min:0',
            'total_amount'              => 'required|numeric|min:0',
        ],[
            '*.required'                        => 'This field is required.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $exists = ExpertReportPricing::where('litigation_type', $request->litigation_type)
                                    ->where('expert_report_type_id', $request->expert_report_type)
                                    ->where('language_id', $request->expert_report_language)
                                    ->exists();

        if ($exists) {
            return redirect()->back()->withInput()->withErrors([
                'litigation_type' => 'Pricing for this combination already exists.'
            ]);
        }

        $rate = ExpertReportPricing::create([
            'litigation_type'       => $request->litigation_type,
            'expert_report_type_id' => $request->expert_report_type,
            'language_id'           => $request->expert_report_language,
            'admin_fee'             => $request->admin_amount,
            'status'                => 1
        ]);

        session()->flash('success','Expert report pricing created successfully.');
       
        return redirect()->route('expert-pricing.index');
    }

    public function editExpertPricing($id){
        $pricing = ExpertReportPricing::find(base64_decode($id));
        $dropdowns = Dropdown::with(['options.translations' => function ($q) {
                                    $q->where('language_code', 'en');
                                }])->whereIn('slug', ['expert_report_type','expert_report_languages'])->get()->keyBy('slug');
        return view('admin.services.edit-report-pricing', compact('dropdowns','pricing'));
    }

    public function updateExpertPricing(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'litigation_type'           => 'required',
            'expert_report_type'        => 'required',
            'expert_report_language'    => 'required',
            'admin_amount'              => 'required|numeric|min:0',
            'tax_amount'                => 'required|numeric|min:0',
            'total_amount'              => 'required|numeric|min:0',
        ],[
            '*.required'                        => 'This field is required.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $pricing = ExpertReportPricing::findOrFail($id);

        $exists = ExpertReportPricing::where('litigation_type', $request->litigation_type)
                                    ->where('expert_report_type_id', $request->expert_report_type)
                                    ->where('language_id', $request->expert_report_language)
                                    ->where('id', '!=', $id)
                                    ->exists();

        if ($exists) {
            return redirect()->back()->withInput()->withErrors([
                'litigation_type' => 'Pricing for this combination already exists.'
            ]);
        }

        $pricing->update([
            'litigation_type'       => $request->litigation_type,
            'expert_report_type_id' => $request->expert_report_type,
            'language_id'           => $request->expert_report_language,
            'admin_fee'             => $request->admin_amount
        ]);

        $url =  session()->get('expert_pricing_last_url') ?? route('expert-pricing.index');
        return redirect($url)->with('success', 'Pricing updated successfully.');
    }

    public function updateExpertPricingStatus(Request $request)
    {
        $price = ExpertReportPricing::findOrFail($request->id);
        
        $price->status = $request->status;
        $price->save();
       
        return 1;
    }

    public function destroyExpertPricing($id)
    {
        $price = ExpertReportPricing::findOrFail($id);
        $price->delete();
        return redirect()->back()->with('success', 'Expert report pricing deleted successfully.');
    }
}
