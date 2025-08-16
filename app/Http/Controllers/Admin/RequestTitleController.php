<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RequestTitle;
use App\Models\RequestType;
use App\Models\RequestTitleTranslation;
use App\Models\Language;
use Illuminate\Http\Request;

class RequestTitleController extends Controller
{
    public function index(Request $request, $id = null)
    {
        $request_typeId = base64_decode($id);
        $request_type = RequestType::find($request_typeId);

        $query = RequestTitle::with('type')->where('request_type_id', $request_typeId);

        if ($request->search) {
            $query->whereHas('translations', function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            // 1 = active, 2 = inactive; 
            if ($request->status == 1) {
                $query->where('status', 1);
            } elseif ($request->status == 2) {
                $query->where('status', 0);
            }
        }

        $titles = $query->orderBy('id','desc')->paginate(15);

        $languages = Language::where('status', 1)->orderBy('id')->get();

        $request_types = RequestType::where('status', 1)
                            ->orderBy('sort_order')
                            ->with(['translations'])
                            ->get();

        return view('admin.request_titles.index', compact('titles', 'languages', 'request_types','request_type'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'request_type_id' => 'required|exists:request_types,id',
            'translations.en.title' => 'required|string|max:255',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer'
        ],[
            'translations.en.title.required' => 'English name field is required',
            'status.required' => 'Status is required',
        ]);

        $title = RequestTitle::create([
            'request_type_id' => $request->request_type_id,
            'status' => $request->status,
            'sort_order' => $request->sort_order,
        ]);

        foreach ($request->translations as $lang => $data) {
            if($lang === 'en'){
                $title->title = $data['title'];
                $title->save();
            }
            if($data['title'] != null){
                $title->translations()->create([
                    'lang' => $lang,
                    'title' => $data['title']
                ]);
            }
        }

        session()->flash('success', 'Request title created successfully.');

        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        $title = RequestTitle::with('translations')->findOrFail($id);

        return response()->json([
            'id' => $title->id,
            'request_type_id' => $title->request_type_id,
            'status' => $title->status,
            'sort_order' => $title->sort_order,
            'translations' => $title->translations->pluck('title', 'lang')
        ]);
    }

    public function update(Request $request, $id)
    {
        $title = RequestTitle::findOrFail($id);

        $data = $request->validate([
            'request_type_id' => 'required|exists:request_types,id',
            'translations.en.title' => 'required|string|max:255',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer'
        ],[
            'translations.en.title.required' => 'English name field is required',
            'status.required' => 'Status is required',
        ]);

        $title->status = $request->input('status');
        $title->sort_order = $request->input('sort_order', 0);
        $title->save();

        foreach ($request->translations as $lang => $data) {
            if($lang === 'en'){
                $title->title = $data['title'];
                $title->save();
            }
            $title->translations()->updateOrCreate(
                ['lang' => $lang],
                ['title' => $data['title']]
            );
        }

        session()->flash('success', 'Request title updated successfully.');
        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request)
    {
        $title = RequestTitle::findOrFail($request->id);
        $title->status = $request->status;
        $title->save();

        return 1;
    }
}

