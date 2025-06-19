<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentTypeTranslation extends Model
{
    protected $fillable = ['document_type_id', 'lang', 'name'];

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }
}