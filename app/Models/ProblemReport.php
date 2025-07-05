<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProblemReport extends Model
{
    protected $fillable = ['user_id', 'email', 'subject', 'message', 'image'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
