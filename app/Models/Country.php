<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = ['shortname', 'name', 'phonecode'];

    public function translations()
    {
        return $this->hasMany(CountryTranslation::class);
    }

    public function translation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations->where('lang', $locale)->first();
    }
}
