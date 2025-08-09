<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Faq;

use Illuminate\Http\Request;

class FaqController extends Controller
{
     function __construct()
    {
        $this->middleware('auth');
       
        $this->middleware('permission:manage_faqs',  ['only' => ['index']]);
        $this->middleware('permission:add_faq',  ['only' => ['create','store']]);
        $this->middleware('permission:edit_faq',  ['only' => ['edit','update']]);
        $this->middleware('permission:delete_faq',  ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $query = Faq::with('translations')->orderBy('sort_order');

        if ($request->filled('status')) {
            // 1 = active, 2 = inactive; 
            if ($request->status == 1) {
                $query->where('status', 1);
            } elseif ($request->status == 2) {
                $query->where('status', 0);
            }
        }

        $faqs = $query->paginate(15);

        return view('admin.faqs.index', compact('faqs'));
    }

    public function create()
    {
        $languages = Language::where('status', 1)->get();
        return view('admin.faqs.create', compact('languages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer',
            'translations.en.question' => 'required|string',
            'translations.en.answer' => 'required|string',
        ],[
            'translations.en.*.required' => 'This field is required',
            '*.required' => 'This field is required',
        ]);

        $faq = Faq::create([
            'status' => $request->status,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        foreach ($request->translations as $lang => $data) {
            if($data['question'] != NULL && $data['answer'] != NULL){
                $faq->translations()->create([
                    'lang' => $lang,
                    'question' => $data['question'],
                    'answer' => $data['answer'],
                ]);
            }
        }
        session()->flash('success', 'FAQ created successfully.');
        return redirect()->route('faqs.index');
    }

    public function edit(Faq $faq)
    {
        $languages = Language::where('status', 1)->get();
        $translations = $faq->translations->keyBy('lang');

        return view('admin.faqs.edit', compact('faq', 'languages', 'translations'));
    }

    public function update(Request $request, Faq $faq)
    {
        $request->validate([
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer',
            'translations.en.question' => 'required|string',
            'translations.en.answer' => 'required|string',
        ],[
            'translations.en.*.required' => 'This field is required',
            '*.required' => 'This field is required',
        ]);

        $faq->update([
            'status' => $request->status,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        foreach ($request->translations as $lang => $data) {
            $faq->translations()->updateOrCreate(
                ['lang' => $lang],
                ['question' => $data['question'], 'answer' => $data['answer']]
            );
        }
        session()->flash('success', 'FAQ updated successfully.');
        return redirect()->route('faqs.index');
    }

    public function updateStatus(Request $request)
    {
        $faq = Faq::findOrFail($request->id);
        $faq->status = $request->status;
        $faq->save();
       
        return 1;
    }

    public function destroy($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->delete();
        session()->flash('success', 'FAQ deleted successfully.');
        return redirect()->route('faqs.index');
    }

}
