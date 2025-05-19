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
}
