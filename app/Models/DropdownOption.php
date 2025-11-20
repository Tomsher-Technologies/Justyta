<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DropdownOption extends Model
{
     protected $fillable = ['dropdown_id', 'status', 'sort_order'];

    public function dropdown()
    {
        return $this->belongsTo(Dropdown::class);
    }

    public function translations()
    {
        return $this->hasMany(DropdownOptionTranslation::class);
    }

    public function translation($langCode = null)
    {
        $langCode = $langCode ?? app()->getLocale();
        return $this->translations()->where('language_code', $langCode)->first();
    }
    public function translators()
    {
        return $this->belongsToMany(Translator::class, 'translator_dropdown_options');
    }

    public function getTranslatedName($langCode = null)
    {
        $langCode = $langCode ?? app()->getLocale();

        $translated = $this->translations->where('language_code', $langCode)->first();

        if (!$translated) {
            $translated = $this->translations->where('language_code', 'en')->first(); 
        }

        return $translated->name ?? $this->name;
    }

    public function getName($lang = null)
    {
        $lang = $lang ?? getActiveLanguage();

        $translation = $this->translations->where('language_code', $lang)->first();

        if (!$translation || empty($translation->name)) {
            $translation = $this->translations->where('language_code', 'en')->first();
        }

        return $translation ? $translation->name : $this->name;
    }

    public function getTranslation($field = '', $langCode = false)
    {
        $langCode = $langCode ?? app()->getLocale();

        $translated = $this->translations->where('language_code', $langCode)->first();

        
        if (!$translated || empty($translated->$field)) {
            $translated = $this->translations->where('language_code', 'en')->first();
        }

        return $translated->name ?? $this->name;
    }
}
