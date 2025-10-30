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
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;

class AdminDashboardController extends Controller
{
    public function dashboard(){
        $data = [];
        $services = Service::orderBy('sort_order', 'asc')->get();
        $serviceCounts = ServiceRequest::selectRaw('service_id, COUNT(*) as total')
                            ->where('request_success', 1)
                            ->groupBy('service_id')
                            ->pluck('total', 'service_id');

        $userCounts = User::select('user_type', DB::raw('count(*) as total'))
                            // ->where('banned', 0)
                            ->groupBy('user_type')
                            ->pluck('total', 'user_type');
        
        $totalJobs = JobPost::count();
        $totalTrainings = TrainingRequest::count();

        $serviceSales = ServiceRequest::where('request_success', 1)->get()->sum('amount');

        $consultationSales = Consultation::where('request_success', 1)->get()->sum('amount');
      
        $totalSales = $serviceSales + $consultationSales;

        // Service request chart
        $chartData = [];
         $colors = [
                '#06b6d482','#7fffd4','#deb887','#a9a9a9','#bdb76b', '#d2a3ff','#f18989','#90f790','#f7d28e','#f3acf3','#f7f779','#f96565','#a6a6ee','#daa9b2','#b8860b','#9acd32','#059ab3', '#902fec','#ff0000','#008000','#ffa500','#ff00ff','#caca03','#a52a2a', '#5f5ff3','#e36179',
                ];
        foreach($services as $i => $service){
            if($service->slug != 'law-firm-services'){
                $chartData[] = [
                    'name' => $service->name,
                    'y' => $serviceCounts[$service->id] ?? 0,
                    'color' => $colors[$i]
                ];
            }
        }

        $recentRequests = ServiceRequest::with('service')
                                        ->where('request_success', 1)
                                        ->whereNotIn('service_slug',['legal-translation'])
                                        ->orderByDesc('id')
                                        ->limit(10)
                                        ->get(); 

        
        return view('admin.dashboard', compact('data', 'services','serviceCounts','userCounts','totalJobs','totalTrainings','chartData','recentRequests','totalSales'));
    }

    public function getSalesData(Request $request)
    {
        $filter = $request->get('filter', 'monthly');
        $year = $request->get('year');
        $month = $request->get('month'); 

       // DB::enableQueryLog();
        $serviceSales = ServiceRequest::when($year, function ($q) use ($year) {
                                        $q->whereYear('created_at', $year);
                                    })
                                    ->when($filter === 'daily' && $month, fn($q) => $q->whereMonth('created_at', $month))
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

        $sales = $serviceSales->merge($consultationSales);

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
        ];

        return response()->json($response);
    }
}
