<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DefaultTranslatorAssignment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'from_language_id',
        'to_language_id',
        'translator_id',
        'assigned_by',
        'assigned_at',
    ];

    public function fromLanguage() {
        return $this->belongsTo(TranslationLanguage::class, 'from_language_id');
    }

    public function toLanguage() {
        return $this->belongsTo(TranslationLanguage::class, 'to_language_id');
    }

    public function translator() {
        return $this->belongsTo(Translator::class);
    }
}
