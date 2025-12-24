<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Consultation;

class HomeController extends Controller
{
    public function home()
    {
        $lang = app()->getLocale() ?? 'en';

        $page = \App\Models\Page::with(['sections' => function ($q) {
            $q->where('status', 1)->orderBy('order');
        }, 'sections.translations'])
            ->where('slug', 'home')
            ->first();

        $news = \App\Models\News::with('translations')
            ->where('status', 1)
            ->orderBy('news_date', 'desc')
            ->limit(6)
            ->get();

        $services = \App\Models\Service::with('translations')
            ->where('status', 1)
            ->orderBy('sort_order')
            ->get();

        return view('frontend.index', compact('page', 'news', 'services', 'lang'));
    }

    public function about()
    {
        return view('frontend.about');
    }
    public function aboutUs()
    {
        $lang = app()->getLocale() ?? 'en';

        $page = \App\Models\Page::with(['sections' => function ($q) {
            $q->where('status', 1)->orderBy('order');
        }, 'sections.translations'])
            ->where('slug', 'about_us')
            ->first();

        return view('frontend.aboutus', compact('page', 'lang'));
    }
    public function contactUs()
    {

        $page = \App\Models\Page::with(['sections' => function ($q) {
            $q->where('status', 1)->orderBy('order');
        }, 'sections.translations'])
            ->where('slug', 'contact_page')
            ->first();

        $lang = app()->getLocale() ?? 'en';


        return view('frontend.contactus', compact('page', 'lang'));
    }
    public function services()
    {
        $lang = app()->getLocale() ?? 'en';
        $page = \App\Models\Page::with(['sections' => function ($q) {
            $q->where('status', 1)->orderBy('order');
        }, 'sections.translations'])
            ->where('slug', 'services_page')
            ->first();

        return view('frontend.services', compact('page', 'lang'));
    }
    public function news()
    {
        $lang = app()->getLocale() ?? 'en';

        $news = \App\Models\News::with('translations')
            ->where('status', 1)
            ->orderBy('news_date', 'desc')
            ->paginate(9);

        $page = \App\Models\Page::with(['sections' => function ($q) {
            $q->where('status', 1)->orderBy('order');
        }, 'sections.translations'])
            ->where('slug', 'news')
            ->first();


        return view('frontend.news', compact('news', 'lang', 'page'));
    }

    public function newsDetails($id)
    {
        $lang = app()->getLocale() ?? 'en';
        $news = \App\Models\News::with('translations')->findOrFail($id);

        return view('frontend.news-details', compact('news', 'lang'));
    }

    public function refundPolicy()
    {
        return view('frontend.refund-policy');
    }

    public function privacyPolicy()
    {
        return view('frontend.privacy-policy');
    }
    public function termsConditions()
    {
        return view('frontend.terms-conditions');
    }
    public function userDashboard()
    {
        $lang = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $services = Service::with(['translations' => function ($query) use ($lang) {
            $query->where('lang', $lang);
        }])
            ->whereNotIn('slug', ['law-firm-services'])
            ->where('status', 1)
            ->orderBy('sort_order', 'ASC')
            ->get();

        return view('frontend.user.dashboard', compact('services'));
    }

    public function checkUserConsultationStatus(Request $request)
    {
        $user = auth()->guard('frontend')->user();
        $consultation = Consultation::where('id', $request->consultation_id)
            ->where('user_id', $user->id)
            ->first();

        if ($consultation && $consultation->status == 'accepted') {
            $signature = generateZoomSignature($consultation->zoom_meeting_id, $user->id, 0);

            return response()->json([
                'status' => true,
                'data' => [
                    'consultation_id' => $consultation->id,
                    'meeting_number' => $consultation->zoom_meeting_id,
                    'role' => 0,
                    'sdk_key' => config('services.zoom.sdk_key'),
                    'signature' => $signature,
                    'duration' => $consultation->duration ?? 0
                ]
            ]);
        } else {
            return response()->json(['status' => false, 'message' => 'No active consultation', 'data' => $consultation->status], 200);
        }
    }

    public function consultationCancel(Request $request)
    {
        $lang = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $data = getPageDynamicContent('consultancy_request_failed', $lang);
        return view('frontend.user.consultation-cancel', compact('data'));
    }

    public function saveStartTime(Request $request)
    {
        $consult = Consultation::find($request->consultation_id);

        $consult->status = 'in_progress';

        if (!$consult->meeting_start_time) {
            $start_time = $request->start_time / 1000;
            $consult->meeting_start_time = date('Y-m-d H:i:s');
        }
        $consult->save();
        return response()->json(['success' => true]);
    }

    public function getStartTime($id)
    {
        $consult = Consultation::find($id);

        return response()->json([
            'start_time' => strtotime($consult->meeting_start_time) * 1000 ?? null
        ]);
    }

    public function statusConsultation($consultationId)
    {
        // Fetch consultation by ID
        $consultation = Consultation::find($consultationId);

        if (!$consultation) {
            return response()->json([
                'status' => 'not_found'
            ], 404);
        }

        return response()->json([
            'status' => $consultation->status, // e.g., 'completed', 'ongoing'
        ]);
    }
    public function contactSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile' => 'required|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        \App\Models\Contacts::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->mobile,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        return redirect()->back()->with('success', __('Your message has been sent successfully.'));
    }
}
