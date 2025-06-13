<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DocumentType;

class DocumentTypeController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
       
        $this->middleware('permission:manage_document_type',  ['only' => ['index','destroy']]);
        $this->middleware('permission:add_document_type',  ['only' => ['create','store']]);
        $this->middleware('permission:edit_document_type',  ['only' => ['edit','update']]);
    }

    public function index(Request $request)
    {
        $query = DocumentType::with('children')->whereNull('parent_id');

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by parent type
        if ($request->filled('ptype_id')) {
            $query->where('id', $request->ptype_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            // Assuming 1 = active, 2 = inactive; 
            if ($request->status == 1) {
                $query->where('status', 1);
            } elseif ($request->status == 2) {
                $query->where('status', 0);
            }
        }

        $documentTypes = $query->orderBy('sort_order')->paginate(20)->appends($request->all());

        $allParentTypes = DocumentType::whereNull('parent_id')->orderBy('name')->get();

        return view('admin.document_types.index', compact('documentTypes', 'allParentTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:document_types,id',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer'
        ]);

        $type = DocumentType::create($request->all());
        session()->flash('success', 'Document type created successfully.');

        return response()->json(['success' => true, 'data' => $type]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:document_types,id',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer'
        ]);

        // Find the document type by ID
        $documentType = DocumentType::find($id);

        if (!$documentType) {
            return response()->json([
                'error' => 'Document Type not found.'
            ], 404);
        }

        $documentType->name = $request->input('name');
        $documentType->parent_id = $request->input('parent_id');
        $documentType->status = $request->input('status');
        $documentType->sort_order = $request->input('sort_order', 0);
        $documentType->save();

        session()->flash('success', 'Document type updated successfully.');
        // return response()->json(['success' => true, 'data' => $documentType]);
        return response()->json([
            'message' => 'Document Type updated successfully',
            'documentType' => $documentType
        ]);
    }

    public function edit($id)
    {
        $type = DocumentType::findOrFail($id);
        return response()->json($type);
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
