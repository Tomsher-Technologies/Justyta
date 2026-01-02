<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\JobPost;
use App\Models\TrainingRequest;
use App\Models\Consultation;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ServiceSalesExport;
use App\Exports\SubscriptionSalesExport;
use App\Models\Vendor;
use App\Models\VendorSubscription;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;

class AdminDashboardController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
       
        $this->middleware('permission:dashboard_total_sales_view',  ['only' => ['getServiceSalesData','exportServiceSales','getSubscriptionSalesData','exportSubscriptionSales']]);
        $this->middleware('permission:manage_users',  ['only' => ['allUsers']]);
        $this->middleware('permission:view_users',  ['only' => ['allUsers']]);
        $this->middleware('permission:ban_user',  ['only' => ['updateUserStatus']]);
    }
    public function dashboard(Request $request){
        $data = [];

        $daterangeCommon = $request->has('daterangeCommon') ? $request->daterangeCommon : null;

        $daterangeCommon = explode(' - ', $daterangeCommon);

        $date1Common = $date2Common = null;
        if (count($daterangeCommon) == 2) {
            $date1Common = trim($daterangeCommon[0]);
            $date2Common = trim($daterangeCommon[1]);
        }

        $dateFilter = function($query) use ($date1Common, $date2Common) {
                        if ($date1Common && $date2Common) {
                            $query->whereDate('created_at', '>=', $date1Common)
                                ->whereDate('created_at', '<=', $date2Common);
                        }
                    };

        $services = Service::where('status', 1)->orderBy('sort_order', 'asc')->get();
        
        $userCounts = User::select('user_type', DB::raw('count(*) as total'))
                            // ->where('banned', 0)
                            ->when($date1Common && $date2Common, $dateFilter)
                            ->groupBy('user_type')
                            ->pluck('total', 'user_type');
        
        $totalJobs = JobPost::when($date1Common && $date2Common, $dateFilter)->count();
        $totalTrainings = TrainingRequest::when($date1Common && $date2Common, $dateFilter)->count();

        $serviceSales = ServiceRequest::where('request_success', 1)->when($date1Common && $date2Common, $dateFilter)->get()->sum('amount');

        $consultationSales = Consultation::where('request_success', 1)->when($date1Common && $date2Common, $dateFilter)->get()->sum('amount');
      
        $totalSales = $serviceSales + $consultationSales;

        $totalSubscriptionSales = VendorSubscription::whereIn('status', ['active','expired'])
                                    ->whereHas('vendor', function ($q) {
                                        $q->where('is_default', 0);
                                    })
                                    ->when($date1Common && $date2Common, $dateFilter)
                                    ->get()
                                    ->sum('amount');

        // Total Users
        $totalUsers = User::where('user_type', 'user')
                            ->when($date1Common && $date2Common, $dateFilter)
                            ->count();
        // Service request chart

        $daterangeService = $request->has('daterangeService') ? $request->daterangeService : null;

        $daterangeService = explode(' - ', $daterangeService);

        $date1Service = $date2Service = null;
        if (count($daterangeService) == 2) {
            $date1Service = trim($daterangeService[0]);
            $date2Service = trim($daterangeService[1]);
        }

        $serviceCounts = ServiceRequest::selectRaw('service_id, COUNT(*) as total')
                            ->where('request_success', 1)
                            ->when($date1Service && $date2Service, function($query) use ($date1Service, $date2Service) {
                                if ($date1Service && $date2Service) {
                                    $query->whereDate('created_at', '>=', $date1Service)
                                        ->whereDate('created_at', '<=', $date2Service);
                                }
                            })
                            ->groupBy('service_id')
                            ->pluck('total', 'service_id');

        $chartData = [];
        $colors = [
                '#06b6d482','#7fffd4','#deb887','#a9a9a9','#bdb76b', '#d2a3ff','#f18989','#90f790','#f7d28e','#f3acf3','#f7f779','#f96565','#a6a6ee','#daa9b2','#b8860b','#9acd32','#059ab3', '#902fec','#ff0000','#008000','#ffa500','#ff00ff','#caca03','#a52a2a', '#5f5ff3','#e36179',
                ];

        $paymentServices = [];       

        foreach($services as $i => $service){
            if($service->slug != 'law-firm-services'){
                if($service->payment_active == 1){
                    $paymentServices[] = [
                        'name' => $service->name,
                        'id' => $service->id,
                        'slug' => $service->slug
                    ];
                }
            }
        }

        
        $services = $services->filter(function($service) {
                            return auth()->user()->can('view-' . $service->slug);
                        })->values();

        $allowedServiceSlugs = [];
        foreach($services as $i => $service){
            if($service->slug != 'law-firm-services'){
                $chartData[] = [
                    'name' => $service->name,
                    'y' => $serviceCounts[$service->id] ?? 0,
                    'color' => $colors[$i]
                ];
            }

            $permission = 'view-' . $service->slug;
            if (auth()->user()->can($permission)) {
                $allowedServiceSlugs[] = $service->slug;
            }
        }

        $recentRequests = ServiceRequest::with('service')
                                        ->where('request_success', 1)
                                        ->whereNotIn('service_slug',['legal-translation'])
                                        ->orderByDesc('id')
                                        ->whereIn('service_slug', $allowedServiceSlugs)
                                        ->limit(10)
                                        ->get(); 

        
        return view('admin.dashboard', compact('data', 'services','serviceCounts','userCounts','totalJobs','totalTrainings','chartData','recentRequests','totalSales','paymentServices','totalSubscriptionSales','totalUsers'));
    }

    public function getSalesData(Request $request)
    {
        $filter = $request->get('filter', 'monthly');
        $year = $request->get('year');
        $month = $request->get('month'); 
        $service = $request->get('service','all');

        $serviceData = ($service && $service !== 'all') ? Service::where('slug', $service)->first() : null;
       // DB::enableQueryLog();
        $serviceSales = ServiceRequest::when($year, function ($q) use ($year) {
                                        $q->whereYear('created_at', $year);
                                    })
                                    ->when($filter === 'daily' && $month, fn($q) => $q->whereMonth('created_at', $month))
                                    ->when($service, function($query) use ($service) {
                                        if ($service !== 'all' && $service !== 'online-live-consultancy') {
                                            $query->where('service_slug', $service);
                                        }
                                    })
                                    ->where('request_success', 1)
                                    // ->whereNotNull('payment_reference')
                                    ->get(['amount', 'created_at']);
        
        $consultationSales = Consultation::when($year, function ($q) use ($year) {
                                            $q->whereYear('created_at', $year);
                                        })
                                        ->when($filter === 'daily' && $month, fn($q) => $q->whereMonth('created_at', $month))
                                        ->where('request_success', 1)
                                        ->get(['amount', 'created_at']);
        // dd(DB::getQueryLog());
        $serviceSales = collect($serviceSales);
        $consultationSales = collect($consultationSales);

        if ($service === 'all') {
            $sales = $serviceSales->merge($consultationSales);
        } elseif ($service === 'online-live-consultancy') {
            $sales = $consultationSales;
        } else {
            $sales = $serviceSales; 
        } 

        $getKey = function ($item, $filter) {
            $dt = $item->created_at ?? ($item['created_at'] ?? null);
            if (!$dt) return null;
            $carbon = Carbon::parse($dt);

            return match($filter) {
                'daily'   => $carbon->format('Y-m-d'),
                'weekly'  => 'Week ' . $carbon->weekOfYear,
                'yearly'  => $carbon->year,
                default   => $carbon->format('M'), // monthly -> 'Jan', 'Feb', ...
            };
        };

        switch ($filter) {
            case 'daily':
                $month = $month ?? Carbon::now()->month;
                $start = Carbon::create($year, $month, 1)->startOfMonth();
                $end = Carbon::create($year, $month, 1)->endOfMonth();
                $period = CarbonPeriod::create($start, $end);
                $labels = [];
                foreach ($period as $date) $labels[$date->format('Y-m-d')] = 0;
                break;

            case 'weekly':
                $weeksInYear = Carbon::create($year)->weeksInYear;
                $labels = [];
                for ($i = 1; $i <= $weeksInYear; $i++) $labels["Week {$i}"] = 0;
                break;

            case 'yearly':
                $currentYear = now()->year;
                $minYear = 2024;
                $startYear = max($currentYear - 9, $minYear);
                $labels = [];
                for ($i = $startYear; $i <= $currentYear; $i++) $labels[$i] = 0;
                break;

            default: 
                $labels = [
                    'Jan'=>0,'Feb'=>0,'Mar'=>0,'Apr'=>0,'May'=>0,'Jun'=>0,
                    'Jul'=>0,'Aug'=>0,'Sep'=>0,'Oct'=>0,'Nov'=>0,'Dec'=>0,
                ];
                break;
        }
                
        $map = [];

        // iterate through each sale item and add its amount to the proper bucket
        foreach ($sales as $row) {
            $key = $getKey($row, $filter);
            if ($key === null) continue;

            $rawAmount = $row->amount ?? ($row['amount'] ?? 0);
            if (is_string($rawAmount)) {
                $rawAmount = str_replace(',', '', $rawAmount);
            }
            $amount = (float) $rawAmount;

            if (!isset($map[$key])) $map[$key] = 0.0;
            $map[$key] += $amount;
        }
        
        foreach ($map as $k => $v) {
            if (array_key_exists($k, $labels)) {
                $labels[$k] = round($v, 2);
            } else {
                if ($filter === 'monthly') {
                    try {
                        $maybeMonth = Carbon::parse($k)->format('M');
                        if (array_key_exists($maybeMonth, $labels)) {
                            $labels[$maybeMonth] += round($v, 2);
                            continue;
                        }
                    } catch (\Exception $e) { /* ignore */ }
                }
                // if you prefer to include unexpected keys, uncomment:
                $labels[$k] = round($v, 2);
            }
        }

        $response = [
            'labels' => array_keys($labels),
            'data'   => array_values($labels),
            'title'  => $service === 'all' ? 'All Services' : $serviceData?->name,
        ];

        return response()->json($response);
    }

    public function getServiceSalesData(Request $request)
    {
        $services = Service::where('status', 1)
                        ->where('payment_active', 1)
                        ->orderBy('sort_order', 'asc')
                        ->get();

        $selectedService = $request->get('service_id');

        $dateQuery = false;
        if ($request->filled('daterange')) {
            $dates = explode(' to ', $request->daterange);
            if (count($dates) === 2) {
                $dateQuery = true;
            }
        }

        if ($selectedService == 'online-live-consultancy') {
            $request->session()->put('last_page_consultations', url()->full());
            $conQuery = Consultation::with(['user', 'lawyer'])
                ->where('request_success', 1);

            if ($dateQuery) {
                $conQuery->whereBetween('created_at', [
                    Carbon::parse($dates[0])->startOfDay(),
                    Carbon::parse($dates[1])->endOfDay()
                ]);
            }
            $consultations = $conQuery->orderBy('id', 'desc')
                                ->paginate(20);

            return view('admin.sales.service-sales', compact('services', 'selectedService', 'consultations'));
        }

        if ($selectedService == 'legal-translation') {
            $request->session()->put('translation_service_request_last_url', url()->full());
        }else{
            $request->session()->put('service_request_last_url', url()->full());
        }
        
        $serviceQuery = ServiceRequest::with(['user', 'service'])
            ->where('request_success', 1)
            ->when($selectedService, function ($query) use ($selectedService) {
                $query->where('service_slug', $selectedService);
            });

        if ($dateQuery) {
            $serviceQuery->whereBetween('submitted_at', [
                Carbon::parse($dates[0])->startOfDay(),
                Carbon::parse($dates[1])->endOfDay()
            ]);
        }
        $serviceRequests = $serviceQuery->orderBy('id', 'desc')->paginate(10);

        return view('admin.sales.service-sales', compact('services', 'selectedService', 'serviceRequests'));
    }

    public function exportServiceSales(Request $request)
    {
        $serviceSlug = $request->get('service_id');
        $fileName = $serviceSlug ? "{$serviceSlug}-sales.xlsx" : "service-sales.xlsx";
        $dates = null;

        if ($request->filled('daterange')) {
            $dates = explode(' to ', $request->daterange);
        }

        return Excel::download(new ServiceSalesExport($serviceSlug, $dates), $fileName);
    }

    public function getSubscriptionSalesData(Request $request)
    {
        $plans = \App\Models\MembershipPlan::where('is_active', 1)->orderBy('title', 'asc')->get();
        $vendors = Vendor::where('is_default', 0)->orderBy('law_firm_name', 'asc')->get();

        $subscriptionSales = VendorSubscription::with(['vendor', 'plan'])
                                            ->whereHas('vendor', function ($q) {
                                                $q->where('is_default', 0);
                                            });
        // Status filter
        if ($request->filled('status')) {
            $subscriptionSales->where('status', $request->status);
        } else {
            $subscriptionSales->whereIn('status', ['active', 'expired']);
        }

        // Vendor filter
        if ($request->filled('vendor_id')) {
            $subscriptionSales->where('vendor_id', $request->vendor_id);
        }

        // Plan filter
        if ($request->filled('plan_id')) {
            $subscriptionSales->where('membership_plan_id', $request->plan_id);
        }

        // Date range filter
        if ($request->filled('daterange')) {
            $dates = explode(' to ', $request->daterange);

            if (count($dates) === 2) {
                $subscriptionSales->whereBetween('created_at', [
                    Carbon::parse($dates[0])->startOfDay(),
                    Carbon::parse($dates[1])->endOfDay()
                ]);
            }
        }
        $subscriptionSales = $subscriptionSales->orderBy('id', 'desc')->paginate(20);

        return view('admin.sales.subscription-sales', compact('subscriptionSales', 'plans', 'vendors'));
    }

    public function exportSubscriptionSales(Request $request)
    {
        $fileName = "subscription-sales-".date('d-m-Y').".xlsx";
        $filters = [
            'status' => $request->status ?? null,
            'vendor_id' => $request->vendor_id ?? null,
            'plan_id' => $request->plan_id ?? null,
            'daterange' => $request->daterange ?? null,
        ];

        return Excel::download(new SubscriptionSalesExport($filters), $fileName);
    }

    public function allUsers(Request $request)
    {
        $sort_search = $request->has('search') ? $request->search : '';
        $users = User::where('user_type', 'user');

        if($sort_search){
            $users = $users->where(function ($query) use ($sort_search){
                        $query->where('name', 'like','%' . $sort_search . '%')
                            ->orWhere('email', 'like', '%' . $sort_search . '%')
                            ->orWhere('phone', 'like', '%' . $sort_search . '%');
                    });
        }

        // Date range filter
        if ($request->filled('daterange')) {
            $dates = explode(' to ', $request->daterange);

            if (count($dates) === 2) {
                $users->whereBetween('created_at', [
                    Carbon::parse($dates[0])->startOfDay(),
                    Carbon::parse($dates[1])->endOfDay()
                ]);
            }
        }

        if ($request->filled('status')) {
            // 1 = active, 2 = inactive; 
            if ($request->status == 1) {
                $users->where('banned', 0);
            } elseif ($request->status == 2) {
                $users->where('banned', 1);
            }
        }

        $users = $users->orderBy('id', 'desc')->paginate(20);

        return view('admin.users', compact('users'));
    }

    public function updateUserStatus(Request $request)
    {
        $user = User::findOrFail($request->id);
        
        $user->banned = $request->status;
        $user->save();
       
        return 1;
    }
}
