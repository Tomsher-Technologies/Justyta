<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Emirate extends Model
{
    protected $fillable = [];

    public function translations()
    {
        return $this->hasMany(EmirateTranslation::class);
    }

    public function translation($lang = null)
    {
        $lang = $lang ?: app()->getLocale();
        return $this->translations->where('lang', $lang)->first();
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
