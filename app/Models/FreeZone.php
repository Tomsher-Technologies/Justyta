<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FreeZone extends Model
{
    protected $fillable = [
        'emirate_id', 'status', 'sort_order'
    ];

     public function emirate()
    {
        return $this->belongsTo(Emirate::class);
    }

    public function translations()
    {
        return $this->hasMany(FreeZoneTranslation::class);
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
    
         
        if (!$translations || empty($translations->$field)) {
            $translations = $this->translations->where('lang', 'en')->first();
        }

        return $translations != null ? $translations->$field : $this->$field;
    }
}
