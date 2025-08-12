<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LawyerDropdownOption extends Model
{
    protected $fillable = [
        'lawyer_id',
        'type',
        'dropdown_option_id'
    ];

    public $timestamps = false; 
}