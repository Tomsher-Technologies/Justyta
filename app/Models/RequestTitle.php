<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestTitle extends Model
{
    protected $fillable = ['request_type_id', 'title', 'status'];

    public function translations()
    {
        return $this->hasMany(RequestTitleTranslation::class);
    }

    public function type()
    {
        return $this->belongsTo(RequestType::class,'request_type_id');
    }

    public function pricings()
    {
        return $this->hasMany(RequestSubmissionPricing::class, 'request_title_id');
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
