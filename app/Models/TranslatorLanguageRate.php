<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranslatorLanguageRate extends Model
{
    protected $fillable = [
        'translator_id', 'from_language_id', 'to_language_id', 'doc_type_id', 'doc_subtype_id', 'normal_hours_1_10', 'normal_hours_11_20', 'normal_hours_21_30', 'normal_hours_31_50', 'normal_hours_above_50', 'urgent_hours_1_10', 'urgent_hours_11_20', 'urgent_hours_21_30', 'urgent_hours_31_50', 'urgent_hours_above_50', 'status'
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
