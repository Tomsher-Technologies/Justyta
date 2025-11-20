<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmirateLitigation extends Model
{
    protected $fillable = [
        'emirate_id', 'slug', 'is_litigation_type', 'is_federal', 'is_local', 'status'
    ];

    public function emirate()
    {
        return $this->belongsTo(Emirate::class);
    }

   
}
