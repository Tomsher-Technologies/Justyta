<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\AdFile;
use App\Models\AdsPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AdController extends Controller
{

    function __construct()
    {
        $this->middleware('auth');
       
        $this->middleware('permission:manage_ads',  ['only' => ['index','create','store','edit','update','show']]);
        $this->middleware('permission:add_ads',  ['only' => ['create','store']]);
        $this->middleware('permission:edit_ads',  ['only' => ['edit','update']]);
        $this->middleware('permission:view_ads',  ['only' => ['index','show']]);
        $this->middleware('permission:delete_ads',  ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $query = Ad::with('page')->orderBy('id', 'desc');
        
        if ($request->filled('status')) {
            if ($request->status == 1) {
                $query->where('status', 1);
            } elseif ($request->status == 2) {
                $query->where('status', 0);
            }
        }

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                ->orWhere('customer_name', 'like', "%{$keyword}%")
                ->orWhere('customer_email', 'like', "%{$keyword}%")
                ->orWhere('customer_phone', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('page_id')) {
            $query->where('page_id', $request->page_id);
        }

        if ($request->filled('daterange')) {
            $dates = explode(' to ', $request->daterange);
            if (count($dates) === 2) {
                $from = trim($dates[0]);
                $to   = trim($dates[1]);

                $query->where(function ($q) use ($from, $to) {
                    $q->whereDate('start_date', '<=', $to)
                    ->whereDate('end_date', '>=', $from);
                });
            }
        }

        $ads = $query->paginate(10);
        $pages = AdsPage::all();
        return view('admin.ads.index', compact('ads','pages'));
    }

    public function create()
    {
        $pages = AdsPage::all();
        return view('admin.ads.create', compact('pages'));
    }

    public function store(Request $request)
    {
        $rules = [
            'title'             => 'required|string|max:255',
            'page_id'           => 'required|exists:ads_pages,id',
            'start_date'        => 'required|date',
            'end_date'          => 'required|date|after_or_equal:start_date',
            'status'            => 'sometimes|boolean',
            'customer_name'     => 'required|string|max:255',
            'customer_email'    => 'nullable|email|max:255',
            'customer_phone'    => 'nullable|string|max:30',
            'redirection_url'   => 'nullable|url|max:255',
            'web_file'          => 'required|file|mimes:jpeg,jpg,png,avif,webp,svg,gif,mp4,mov,avi|max:10240', // max 10MB
            'mob_file'          => 'required|file|mimes:jpeg,jpg,png,avif,webp,svg,gif,mp4,mov,avi|max:10240', // max 10MB
        ];

        $validated = $request->validate($rules);

        $newStart = Carbon::parse($validated['start_date']);
        $newEnd   = Carbon::parse($validated['end_date']);

        $overlappingAd = Ad::where('page_id', $validated['page_id'])
                            ->where(function ($query) use ($newStart, $newEnd) {
                                $query->where(function ($q) use ($newStart, $newEnd) {
                                    $q->where('start_date', '<=', $newEnd)
                                    ->where('end_date', '>=', $newStart);
                                });
                            })
                            ->exists();
         
        if ($overlappingAd) {
            return back()->withErrors(['start_date' => 'An ad already exists for this page during the selected date range.'])->withInput();
        }

        $ad = Ad::create([
            'title'             => $validated['title'],
            'page_id'           => $validated['page_id'],
            'start_date'        => $validated['start_date'],
            'end_date'          => $validated['end_date'],
            'status'            => $request->has('status') ? $request->status : true,
            'customer_name'     => $validated['customer_name'] ?? null,
            'customer_email'    => $validated['customer_email'] ?? null,
            'customer_phone'    => $validated['customer_phone'] ?? null,
            'cta_url'           => $validated['redirection_url'] ?? null,
        ]);

        if ($request->hasFile('web_file')) {
            $fileType = $this->determineFileType($request->web_file->getClientOriginalExtension());
            $device = 'web';
            $data['web_file'] = uploadImage('ads', $request->web_file, 'ad');
            AdFile::create([
                        'ad_id' => $ad->id,
                        'file_path' => $data['web_file'],
                        'file_type' => $fileType,
                        'device' => $device,
                        'language' => null,
                        'order' => 0,
                    ]);
        }

        if ($request->hasFile('mob_file')) {
            $fileType = $this->determineFileType($request->mob_file->getClientOriginalExtension());
            $device = 'mobile';
            $data['mob_file'] = uploadImage('ads', $request->mob_file, 'ad');
            AdFile::create([
                        'ad_id' => $ad->id,
                        'file_path' => $data['mob_file'],
                        'file_type' => $fileType,
                        'device' => $device,
                        'language' => null,
                        'order' => 0,
                    ]);
        }

        session()->flash('success', 'Ad created successfully.');
        return redirect()->route('ads.index')->with('success', 'Ad created successfully.');
    }

    public function show($id)
    {
        $ad = Ad::with('files')->findOrFail($id);
        // Increase impression count on view
        $ad->increment('impressions');

        return view('ads.show', compact('ad'));
    }

    public function click($id)
    {
        $ad = Ad::findOrFail($id);
        $ad->increment('clicks');

        return redirect()->away($ad->cta_url ?? url('/'));
    }

    public function edit($id)
    {
        $ad = Ad::with('files')->findOrFail($id);
        $pages = AdsPage::all();
        return view('admin.ads.edit', compact('ad', 'pages'));
    }

    public function update(Request $request, $id)
    {
        $ad = Ad::findOrFail($id);
        $rules = [
            'title'             => 'required|string|max:255',
            'page_id'           => 'required|exists:ads_pages,id',
            'start_date'        => 'required|date',
            'end_date'          => 'required|date|after_or_equal:start_date',
            'status'            => 'sometimes|boolean',
            'customer_name'     => 'required|string|max:255',
            'customer_email'    => 'nullable|email|max:255',
            'customer_phone'    => 'nullable|string|max:30',
            'redirection_url'   => 'nullable|url|max:255',
            'web_file'          => 'nullable|file|mimes:jpeg,jpg,png,avif,webp,svg,gif,mp4,mov,avi|max:10240', // max 10MB
            'mob_file'          => 'nullable|file|mimes:jpeg,jpg,png,avif,webp,svg,gif,mp4,mov,avi|max:10240', // max 10MB
        ];

        $validated = $request->validate($rules);

        $newStart = Carbon::parse($validated['start_date']);
        $newEnd   = Carbon::parse($validated['end_date']);

        $overlappingAd = Ad::where('page_id', $validated['page_id'])
                            ->where('id', '!=', $ad->id)
                            ->where(function ($query) use ($newStart, $newEnd) {
                                $query->where(function ($q) use ($newStart, $newEnd) {
                                    $q->where('start_date', '<=', $newEnd)
                                    ->where('end_date', '>=', $newStart);
                                });
                            })
                            ->exists();
         
        if ($overlappingAd) {
           
            return back()->withErrors(['start_date' => 'An ad already exists for this page during the selected date range.']);
        }

        $ad->update([
            'title'             => $validated['title'],
            'page_id'           => $validated['page_id'],
            'start_date'        => $validated['start_date'],
            'end_date'          => $validated['end_date'],
            'status'            => $request->has('status') ? $request->status : true,
            'customer_name'     => $validated['customer_name'] ?? null,
            'customer_email'    => $validated['customer_email'] ?? null,
            'customer_phone'    => $validated['customer_phone'] ?? null,
            'cta_text'          => $validated['cta_text'] ?? null,
            'cta_url'           => $validated['redirection_url'] ?? null,
        ]);

        if ($request->hasFile('web_file')) {
            $device = 'web';

            $oldFile = AdFile::where('ad_id', $ad->id)->where('device', $device)->first();
            if ($oldFile) {
                $icon = str_replace('/storage/', '', $oldFile->file_path);
                if ($icon && Storage::disk('public')->exists($icon)) {
                    Storage::disk('public')->delete($icon);
                }
                $oldFile->delete();
            }

            $fileType = $this->determineFileType($request->web_file->getClientOriginalExtension());
            $data['web_file'] = uploadImage('ads', $request->web_file, 'ad');
            AdFile::create([
                        'ad_id' => $ad->id,
                        'file_path' => $data['web_file'],
                        'file_type' => $fileType,
                        'device' => $device,
                        'language' => null,
                        'order' => 0,
                    ]);
        }

        if ($request->hasFile('mob_file')) {

            $device = 'mobile';

            $oldFile = AdFile::where('ad_id', $ad->id)->where('device', $device)->first();
            if ($oldFile) {
                $icon = str_replace('/storage/', '', $oldFile->file_path);
                if ($icon && Storage::disk('public')->exists($icon)) {
                    Storage::disk('public')->delete($icon);
                }
                $oldFile->delete();
            }

            $fileType = $this->determineFileType($request->mob_file->getClientOriginalExtension());
            $data['mob_file'] = uploadImage('ads', $request->mob_file, 'ad');
            AdFile::create([
                        'ad_id' => $ad->id,
                        'file_path' => $data['mob_file'],
                        'file_type' => $fileType,
                        'device' => $device,
                        'language' => null,
                        'order' => 0,
                    ]);
        }

        return redirect()->route('ads.index')->with('success', 'Ad updated successfully.');
    }

    public function destroy($id)
    {
        $ad = Ad::findOrFail($id);
        $ad->delete();
        return redirect()->route('ads.index')->with('success', 'Ad deleted successfully.');
    }

    private function determineFileType($extension)
    {
        $extension = strtolower($extension);
        if (in_array($extension, ['mp4', 'mov', 'avi'])) {
            return 'video';
        } elseif ($extension === 'gif') {
            return 'gif';
        } else {
            return 'image';
        }
    }

    private function determineDeviceFromInputName($inputName)
    {
        if (str_contains($inputName, 'mob')) {
            return 'mobile';
        }
        return 'web';
    }

    public function updateStatus(Request $request)
    {
        $ad = Ad::findOrFail($request->id);
        $ad->status = $request->status;
        $ad->save();
       
        return 1;
    }
}
