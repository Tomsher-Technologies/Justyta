<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\JobPost;
use App\Models\JobApplication;
use App\Models\JobPostTranslation;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class JobPostController extends Controller
{

    function __construct()
    {
        $this->middleware('auth');
       
        $this->middleware('permission:manage_job_post',  ['only' => ['index','destroy','applications']]);
        $this->middleware('permission:add_job_post',  ['only' => ['create','store']]);
        $this->middleware('permission:edit_job_post',  ['only' => ['edit','update']]);
    }

    public function index(Request $request)
    {
        $query = JobPost::with(['translations','location','post_owner'])->orderBy('id','desc');

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('ref_no', 'like', "%{$keyword}%")
                ->orWhereHas('translations', function ($qu) use ($keyword) {
                    $qu->where('title', 'like', "%{$keyword}%");
                });
            });
        }

        if ($request->filled('status')) {
            // 1 = active, 2 = inactive; 
            if ($request->status == 1) {
                $query->where('status', 1);
            } elseif ($request->status == 2) {
                $query->where('status', 0);
            }
        }

        if($request->filled('user_id')){
            $query->where('user_id', $request->user_id);
        }

        if($request->filled('posted_date')){
            $dates = explode(' - ', $request->posted_date);

            if (count($dates) === 2) {
                $startDate = $dates[0];
                $endDate = $dates[1];

                $query->whereBetween('job_posted_date', [$startDate, $endDate]);
            }
        }

        if($request->filled('deadline_date')){
            $datesDead = explode(' - ', $request->deadline_date);

            if (count($datesDead) === 2) {
                $startDateDead = $datesDead[0];
                $endDateDead = $datesDead[1];

                $query->whereBetween('deadline_date', [$startDateDead, $endDateDead]);
            }
        }

        $job_posts = $query->paginate(15);
        $users = User::whereIn('user_type', ['admin','staff','vendor'])->orderBy('name','asc')->get();
        return view('admin.job_posts.index', compact('job_posts','users'));
    }

    public function create()
    {
        $languages = Language::where('status', 1)->orderBy('id')->get();
        return view('admin.job_posts.create',compact('languages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'emirate' => 'required',
            'deadline_date' => 'required|date',
            'translations.en.title' => 'required',
            'translations.en.description' => 'required',
        ],[
            '*.required' => 'This field is required',
            'translations.en.*.required' => 'This field is required',
        ]);

        $job = JobPost::create([
            'type' => $request->type,
            'job_posted_date' => date('Y-m-d'),
            'deadline_date' => $request->deadline_date ? Carbon::parse($request->deadline_date)->format('Y-m-d') : null,
            'user_id' => auth()->id(),
            'user_type' => 'admin',
            'emirate' => $request->emirate
        ]);

        foreach ($request->translations as $lang => $data) {
            $data['lang'] = $lang;
            $data['job_post_id'] = $job->id;
            JobPostTranslation::create($data);
        }
        session()->flash('success', 'Job post created successfully.');
        return redirect()->route('job-posts.index');
    }

    public function edit(JobPost $jobPost)
    {
        $jobPost->load('translations');
        $languages = Language::where('status', 1)->orderBy('id')->get();
        return view('admin.job_posts.edit', compact('jobPost','languages'));
    }

    public function update(Request $request, JobPost $jobPost)
    {

        $request->validate([
            'type' => 'required',
            'emirate' => 'required',
            'deadline_date' => 'required|date',
            'translations.en.title' => 'required',
            'translations.en.description' => 'required',
        ],[
            '*.required' => 'This field is required',
            'translations.en.*.required' => 'This field is required',
        ]);

        $jobPost->update([
            'type' => $request->type,
            'emirate' => $request->emirate,
            'deadline_date' => $request->deadline_date ? Carbon::parse($request->deadline_date)->format('Y-m-d') : null,
        ]);
        foreach ($request->translations as $lang => $data) {
            JobPostTranslation::updateOrCreate(
                ['job_post_id' => $jobPost->id, 'lang' => $lang],
                $data
            );
        }
        session()->flash('Job post details updated successfully.');
        return redirect()->route('job-posts.index');
    }

    public function updateStatus(Request $request)
    {
        $jobpost = JobPost::findOrFail($request->id);
        $jobpost->status = $request->status;
        $jobpost->save();
       
        return 1;
    }

    public function applications($id)
    {
        $jobId = base64_decode($id);
        $job = JobPost::find($jobId);

        $applications = JobApplication::where('job_post_id', $jobId)->paginate(30);
        return view('admin.job_posts.applications', compact('job','applications'));
    }
}
