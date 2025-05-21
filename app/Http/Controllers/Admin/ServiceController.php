<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Language;
use App\Models\ServiceTranslation;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
       
        $this->middleware('permission:manage_service',  ['only' => ['index','destroy']]);
        $this->middleware('permission:view_service',  ['only' => ['index']]);
        $this->middleware('permission:edit_service',  ['only' => ['edit','update','updateStatus']]);
    }

    public function index()
    {
        $services = Service::with([
            'translations',
            'children.translations'
        ])->orderBy('id')->get();

        $defaultLangId = Language::where('code', 'en')->first()->id ?? 1;

        return view('admin.services.index', compact('services', 'defaultLangId'));
    }

    public function edit($id)
    {
        $service = Service::with('translations')->findOrFail($id);
        $languages = Language::where('status', 1)->get();
        return view('admin.services.edit', compact('service', 'languages'));
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        // echo '<pre>';
        // print_r($request->all());
        // die;
        $request->validate([
            'icon' => 'nullable|image|mimes:png|max:150',
            'sort_order' => 'nullable|integer',
            'status' => 'required|boolean',
            'translations.en.description' => 'required|string',
        ],[
            'translations.en.description.required' => 'The English description field is required.',
        ]);

        $iconPath = $service->icon;
        if ($request->hasfile('icon')) {
            $icon = str_replace('/storage/', '', $service->icon);
            if ($icon && Storage::disk('public')->exists($icon)) {
                Storage::disk('public')->delete($icon);
            }
            $iconPath = uploadImage('services', $request->icon, 'image');
        }

        $service->update([
            'icon' => $iconPath,
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->status,
        ]);

        foreach ($request->translations as $langId => $transData) {
            ServiceTranslation::updateOrCreate(
                ['service_id' => $service->id, 'lang' => $langId],
                ['description' => $transData['description'] ?? null,'info' => $transData['info'] ?? null]
            );
        }
        session()->flash('success', 'Service updated successfully.');
        return redirect()->route('services.index');
    }

    public function updateStatus(Request $request)
    {
        $newStatus = $request->status;
        $service = Service::findOrFail($request->id);
        $service->status = $newStatus;
        $service->save();

         // 1. If this is a parent and status changes, update all children
        if ($service->parent_id === null) {
            // Update children
            Service::where('parent_id', $service->id)
                ->update(['status' => $newStatus]);
        } else {
            // 2. If child is activated but parent is inactive, activate parent
            $parent = Service::find($service->parent_id);

            if ($newStatus == 1 && $parent && $parent->status == 0) {
                $parent->status = 1;
                $parent->save();
            }

            // 3. If child is inactivated and all siblings are also inactive, inactivate parent
            if ($newStatus == 0 && $parent) {
                $allSiblingsInactive = Service::where('parent_id', $parent->id)
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
