<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageSection extends Model
{
    protected $fillable = ['page_id', 'section_type', 'section_key', 'image', 'order', 'status','services', 'image1', 'image2', 'link1', 'link2'];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function translations()
    {
        return $this->hasMany(PageSectionTranslation::class);
    }

    public function translation($lang)
    {
        return $this->translations->firstWhere('lang', $lang);
    }

    public function getTranslation($field = '', $lang = false)
    {
        $lang = $lang == false ? getActiveLanguage() : $lang;
        $translations = $this->translations->where('lang', $lang)->first();
    
        if (!$translations || empty($translations->$field)) {
            $translations = $this->translations->where('lang', 'en')->first();
        }

        return $translations != null ? $translations->$field : $this->$field;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
}
