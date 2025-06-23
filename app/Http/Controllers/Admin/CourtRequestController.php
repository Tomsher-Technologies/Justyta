<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CourtRequest;
use App\Models\Language;

class CourtRequestController extends Controller
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
        $query = CourtRequest::with(['children' => function ($childQuery) use ($statusFilter) {
                if ($statusFilter == 1) {
                    $childQuery->where('status', 1);
                } elseif ($statusFilter == 2) {
                    $childQuery->where('status', 0);
                }
            }])->whereNull('parent_id');


        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by parent type
        if ($request->filled('ptype_id')) {
            $query->where('id', $request->ptype_id);
        }

        // Apply status filter on parents
        if ($statusFilter == 1 || $statusFilter == 2) {
            $query->where(function ($q) use ($statusFilter) {
                $q->where('status', $statusFilter == 1 ? 1 : 0)
                ->orWhereHas('children', function ($q2) use ($statusFilter) {
                    $q2->where('status', $statusFilter == 1 ? 1 : 0);
                });
            });
        }

        $courtRequests = $query->orderBy('sort_order')->paginate(10)->appends($request->all());

        $allParentTypes = CourtRequest::whereNull('parent_id')->orderBy('name')->get();

        $languages = Language::where('status', 1)->orderBy('id')->get();

        return view('admin.court_requests.index', compact('courtRequests', 'allParentTypes','languages'));
    }

    public function store(Request $request)
    {
       
        $request->validate([
            'parent_id' => 'nullable|exists:court_requests,id',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer',
            'translations.en.name' => 'required|string|max:255'
        ],[
            'translations.en.name.required' => 'English name field is required',
            'status.required' => 'Status is required',
        ]);

        $type = CourtRequest::create([
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

        session()->flash('success', 'Court request created successfully.');

        return response()->json(['success' => true, 'data' => $type]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'parent_id' => 'nullable|exists:court_requests,id',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer',
            'translations.en.name' => 'required|string|max:255'
        ],[
            'translations.en.name.required' => 'English name field is required',
            'status.required' => 'Status is required',
        ]);

        // Find the Court request by ID
        $courtRequest = CourtRequest::find($id);

        if (!$courtRequest) {
            return response()->json([
                'error' => 'Court request not found.'
            ], 404);
        }
        $courtRequest->parent_id = $request->input('parent_id');
        $courtRequest->status = $request->input('status');
        $courtRequest->sort_order = $request->input('sort_order', 0);
        $courtRequest->save();

        foreach ($request->translations as $lang => $data) {
            if($lang === 'en'){
                $courtRequest->name = $data['name'];
                $courtRequest->save();
            }
            $courtRequest->translations()->updateOrCreate(
                ['lang' => $lang],
                ['name' => $data['name']]
            );
        }

        session()->flash('success', 'Court request updated successfully.');
        // return response()->json(['success' => true, 'data' => $courtRequest]);
        return response()->json([
            'message' => 'Court request updated successfully',
            'courtRequest' => $courtRequest
        ]);
    }

    public function edit($id)
    {
        $type = CourtRequest::with('translations')->findOrFail($id);

        // Return both main type fields and translations
        return response()->json([
            'id' => $type->id,
            'parent_id' => $type->parent_id,
            'status' => $type->status,
            'sort_order' => $type->sort_order,
            'translations' => $type->translations->pluck('name', 'lang'),
        ]);
    }

    public function destroy(CourtRequest $courtRequest)
    {
        $courtRequest->delete();
        return back()->with('success', 'Court request deleted.');
    }

    public function updateStatus(Request $request)
    {
        $courtRequest = CourtRequest::findOrFail($request->id);
        $newStatus = $request->status;

        $courtRequest->status = $newStatus;
        $courtRequest->save();

        // 1. If this is a parent and status changes, update all children
        if ($courtRequest->parent_id === null) {
            // Update children
            CourtRequest::where('parent_id', $courtRequest->id)
                ->update(['status' => $newStatus]);
        } else {
            // 2. If child is activated but parent is inactive, activate parent
            $parent = CourtRequest::find($courtRequest->parent_id);

            if ($newStatus == 1 && $parent && $parent->status == 0) {
                $parent->status = 1;
                $parent->save();
            }

            // 3. If child is inactivated and all siblings are also inactive, inactivate parent
            if ($newStatus == 0 && $parent) {
                $allSiblingsInactive = CourtRequest::where('parent_id', $parent->id)
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
