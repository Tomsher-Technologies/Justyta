<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FreeZone;
use App\Models\Language;
use App\Models\Emirate;

class FreezoneController extends Controller
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
        $query = FreeZone::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('ptype_id')) {
            $query->where('emirate_id', $request->ptype_id);
        }

        if ($request->filled('status')) {
            // 1 = active, 2 = inactive; 
            if ($request->status == 1) {
                $query->where('status', 1);
            } elseif ($request->status == 2) {
                $query->where('status', 0);
            }
        }

        $freezones = $query->orderBy('sort_order')->paginate(20)->appends($request->all());

        $allParentTypes = Emirate::where('status',1)->orderBy('id')->get();

        $languages = Language::where('status', 1)->orderBy('id')->get();

        return view('admin.free-zones.index', compact('freezones', 'allParentTypes','languages'));
    }

    public function store(Request $request)
    {
       
        $request->validate([
            'emirate_id' => 'required|exists:emirates,id',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer',
            'translations.en.name' => 'required|string|max:255'
        ],[
            'translations.en.name.required' => 'English name field is required',
            'status.required' => 'Status is required',
            'emirate_id.required' => 'Emirate is required',
        ]);

        $type = FreeZone::create([
            'emirate_id' => $request->emirate_id,
            'status' => $request->status,
            'sort_order' => $request->sort_order,
        ]);

        foreach ($request->translations as $lang => $data) {
            if($lang === 'en'){
                $type->name = $data['name'];
                $type->save();
            }
            if($data['name'] != null){
                $type->translations()->create([
                    'lang' => $lang,
                    'name' => $data['name']
                ]);
            }
        }

        session()->flash('success', 'Free zone created successfully.');

        return response()->json(['success' => true, 'data' => $type]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'emirate_id' => 'required|exists:emirates,id',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer',
            'translations.en.name' => 'required|string|max:255'
        ],[
            'translations.en.name.required' => 'English name field is required',
            'status.required' => 'Status is required',
            'emirate_id.required' => 'Emirate is required',
        ]);

        $freeZone = FreeZone::find($id);

        if (!$freeZone) {
            return response()->json([
                'error' => 'Free zone not found.'
            ], 404);
        }
        $freeZone->emirate_id = $request->input('emirate_id');
        $freeZone->status = $request->input('status');
        $freeZone->sort_order = $request->input('sort_order', 0);
        $freeZone->save();

        foreach ($request->translations as $lang => $data) {
            if($lang === 'en'){
                $freeZone->name = $data['name'];
                $freeZone->save();
            }
            $freeZone->translations()->updateOrCreate(
                ['lang' => $lang],
                ['name' => $data['name']]
            );
        }

        session()->flash('success', 'Free zone updated successfully.');
        return response()->json([
            'message' => 'Free zone updated successfully',
            'freeZone' => $freeZone
        ]);
    }

    public function edit($id)
    {
        $type = FreeZone::with('translations')->findOrFail($id);

        return response()->json([
            'id' => $type->id,
            'emirate_id' => $type->emirate_id,
            'status' => $type->status,
            'sort_order' => $type->sort_order,
            'translations' => $type->translations->pluck('name', 'lang'),
        ]);
    }

    public function destroy(FreeZone $freeZone)
    {
        $freeZone->delete();
        return back()->with('success', 'Free zone deleted.');
    }

    public function updateStatus(Request $request)
    {
        $freeZone = FreeZone::findOrFail($request->id);
        $newStatus = $request->status;

        $freeZone->status = $newStatus;
        $freeZone->save();
       
        return 1;
    }
}
