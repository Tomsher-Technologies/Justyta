<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Models\Page;
use App\Models\PageTranslation;

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
        return view('admin.pages.edit', compact('page', 'languages', 'translations'));
    }

    public function update(Request $request, Page $page)
    {
        $page->update($request->only('name', 'slug'));

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
        session()->flash('success', 'Page content updated successfully.');
        return redirect()->route('pages.index');
    }


}
