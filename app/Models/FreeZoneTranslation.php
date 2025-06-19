<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FreeZoneTranslation extends Model
{
    protected $fillable = [
        'free_zone_id', 'lang', 'name'
    ];

    public function freeZone()
    {
        return $this->belongsTo(FreeZone::class);
    }
}