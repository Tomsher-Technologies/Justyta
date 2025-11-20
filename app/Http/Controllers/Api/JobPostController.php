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
use App\Mail\JobApplicationReceived;
use Illuminate\Support\Facades\Mail;

class JobPostController extends Controller
{
    public function index(Request $request)
    {
        $lang       = $request->header('lang') ?? env('APP_LOCALE','en');
        $sort       = $request->input('sort', 'newest'); 
        $perPage    = $request->input('limit', 10);

        $query = JobPost::where('status', 1);
       
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

        $ads = getActiveAd('lawfirm_jobs', 'mobile');

        $response['banner'] = [];
        if ($ads) {
            $file = $ads->files->first();
            $response['banner'] = [
                'file' => getUploadedFile($file->file_path),
                'file_type' => $file->file_type,
                'url' => $ads->cta_url
            ];
        }

        return response()->json([
            'status'        => true,
            'message'       => 'Details fetched successfully.',
            'data'          => $data,
            'current_page'  => $jobPosts->currentPage(),
            'last_page'     => $jobPosts->lastPage(),
            'limit'         => $jobPosts->perPage(),
            'total'         => $jobPosts->total(),
            'banner'        => $response['banner']
        ], 200);
    }

    public function jobDetails($id, Request $request)
    {
        $lang = $request->header('lang') ?? env('APP_LOCALE', 'en');

        $job = JobPost::where('status', 1)
                    ->where('id', $id)
                    ->with([ 'location'])
                    ->first();

        if (!$job) {
            return response()->json([
                'status'    => false,
                'message'   => __('messages.job_not_found'),
            ], 200);
        }

        $ads = getActiveAd('lawfirm_jobs', 'mobile');

        $response['banner'] = [];
        if ($ads) {
            $file = $ads->files->first();
            $response['banner'] = [
                'file' => getUploadedFile($file->file_path),
                'file_type' => $file->file_type,
                'url' => $ads->cta_url
            ];
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
                'banner' => $response['banner'] 
            ]
        ], 200);
    }

    public function applyJobFormData(Request $request,$id){
        $lang = $request->header('lang') ?? env('APP_LOCALE', 'en');

        $job = JobPost::where('status', 1)
                    ->where('id', $id)
                    ->with([ 'location']) 
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
                    ])->whereIn('slug', ['job_positions'])->get()->keyBy('slug');
       
           
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

            if(isset($response['job_positions'])){
                $response['positions'] = $response['job_positions'];
                unset($response['job_positions']);
            }

            $ads = getActiveAd('lawfirm_jobs', 'mobile');

            $response['banner'] = [];
            if ($ads) {
                $file = $ads->files->first();
                $response['banner'] = [
                    'file' => getUploadedFile($file->file_path),
                    'file_type' => $file->file_type,
                    'url' => $ads->cta_url
                ];
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
        $application->resume_path   = $resumeUrl;
        $application->save();

        $jobOwner = $job->post_owner;

        if ($jobOwner && $jobOwner->email) {
            Mail::to($jobOwner->email)->send(new JobApplicationReceived($job, $application));
        }

        return response()->json([
            'status'    => true,
            'message'   => __('messages.job_apply_success')
        ], 200);
    }
}
