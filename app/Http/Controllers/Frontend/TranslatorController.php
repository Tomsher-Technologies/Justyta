<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceRequest;
use App\Models\RequestLegalTranslation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

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
            return $item->serviceRequest && in_array($item->serviceRequest->status, ['completed']);
        })->count();

        $pendingTranslations = $legalTranslationRequests->filter(function ($item) {
            return $item->serviceRequest && in_array($item->serviceRequest->status, ['pending']);
        })->count();

        $inProgressTranslations = $legalTranslationRequests->filter(function ($item) {
            return $item->serviceRequest && in_array($item->serviceRequest->status, ['under_review', 'ongoing']);
        })->count();

        $currentMonthIncome = $legalTranslationRequests->filter(function ($item) {
            return $item->serviceRequest &&
                $item->serviceRequest->paid_at &&
                Carbon::parse($item->serviceRequest->paid_at)->isCurrentMonth()
                && in_array($item->serviceRequest->status, ['completed']);
        })->sum('translator_amount');

        $totalIncome = $legalTranslationRequests->filter(function ($item) {
            return $item->serviceRequest &&
                $item->serviceRequest->paid_at
                && in_array($item->serviceRequest->status, ['completed']);
        })->sum('translator_amount');

        $currentYear = Carbon::now()->year;
        $year = request()->get('consultation_year', $currentYear);

        $monthlyTranslations = RequestLegalTranslation::where('assigned_translator_id', Auth::guard('frontend')->user()->translator?->id)
            ->whereHas('serviceRequest', function ($query) use ($year) {
                $query->whereYear('created_at', $year);
            })
            ->with(['serviceRequest'])
            ->get()
            ->groupBy(function ($item) {
                return $item->serviceRequest ? Carbon::parse($item->serviceRequest->created_at)->format('m') : null;
            })
            ->map(function ($group) {
                return $group->count();
            })
            ->toArray();


        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $month = str_pad($i, 2, '0', STR_PAD_LEFT);
            $monthlyData[$month] = $monthlyTranslations[$month] ?? 0;
        }


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
            'notifications',
            'monthlyData',
            'year'
        ));
    }

    public function serviceRequestsIndex(Request $request)
    {
        $translator = Auth::guard('frontend')->user()->translator;

        if ($translator) {
            $translator->load('languageRates.fromLanguage', 'languageRates.toLanguage');
        }

        $query = RequestLegalTranslation::where('assigned_translator_id', Auth::guard('frontend')->user()->translator?->id)
            ->with(['serviceRequest', 'documentLanguage', 'translationLanguage']);

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($q) use ($search) {
                $q->where('no_of_pages', 'LIKE', "%{$search}%");

                $q->orWhereHas('serviceRequest', function ($sq) use ($search) {
                    $sq->where('reference_code', 'LIKE', "%{$search}%")
                        ->orWhere('status', 'LIKE', "%{$search}%");
                });

                $q->orWhereHas('documentLanguage', function ($lq) use ($search) {
                    $lq->where('name', 'LIKE', "%{$search}%");
                });

                $q->orWhereHas('translationLanguage', function ($lq) use ($search) {
                    $lq->where('name', 'LIKE', "%{$search}%");
                });
            });
        }

        if ($request->filled('date_range') && !empty($request->date_range)) {
            $dateRange = $request->date_range;
            $dateRange = explode(' - ', $dateRange);

            if (count($dateRange) == 2) {
                $date1 = trim($dateRange[0]);
                $date2 = trim($dateRange[1]);

                $dateFromParsed = \Carbon\Carbon::createFromFormat('Y-m-d', $date1);
                $dateToParsed = \Carbon\Carbon::createFromFormat('Y-m-d', $date2);

                if ($dateFromParsed && $dateToParsed) {
                    $dateFrom = $dateFromParsed->startOfDay();
                    $dateTo = $dateToParsed->endOfDay();

                    $query->whereHas('serviceRequest', function ($q) use ($dateFrom, $dateTo) {
                        $q->whereBetween('created_at', [$dateFrom, $dateTo]);
                    });
                }
            }
        }

        if ($request->has('language_pair') && !empty($request->language_pair) && $request->language_pair !== 'all') {
            $languagePair = $request->language_pair;
            $parts = explode(' - ', $languagePair);
            if (count($parts) == 2) {
                $fromLanguage = $parts[0];
                $toLanguage = $parts[1];

                $query->whereHas('documentLanguage', function ($subQ) use ($fromLanguage) {
                    $subQ->where('name', 'LIKE', "%{$fromLanguage}%");
                })->whereHas('translationLanguage', function ($subQ) use ($toLanguage) {
                    $subQ->where('name', 'LIKE', "%{$toLanguage}%");
                });
            }
        }

        if ($request->has('status') && !empty($request->status) && $request->status !== 'all') {
            $status = $request->status;
            $query->whereHas('serviceRequest', function ($q) use ($status) {
                $q->where('status', $status);
            });
        }

        $legalTranslationRequests = $query->get();

        $serviceRequests = $legalTranslationRequests
            ->sortByDesc(function ($item) {
                return $item->serviceRequest ? $item->serviceRequest->created_at : $item->created_at;
            })
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

        $languagePairs = $this->getTranslatorLanguagePairs();

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $currentItems = $serviceRequests->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $serviceRequests = new LengthAwarePaginator($currentItems, $serviceRequests->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);

        return view('frontend.translator.service-requests.index', compact(
            'serviceRequests',
            'languagePairs'
        ));
    }

    public function updateServiceRequestStatus(Request $request, $id)
    {
        $request->merge([
            'supporting_docs' => filter_var($request->input('supporting_docs'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'supporting_docs_any' => filter_var($request->input('supporting_docs_any'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
        ]);

        try {
            $validatedData = $request->validate([
                'status' => 'required|in:pending,under_review,ongoing,completed,rejected',
                'reason' => 'nullable|string',
                'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
                'supporting_docs' => 'boolean',
                'supporting_docs_any' => 'boolean',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $user = Auth::guard('frontend')->user();
        $translatorId = $user->translator?->id;

        if (!$translatorId) {
            return response()->json(['success' => false, 'message' => 'Translator not found'], 403);
        }

        $serviceRequest = ServiceRequest::findOrFail($id);

        if ($serviceRequest->status === 'completed') {
            return response()->json(['success' => false, 'message' => __('frontend.status_completed_no_change')], 400);
        }

        $serviceRequest->status = $request->status;
        $serviceRequest->save();

        $uploadedFileUrl = null;
        if ($request->hasFile('file') && $request->status === 'completed') {
            $file = $request->file('file');
            $uniqueName = 'completed_file_' . uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filename = "uploads/{$serviceRequest->service_slug}/{$serviceRequest->id}/" . $uniqueName;
            $fileContents = file_get_contents($file);
            Storage::disk('public')->put($filename, $fileContents);
            $uploadedFileUrl = Storage::url($filename);

            if ($serviceRequest) {
                $completedFiles = $serviceRequest->completed_files ?? [];
                if (!is_array($completedFiles)) {
                    $completedFiles = [];
                }
                $completedFiles[] = $uploadedFileUrl;
                $serviceRequest->update(['completed_files' => $completedFiles]);
            }
        }

        $meta = [
            'updated_by' => 'translator',
            'translator_id' => $translatorId
        ];

        if ($request->status === 'rejected') {
            $meta['rejection_details'] = [
                'supporting_docs' => $request->boolean('supporting_docs', false),
                'supporting_docs_any' => $request->boolean('supporting_docs_any', false),
                'reason' => $request->reason ?? ''
            ];
        }

        if ($request->status === 'completed' && $uploadedFileUrl) {
            $meta['completion_details'] = [
                'file_path' => $uploadedFileUrl
            ];
        }

        \App\Models\ServiceRequestTimeline::create([
            'service_request_id' => $serviceRequest->id,
            'service_slug' => $serviceRequest->service_slug,
            'status' => $request->status,
            'note' => $request->reason ?? '',
            'changed_by' => $user->id,
            'meta' => $meta
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'new_status' => ucfirst($request->status)
        ]);
    }


    public function account()
    {
        $user   = Auth::guard('frontend')->user();
        return view('frontend.translator.account', compact('user'));
    }


    public function showServiceRequest($id)
    {
        $lang           = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $user = Auth::guard('frontend')->user();
        $translatorId = $user->translator?->id;

        $serviceRequest = ServiceRequest::with('service', 'statusHistories')->findOrFail($id);

        $relation = getServiceRelationName($serviceRequest->service_slug);

        if (!$relation || !$serviceRequest->relationLoaded($relation)) {
            $serviceRequest->load($relation);
        }

        $serviceDetails = $serviceRequest->$relation;

        if (!$serviceDetails) {
            return redirect()->back()->with('error', __('frontend.no_details_found'));
        }

        $timeline = getFullStatusHistory($serviceRequest);

        $translatedData = getServiceHistoryTranslatedFields($serviceRequest->service_slug, $serviceDetails, $lang);

        // dd($serviceDetails);

        $details = [
            'id'                => $serviceRequest->id,
            'service_slug'      => $serviceRequest->service_slug,
            'service_name'      => $serviceRequest->service->getTranslation('title', $lang),
            'reference_code'    => $serviceRequest->reference_code,
            'status'            => $serviceRequest->status ?? "",
            'payment_status'    => $serviceRequest->payment_status,
            'payment_reference' => $serviceRequest->payment_reference,
            'amount'            => $serviceRequest->amount,
            'submitted_at'      => $serviceRequest->submitted_at,
            'created_at'        => ($serviceRequest?->created_at?->format('Y-m-d h:i A')) ?? 'N/A',
            'document_title'    => $serviceRequest?->title ?? 'N/A',
            'sub_document_type' => $serviceRequest?->documentSubType->name ?? 'N/A',
            'payment_status'    => $serviceRequest->payment_status,
            'amount'            => $serviceRequest->amount,
            'service_details'   => $translatedData,
            'timeline'          => $timeline,
        ];

        return view('frontend.translator.service-requests.service-details', compact(
            'details',
        ));
    }

    private function getTranslatorLanguagePairs()
    {
        $translator = Auth::guard('frontend')->user()->translator;

        if (!$translator) {
            return collect([]);
        }

        $translator->load('languageRates.fromLanguage', 'languageRates.toLanguage');

        $languagePairs = $translator->languageRates
            ->map(function ($rate) {
                $from = $rate->fromLanguage ? $rate->fromLanguage->name : 'N/A';
                $to = $rate->toLanguage ? $rate->toLanguage->name : 'N/A';
                return [
                    'from' => $from,
                    'to' => $to,
                    'combined' => $from . ' - ' . $to
                ];
            })
            ->unique('combined')
            ->filter(function ($pair) {
                return $pair['from'] !== 'N/A' && $pair['to'] !== 'N/A';
            })
            ->values();

        return $languagePairs;
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

    public function downloadServiceRequest($id)
    {
        $user = Auth::guard('frontend')->user();
        $translatorId = $user->translator?->id;

        if (!$translatorId) {
            abort(403, __('frontend.unauthorized'));
        }

        $serviceRequest = ServiceRequest::with('service', 'statusHistories')->findOrFail($id);

        if ($serviceRequest->status !== 'completed') {
            abort(403, __('frontend.download_not_allowed'));
        }

        $relation = getServiceRelationName($serviceRequest->service_slug);

        if ($relation) {
            $serviceDetails = $serviceRequest->$relation;
            if ($serviceDetails && $serviceDetails->assigned_translator_id != $translatorId) {
                abort(403, __('frontend.unauthorized'));
            }
        }

        $completedFiles = $serviceRequest->completed_files ?? [];

        if (empty($completedFiles)) {
            abort(404, __('frontend.no_files_available'));
        }

        if (count($completedFiles) === 1) {
            $fileUrl = $completedFiles[0];

            $storagePath = str_replace('/storage/', 'public/', $fileUrl);
            $fullPath = storage_path("app/{$storagePath}");

            if (file_exists($fullPath)) {
                return response()->download($fullPath);
            } else {
                abort(404, __('frontend.file_not_found'));
            }
        }

        $zipFileName = "completed_service_{$serviceRequest->id}.zip";
        $zipPath = storage_path("app/temp/{$zipFileName}");

        if (!file_exists(storage_path("app/temp"))) {
            mkdir(storage_path("app/temp"), 0755, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            foreach ($completedFiles as $index => $fileUrl) {
                $storagePath = str_replace('/storage/', 'public/', $fileUrl);
                $fullPath = storage_path("app/{$storagePath}");
                $fileName = basename($fileUrl);

                if (file_exists($fullPath)) {
                    $zip->addFile($fullPath, $fileName);
                }
            }
            $zip->close();

            return response()->download($zipPath)->deleteFileAfterSend(true);
        } else {
            abort(500, 'Could not create archive');
        }
    }
}
