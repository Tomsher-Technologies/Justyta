<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicProsecutionTranslation extends Model
{
    protected $fillable = ['public_prosecution_id', 'lang', 'name'];

    public function publicProsecution()
    {
        return $this->belongsTo(PublicProsecution::class);
    }
}
