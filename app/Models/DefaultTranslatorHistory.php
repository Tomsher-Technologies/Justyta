<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DefaultTranslatorHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'translator_id',
        'started_at',
        'ended_at',
    ];

    public function translator()
    {
        return $this->belongsTo(Translator::class);
    }
}
