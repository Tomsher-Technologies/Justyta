<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dropdown;
use App\Models\DropdownOption;
use App\Models\DropdownOptionTranslation;
use App\Models\Language;

class DropdownOptionController extends Controller
{
    public function dropdowns()
    {
        $dropdowns = Dropdown::orderBy('name')->get();

        return view('admin.dropdown-options.dropdowns', compact('dropdowns'));
    }

    public function index($dropdownId)
    {
        $dropdown = Dropdown::findOrFail($dropdownId);

        $languages = Language::where('status', 1)->orderBy('id')->get();

        $options = $dropdown->options()->with('translations')->orderBy('sort_order')->get();

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
            'translations.*.name' => 'required|string',
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
