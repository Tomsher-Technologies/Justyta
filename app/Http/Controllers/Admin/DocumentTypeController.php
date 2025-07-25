<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DocumentType;
use App\Models\Language;

class DocumentTypeController extends Controller
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
        $query = DocumentType::with(['children' => function ($childQuery) use ($statusFilter) {
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

        $documentTypes = $query->orderBy('sort_order')->paginate(10)->appends($request->all());

        $allParentTypes = DocumentType::whereNull('parent_id')->orderBy('name')->get();

        $languages = Language::where('status', 1)->orderBy('id')->get();

        return view('admin.document_types.index', compact('documentTypes', 'allParentTypes','languages'));
    }

    public function store(Request $request)
    {
       
        $request->validate([
            'parent_id' => 'nullable|exists:document_types,id',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer',
            'translations.en.name' => 'required|string|max:255'
        ],[
            'translations.en.name.required' => 'English name field is required',
            'status.required' => 'Status is required',
        ]);

        $type = DocumentType::create([
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

        session()->flash('success', 'Document type created successfully.');

        return response()->json(['success' => true, 'data' => $type]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'parent_id' => 'nullable|exists:document_types,id',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer',
            'translations.en.name' => 'required|string|max:255'
        ],[
            'translations.en.name.required' => 'English name field is required',
            'status.required' => 'Status is required',
        ]);

        // Find the document type by ID
        $documentType = DocumentType::find($id);

        if (!$documentType) {
            return response()->json([
                'error' => 'Document Type not found.'
            ], 404);
        }
        $documentType->parent_id = $request->input('parent_id');
        $documentType->status = $request->input('status');
        $documentType->sort_order = $request->input('sort_order', 0);
        $documentType->save();

        foreach ($request->translations as $lang => $data) {
            if($lang === 'en'){
                $documentType->name = $data['name'];
                $documentType->save();
            }
            $documentType->translations()->updateOrCreate(
                ['lang' => $lang],
                ['name' => $data['name']]
            );
        }

        session()->flash('success', 'Document type updated successfully.');
        // return response()->json(['success' => true, 'data' => $documentType]);
        return response()->json([
            'message' => 'Document Type updated successfully',
            'documentType' => $documentType
        ]);
    }

    public function edit($id)
    {
        $type = DocumentType::with('translations')->findOrFail($id);

        // Return both main type fields and translations
        return response()->json([
            'id' => $type->id,
            'parent_id' => $type->parent_id,
            'status' => $type->status,
            'sort_order' => $type->sort_order,
            'translations' => $type->translations->pluck('name', 'lang'),
        ]);
    }

    public function destroy(DocumentType $documentType)
    {
        $documentType->delete();
        return back()->with('success', 'Document Type deleted.');
    }

    public function updateStatus(Request $request)
    {
        $documentType = DocumentType::findOrFail($request->id);
        $newStatus = $request->status;

        $documentType->status = $newStatus;
        $documentType->save();

        // 1. If this is a parent and status changes, update all children
        if ($documentType->parent_id === null) {
            // Update children
            DocumentType::where('parent_id', $documentType->id)
                ->update(['status' => $newStatus]);
        } else {
            // 2. If child is activated but parent is inactive, activate parent
            $parent = DocumentType::find($documentType->parent_id);

            if ($newStatus == 1 && $parent && $parent->status == 0) {
                $parent->status = 1;
                $parent->save();
            }

            // 3. If child is inactivated and all siblings are also inactive, inactivate parent
            if ($newStatus == 0 && $parent) {
                $allSiblingsInactive = DocumentType::where('parent_id', $parent->id)
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
