<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Emirate;
use App\Models\Dropdown;
use App\Models\DropdownOption;
use App\Models\DropdownOptionTranslation;
use App\Models\Language;

class DropdownOptionController extends Controller
{

    function __construct()
    {
        $this->middleware('auth');
       
        $this->middleware('permission:manage_dropdown_option',  ['only' => ['index','destroy']]);
        $this->middleware('permission:view_dropdown_option',  ['only' => ['dropdowns','index']]);
        $this->middleware('permission:add_dropdown_option',  ['only' => ['create','store']]);
        $this->middleware('permission:edit_dropdown_option',  ['only' => ['edit','update']]);
    }

    public function dropdowns()
    {
        $dropdowns = Dropdown::where('is_active',1)->orderBy('name')->get();

        return view('admin.dropdown-options.dropdowns', compact('dropdowns'));
    }

    public function index($dropdownSlug)
    {
        $dropdown = Dropdown::where('slug', $dropdownSlug)->first();

        $languages = Language::where('status', 1)->orderBy('id')->get();

        $options = $dropdown->options()->with('translations')->orderBy('sort_order')->get();

        if($dropdown->slug === 'zones'){
            $emirates = Emirate::where('status',1)->orderBy('id', 'ASC')->get();
            return view('admin.dropdown-options.zones', compact('dropdown', 'languages','emirates', 'options'));
        }

        return view('admin.dropdown-options.index', compact('dropdown', 'languages', 'options'));
    }

    public function store(Request $request, $dropdownId)
    {
        $request->validate([
            'status' => 'required|in:active,inactive',
            'sort_order' => 'nullable|integer',
            'translations.en.name' => 'required|string',
        ],[
            'translations.en.name.required' => 'This field is required',
            'status.required' => 'Status is required',
        ]);

        $option = DropdownOption::create([
            'dropdown_id' => $dropdownId,
            'status' => $request->status,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        foreach ($request->translations as $lang => $data) {
            if($data['name'] != ''){
                DropdownOptionTranslation::create([
                    'dropdown_option_id' => $option->id,
                    'language_code' => $lang,
                    'name' => $data['name'],
                ]);
            }
        }

        return back()->with('success', 'Dropdown option added.');
    }

    public function update(Request $request, $id)
    {
        $option = DropdownOption::findOrFail($id);

        $request->validate([
            'status' => 'required|in:active,inactive',
            'sort_order' => 'nullable|integer',
            'translations.en.name' => 'required|string',
        ],[
            'translations.en.name.required' => 'This field is required',
            'status.required' => 'Status is required',
        ]);

        $option->update([
            'status' => $request->status,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        foreach ($request->translations as $lang => $data) {
            $option->translations()->updateOrCreate(
                ['language_code' => $lang],
                ['name' => $data['name']]
            );
        }

        return back()->with('success', 'Dropdown option updated.');
    }

    public function destroy($id)
    {
        DropdownOption::findOrFail($id)->delete();
        return back()->with('success', 'Dropdown option deleted.');
    }

    public function updateStatus(Request $request)
    {
        $opt = DropdownOption::findOrFail($request->id);
        
        $opt->status = $request->status;
        $opt->save();
       
        return 1;
    }
}
