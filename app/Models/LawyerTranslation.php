<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LawyerTranslation extends Model
{
    protected $fillable = ['lawyer_id', 'lang', 'full_name'];

    public function lawyer()
    {
        return $this->belongsTo(Lawyer::class);
    }
}

