<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdsPage extends Model
{
    protected $fillable = ['name', 'slug'];

    public function ads()
    {
        return $this->hasMany(Ad::class, 'page_id');
    }
}
