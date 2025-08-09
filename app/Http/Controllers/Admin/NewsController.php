<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\News;
use App\Models\Language;
use Carbon\Carbon;

class NewsController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
       
        $this->middleware('permission:manage_news',  ['only' => ['index']]);
        $this->middleware('permission:delete_news',  ['only' => ['destroy']]);
        $this->middleware('permission:add_news',  ['only' => ['create','store']]);
        $this->middleware('permission:edit_news',  ['only' => ['edit','update']]);
    }

    public function index(Request $request)
    {
        $query = News::with('translations')->orderBy('news_date','desc');
        
        if ($request->filled('status')) {
            // 1 = active, 2 = inactive; 
            if ($request->status == 1) {
                $query->where('status', 1);
            } elseif ($request->status == 2) {
                $query->where('status', 0);
            }
        }

        $news = $query->paginate(15);
        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        $languages = Language::where('status', 1)->get();
        return view('admin.news.create', compact('languages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'news_date' => 'required|date',
            'image' => 'required|image',
            'translations.en.title' => 'required|string|max:255',
            'translations.en.description' => 'required|min:50'
        ],[
            'translations.en.title.required' => 'The english title field is required.',
            'translations.en.title.max' => 'The english title may not be greater than 255 characters.',
            'translations.en.title.string' => 'The english title must be a valid text string.',
            'translations.en.description.required' => 'The english description field is required.',
            'translations.en.description.min' => 'The english description field must be at least 50 characters.'
        ]);

        $data['news_date'] = $request->news_date ? Carbon::parse($request->news_date)->format('Y-m-d') : null;
        $data['status'] = $request->status ?? 1;

        if ($request->hasFile('image')) {
            $data['image'] = uploadImage('news', $request->image, 'news_image');
        }

        $news = News::create($data);

        foreach ($request->translations as $lang => $trans) {
            $news->translations()->create([
                'lang' => $lang,
                'title' => $trans['title'],
                'description' => $trans['description'] ?? null,
                'meta_title' => $trans['meta_title'] ?? null,
                'meta_description' => $trans['meta_description'] ?? null,
                'meta_keywords' => $trans['meta_keywords'] ?? null,
                'twitter_title' => $trans['twitter_title'] ?? null, 
                'twitter_description' => $trans['twitter_description'] ?? null,
                'og_title' => $trans['og_title'] ?? null, 
                'og_description' => $trans['og_description'] ?? null,
            ]);
        }

        session()->flash('success', 'News created successfully.');
        return redirect()->route('news.index');
    }

    public function edit(News $news)
    {
        $news->load('translations');
        $languages = Language::where('status', 1)->get();
        return view('admin.news.edit', compact('news', 'languages'));
    }

    public function update(Request $request, News $news)
    {
        $request->validate([
            'news_date' => 'required|date',
            'image' => 'nullable|image',
            'translations.en.title' => 'required|string|max:255',
            'translations.en.description' => 'required|min:50'
        ],[
            'translations.en.title.required' => 'The english title field is required.',
            'translations.en.title.max' => 'The english title may not be greater than 255 characters.',
            'translations.en.title.string' => 'The english title must be a valid text string.',
            'translations.en.description.required' => 'The english description field is required.',
            'translations.en.description.min' => 'The english description field must be at least 50 characters.'
        ]);
        
        $data['news_date'] = $request->news_date ? Carbon::parse($request->news_date)->format('Y-m-d') : null;
        $data['status'] = $request->status ?? 1;

        $data['image'] = $news->image;
        if ($request->hasfile('image')) {
            $icon = str_replace('/storage/', '', $news->image);
            if ($icon && Storage::disk('public')->exists($icon)) {
                Storage::disk('public')->delete($icon);
            }
            $data['image'] = uploadImage('news', $request->image, 'news_image');
        }

        $news->update($data);

        foreach ($request->translations as $lang => $trans) {
            $news->translations()->updateOrCreate(
                ['lang' => $lang],
                [
                    'title' => $trans['title'],
                    'description' => $trans['description'] ?? null,
                    'meta_title' => $trans['meta_title'] ?? null,
                    'meta_description' => $trans['meta_description'] ?? null,
                    'meta_keywords' => $trans['meta_keywords'] ?? null,
                    'twitter_title' => $trans['twitter_title'] ?? null, 
                    'twitter_description' => $trans['twitter_description'] ?? null,
                    'og_title' => $trans['og_title'] ?? null, 
                    'og_description' => $trans['og_description'] ?? null,
                ]
            );
        }
        session()->flash('success', 'News updated successfully.');
        return redirect()->route('news.index');
    }

    public function destroy($id)
    {
        $news = News::findOrFail($id);

        if ($news->image != NULL) {
            $icon = str_replace('/storage/', '', $news->image);
            if ($icon && Storage::disk('public')->exists($icon)) {
                Storage::disk('public')->delete($icon);
            }
        }
        $news->delete();
        session()->flash('success', 'News deleted successfully.');
        return redirect()->route('news.index');
    }

    public function updateStatus(Request $request)
    {
        $news = News::findOrFail($request->id);
        $news->status = $request->status;
        $news->save();
       
        return 1;
    }
}
