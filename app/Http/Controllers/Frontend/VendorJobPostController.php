<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\JobPost;
use App\Models\JobPostTranslation;
use App\Models\User;
use App\Models\Dropdown;
use App\Models\DropdownOption;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;

class VendorJobPostController extends Controller
{
     public function index(Request $request)
    {
        $request->session()->put('jobs_last_url', url()->full());
        $query = JobPost::with(['translations','location','post_owner'])->where('user_id', Auth::guard('frontend')->user()->id)->orderBy('id','desc');

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

        $job_posts = $query->paginate(10);
        $users = User::whereIn('user_type', ['admin','staff','vendor'])->orderBy('name','asc')->get();
        return view('frontend.vendor.jobs.index', compact('job_posts','users'));
    }

    public function create()
    {
        $languages = Language::where('status', 1)->orderBy('id')->get();

        $dropdowns = Dropdown::with(['options.translations'])->whereIn('slug', ['specialities', 'years_experience'])->get()->keyBy('slug');

        if (isVendorCanCreateJobs()) {
            return view('frontend.vendor.jobs.create',compact('languages','dropdowns'));
        }else{
            session()->flash('error', __('frontend.job_post_limit_reached'));
            return redirect()->route('jobs.index');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'emirate' => 'required',
            'deadline_date' => 'required|date',
            'experience' => 'required',
            'specialities' => 'required',
            'no_of_vacancies' => 'required',
            'translations.en.title' => 'required',
            'translations.en.description' => 'required'
        ],[
            '*.required' => __('frontend.this_field_required'),
            'translations.en.*.required' => __('frontend.this_field_required'),
        ]);

        $job = JobPost::create([
            'type' => $request->type,
            'job_posted_date' => date('Y-m-d'),
            'deadline_date' => $request->deadline_date ? Carbon::parse($request->deadline_date)->format('Y-m-d') : null,
            'user_id' => Auth::guard('frontend')->user()->id ,
            'user_type' => 'vendor',
            'years_of_experience' => $request->experience,
            'specialties' => json_encode($request->specialities),
            'no_of_vacancies' => $request->no_of_vacancies,
            'emirate' => $request->emirate
        ]);

        foreach ($request->translations as $lang => $data) {
            $data['lang'] = $lang;
            $data['job_post_id'] = $job->id;
            if($data['title'] != null || $data['description'] != null || $data['salary'] != null){
                JobPostTranslation::create($data);
            }
        }
        session()->flash('success', __('frontend.job_post_created_successfully'));
        return redirect()->route('jobs.index');
    }

    public function edit($id)
    {
        $jobPost = JobPost::findOrFail(base64_decode($id));
        $jobPost->load('translations');
        $languages = Language::where('status', 1)->orderBy('id')->get();
        $dropdowns = Dropdown::with(['options.translations'])->whereIn('slug', ['specialities', 'years_experience'])->get()->keyBy('slug');
        return view('frontend.vendor.jobs.edit', compact('jobPost','languages','dropdowns'));
    }

    public function update(Request $request, $id)
    {
        $jobPost = JobPost::findOrFail(base64_decode($id));
        $request->validate([
            'type' => 'required',
            'emirate' => 'required',
            'deadline_date' => 'required|date',
            'experience' => 'required',
            'specialities' => 'required',
            'no_of_vacancies' => 'required',
            'translations.en.title' => 'required',
            'translations.en.description' => 'required',
        ],[
            '*.required' => __('frontend.this_field_required'),
            'translations.en.*.required' => __('frontend.this_field_required'),
        ]);

        $jobPost->update([
            'type' => $request->type,
            'emirate' => $request->emirate,
            'deadline_date' => $request->deadline_date ? Carbon::parse($request->deadline_date)->format('Y-m-d') : null,
            'years_of_experience' => $request->experience,
            'specialties' => json_encode($request->specialities),
            'no_of_vacancies' => $request->no_of_vacancies,
        ]);
        foreach ($request->translations as $lang => $data) {
            JobPostTranslation::updateOrCreate(
                ['job_post_id' => $jobPost->id, 'lang' => $lang],
                $data
            );
        }
        session()->flash('success', __('frontend.job_post_updated_successfully'));
        return redirect()->route('jobs.index');
    }

    public function updateStatus(Request $request)
    {
        $jobpost = JobPost::findOrFail($request->id);
        $jobpost->status = $request->status;
        $jobpost->save();
       
        return 1;
    }

     public function jobPostDetails($id, Request $request)
    {
        $id     = base64_decode($id);
        $user   = Auth::guard('frontend')->user();
        $lang   = app()->getLocale() ?? env('APP_LOCALE','en'); 

        $job = JobPost::where('id', $id)
                        ->with([ 'location']) 
                        ->first();

        if (!$job) {
            return redirect()->back()->with('error', __('frontend.no_jobs_found'));
        }

        $specialties = json_decode($job->specialties);

        if ($specialties) {
             $specialties = DropdownOption::with('translations')->whereIn('id', $specialties)->get();

            $specialties = $specialties->map(function ($item) {
                return $item->getTranslatedName(app()->getLocale());
            });
        }

        if($job->years_of_experience != null){
            $yearsExp = DropdownOption::with('translations')->where('id', $job->years_of_experience)->first();
            if($yearsExp){
                $job->years_of_experience = $yearsExp->getTranslatedName(app()->getLocale());
            }
        }

        $jobPost = [
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
                    'no_of_vacancies' => $job->no_of_vacancies,
                    'specialties' => $specialties,
                    'years_of_experience' => $job->years_of_experience
                ];
        
        return view('frontend.vendor.jobs.show', compact('lang','jobPost'));
    }

    public function destroy( Request $request)
    {
        $id = $request->job_id;
        $jobPost = JobPost::findOrFail(base64_decode($id));
        $jobPost->delete();
      
        return response()->json(['success' => true, 'message' =>  __('frontend.job_post_deleted_successfully')]);
    }

    public function applications ($id) {
        $id = base64_decode($id);
        $lang   = app()->getLocale() ?? env('APP_LOCALE','en');
        $applications = JobApplication::where('job_post_id', $id)->paginate(10);
        $jobPost = JobPost::where('id', $id)->first();

        return view('frontend.vendor.jobs.applications', compact('jobPost', 'lang','applications'));
    }
}
