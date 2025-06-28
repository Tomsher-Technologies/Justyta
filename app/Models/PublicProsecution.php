<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicProsecution extends Model
{
    protected $fillable = ['name', 'parent_id','status','sort_order'];

    // Relationship to parent
    public function parent()
    {
        return $this->belongsTo(PublicProsecution::class, 'parent_id');
    }

    // Relationship to children (sub court requests)
    public function children()
    {
        return $this->hasMany(PublicProsecution::class, 'parent_id')->orderBy('sort_order');
    }

    // Scope for only main types
    public function scopeMain($query)
    {
        return $query->whereNull('parent_id');
    }

    // Scope for only sub types
    public function scopeSub($query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function translations()
    {
        return $this->hasMany(PublicProsecutionTranslation::class);
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
    
         // If not found OR name is empty, fallback to 'en'
        if (!$translations || empty($translations->$field)) {
            $translations = $this->translations->where('lang', 'en')->first();
        }

        return $translations != null ? $translations->$field : $this->$field;
    }
}
