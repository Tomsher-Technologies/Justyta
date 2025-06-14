<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\News;
use Illuminate\Http\Request;
use DB;

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

    public function search(Request $request)
    {
        $lang = $request->header('lang') ?? 'en'; // default to English
        $keyword = $request->get('keyword');
        // DB::enableQueryLog();
        $services = Service::where('status', 1)
                    ->whereHas('translations', function ($query) use ($keyword) {
                        $query->where(function ($q) use ($keyword) {
                            $q->where('title', 'LIKE', "%$keyword%")
                            ->orWhere('description', 'LIKE', "%$keyword%");
                        });
                    })
                    ->with(['translations' => function ($query) use ($lang) {
                        $query->where('lang', $lang);
                    }])
                    ->orderBy('sort_order', 'ASC')
                    ->get();
                    // dd(DB::getQueryLog());

        $servs = $services->map(function ($service) {
                    $translation = $service->translations;
                    return [
                        'id' => $service->id,
                        'title' => __('messages.'.$service->slug) ?? '',
                        'icon' => asset($service->icon),
                    ];
                });
        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $servs,
        ], 200);
    }

    public function news(Request $request)
    {
        $lang = $request->header('lang') ?? 'en';  // default to 'en'
        $limit = $request->get('limit', 10);

        $news = News::with(['translations' => function ($q) use ($lang) {
                    $q->where('lang', $lang);
                }])
                ->where('status', 1)
                ->orderByDesc('news_date')
                ->paginate($limit);

        $newsData = $news->map(function ($item) {
            $translation = $item->translations->first();

            return [
                'id' => $item->id,
                'image' => $item->image,
                'news_date' => $item->news_date,
                'title' => $translation->title ?? '',
                'description' => $translation->description ?? '',
                'meta_title' => $translation->meta_title ?? '',
                'meta_description' => $translation->meta_description ?? '',
                'meta_keywords' => $translation->meta_keywords ?? '',
                'twitter_title' => $translation->twitter_title ?? '',
                'twitter_description' => $translation->twitter_description ?? '',
                'og_title' => $translation->og_title ?? '',
                'og_description' => $translation->og_description ?? '',
            ];
        });
              
        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $newsData,
            'current_page' => $news->currentPage(),
            'last_page' => $news->lastPage(),
            'limit' => $news->perPage(),
            'total' => $news->total(),
        ]);
    }

    public function newsDetails(Request $request)
    {
        $lang = $request->header('lang') ?? 'en';
        $id = $request->get('id');

        $news = News::with(['translations' => function ($q) use ($lang) {
            $q->where('lang', $lang);
        }])->where('status', 1)->find($id);

        if (!$news) {
            return response()->json([
                'status' => true,
                'message' => 'News not found',
            ], 200);
        }

        $translation = $news->translations->first();

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $news->id,
                'image' => $news->image,
                'news_date' => $news->news_date,
                'title' => $translation->title ?? '',
                'description' => $translation->description ?? '',
                'meta_title' => $translation->meta_title ?? '',
                'meta_description' => $translation->meta_description ?? '',
                'meta_keywords' => $translation->meta_keywords ?? '',
                'twitter_title' => $translation->twitter_title ?? '',
                'twitter_description' => $translation->twitter_description ?? '',
                'og_title' => $translation->og_title ?? '',
                'og_description' => $translation->og_description ?? '',
            ]
        ]);
    }
}
