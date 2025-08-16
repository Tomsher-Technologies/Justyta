<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestTypeTranslation extends Model
{
    protected $fillable = ['request_type_id', 'lang', 'title'];

    public function type()
    {
        return $this->belongsTo(RequestType::class);
    }
}
