<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestLegalTranslation extends Model
{
    protected $table = 'request_legal_translations';

    protected $fillable = [
        'service_request_id',
        'user_id',
        'priority_level',
        'document_language',
        'translation_language',
        'document_type',
        'document_sub_type',
        'receive_by',
        'no_of_pages',
        'memo',
        'documents',
        'additional_documents',
        'trade_license',
    ];

    protected $casts = [
        'memo' => 'array',
        'documents' => 'array',
        'additional_documents' => 'array',
        'trade_license' => 'array',
    ];

    // Relationships

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function documentLanguage()
    {
        return $this->belongsTo(TranslationLanguage::class, 'document_language');
    }

    public function translationLanguage()
    {
        return $this->belongsTo(TranslationLanguage::class, 'translation_language');
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class, 'document_type');
    }

    public function documentSubType()
    {
        return $this->belongsTo(DocumentType::class, 'document_sub_type');
    }
}
