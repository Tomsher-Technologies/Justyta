<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\News;
use App\Models\Consultation;
use App\Models\Page;
use App\Models\Contacts;
use Illuminate\Http\Request;
use App\Mail\ContactEnquiry;
use Illuminate\Support\Facades\Validator;
use DB;
use Mail;

class HomeController extends Controller
{

    public function getBanners(Request $request){
        $lang = request()->header('lang') ?? env('APP_LOCALE','en'); 
        $slug = $request->get('slug');

        $ads = getActiveAd($slug, 'mobile');

        $data = [];
        if ($ads) {
            $file = $ads->files->first();
            $data = [
                'file' => getUploadedFile($file->file_path),
                'file_type' => $file->file_type,
                'url' => $ads->cta_url
            ];
        }
        
        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $data
        ]);
    }
    public function pageContents(Request $request){
        $lang = request()->header('lang') ?? env('APP_LOCALE','en'); 
        $slug = $request->get('slug');

        $page = getPageDynamicContent($slug, $lang);
        
        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $page
        ]);
    }
    public function home(Request $request)
    {

        $lang = $request->header('lang') ?? env('APP_LOCALE','en'); 
        $services = Service::with(['translations' => function ($query) use ($lang) {
                        $query->where('lang', $lang);
                    }])
                    ->whereNull('parent_id')
                    ->where('status', 1)
                    ->orderBy('sort_order', 'ASC')
                    ->get();

        $data['services'] = $services->map(function ($service) {
            $translation = $service->translations->first();
            return [
                'id' => $service->id,
                'slug' => $service->slug,
                'title' => $translation->title ?? '',
                'icon' => asset(getUploadedImage($service->icon)),
            ];
        });
      
        $quickServices = Page::where('slug', 'user_app_home')->pluck('content');
        $quickIds = $quickServices ? json_decode($quickServices[0]) : [];
        
        $servicesQuick = Service::with(['translations' => function ($queryQ) use ($lang) {
                        $queryQ->where('lang', $lang);
                    }])
                    ->whereIn('id', $quickIds)
                    ->where('status', 1)
                    ->orderBy('sort_order', 'ASC')
                    ->get();

        $data['quick_link'] = $servicesQuick->map(function ($serv) {
            $translationQ = $serv->translations->first();
            return [
                'id' => $serv->id,
                'slug' => $serv->slug,
                'title' => $translationQ->title ?? ''
            ];
        });

        $ads = getActiveAd('lawfirm_services', 'mobile');

        $data['banner'] = [];
        if ($ads) {
            $file = $ads->files->first();
            $data['banner'] = [
                'file' => getUploadedFile($file->file_path),
                'file_type' => $file->file_type,
                'url' => $ads->cta_url
            ];
        }

        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $data
        ], 200);
    }

    public function lawfirmServices(Request $request)
    {
        $lang = $request->header('lang') ?? env('APP_LOCALE','en'); 
        $services = Service::with(['translations' => function ($query) use ($lang) {
                $query->where('lang', $lang);
            }])
            ->where('parent_id', 3)
            ->where('status', 1)
            ->orderBy('sort_order', 'ASC')
            ->get();

        $data['services'] = $services->map(function ($service) {
            $translation = $service->translations->first();
            return [
                'id' => $service->id,
                'slug' => $service->slug,
                'title' => $translation->title ?? '',
                'icon' => asset(getUploadedImage($service->icon)),
            ];
        });
      
        $ads = getActiveAd('lawfirm_services', 'mobile');

        $data['banner'] = [];
        if ($ads) {
            $file = $ads->files->first();
            $data['banner'] = [
                'file' => getUploadedFile($file->file_path),
                'file_type' => $file->file_type,
                'url' => $ads->cta_url
            ];
        }
        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $data
        ], 200);
    }

    public function search(Request $request)
    {
        $lang = $request->header('lang') ?? env('APP_LOCALE','en');
        $keyword = $request->get('keyword');
        
        $services = Service::where('status', 1)
                    ->whereNotIn('slug',['law-firm-services'])
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
                  
        $servs = $services->map(function ($service) {
                    $translation = $service->translations->first();
                    return [
                        'id' => $service->id,
                        'slug' => $service->slug,
                        'title' => $translation->title ?? '',
                        'icon' => asset(getUploadedImage($service->icon)),
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
        $lang = $request->header('lang') ?? env('APP_LOCALE','en');  
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

        $ads = getActiveAd('news', 'mobile');

        $data['banner'] = [];
        if ($ads) {
            $file = $ads->files->first();
            $data['banner'] = [
                'file' => getUploadedFile($file->file_path),
                'file_type' => $file->file_type,
                'url' => $ads->cta_url
            ];
        }
              
        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $newsData,
            'current_page' => $news->currentPage(),
            'last_page' => $news->lastPage(),
            'limit' => $news->perPage(),
            'total' => $news->total(),
            'banner' => $data['banner'],
        ], 200);
    }

    public function newsDetails(Request $request)
    {
        $lang = $request->header('lang') ?? env('APP_LOCALE','en');
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

        $ads = getActiveAd('news', 'mobile');

        $data['banner'] = [];
        if ($ads) {
            $file = $ads->files->first();
            $data['banner'] = [
                'file' => getUploadedFile($file->file_path),
                'file_type' => $file->file_type,
                'url' => $ads->cta_url
            ];
        }

        return response()->json([
            'status' => true,
            'message' => 'Success',
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
                'banner' => $data['banner']
            ]
        ], 200);
    }

    public function contactUs(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'nullable',
            'email' => 'required|email',
            'phone' => 'required|numeric',
            'subject' => 'required',
            'message' => 'required'
        ], [
            'email.required'     => __('messages.email_required'),
            'email.email'        => __('messages.valid_email'),
            'phone.required'     => __('messages.phone_required'),
            'message.required' => __('messages.enter_message'),
            'subject.required' => __('messages.enter_subject')
        ]);

        if ($validator->fails()) {
            $message = implode(' ', $validator->errors()->all());

            return response()->json([
                'status' => false,
                'message' => $message,
            ], 200);
        }

        $user = $request->user();

        $con                = new Contacts;
        $con->name          = $request->name ?? $user->name;
        $con->email         = $request->email ?? NULL;
        $con->phone         = $request->phone ?? NULL;
        $con->subject       = $request->subject ?? NULL;
        $con->message       = $request->message ?? NULL;
        $con->save();

        Mail::to(env('MAIL_ADMIN'))->queue(new ContactEnquiry($con));

        return response()->json(['status' => true,"message"=> __('messages.contact_us_success'),"data" => []],200);
    }

    public function handleZoomWebhook(Request $request)
    {
        $zoomSecret = config('services.zoom.webhook_secret'); 
        $providedSecret = $request->header('Authorization');

        if ($zoomSecret && $providedSecret !== "Bearer {$zoomSecret}") {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $event = $request->event;
        $meetingId = $request->input('payload.object.id');

        if ($event === 'meeting.started') {
            $consultation = Consultation::where('zoom_meeting_id', $meetingId)->first();
            if ($consultation) {
                $consultation->status = 'in_progress';
                $consultation->save();

                $consultation->lawyer->update(['is_busy' => 1]);
            }
        }

        if ($event === 'meeting.ended') {
            $consultation = Consultation::where('zoom_meeting_id', $meetingId)->first();
            if ($consultation) {
                $consultation->status = 'completed';
                $consultation->save();

                $consultation->lawyer->update(['is_busy' => 0]);
            }
        }

        return response()->json(['message' => 'Webhook received'], 200);
    }
}
