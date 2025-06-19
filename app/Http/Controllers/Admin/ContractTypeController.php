<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContractType;
use App\Models\Language;

class ContractTypeController extends Controller
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
        $query = ContractType::with('children')->whereNull('parent_id');

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

        $contractTypes = $query->orderBy('sort_order')->paginate(20)->appends($request->all());

        $allParentTypes = ContractType::whereNull('parent_id')->orderBy('name')->get();

        $languages = Language::where('status', 1)->orderBy('id')->get();

        return view('admin.contract_types.index', compact('contractTypes', 'allParentTypes','languages'));
    }

    public function store(Request $request)
    {
       
        $request->validate([
            'parent_id' => 'nullable|exists:contract_types,id',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer',
            'translations.en.name' => 'required|string|max:255'
        ],[
            'translations.en.name.required' => 'English name field is required',
            'status.required' => 'Status is required',
        ]);

        $type = ContractType::create([
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

        session()->flash('success', 'Contract type created successfully.');

        return response()->json(['success' => true, 'data' => $type]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'parent_id' => 'nullable|exists:contract_types,id',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer',
            'translations.en.name' => 'required|string|max:255'
        ],[
            'translations.en.name.required' => 'English name field is required',
            'status.required' => 'Status is required',
        ]);

        // Find the contract type by ID
        $contractType = ContractType::find($id);

        if (!$contractType) {
            return response()->json([
                'error' => 'Contract Type not found.'
            ], 404);
        }
        $contractType->parent_id = $request->input('parent_id');
        $contractType->status = $request->input('status');
        $contractType->sort_order = $request->input('sort_order', 0);
        $contractType->save();

        foreach ($request->translations as $lang => $data) {
            if($lang === 'en'){
                $contractType->name = $data['name'];
                $contractType->save();
            }
            $contractType->translations()->updateOrCreate(
                ['lang' => $lang],
                ['name' => $data['name']]
            );
        }

        session()->flash('success', 'Contract type updated successfully.');
        // return response()->json(['success' => true, 'data' => $contractType]);
        return response()->json([
            'message' => 'Contract Type updated successfully',
            'contractType' => $contractType
        ]);
    }

    public function edit($id)
    {
        $type = ContractType::with('translations')->findOrFail($id);

        // Return both main type fields and translations
        return response()->json([
            'id' => $type->id,
            'parent_id' => $type->parent_id,
            'status' => $type->status,
            'sort_order' => $type->sort_order,
            'translations' => $type->translations->pluck('name', 'lang'),
        ]);
    }

    public function destroy(ContractType $contractType)
    {
        $contractType->delete();
        return back()->with('success', 'Contract Type deleted.');
    }

    public function updateStatus(Request $request)
    {
        $contractType = ContractType::findOrFail($request->id);
        $newStatus = $request->status;

        $contractType->status = $newStatus;
        $contractType->save();

        // 1. If this is a parent and status changes, update all children
        if ($contractType->parent_id === null) {
            // Update children
            ContractType::where('parent_id', $contractType->id)
                ->update(['status' => $newStatus]);
        } else {
            // 2. If child is activated but parent is inactive, activate parent
            $parent = ContractType::find($contractType->parent_id);

            if ($newStatus == 1 && $parent && $parent->status == 0) {
                $parent->status = 1;
                $parent->save();
            }

            // 3. If child is inactivated and all siblings are also inactive, inactivate parent
            if ($newStatus == 0 && $parent) {
                $allSiblingsInactive = ContractType::where('parent_id', $parent->id)
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
