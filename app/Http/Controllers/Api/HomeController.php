<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home(Request $request)
    {

        $lang = $request->header('lang') ?? 'en'; // default to English
        $services = Service::with(['translations' => function ($query) use ($lang) {
                $query->where('lang', $lang);
            }])
            ->whereNull('parent_id')
            ->where('status', 1)
            ->orderBy('sort_order', 'ASC')
            ->get();

        // Optionally transform the result to extract only translated fields
        $data['services'] = $services->map(function ($service) {
            $translation = $service->translations->first();
            return [
                'id' => $service->id,
                'title' => __('messages.'.$service->slug) ?? '',
                'icon' => asset($service->icon),
            ];
        });
      
        $data['quick_link'] = [];
        $data['banner'] = null;
        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $data
        ], 200);
    }

    public function lawfirmServices(Request $request)
    {

        $lang = $request->header('lang') ?? 'en'; // default to English
        $services = Service::with(['translations' => function ($query) use ($lang) {
                $query->where('lang', $lang);
            }])
            ->where('parent_id', 3)
            ->where('status', 1)
            ->orderBy('sort_order', 'ASC')
            ->get();

        // Optionally transform the result to extract only translated fields
        $data['services'] = $services->map(function ($service) {
            $translation = $service->translations->first();
            return [
                'id' => $service->id,
                'title' => __('messages.'.$service->slug) ?? '',
                'icon' => asset($service->icon),
            ];
        });
      
        $data['banner'] = null;
        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $data
        ], 200);
    }
}
