<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourtRequestTranslation extends Model
{
    protected $fillable = ['court_request_id', 'lang', 'name'];

    public function courtRequest()
    {
        return $this->belongsTo(CourtRequest::class);
    }
}