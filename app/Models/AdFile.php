<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdFile extends Model
{
    protected $fillable = ['ad_id', 'file_path', 'file_type', 'device', 'language', 'order'];

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }
}
