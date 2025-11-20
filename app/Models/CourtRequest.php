<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourtRequest extends Model
{
    protected $fillable = ['name', 'parent_id','status','sort_order'];

    public function parent()
    {
        return $this->belongsTo(CourtRequest::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(CourtRequest::class, 'parent_id')->orderBy('sort_order');
    }

    public function scopeMain($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeSub($query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function translations()
    {
        return $this->hasMany(CourtRequestTranslation::class);
    }

    public function translation($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        return $this->translations()->where('lang', $locale)->first();
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
