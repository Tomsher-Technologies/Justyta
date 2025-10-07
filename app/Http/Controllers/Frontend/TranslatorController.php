<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceRequest;
use App\Models\RequestLegalTranslation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class TranslatorController extends Controller
{
    public function dashboard()
    {
        $translatorId = Auth::id();

        $lang = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $services = \App\Models\Service::with('translations')->get();

        $legalTranslationRequests = RequestLegalTranslation::where('assigned_translator_id', Auth::guard('frontend')->user()->translator?->id)
            ->with(['serviceRequest', 'documentLanguage', 'translationLanguage'])
            ->get();

        $totalTranslations = $legalTranslationRequests->count();

        $completedTranslations = $legalTranslationRequests->filter(function ($item) {
            return $item->serviceRequest && in_array($item->serviceRequest->status, ['completed', 'delivered']);
        })->count();

        $pendingTranslations = $legalTranslationRequests->filter(function ($item) {
            return $item->serviceRequest && in_array($item->serviceRequest->status, ['pending', 'processing']);
        })->count();

        $inProgressTranslations = $legalTranslationRequests->filter(function ($item) {
            return $item->serviceRequest && in_array($item->serviceRequest->status, ['in_progress', 'review']);
        })->count();

        $currentMonthIncome = $legalTranslationRequests->filter(function ($item) {
            return $item->serviceRequest &&
                $item->serviceRequest->paid_at &&
                Carbon::parse($item->serviceRequest->paid_at)->isCurrentMonth() &&
                in_array($item->serviceRequest->status, ['completed', 'delivered']);
        })->sum('translator_amount');

        $totalIncome = $legalTranslationRequests->filter(function ($item) {
            return $item->serviceRequest &&
                $item->serviceRequest->paid_at &&
                in_array($item->serviceRequest->status, ['completed', 'delivered']);
        })->sum('translator_amount');

        $serviceRequests = $legalTranslationRequests
            ->sortByDesc(function ($item) {
                return $item->serviceRequest ? $item->serviceRequest->created_at : $item->created_at;
            })
            ->take(10)
            ->map(function ($item) {
                $serviceRequest = $item->serviceRequest;
                return [
                    'reference_code' => $serviceRequest ? $serviceRequest->reference_code : 'N/A',
                    'date_time' => $serviceRequest ? $serviceRequest->created_at->format('Y-m-d H:i A') : $item->created_at->format('Y-m-d H:i A'),
                    'document_language' => $item->documentLanguage ? $item->documentLanguage->name : 'N/A',
                    'translation_language' => $item->translationLanguage ? $item->translationLanguage->name : 'N/A',
                    'no_of_pages' => $item->no_of_pages ?? 'N/A',
                    'status' => $serviceRequest ? $serviceRequest->status : 'N/A',
                    'service_request_id' => $serviceRequest ? $serviceRequest->id : null
                ];
            });

        $notificationsResult = $result = $this->getTranslatorNotifications();
        $notifications = $notificationsResult['notifications'];

        return view('frontend.translator.dashboard', compact(
            'totalTranslations',
            'completedTranslations',
            'pendingTranslations',
            'inProgressTranslations',
            'currentMonthIncome',
            'serviceRequests',
            'totalIncome',
            'notifications'
        ));
    }

    public function notifications(Request $request)
    {
        $result = $this->getTranslatorNotifications();
        $paginated = $result['paginatedNot'];
        $allShownIds = collect($paginated->items())->pluck('id')->filter()->values();

        if ($allShownIds->isNotEmpty()) {
            Auth::guard('frontend')
                ->user()
                ->unreadNotifications()
                ->whereIn('id', $allShownIds)
                ->update(['read_at' => now()]);
        }

        return view('frontend.translator.notifications', [
            'notifications' => $result['notifications'],
            'paginatedNot'  => $result['paginatedNot'],
        ]);
    }

    public function clearAllNotifications()
    {
        Auth::guard('frontend')->user()->notifications()->delete();
        return response()->json(['success' => true, 'message' =>  __('messages.notifications_cleared_successfully')]);
    }

    public function deleteSelectedNotifications(Request $request)
    {
        $ids = $request->notification_ids ?? [];

        if (!empty($ids)) {
            Auth::guard('frontend')->user()->notifications()->whereIn('id', $ids)->delete();
        }
        return response()->json(['success' => true, 'message' =>  __('messages.selected_notifications_cleared_successfully')]);
    }

    public function getTranslatorNotifications()
    {
        $lang       = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $services   = \App\Models\Service::with('translations')->get();

        $serviceMap = [];

        foreach ($services as $service) {
            foreach ($service->translations as $translation) {
                $serviceMap[$service->slug][$translation->lang] = $translation->title;
            }
        }

        $allNotifications =  Auth::guard('frontend')->user()->notifications();

        $paginatedNot = (clone $allNotifications)
            ->orderByDesc('created_at')
            ->paginate(10);

        $notifications = collect($paginatedNot->items())
            ->map(function ($notification) use ($lang, $serviceMap) {
                $data = $notification->data;
                $slug = $data['service'] ?? null;

                $serviceName =  $slug && isset($serviceMap[$slug]) ? ($serviceMap[$slug][$lang] ?? $serviceMap[$slug][env('APP_LOCALE', 'en')] ?? $slug) : '';

                return [
                    'id'   => $notification->id,
                    'message'   => __($notification->data['message'], [
                        'service'   => $serviceName,
                        'reference' => $data['reference_code'],
                    ]),
                    'time'      => $notification->created_at->format('d M, Y h:i A'),
                ];
            });

        return [
            'notifications' => $notifications,
            'paginatedNot'  => $paginatedNot,
        ];
    }

    public function account()
    {
        $user   = Auth::guard('frontend')->user();
        return view('frontend.translator.account', compact('user'));
    }

    public function showServiceRequest()
    {

        return view('frontend.translator.service-requests.service-details');
    }
}
