<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestType extends Model
{
    protected $fillable = ['litigation_place', 'litigation_type', 'title', 'status'];

    public function translations()
    {
        return $this->hasMany(RequestTypeTranslation::class);
    }

    public function titles()
    {
        return $this->hasMany(RequestTitle::class);
    }

    public function pricings()
    {
        return $this->hasMany(RequestSubmissionPricing::class, 'request_type_id');
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
