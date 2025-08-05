<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Emirate;
use App\Models\Language;

class EmirateController extends Controller
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
        $query = Emirate::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            if ($request->status == 1) {
                $query->where('status', 1);
            } elseif ($request->status == 2) {
                $query->where('status', 0);
            }
        }

        $emirates = $query->orderBy('name','ASC')->paginate(20)->appends($request->all());

        $languages = Language::where('status', 1)->orderBy('id')->get();

        return view('admin.emirates.index', compact('emirates', 'languages'));
    }

     public function store(Request $request)
    {
       
        $request->validate([
            'status' => 'required|boolean',
            'translations.en.name' => 'required|string|max:255'
        ],[
            'translations.en.name.required' => 'English name field is required',
            'status.required' => 'Status is required',
        ]);

        $emirate = Emirate::create([
            'status' => $request->status
        ]);

        foreach ($request->translations as $lang => $data) {
            if($lang === 'en'){
                $emirate->name = $data['name'];
                $emirate->save();
            }
            if($data['name'] != null){
                $emirate->translations()->create([
                    'lang' => $lang,
                    'name' => $data['name']
                ]);
            }
        }

        session()->flash('success', 'Emirate created successfully.');

        return response()->json(['success' => true, 'data' => $emirate]);
    }

    public function edit($id)
    {
        $emirate = Emirate::with('translations')->findOrFail($id);

        // Return both main type fields and translations
        return response()->json([
            'id' => $emirate->id,
            'status' => $emirate->status,
            'translations' => $emirate->translations->pluck('name', 'lang'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|boolean',
            'translations.en.name' => 'required|string|max:255'
        ],[
            'translations.en.name.required' => 'English name field is required',
            'status.required' => 'Status is required',
        ]);

        // Find the contract type by ID
        $emirate = Emirate::find($id);

        if (!$emirate) {
            return response()->json([
                'error' => 'Emirate not found.'
            ], 404);
        }
        $emirate->status = $request->input('status');
        $emirate->save();

        foreach ($request->translations as $lang => $data) {
            if($lang === 'en'){
                $emirate->name = $data['name'];
                $emirate->save();
            }
            $emirate->translations()->updateOrCreate(
                ['lang' => $lang],
                ['name' => $data['name']]
            );
        }

        session()->flash('success', 'Emirate updated successfully.');
        // return response()->json(['success' => true, 'data' => $contractType]);
        return response()->json([
            'message' => 'Emirate updated successfully',
            'emirate' => $emirate
        ]);
    }

    public function updateStatus(Request $request)
    {
        $emirate = Emirate::findOrFail($request->id);
        $newStatus = $request->status;
        $emirate->status = $newStatus;
        $emirate->save();

        return 1;
    }

    public function updateFederalStatus(Request $request)
    {
        $emirate = Emirate::findOrFail($request->id);
        $newStatus = $request->status;
        $emirate->is_federal = $newStatus;
        $emirate->save();

        return 1;
    }
}
