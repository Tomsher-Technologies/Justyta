<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranslatorLanguageRate extends Model
{
    protected $fillable = [
        'translator_id', 'from_language_id', 'to_language_id', 'doc_type_id', 'doc_subtype_id', 'hours_1_10', 'hours_11_20', 'hours_21_30', 'hours_31_50', 'hours_above_50', 'status'
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

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class, 'doc_type_id');
    }

    public function documentSubType()
    {
        return $this->belongsTo(DocumentType::class, 'doc_subtype_id');
    }
    
    public function deliveries()
    {
        return $this->hasMany(TranslatorRateDelivery::class, 'rate_id'); 
    }

}
