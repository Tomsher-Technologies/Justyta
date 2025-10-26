<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\JobPost;
use App\Models\TrainingRequest;
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
        return view('admin.dashboard', compact('data', 'services','serviceCounts','userCounts','totalJobs','totalTrainings','chartData','recentRequests'));
    }
}
