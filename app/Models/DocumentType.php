<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $fillable = ['name', 'parent_id','status','sort_order'];

    // Relationship to parent
    public function parent()
    {
        return $this->belongsTo(DocumentType::class, 'parent_id');
    }

    // Relationship to children (sub document types)
    public function children()
    {
        return $this->hasMany(DocumentType::class, 'parent_id')->orderBy('sort_order');
    }

    // Scope for only main types
    public function scopeMain($query)
    {
        return $query->whereNull('parent_id');
    }

    // Scope for only sub types
    public function scopeSub($query)
    {
        return $query->whereNotNull('parent_id');
    }
}
