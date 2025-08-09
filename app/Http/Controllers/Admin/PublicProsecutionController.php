<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PublicProsecution;
use App\Models\Language;

class PublicProsecutionController extends Controller
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
        $statusFilter = $request->input('status');
        $query = PublicProsecution::with(['children' => function ($childQuery) use ($statusFilter) {
                if ($statusFilter == 1) {
                    $childQuery->where('status', 1);
                } elseif ($statusFilter == 2) {
                    $childQuery->where('status', 0);
                }
            }])->whereNull('parent_id');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('ptype_id')) {
            $query->where('id', $request->ptype_id);
        }

        if ($statusFilter == 1 || $statusFilter == 2) {
            $query->where(function ($q) use ($statusFilter) {
                $q->where('status', $statusFilter == 1 ? 1 : 0)
                ->orWhereHas('children', function ($q2) use ($statusFilter) {
                    $q2->where('status', $statusFilter == 1 ? 1 : 0);
                });
            });
        }

        $publicProsecutions = $query->orderBy('sort_order')->paginate(10)->appends($request->all());

        $allParentTypes = PublicProsecution::whereNull('parent_id')->orderBy('name')->get();

        $languages = Language::where('status', 1)->orderBy('id')->get();

        return view('admin.public_prosecutions.index', compact('publicProsecutions', 'allParentTypes','languages'));
    }

    public function store(Request $request)
    {
       
        $request->validate([
            'parent_id' => 'nullable|exists:public_prosecutions,id',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer',
            'translations.en.name' => 'required|string|max:255'
        ],[
            'translations.en.name.required' => 'English name field is required',
            'status.required' => 'Status is required',
        ]);

        $type = PublicProsecution::create([
            'parent_id' => $request->parent_id,
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

        session()->flash('success', 'Public prosecution type created successfully.');

        return response()->json(['success' => true, 'data' => $type]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'parent_id' => 'nullable|exists:public_prosecutions,id',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer',
            'translations.en.name' => 'required|string|max:255'
        ],[
            'translations.en.name.required' => 'English name field is required',
            'status.required' => 'Status is required',
        ]);

        $publicProsecution = PublicProsecution::find($id);

        if (!$publicProsecution) {
            return response()->json([
                'error' => 'Public prosecution type not found.'
            ], 404);
        }
        $publicProsecution->parent_id = $request->input('parent_id');
        $publicProsecution->status = $request->input('status');
        $publicProsecution->sort_order = $request->input('sort_order', 0);
        $publicProsecution->save();

        foreach ($request->translations as $lang => $data) {
            if($lang === 'en'){
                $publicProsecution->name = $data['name'];
                $publicProsecution->save();
            }
            $publicProsecution->translations()->updateOrCreate(
                ['lang' => $lang],
                ['name' => $data['name']]
            );
        }

        session()->flash('success', 'Public prosecution type updated successfully.');
        return response()->json([
            'message' => 'Public prosecution type updated successfully',
            'publicProsecution' => $publicProsecution
        ]);
    }

    public function edit($id)
    {
        $type = PublicProsecution::with('translations')->findOrFail($id);

        return response()->json([
            'id' => $type->id,
            'parent_id' => $type->parent_id,
            'status' => $type->status,
            'sort_order' => $type->sort_order,
            'translations' => $type->translations->pluck('name', 'lang'),
        ]);
    }

    public function destroy(PublicProsecution $publicProsecution)
    {
        $publicProsecution->delete();
        return back()->with('success', 'Public prosecution type deleted.');
    }

    public function updateStatus(Request $request)
    {
        $publicProsecution = PublicProsecution::findOrFail($request->id);
        $newStatus = $request->status;

        $publicProsecution->status = $newStatus;
        $publicProsecution->save();

        if ($publicProsecution->parent_id === null) {
            PublicProsecution::where('parent_id', $publicProsecution->id)
                ->update(['status' => $newStatus]);
        } else {
            $parent = PublicProsecution::find($publicProsecution->parent_id);

            if ($newStatus == 1 && $parent && $parent->status == 0) {
                $parent->status = 1;
                $parent->save();
            }
            if ($newStatus == 0 && $parent) {
                $allSiblingsInactive = PublicProsecution::where('parent_id', $parent->id)
                    ->where('status', 1)
                    ->exists() === false;

                if ($allSiblingsInactive) {
                    $parent->status = 0;
                    $parent->save();
                }
            }
        }
        return 1;
    }
}
