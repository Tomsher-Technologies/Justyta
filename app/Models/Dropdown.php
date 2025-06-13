<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dropdown extends Model
{
    protected $fillable = ['name', 'slug'];

    public function options()
    {
        return $this->hasMany(DropdownOption::class);
    }
}