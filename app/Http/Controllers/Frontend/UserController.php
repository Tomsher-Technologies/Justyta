<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rating;
use App\Models\User;
use App\Models\Dropdown;
use App\Models\Emirate;
use App\Models\JobPost;
use App\Models\JobApplication;
use App\Models\Service;
use App\Models\Vendor;
use App\Models\AnnualAgreementInstallment;
use App\Models\TrainingRequest;
use App\Models\ProblemReport;
use App\Models\ServiceRequest;
use App\Mail\JobApplicationReceived;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Notifications\ProblemReported;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TrainingRequestSubmitted;
use App\Services\ServiceRequestFileService;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class UserController extends Controller
{

    protected $fileService;

    public function __construct(ServiceRequestFileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function reportProblem()
    {
        $lang = app()->getLocale() ?? env('APP_LOCALE', 'en');

        $pageData = getPageDynamicContent('report_problem', $lang);

        return view('frontend.user.report_problem', compact('lang', 'pageData'));
    }

    public function submitReportProblem(Request $request)
    {
        $lang = app()->getLocale() ?? env('APP_LOCALE', 'en');

        $validator = Validator::make($request->all(), [
            'email'     => 'required|email',
            'subject'   => 'required|string|max:100',
            'message'   => 'required|string|max:2000',
            'image'     => 'nullable|image|max:500',
        ], [
            'email.required'    => __('messages.email_required'),
            'email.email'       => __('messages.valid_email'),
            'subject.required'  => __('messages.enter_subject'),
            'subject.string'    => __('messages.subject_string'),
            'subject.max'       => __('messages.subject_max'),
            'message.max'       => __('messages.message_max'),
            'message.required'  => __('messages.enter_message'),
            'message.string'    => __('messages.message_string'),
            'image.image'       => __('messages.image_image'),
            'image.max'         => __('messages.image_max'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user   = Auth::guard('frontend')->user();

        $data = [
            'user_id'   => $user->id,
            'email'     => $request->email ?? $user->email,
            'subject'   => $request->subject ?? NULL,
            'message'   => $request->message ?? NULL,
        ];
        if ($request->hasfile('image')) {
            $data['image'] = uploadImage('report_problem', $request->image, 'report_');
        }

        $report = ProblemReport::create($data);

        $usersToNotify = getUsersWithPermissions(['reported_problems']);
        Notification::send($usersToNotify, new ProblemReported($report));

        $pageData = getPageDynamicContent('report_problem_success', $lang);

        return redirect()->back()->with('success', $pageData['content'] ?? __('messages.problem_report_success'));
    }

    public function rateUs()
    {
        $lang = app()->getLocale() ?? env('APP_LOCALE', 'en');

        $pageData = getPageDynamicContent('rate_us_form_info', $lang);

        return view('frontend.user.rate_us', compact('lang', 'pageData'));
    }

    public function rateUsSave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rating'    => 'required|integer|min:1|max:5',
            'comment'   => 'nullable|string|max:1000'
        ], [
            'rating.required'   => __('messages.rating_required'),
            'rating.integer'    => __('messages.rating_number'),
            'rating.min'        => __('messages.minimum_rating'),
            'rating.max'        => __('messages.maximum_rating'),
            'comment.string'    => __('messages.comment_string'),
            'comment.max'       => __('messages.comment_max'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $lang = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $user   = Auth::guard('frontend')->user();

        $existingRating = Rating::where('user_id', $user->id)->first();

        if ($existingRating) {
            return redirect()->back()->with('error', __('messages.rating_already_done'));
        }

        $rating = Rating::create([
            'user_id' => $user->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        $pageData = getPageDynamicContent('rate_us_success', $lang);

        return redirect()->back()->with('success', $pageData['content'] ?? __('messages.thank_you_feedback'));
    }

    public function getTrainingFormData()
    {
        $lang = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $dropdowns  = Dropdown::with([
            'options' => function ($q) {
                $q->where('status', 'active')->orderBy('sort_order');
            },
            'options.translations' => function ($q) use ($lang) {
                $q->whereIn('language_code', [$lang, 'en']);
            }
        ])->whereIn('slug', ['training_positions', 'residency_status'])->get()->keyBy('slug');

        $response   = [];
        $emirates   = Emirate::where('status', 1)->orderBy('id')->get();

        $response['emirates'] = $emirates->map(function ($emirate) use ($lang) {
            return [
                'id'    => $emirate->id,
                'value' => $emirate->getTranslation('name', $lang),
            ];
        });

        foreach ($dropdowns as $slug => $dropdown) {
            $response[$slug] = $dropdown->options->map(function ($option) use ($lang) {
                return [
                    'id'    => $option->id,
                    'value' => $option->getTranslation('name', $lang),
                ];
            });
        }

        return view('frontend.user.training_request', compact('lang', 'response'));
    }

    public function requestTraining(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'emirate_id'        => 'required',
            'position'          => 'required',
            'start_date'        => 'required',
            'residency_status'  => 'required',
            'documents'         => 'nullable|array',
            'documents.*'       => 'file|mimes:pdf,jpg,jpeg,webp,png,svg,doc,docx|max:1024',
        ], [
            'emirate_id.required'       => __('messages.emirate_required'),
            'position.required'         => __('messages.position_required'),
            'start_date.required'       => __('messages.start_date_required'),
            'residency_status.required' => __('messages.residency_status_required'),
            'documents.*.file'          => __('messages.document_file_invalid'),
            'documents.*.mimes'         => __('messages.document_file_mimes'),
            'documents.*.max'           => __('messages.document_file_max'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user   = Auth::guard('frontend')->user();
        $lang = app()->getLocale() ?? env('APP_LOCALE', 'en');

        $trainingRequest = TrainingRequest::create([
            'user_id'           => $user->id,
            'emirate_id'        => $request->input('emirate_id') ?? NULL,
            'position'          => $request->input('position') ?? NULL,
            'start_date'        => $request->input('start_date') ?? NULL,
            'residency_status'  => $request->input('residency_status') ?? NULL,
            'documents'         => [],
        ]);

        $requestFolder = "training_request/{$trainingRequest->id}/";

        $fileFields = [
            'documents'     => 'documents',
        ];

        $filePaths = [];

        foreach ($fileFields as $inputName => $columnName) {
            $filePaths[$columnName] = [];
            if ($request->hasFile($inputName)) {
                $files = $request->file($inputName);
                if (!is_array($files)) {
                    $files = [$files];
                }
                foreach ($files as $file) {
                    $uniqueName     = $inputName . '_' . uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filename       = $requestFolder . $uniqueName;
                    $fileContents   = file_get_contents($file);
                    Storage::disk('public')->put($filename, $fileContents);
                    $filePaths[$columnName][] = Storage::url($filename);
                }
            }
        }

        $trainingRequest->update($filePaths);

        $request->user()->notify(new TrainingRequestSubmitted($trainingRequest));

        $usersToNotify = getUsersWithPermissions(['view_training_requests', 'export_training_requests']);
        Notification::send($usersToNotify, new TrainingRequestSubmitted($trainingRequest, true));

        return redirect()->back()->with('success', __('messages.training_request_submit_success'));
    }

    public function jobPosts(Request $request)
    {
        $request->session()->put('jobs_last_url', url()->full());

        $user       = Auth::guard('frontend')->user();
        $lang       = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $keyword    = $request->has('keyword') ? $request->input('keyword') : NULL;

        $query = JobPost::where('status', 1);

        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('ref_no', 'LIKE', "%$keyword%")
                    ->orWhereHas('translations', function ($tq) use ($keyword) {
                        $tq->where(function ($ttq) use ($keyword) {
                            $ttq->where('title', 'LIKE', "%$keyword%")
                                ->orWhere('description', 'LIKE', "%$keyword%");
                        });
                    });
            });
        }

        $jobPosts = $query->paginate(12);

        return view('frontend.user.jobs', compact('lang', 'jobPosts'));
    }

    public function jobPostDetails($id, Request $request)
    {
        $request->session()->put('job_details_last_url', url()->full());
        $id     = base64_decode($id);
        $user   = Auth::guard('frontend')->user();
        $lang   = app()->getLocale() ?? env('APP_LOCALE', 'en');

        $job = JobPost::where('status', 1)
            ->where('id', $id)
            ->with(['location'])
            ->first();

        if (!$job) {
            return redirect()->back()->with('error', __('messages.no_jobs_found'));
        }
        //'user-lawfirm-jobs'

        $hasApplied = JobApplication::where('job_post_id', $job->id)
            ->where('user_id', $user->id)
            ->exists();

        $jobPost = [
            'id' => $job->id,
            'ref_no' => $job->ref_no,
            'type' => __('messages.' . $job->type),
            'title' => $job->getTranslation('title', $lang) ?? NULL,
            'description' => $job->getTranslation('description', $lang) ?? NULL,
            'salary' => $job->getTranslation('salary', $lang) ?? NULL,
            'location' => $job->location?->getTranslation('name', $lang) ?? NULL,
            'job_posted_date' => $job->job_posted_date,
            'deadline_date' => $job->deadline_date,
            'status' => $job->status,
        ];

        return view('frontend.user.job-details', compact('lang', 'jobPost', 'hasApplied'));
    }

    public function jobPostApply(Request $request, $id)
    {
        $id     = base64_decode($id);
        $user   = Auth::guard('frontend')->user();
        $lang   = app()->getLocale() ?? env('APP_LOCALE', 'en');

        $job = JobPost::where('status', 1)
            ->where('id', $id)
            ->with(['location'])
            ->first();

        if (!$job) {
            return redirect()->back()->with('error', __('messages.no_jobs_found'));
        }

        $user_id = $job->user_id;

        $userType = $job->user_type;

        $lawfirm = [];

        if ($userType != 'admin') {
            $lawfirm = Vendor::where('user_id', $user_id)->first();
        }

        $dropdowns = Dropdown::with([
            'options' => function ($q) {
                $q->where('status', 'active')->orderBy('sort_order');
            },
            'options.translations' => function ($q) use ($lang) {
                $q->whereIn('language_code', [$lang, 'en']);
            }
        ])->whereIn('slug', ['job_positions'])->get()->keyBy('slug');

        $response = [];

        $response['details'] =  array(
            'job_id' => $job->id,
            'title' => $job->getTranslation('title', $lang) ?? NULL,
            'lawfirm_name' => $lawfirm ? $lawfirm->getTranslation('law_firm_name', $lang) : NULL,
            'about' => $lawfirm ? $lawfirm->getTranslation('about', $lang) : NULL,
            'location' => $lawfirm ? $lawfirm->location?->getTranslation('name', $lang) : NULL,
            'email' => $lawfirm ? $lawfirm->law_firm_email : NULL,
            'phone' => $lawfirm ? $lawfirm->law_firm_phone : NULL,
        );

        foreach ($dropdowns as $slug => $dropdown) {
            $response[$slug] = $dropdown->options->map(function ($option) use ($lang) {
                return [
                    'id'    => $option->id,
                    'value' => $option->getTranslation('name', $lang),
                ];
            });
        }

        return view('frontend.user.job_apply', compact('lang', 'response', 'user'));
    }

    public function applyJob(Request $request)
    {
        $id     = base64_decode($request->job_id);
        $job = JobPost::find($id);

        $user   = Auth::guard('frontend')->user();
        $lang   = app()->getLocale() ?? env('APP_LOCALE', 'en');

        if (!$job) {
            return redirect()->back()->with('error', __('messages.no_jobs_found'));
        }

        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'email'     => 'required',
            'phone'     => 'required',
            'position'  => 'required',
            'resume'    => 'required|file|mimes:pdf,doc,docx|max:2048', // 2MB max
        ], [
            'full_name.required'    => __('messages.full_name_required'),
            'email.required'        => __('messages.email_required'),
            'phone.required'        => __('messages.phone_required'),
            'position.required'     => __('messages.position_required'),
            'resume.required'       => __('messages.resume_required'),
            'resume.*.file'         => __('messages.resume_invalid'),
            'resume.*.mimes'        => __('messages.resume_mimes'),
            'resume.*.max'          => __('messages.resume_max'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $redirectionUrl = Session::has('job_details_last_url') ? Session::get('job_details_last_url') : route('user-lawfirm-jobs');

        $alreadyApplied = JobApplication::where('job_post_id', $job->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyApplied) {
            return redirect($redirectionUrl)->with('error', __('messages.already_applied'));
        }

        $resumeUrl = '';

        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->store('resumes', 'public');
            $resumeUrl  = Storage::url($resumePath);
        }

        $application                = new JobApplication();
        $application->job_post_id   = $job->id;
        $application->user_id       = $user->id;
        $application->full_name     = $request->full_name;
        $application->email         = $request->email;
        $application->phone         = $request->phone;
        $application->position      = $request->position;
        $application->resume_path   = 'storage/' . $resumeUrl;
        $application->save();

        $jobOwner = $job->post_owner;

        if ($jobOwner && $jobOwner->email) {
            Mail::to($jobOwner->email)->send(new JobApplicationReceived($job, $application));
        }

        return redirect($redirectionUrl)->with('success', __('messages.job_apply_success'));
    }

    public function serviceHistory(Request $request)
    {
        $page       = 'history';
        $pageTitle = __('frontend.service_history');
        $lang       = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $tab        = $serviceSlug = $request->query('tab', 'online-live-consultancy');
        $perPage    = 9;

        $request->session()->put('service_last_url', url()->full());

        $query = ServiceRequest::with('user', 'service')->where('request_success', 1)->where('user_id', auth()->id());

        if ($serviceSlug) {
            if ($serviceSlug === 'law-firm-services') {
                $slugs = Service::whereHas('parent', function ($query) {
                    $query->where('slug', 'law-firm-services');
                })->pluck('slug');

                $query->whereIn('service_slug', $slugs);
            } else {
                $query->where('service_slug', $serviceSlug);
            }
        }

        $serviceRequests = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends(['tab' => $serviceSlug]);

        $services = Service::with(['translations' => function ($newquery) use ($lang) {
            $newquery->where('lang', $lang);
        }])
            ->whereNull('parent_id')
            // ->where('status', 1)
            ->orderBy('sort_order', 'ASC')
            ->get();

        $mainServices = $services->map(function ($service) {
            $translation = $service->translations->first();
            return [
                'id' => $service->id,
                'slug' => $service->slug,
                'title' => $translation->title ?? '',
                'icon' => asset(getUploadedImage($service->icon)),
            ];
        });

        return view('frontend.user.service-history', compact('serviceRequests', 'mainServices', 'tab', 'lang', 'page', 'pageTitle'));
    }

    public function servicePending(Request $request)
    {
        $page = 'pending';
        $pageTitle = __('frontend.pending_service');
        $lang   = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $tab = $serviceSlug = $request->query('tab', 'request-submission');
        $perPage = 9;
        $request->session()->put('service_last_url', url()->full());

        $query = ServiceRequest::with('user', 'service')
            ->where('status', 'pending')
            ->where('request_success', 1)
            ->where('user_id', auth()->id());

        if ($serviceSlug) {
            if ($serviceSlug === 'law-firm-services') {
                $slugs = Service::whereHas('parent', function ($query) {
                    $query->where('slug', 'law-firm-services');
                })->pluck('slug');

                $query->whereIn('service_slug', $slugs);
            } else {
                $query->where('service_slug', $serviceSlug);
            }
        }

        $serviceRequests = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends(['tab' => $serviceSlug]);

        $services = Service::with(['translations' => function ($newquery) use ($lang) {
            $newquery->where('lang', $lang);
        }])
            ->whereNull('parent_id')
            // ->where('status', 1)
            ->orderBy('sort_order', 'ASC')
            ->get();

        $mainServices = $services->map(function ($service) {
            $translation = $service->translations->first();
            return [
                'id' => $service->id,
                'slug' => $service->slug,
                'title' => $translation->title ?? '',
                'icon' => asset(getUploadedImage($service->icon)),
            ];
        });

        return view('frontend.user.service-history', compact('serviceRequests', 'mainServices', 'tab', 'lang', 'page', 'pageTitle'));
    }

    public function servicePayment(Request $request)
    {
        $page = 'payment';
        $pageTitle = __('frontend.payment_history');
        $lang   = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $tab = $serviceSlug = $request->query('tab', 'online-live-consultancy');
        $perPage = 9;
        $request->session()->put('service_last_url', url()->full());

        $query = ServiceRequest::with('user', 'service')
            ->whereNotNull('payment_status')
            ->where('request_success', 1)
            ->where('user_id', auth()->id());

        if ($serviceSlug) {
            if ($serviceSlug === 'law-firm-services') {
                $slugs = Service::whereHas('parent', function ($query) {
                    $query->where('slug', 'law-firm-services');
                })->pluck('slug');

                $query->whereIn('service_slug', $slugs);
            } else {
                $query->where('service_slug', $serviceSlug);
            }
        }

        $serviceRequests = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends(['tab' => $serviceSlug]);

        $services = Service::with(['translations' => function ($newquery) use ($lang) {
            $newquery->where('lang', $lang);
        }])
            ->whereNull('parent_id')
            // ->where('status', 1)
            ->orderBy('sort_order', 'ASC')
            ->get();

        $mainServices = $services->map(function ($service) {
            $translation = $service->translations->first();
            return [
                'id' => $service->id,
                'slug' => $service->slug,
                'title' => $translation->title ?? '',
                'icon' => asset(getUploadedImage($service->icon)),
            ];
        });

        return view('frontend.user.service-history', compact('serviceRequests', 'mainServices', 'tab', 'lang', 'page', 'pageTitle'));
    }

    public function getServiceHistoryDetails(Request $request, $id)
    {
        $lang           = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $id             = base64_decode($id);
        $serviceRequest = ServiceRequest::with('service', 'statusHistories')->findOrFail($id);

        $relation = getServiceRelationName($serviceRequest->service_slug);

        if (!$relation || !$serviceRequest->relationLoaded($relation)) {
            $serviceRequest->load($relation);
        }

        $serviceDetails = $serviceRequest->$relation;

        if (!$serviceDetails) {
            return redirect()->back()->with('error', __('frontend.no_details_found'));
        }

        $translatedData = getServiceHistoryTranslatedFields($serviceRequest->service_slug, $serviceDetails, $lang);

        $timeline = getFullStatusHistory($serviceRequest);

        $installments = [];

        $dataService = [
            'id'                => $serviceRequest->id,
            'service_slug'      => $serviceRequest->service_slug,
            'service_name'      => $serviceRequest->service->getTranslation('title', $lang),
            'reference_code'    => $serviceRequest->reference_code,
            'status'            => $serviceRequest->status,
            'payment_status'    => $serviceRequest->payment_status,
            'payment_reference' => $serviceRequest->payment_reference,
            'amount'            => $serviceRequest->amount,
            'submitted_at'      => $serviceRequest->submitted_at,
            'completed_files'   => $serviceRequest->completed_files,
            'service_details'   => $translatedData,
            'timeline'          => $timeline
        ];

        if ($serviceRequest->status === 'rejected') {
            $rejectionDetails = $serviceRequest->getLatestRejectionDetails();
            if ($rejectionDetails) {
                $dataService['rejection_meta'] = $rejectionDetails->meta;
            }
        }

        if ($serviceRequest->service_slug === 'annual-retainer-agreement') {
            $installmentAnnual = AnnualAgreementInstallment::where('service_request_id', $serviceRequest->id)->get();

            $installments = $installmentAnnual->map(function ($inst) {
                return [
                    'id' => $inst->id,
                    'installment_no' => $inst->installment_no,
                    'amount' => $inst->amount ?? 0,
                    'status' => $inst->status,
                ];
            });
            $dataService['installments'] = $installments;
        }

        return view('frontend.user.service_history_details', compact('dataService', 'lang'));
    }

    public function account()
    {
        $user   = Auth::guard('frontend')->user();
        return view('frontend.user.account', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::guard('frontend')->user();

        $validator = Validator::make($request->all(), [
            'full_name'     => 'required|string|max:255',
            'phone'    => 'nullable|string|max:20',
            'language' => 'nullable|string|in:en,ar,fr,fa,ru,zh',
        ], [
            'full_name.required' => __('messages.full_name_required'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user->name = $request->full_name ?? $user->name;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->language = $request->language;
        $user->save();

        session(['locale' => $user->language]);

        return redirect()->back()->with('success', __('frontend.profile_updated'));
    }

    public function deleteAccount(Request $request)
    {
        $user = Auth::guard('frontend')->user();

        if (method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }

        $user->delete();

        return response()->json([
            'status' => true,
            'message' => __('messages.account_deleted_successfully')
        ]);
    }

    public function changePassword()
    {
        return view('frontend.user.change-password');
    }

    public function updateNewPassword(Request $request)
    {
        $user = Auth::guard('frontend')->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => [
                'required',
                'string',
                'min:6',
                'confirmed'
            ],
        ], [
            'current_password.required'    => __('messages.current_password_required'),
            'new_password.required'        => __('messages.new_password_required'),
            'new_password.min'             => __('messages.new_password_min'),
            'new_password.confirmed'       => __('messages.new_password_confirmed'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => __('messages.current_password_incorrect')])->withInput();
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', __('messages.password_changed_successfully'));
    }

    public function notifications(Request $request)
    {
        $lang       = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $services   = \App\Models\Service::with('translations')->get();

        $serviceMap = [];

        foreach ($services as $service) {
            foreach ($service->translations as $translation) {
                $serviceMap[$service->slug][$translation->lang] = $translation->title;
            }
        }

        $allShownIds = [];
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
                        'status' => $data['status'] ?? "",
                    ]),
                    'time'      => $notification->created_at->format('d M, Y h:i A'),
                ];
            });

        $allShownIds = collect($paginatedNot->items())
            ->pluck('id');
        Auth::guard('frontend')->user()->unreadNotifications()
            ->whereIn('id', $allShownIds)
            ->update(['read_at' => now()]);

        return view('frontend.user.notifications', compact('notifications', 'paginatedNot'));
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

    public function searchService(Request $request)
    {
        $query = $request->get('q');

        $lang       = app()->getLocale() ?? env('APP_LOCALE', 'en');
        $keyword    = $request->get('q');
        $services = Service::where('status', 1)
            ->whereNotIn('slug', ['law-firm-services'])
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

        return response()->json($servs);
    }


    public function downloadServiceCompletedFiles($id)
    {
        $user = Auth::user();

        $serviceRequest = ServiceRequest::findOrFail($id);

        if ($serviceRequest->user_id !== $user->id) {
            abort(403, __('frontend.unauthorized'));
        }

        return $this->fileService->download($id);
    }
}
