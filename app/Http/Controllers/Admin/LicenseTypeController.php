<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LicenseType;
use App\Models\Language;

class LicenseTypeController extends Controller
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
        $query = LicenseType::with(['children' => function ($childQuery) use ($statusFilter) {
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

        $licenseTypes = $query->orderBy('sort_order')->paginate(10)->appends($request->all());

        $allParentTypes = LicenseType::whereNull('parent_id')->orderBy('name')->get();

        $languages = Language::where('status', 1)->orderBy('id')->get();

        return view('admin.license_types.index', compact('licenseTypes', 'allParentTypes','languages'));
    }

    public function store(Request $request)
    {
       
        $request->validate([
            'parent_id' => 'nullable|exists:license_types,id',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer',
            'translations.en.name' => 'required|string|max:255'
        ],[
            'translations.en.name.required' => 'English name field is required',
            'status.required' => 'Status is required',
        ]);

        $type = LicenseType::create([
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

        session()->flash('success', 'License type created successfully.');

        return response()->json(['success' => true, 'data' => $type]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'parent_id' => 'nullable|exists:license_types,id',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer',
            'translations.en.name' => 'required|string|max:255'
        ],[
            'translations.en.name.required' => 'English name field is required',
            'status.required' => 'Status is required',
        ]);

        // Find the license type by ID
        $licenseType = LicenseType::find($id);

        if (!$licenseType) {
            return response()->json([
                'error' => 'License Type not found.'
            ], 404);
        }
        $licenseType->parent_id = $request->input('parent_id');
        $licenseType->status = $request->input('status');
        $licenseType->sort_order = $request->input('sort_order', 0);
        $licenseType->save();

        foreach ($request->translations as $lang => $data) {
            if($lang === 'en'){
                $licenseType->name = $data['name'];
                $licenseType->save();
            }
            $licenseType->translations()->updateOrCreate(
                ['lang' => $lang],
                ['name' => $data['name']]
            );
        }

        session()->flash('success', 'License type updated successfully.');
        // return response()->json(['success' => true, 'data' => $licenseType]);
        return response()->json([
            'message' => 'License Type updated successfully',
            'licenseType' => $licenseType
        ]);
    }

    public function edit($id)
    {
        $type = LicenseType::with('translations')->findOrFail($id);

        // Return both main type fields and translations
        return response()->json([
            'id' => $type->id,
            'parent_id' => $type->parent_id,
            'status' => $type->status,
            'sort_order' => $type->sort_order,
            'translations' => $type->translations->pluck('name', 'lang'),
        ]);
    }

    public function destroy(LicenseType $licenseType)
    {
        $licenseType->delete();
        return back()->with('success', 'License Type deleted.');
    }

    public function updateStatus(Request $request)
    {
        $licenseType = LicenseType::findOrFail($request->id);
        $newStatus = $request->status;

        $licenseType->status = $newStatus;
        $licenseType->save();

        // 1. If this is a parent and status changes, update all children
        if ($licenseType->parent_id === null) {
            // Update children
            LicenseType::where('parent_id', $licenseType->id)
                ->update(['status' => $newStatus]);
        } else {
            // 2. If child is activated but parent is inactive, activate parent
            $parent = LicenseType::find($licenseType->parent_id);

            if ($newStatus == 1 && $parent && $parent->status == 0) {
                $parent->status = 1;
                $parent->save();
            }

            // 3. If child is inactivated and all siblings are also inactive, inactivate parent
            if ($newStatus == 0 && $parent) {
                $allSiblingsInactive = LicenseType::where('parent_id', $parent->id)
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
