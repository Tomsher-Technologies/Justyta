<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserOnlineLog extends Model
{
    protected $table = 'user_online_logs';

    protected $fillable = [
        'user_id',
        'status',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
