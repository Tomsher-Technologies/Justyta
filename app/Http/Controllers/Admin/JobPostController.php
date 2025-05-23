<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobPost;
use App\Models\JobPostTranslation;
use Illuminate\Http\Request;

class JobPostController extends Controller
{

    function __construct()
    {
        $this->middleware('auth');
       
        $this->middleware('permission:manage_plan',  ['only' => ['index','destroy']]);
        $this->middleware('permission:add_plan',  ['only' => ['create','store']]);
        $this->middleware('permission:edit_plan',  ['only' => ['edit','update']]);
    }

    public function index()
    {
        $job_posts = JobPost::with('translations')->orderBy('id','desc')->paginate(15);
        return view('admin.job_posts.index', compact('job_posts'));
    }

    public function create()
    {
        return view('admin.job_posts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'salary_type' => 'required',
            'job_posted_date' => 'required|date',
            'deadline_date' => 'required|date',
            'translations.*.title' => 'required',
            'translations.*.description' => 'required',
            'translations.*.salary' => 'required',
            'translations.*.job_location' => 'required',
        ]);

        $job = JobPost::create([
            'type' => $request->type,
            'salary_type' => $request->salary_type,
            'job_posted_date' => $request->job_posted_date,
            'deadline_date' => $request->deadline_date,
            'user_id' => auth()->id(),
        ]);

        foreach ($request->translations as $locale => $data) {
            $data['locale'] = $locale;
            $data['job_post_id'] = $job->id;
            JobPostTranslation::create($data);
        }

        return redirect()->route('job-posts.index')->with('success', 'Job created!');
    }

    public function edit(JobPost $jobPost)
    {
        $jobPost->load('translations');
        return view('admin.job_posts.edit', compact('jobPost'));
    }

    public function update(Request $request, JobPost $jobPost)
    {
        $jobPost->update([
            'type' => $request->type,
            'salary_type' => $request->salary_type,
            'job_posted_date' => $request->job_posted_date,
            'deadline_date' => $request->deadline_date,
        ]);

        foreach ($request->translations as $locale => $data) {
            JobPostTranslation::updateOrCreate(
                ['job_post_id' => $jobPost->id, 'locale' => $locale],
                $data
            );
        }

        return redirect()->route('job-posts.index')->with('success', 'Job updated!');
    }
}
