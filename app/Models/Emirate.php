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
}
