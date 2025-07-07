<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DefaultTranslatorAssignmentHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'from_language_id',
        'to_language_id',
        'translator_id',
        'assigned_by',
        'assigned_at',
    ];

    public function translator() {
        return $this->belongsTo(Translator::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}

