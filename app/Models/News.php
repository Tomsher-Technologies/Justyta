<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = ['image', 'news_date', 'status'];

    public function translations()
    {
        return $this->hasMany(NewsTranslation::class);
    }

    public function translate($lang = null)
    {
        $lang = $lang ?? app()->getLocale();
        return $this->translations->where('lang', $lang)->first();
    }
}
