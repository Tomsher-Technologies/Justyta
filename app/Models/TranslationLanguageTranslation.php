<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranslationLanguageTranslation extends Model
{
    protected $fillable = [
        'translation_language_id', 'lang', 'name'
    ];

    public function translationLanguage()
    {
        return $this->belongsTo(TranslationLanguage::class);
    }
}
