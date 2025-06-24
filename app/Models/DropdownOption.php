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
            $translated = $this->translations->where('language_code', 'en')->first(); // fallback
        }

        return $translated->name ?? $this->name;
    }

    public function getTranslation($field = '', $langCode = false)
    {
        $langCode = $langCode ?? app()->getLocale();

        $translated = $this->translations->where('language_code', $langCode)->first();

        // If not found OR name is empty, fallback to 'en'
        if (!$translated || empty($translated->$field)) {
            $translated = $this->translations->where('language_code', 'en')->first();
        }

        return $translated->name ?? $this->name;
    }
}
