<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WebsiteSetting;

class WebsiteContentController extends Controller
{
    public function menuAppearance()
    {
        $settings = WebsiteSetting::pluck('value', 'key')->toArray();
        return view('admin.website_contents.menu_appearance', compact('settings'));
    }

    public function updateMenuAppearance(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'shop_description' => 'nullable|string',
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

        if ($request->has('shop_description')) {
            WebsiteSetting::updateOrCreate(['key' => 'shop_description'], ['value' => $request->shop_description]);
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
