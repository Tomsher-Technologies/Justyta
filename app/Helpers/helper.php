<?php

use App\Models\BusinessSetting;
use App\Utility\CategoryUtility;
use App\Models\EnquiryStatus;
use App\Models\Service;
use App\Models\Page;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request;

if (!function_exists('getBaseURL')) {
    function getBaseURL()
    {
        $root = '//' . $_SERVER['HTTP_HOST'];
        $root .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

        return $root;
    }
}


//highlights the selected navigation on admin panel
if (!function_exists('areActiveRoutes')) {
    function areActiveRoutes(array $routes, $output = "active open")
    {
        foreach ($routes as $route) {
            if (Route::currentRouteName() == $route) return $output;
        }
    }
}

function getActiveLanguage()
{
    if (Session::exists('locale')) {
        return Session::get('locale');
    }
    return 'en';
}

function uploadImage($type, $imageUrl, $filename = null){
    $data_url = '';
    $ext = $imageUrl->getClientOriginalExtension();
    
    $path = $type.'/';
    
    $filename = $path . $filename.'_'.time().'_'.rand(10, 9999) . '.' . $ext;

    $imageContents = file_get_contents($imageUrl);

    // Save the original image in the storage folder
    Storage::disk('public')->put($filename, $imageContents);
    $data_url = Storage::url($filename);
    
    return $data_url;
}

function getUploadedImage(?string $path, string $default = 'assets/img/default_image.png'): string
{
    if ($path) {
        $relativePath = str_replace('/storage/', '', $path);
        if (Storage::disk('public')->exists($relativePath)) {
            return asset($path);
        }
    }

    return asset($default);
}

function getServiceId($slug){
    $service =  Service::where('slug', $slug)->pluck('id');

    return $service[0] ?? NULL;
}

function getPageDynamicContent($slug,$lang = 'en'){
    $data = Page::with('translations')->where('slug', $slug)->first();

    $response =  [
                    'title' => $data->getTranslation('title',$lang),
                    'description' => $data->getTranslation('description',$lang),
                    'content' => $data->getTranslation('content',$lang),
                ];
    return $response;
}
