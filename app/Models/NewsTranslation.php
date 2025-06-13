<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['news_id', 'lang', 'title', 'description', 'meta_title', 'meta_description', 'meta_keywords', 'twitter_title', 'twitter_description', 'og_title', 'og_description'];

    public function news()
    {
        return $this->belongsTo(News::class);
    }
}