<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Models\Service;
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
        $services = Service::where('status', 1)->orderBy('sort_order')->get();
        return view('admin.pages.edit', compact('page', 'languages', 'translations','services'));
    }

    public function update(Request $request, Page $page)
    {
        $page->update($request->only('name', 'slug'));

        if($request->has('translations')) {
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

        if($page->slug === 'user_app_home') {
            if($request->has('service_id')) {
                $page->content = json_encode($request->service_id);
                $page->save();
            }else{
                $page->content = json_encode([]);
                $page->save();
            }
        }
        
        
        session()->flash('success', 'Page content updated successfully.');
        return redirect()->route('pages.index');
    }


}
