<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobPost;
use App\Models\Vendor;
use App\Models\Dropdown;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class JobPostController extends Controller
{
    public function index(Request $request)
    {
        $lang       = $request->header('lang') ?? env('APP_LOCALE','en');
        $sort       = $request->input('sort', 'newest'); // 'newest' or 'oldest'
        $perPage    = $request->input('limit', 10);

        $query = JobPost::where('status', 1);
        // Sort
        if ($sort === 'oldest') {
            $query->orderBy('job_posted_date', 'asc');
        } else {
            $query->orderBy('job_posted_date', 'desc');
        }

        $jobPosts = $query->paginate($perPage);

        $data = $jobPosts->map(function ($job) use($lang) {
            return [
                'id'                => $job->id,
                'ref_no'            => $job->ref_no,
                'title'             =>  $job->getTranslation('title',$lang) ?? NULL,
                'type'              => __('messages.'.$job->type),
                'location'           => $job->location->getTranslation('name', $lang) ?? NULL,
                'job_posted_date'   => $job->job_posted_date,
                'deadline_date'     => $job->deadline_date,
                // 'status' => $job->status,
                // 'description' => optional($job->translation)->description,
                // 'salary' => optional($job->translation)->salary,
            ];
        });

        return response()->json([
            'status'        => true,
            'message'       => 'Details fetched successfully.',
            'data'          => $data,
            'current_page'  => $jobPosts->currentPage(),
            'last_page'     => $jobPosts->lastPage(),
            'limit'         => $jobPosts->perPage(),
            'total'         => $jobPosts->total(),
        ], 200);
    }

    public function jobDetails($id, Request $request)
    {
        $lang = $request->header('lang') ?? env('APP_LOCALE', 'en');

        $job = JobPost::where('status', 1)
                    ->where('id', $id)
                    ->with([ 'location']) // eager load relationships
                    ->first();

        if (!$job) {
            return response()->json([
                'status'    => false,
                'message'   => __('messages.job_not_found'),
            ], 200);
        }

        return response()->json([
            'status'    => true,
            'message'   => 'Job details found.',
            'data' => [
                'id' => $job->id,
                'ref_no' => $job->ref_no,
                'type' => __('messages.' . $job->type),
                'title' => $job->getTranslation('title',$lang) ?? NULL,
                'description' => $job->getTranslation('description', $lang) ?? NULL,
                'salary' => $job->getTranslation('salary', $lang) ?? NULL,
                'location' => $job->location?->getTranslation('name', $lang) ?? NULL,
                'job_posted_date' => $job->job_posted_date,
                'deadline_date' => $job->deadline_date,
                'status' => $job->status,
            ]
        ], 200);
    }

    public function applyJobFormData(Request $request,$id){
        $lang = $request->header('lang') ?? env('APP_LOCALE', 'en');

        $job = JobPost::where('status', 1)
                    ->where('id', $id)
                    ->with([ 'location']) // eager load relationships
                    ->first();

        if (!$job) {
            return response()->json([
                'status'    => false,
                'message'   => __('messages.job_not_found'),
            ], 200);
        }else{
            $user_id = $job->user_id;

            $userType = $job->user_type;
            $lawfirm = [];
            if($userType != 'admin'){
                $lawfirm = Vendor::where('user_id', $user_id)->first();
            }

             $dropdowns = Dropdown::with([
                        'options' => function ($q) {
                            $q->where('status', 'active')->orderBy('sort_order');
                        },
                        'options.translations' => function ($q) use ($lang) {
                            $q->whereIn('language_code', [$lang, 'en']);
                        }
                    ])->whereIn('slug', ['positions'])->get()->keyBy('slug');
       
            // Transform each dropdown
            $response = [];

            $response['details'] =  array(
                                    'job_id' => $job->id,
                                    'lawfirm_name' => $lawfirm ? $lawfirm->getTranslation('law_firm_name',$lang) : NULL,
                                    'about' => $lawfirm ? $lawfirm->getTranslation('about',$lang) : NULL,
                                    'location' => $lawfirm ? $lawfirm->location?->getTranslation('name', $lang) : NULL,
                                    'email' => $lawfirm ? $lawfirm->law_firm_email : NULL,
                                    'phone' => $lawfirm ? $lawfirm->law_firm_phone : NULL,
                                );
        
            foreach ($dropdowns as $slug => $dropdown) {
                $response[$slug] = $dropdown->options->map(function ($option) use ($lang){
                    return [
                        'id'    => $option->id,
                        'value' => $option->getTranslation('name',$lang),
                    ];
                });
            }
        
            return response()->json([
                'status'    => true,
                'message'   => 'Success',
                'data'      => $response
            ], 200);
        }
    }

    public function applyJob(Request $request)
    {
        $job = JobPost::find($request->job_id);

        $lang   = $request->header('lang') ?? env('APP_LOCALE','en');
        $user   = $request->user();

        if (!$job) {
            return response()->json([
                'status'    => false,
                'message'   => __('messages.job_not_found')
            ], 200);
        }

        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'email'     => 'required',
            'phone'     => 'required',
            'position'  => 'required',
            'resume'    => 'required|file|mimes:pdf,doc,docx|max:2048', // 2MB max
        ],[
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
            $message = implode(' ', $validator->errors()->all());

            return response()->json([
                'status'    => false,
                'message'   => $message,
            ], 200);
        }

        $alreadyApplied = JobApplication::where('job_post_id', $job->id)
                                        ->where('user_id', $user->id)
                                        ->exists();

        if ($alreadyApplied) {
            return response()->json([
                'status'    => false,
                'message'   => __('messages.already_applied')
            ], 200); 
        }

        $resumeUrl = '';
        // Store file
        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->store('resumes', 'public');
            $resumeUrl  = Storage::url($resumePath);
        }
        
        // Save application
        $application                = new JobApplication();
        $application->job_post_id   = $job->id;
        $application->user_id       = $user->id;
        $application->full_name     = $request->full_name;
        $application->email         = $request->email;
        $application->phone         = $request->phone;
        $application->position      = $request->position;
        $application->resume_path   = 'storage/'.$resumeUrl;
        $application->save();

        return response()->json([
            'status'    => true,
            'message'   => __('messages.job_apply_success')
        ], 200);
    }
}
