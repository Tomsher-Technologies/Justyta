<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranslatorLanguageRate extends Model
{
    protected $fillable = [
        'translator_id',
        'from_language_id',
        'to_language_id',
        'hours_per_page',
        'admin_amount',
        'translator_amount',
        'status',
    ];

    public function translator()
    {
        return $this->belongsTo(Translator::class);
    }

    public function fromLanguage()
    {
        return $this->belongsTo(TranslationLanguage::class, 'from_language_id');
    }

    public function toLanguage()
    {
        return $this->belongsTo(TranslationLanguage::class, 'to_language_id');
    }
}
