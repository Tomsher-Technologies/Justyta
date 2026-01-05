<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Models\WebsiteSetting;

class WebsiteContentController extends Controller
{
    public function menuAppearance()
    {
        $languages = Language::where('status', 1)->get();
        $settings = WebsiteSetting::pluck('value', 'key')->toArray();
        return view('admin.website_contents.menu_appearance', compact('settings', 'languages'));
    }

    public function updateMenuAppearance(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:102400',
            // 'shop_description' => 'nullable|string',
            'email' => 'nullable|email',
            // 'address' => 'nullable|string',
            'footer_links' => 'nullable|array',
            'footer_links.*.title' => 'nullable|string',
            'footer_links.*.icon' => 'nullable|string',
            'footer_links.*.url' => 'nullable|string',
        ]);

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $fileName = time() . '_' . $logo->getClientOriginalName();
            $logoPath = $logo->storeAs('settings', $fileName, 'public');
            WebsiteSetting::updateOrCreate(['key' => 'logo'], ['value' => 'storage/' . $logoPath]);
        }

        $languages = Language::where('status', 1)->get();
        $languageKeys = [
            'shop_description',
            'address',
            'footer_copyright',
            'block_heading_1',
            'block_heading_2',
            'block_heading_3',
            'block_heading_4',
        ];

        $singleKeys = [
            'email',
        ];

        // Save language-specific keys
        foreach ($languageKeys as $key) {
            foreach ($languages as $lang) {
                $inputName = $key . '_' . $lang->code; // e.g., shop_description_en
                if ($request->filled($inputName)) {
                    WebsiteSetting::updateOrCreate(
                        ['key' => $inputName],
                        ['value' => $request->input($inputName)]
                    );
                }
            }
        }

        // Save language-independent keys
        foreach ($singleKeys as $key) {
            if ($request->filled($key)) {
                WebsiteSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $request->input($key)]
                );
            }
        }



        if ($request->has('footer_links')) {
            $links = array_filter($request->footer_links, function ($link) {
                return !empty($link['icon']) || !empty($link['url']) || !empty($link['title']);
            });

            WebsiteSetting::updateOrCreate(['key' => 'footer_links'], ['value' => json_encode(array_values($links))]);
        }

        return redirect()->back()->with('success', 'Menu appearance updated successfully');
    }
}
