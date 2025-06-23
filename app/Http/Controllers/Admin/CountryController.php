<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\Language;

class CountryController extends Controller
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
        $query = Country::query();

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
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

        $countries = $query->orderBy('name','ASC')->paginate(20)->appends($request->all());

        $languages = Language::where('status', 1)->orderBy('id')->get();

        return view('admin.countries.index', compact('countries', 'languages'));
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

        $country = Country::create([
            'status' => $request->status
        ]);

        foreach ($request->translations as $lang => $data) {
            if($lang === 'en'){
                $country->name = $data['name'];
                $country->save();
            }
            if($data['name'] != null){
                $country->translations()->create([
                    'lang' => $lang,
                    'name' => $data['name']
                ]);
            }
        }

        session()->flash('success', 'Country created successfully.');

        return response()->json(['success' => true, 'data' => $country]);
    }

    public function edit($id)
    {
        $country = Country::with('translations')->findOrFail($id);

        // Return both main type fields and translations
        return response()->json([
            'id' => $country->id,
            'status' => $country->status,
            'translations' => $country->translations->pluck('name', 'lang'),
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
        $country = Country::find($id);

        if (!$country) {
            return response()->json([
                'error' => 'Country not found.'
            ], 404);
        }
        $country->status = $request->input('status');
        $country->save();

        foreach ($request->translations as $lang => $data) {
            if($lang === 'en'){
                $country->name = $data['name'];
                $country->save();
            }
            $country->translations()->updateOrCreate(
                ['lang' => $lang],
                ['name' => $data['name']]
            );
        }

        session()->flash('success', 'Country updated successfully.');
        // return response()->json(['success' => true, 'data' => $contractType]);
        return response()->json([
            'message' => 'Country updated successfully',
            'country' => $country
        ]);
    }

    public function updateStatus(Request $request)
    {
        $country = Country::findOrFail($request->id);
        $newStatus = $request->status;

        $country->status = $newStatus;
        $country->save();

        return 1;
    }
}
