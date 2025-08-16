<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestTitleTranslation extends Model
{
    protected $fillable = ['request_title_id', 'lang', 'title'];

    public function title()
    {
        return $this->belongsTo(RequestTitle::class);
    }
}
