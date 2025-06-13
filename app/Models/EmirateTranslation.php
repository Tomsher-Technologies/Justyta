<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmirateTranslation extends Model
{
    protected $fillable = ['emirate_id', 'lang', 'name'];

    public function emirate()
    {
        return $this->belongsTo(Emirate::class);
    }
}
