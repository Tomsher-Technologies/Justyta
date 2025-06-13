<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['faq_id', 'lang', 'question', 'answer'];

    public function faq()
    {
        return $this->belongsTo(Faq::class);
    }
}
