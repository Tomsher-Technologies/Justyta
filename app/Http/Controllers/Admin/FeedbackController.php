<?php

namespace App\Http\Controllers\Admin;

use App\Models\TrainingRequest;
use App\Models\Dropdown;
use App\Models\Contacts;
use App\Models\Emirate;
use App\Models\ProblemReport;
use App\Models\Rating;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\ContactsExport;
use App\Exports\RatingsExport;
use App\Exports\ProblemsExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class FeedbackController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
       
        $this->middleware('permission:manage_user_feedbacks',  ['only' => ['reportedProblems','userRatings','userContacts']]);
        $this->middleware('permission:reported_problems',  ['only' => ['reportedProblems']]);
        $this->middleware('permission:user_ratings',  ['only' => ['userRatings']]);
        $this->middleware('permission:user_contacts',  ['only' => ['userContacts']]);
        $this->middleware('permission:manage_training_requests',  ['only' => ['trainingRequests','exportTrainingRequests']]);
        $this->middleware('permission:view_training_requests',  ['only' => ['trainingRequests']]);
        $this->middleware('permission:export_training_requests',  ['only' => ['exportTrainingRequests']]);
    }

    public function reportedProblems(Request $request){
        $query = ProblemReport::orderBy('id','desc');

        if ($request->filled('daterange')) {
            $dates = explode(' to ', $request->daterange);
            if (count($dates) === 2) {
                $query->whereBetween('created_at', [
                    Carbon::parse($dates[0])->startOfDay(),
                    Carbon::parse($dates[1])->endOfDay()
                ]);
            }
        }

        $problems = $query->paginate(10);
        return view('admin.user_feedbacks.reported_problems', compact('problems'));
    }

    public function userRatings(Request $request){
        $query = Rating::orderBy('id','desc');

        if ($request->filled('daterange')) {
            $dates = explode(' to ', $request->daterange);
            if (count($dates) === 2) {
                $query->whereBetween('created_at', [
                    Carbon::parse($dates[0])->startOfDay(),
                    Carbon::parse($dates[1])->endOfDay()
                ]);
            }
        }

        $ratings = $query->paginate(10);
        return view('admin.user_feedbacks.ratings', compact('ratings'));
    }

    public function userContacts(Request $request){
        $query = Contacts::orderBy('id','desc');

        if ($request->filled('daterange')) {
            $dates = explode(' to ', $request->daterange);
            if (count($dates) === 2) {
                $query->whereBetween('created_at', [
                    Carbon::parse($dates[0])->startOfDay(),
                    Carbon::parse($dates[1])->endOfDay()
                ]);
            }
        }

        $contacts = $query->paginate(10);
        return view('admin.user_feedbacks.contacts', compact('contacts'));
    }

    public function trainingRequests(Request $request){
        $lang = env('APP_LOCALE', 'en');
        $query = TrainingRequest::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('emirate_id')) {
            $query->where('emirate_id', $request->emirate_id);
        }

        if ($request->filled('residency_status')) {
            $query->where('residency_status', $request->residency_status);
        }

        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(10);

        $dropdowns  = Dropdown::with([
                        'options' => function ($q) {
                            $q->where('status', 'active')->orderBy('sort_order');
                        },
                        'options.translations' => function ($q) use ($lang) {
                            $q->whereIn('language_code', [$lang, 'en']);
                        }
                    ])->whereIn('slug', ['training_positions','residency_status'])->get()->keyBy('slug');

        $response   = [];
        $emirates   = Emirate::where('status',1)->orderBy('id')->get();

        $response['emirates'] = $emirates->map(function ($emirate) use($lang) {
                return [
                    'id'    => $emirate->id,
                    'value' => $emirate->getTranslation('name',$lang),
                ];
        });

        foreach ($dropdowns as $slug => $dropdown) {
            $response[$slug] = $dropdown->options->map(function ($option) use ($lang){
                return [
                    'id'    => $option->id,
                    'value' => $option->getTranslation('name',$lang),
                ];
            });
        }

        return view('admin.user_feedbacks.training_requests', compact('requests', 'response'));
    }

    public function exportTrainingRequests(){

    }

    public function exportUserContacts(Request $request)
    {
        $filename = 'Contacts_export_' . now()->format('Y_m_d_h_i_s') . '.xlsx';
        return Excel::download(new ContactsExport($request), $filename);
    }

    public function exportUserRatings(Request $request)
    {
        $filename = 'ratings_export_' . now()->format('Y_m_d_h_i_s') . '.xlsx';
        return Excel::download(new RatingsExport($request), $filename);
    }

    public function exportUserReportProblems(Request $request)
    {
        $filename = 'problems_export_' . now()->format('Y_m_d_h_i_s') . '.xlsx';
        return Excel::download(new ProblemsExport($request), $filename);
    }

}
