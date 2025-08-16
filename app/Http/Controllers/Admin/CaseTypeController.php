<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CaseType;
use App\Models\CaseTypeTranslation;
use App\Models\Language;
use App\Models\Emirate;

class CaseTypeController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
       
        $this->middleware('permission:manage_dropdown_option',  ['only' => ['index','destroy']]);
        $this->middleware('permission:add_dropdown_option',  ['only' => ['create','store']]);
        $this->middleware('permission:edit_dropdown_option',  ['only' => ['edit','update']]);
    }

    public function index(Request $request)
    {
        $query = CaseType::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('litigation_place')) {
            $query->where('litigation_place', $request->litigation_place);
        }

        if ($request->filled('litigation_type')) {
            $query->where('litigation_type', $request->litigation_type);
        }

        if ($request->filled('status')) {
            // 1 = active, 2 = inactive; 
            if ($request->status == 1) {
                $query->where('status', 1);
            } elseif ($request->status == 2) {
                $query->where('status', 0);
            }
        }

        $case_types = $query->orderBy('id','desc')->paginate(20)->appends($request->all());

        $languages = Language::where('status', 1)->orderBy('id')->get();

        return view('admin.case_types.index', compact('case_types','languages'));
    }

    public function store(Request $request)
    {
       
        $request->validate([
            'litigation_place' => 'required',
            'litigation_type' => 'required',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer',
            'translations.en.title' => 'required|string|max:255'
        ],[
            'translations.en.title.required' => 'English name field is required',
            'litigation_place.required' => 'Litigation place is required',
            'litigation_type.required' => 'Litigation type is required',
            'status.required' => 'Status is required',
            'emirate_id.required' => 'Emirate is required',
        ]);

        $type = CaseType::create([
            'litigation_place' => $request->litigation_place,
            'litigation_type' => $request->litigation_type,
            'status' => $request->status,
            'sort_order' => $request->sort_order,
        ]);

        foreach ($request->translations as $lang => $data) {
            if($lang === 'en'){
                $type->title = $data['title'];
                $type->save();
            }
            if($data['title'] != null){
                $type->translations()->create([
                    'lang' => $lang,
                    'title' => $data['title']
                ]);
            }
        }

        session()->flash('success', 'Case type created successfully.');

        return response()->json(['success' => true, 'data' => $type]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'litigation_place' => 'required',
            'litigation_type' => 'required',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer',
            'translations.en.title' => 'required|string|max:255'
        ],[
            'litigation_place.required' => 'Litigation place is required',
            'litigation_type.required' => 'Litigation type is required',
            'translations.en.title.required' => 'English name field is required',
            'status.required' => 'Status is required',
            'emirate_id.required' => 'Emirate is required',
        ]);

        $type = CaseType::find($id);

        if (!$type) {
            return response()->json([
                'error' => 'Case type not found.'
            ], 404);
        }

        $type->litigation_type = $request->input('litigation_type');
        $type->litigation_place = $request->input('litigation_place');
        $type->status = $request->input('status');
        $type->sort_order = $request->input('sort_order', 0);
        $type->save();

        foreach ($request->translations as $lang => $data) {
            if($lang === 'en'){
                $type->title = $data['title'];
                $type->save();
            }
            $type->translations()->updateOrCreate(
                ['lang' => $lang],
                ['title' => $data['title']]
            );
        }

        session()->flash('success', 'Case type updated successfully.');
        return response()->json([
            'message' => 'Case type updated successfully',
            'case_type' => $type
        ]);
    }

    public function edit($id)
    {
        $type = CaseType::with('translations')->findOrFail($id);

        return response()->json([
            'id' => $type->id,
            'litigation_place' => $type->litigation_place,
            'litigation_type' => $type->litigation_type,
            'status' => $type->status,
            'sort_order' => $type->sort_order,
            'translations' => $type->translations->pluck('title', 'lang'),
        ]);
    }

    public function updateStatus(Request $request)
    {
        $type = CaseType::findOrFail($request->id);
        $newStatus = $request->status;

        $type->status = $newStatus;
        $type->save();
       
        return 1;
    }
}
