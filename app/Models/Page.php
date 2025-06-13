<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['name', 'slug'];

    public function translations()
    {
        return $this->hasMany(PageTranslation::class);
    }

    public function translation($lang)
    {
        return $this->translations->firstWhere('lang', $lang);
    }

    public function getTranslation($field = '', $lang = false)
    {
        $lang = $lang == false ? getActiveLanguage() : $lang;
        $translations = $this->translations->where('lang', $lang)->first();
    
         // If not found OR name is empty, fallback to 'en'
        if (!$translations || empty($translations->$field)) {
            $translations = $this->translations->where('lang', 'en')->first();
        }

        return $translations != null ? $translations->$field : $this->$field;
    }
}

