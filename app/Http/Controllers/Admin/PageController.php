<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Models\Service;
use App\Models\Page;
use App\Models\PageTranslation;
use App\Models\PageSection;
use App\Models\PageSectionTranslation;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::with('translations')->orderBy('name')->get();
        return view('admin.pages.index', compact('pages'));
    }

    public function edit(Page $page)
    {
        $languages = Language::where('status', 1)->get();
        $translations = $page->translations->keyBy('lang');
        $services = Service::where('status', 1)->orderBy('sort_order')->get();
        return view('admin.pages.edit', compact('page', 'languages', 'translations', 'services'));
    }

    public function update(Request $request, Page $page)
    {
        $page->update($request->only('name', 'slug'));

        if ($request->has('translations')) {
            foreach ($request->translations as $lang => $data) {
                $page->translations()->updateOrCreate(
                    ['lang' => $lang],
                    [
                        'title' => $data['title'] ?? null,
                        'description' => $data['description'] ?? null,
                        'content' => $data['content'] ?? null,
                    ]
                );
            }
        }

        if ($page->slug === 'user_app_home') {
            if ($request->has('service_id')) {
                $page->content = json_encode($request->service_id);
                $page->save();
            } else {
                $page->content = json_encode([]);
                $page->save();
            }
        }


        session()->flash('success', 'Page content updated successfully.');
        return redirect()->route('pages.index');
    }

    public function sections(Page $page)
    {
        $sections = PageSection::where('page_id', $page->id)
            ->with('translations')
            ->orderBy('order')
            ->get();
        return view('admin.pages.sections.index', compact('page', 'sections'));
    }

    public function createSection(Page $page)
    {
        $languages = Language::where('status', 1)->get();
        return view('admin.pages.sections.create', compact('page', 'languages'));
    }

    public function storeSection(Request $request, Page $page)
    {
        $request->validate([
            'section_type' => 'required|string',
            'section_key' => 'required|string|unique:page_sections,section_key',
            'image' => 'nullable|image',
            'order' => 'required|integer',
            'translations.en.title' => 'required|string|max:255',
            'translations.en.description' => 'nullable|string',
        ], [
            'translations.en.title.required' => 'The english title field is required.',
            'translations.en.title.max' => 'The english title may not be greater than 255 characters.',
            'section_key.unique' => 'This section key already exists.',
        ]);

        $data = $request->only('section_type', 'section_key', 'order');
        $data['page_id'] = $page->id;
        $data['status'] = $request->status ?? 1;

        if ($request->hasFile('image')) {
            $data['image'] = uploadImage('page-sections', $request->image, 'section_image');
        }

        $section = PageSection::create($data);

        foreach ($request->translations as $lang => $trans) {
            $section->translations()->create([
                'lang' => $lang,
                'title' => $trans['title'] ?? null,
                'subtitle' => $trans['subtitle'] ?? null,
                'description' => $trans['description'] ?? null,
                'button_text' => $trans['button_text'] ?? null,
                'button_link' => $trans['button_link'] ?? null,
                'content' => $trans['content'] ?? null, // Add content field
            ]);
        }

        session()->flash('success', 'Section created successfully.');
        return redirect()->route('pages.sections.index', $page);
    }

    public function editSection(Page $page, PageSection $section)
    {
        $section->load('translations');
        $languages = Language::where('status', 1)->get();
        return view('admin.pages.sections.edit', compact('page', 'section', 'languages'));
    }

    public function updateSection(Request $request, Page $page, PageSection $section)
    {
        $request->validate([
            'section_type' => 'required|string',
            'section_key' => 'required|string|unique:page_sections,section_key,' . $section->id,
            'image' => 'nullable|image',
            'order' => 'required|integer',
            'translations.en.title' => 'required|string|max:255',
            'translations.en.description' => 'nullable|string',
        ], [
            'translations.en.title.required' => 'The english title field is required.',
            'translations.en.title.max' => 'The english title may not be greater than 255 characters.',
        ]);

        $data = $request->only('section_type', 'section_key', 'order');
        $data['status'] = $request->status ?? 1;

        $data['image'] = $section->image;
        if ($request->hasfile('image')) {
            $icon = str_replace('/storage/', '', $section->image);
            if ($icon && Storage::disk('public')->exists($icon)) {
                Storage::disk('public')->delete($icon);
            }
            $data['image'] = uploadImage('page-sections', $request->image, 'section_image');
        }

        $section->update($data);

        foreach ($request->translations as $lang => $trans) {
            $section->translations()->updateOrCreate(
                ['lang' => $lang],
                [
                    'title' => $trans['title'] ?? null,
                    'subtitle' => $trans['subtitle'] ?? null,
                    'description' => $trans['description'] ?? null,
                    'button_text' => $trans['button_text'] ?? null,
                    'button_link' => $trans['button_link'] ?? null,
                    'content' => $trans['content'] ?? null, // Add content field
                ]
            );
        }

        session()->flash('success', 'Section updated successfully.');
        return redirect()->route('pages.sections.index', $page);
    }

    public function destroySection(Page $page, PageSection $section)
    {
        if ($section->image != NULL) {
            $icon = str_replace('/storage/', '', $section->image);
            if ($icon && Storage::disk('public')->exists($icon)) {
                Storage::disk('public')->delete($icon);
            }
        }
        $section->delete();
        session()->flash('success', 'Section deleted successfully.');
        return redirect()->route('pages.sections.index', $page);
    }

    public function updateSectionStatus(Request $request)
    {
        $section = PageSection::findOrFail($request->id);
        $section->status = $request->status;
        $section->save();

        return 1;
    }
}
