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
}

