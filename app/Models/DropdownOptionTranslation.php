<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DropdownOptionTranslation extends Model
{
    protected $fillable = ['dropdown_option_id', 'language_code', 'name'];

    public function dropdownOption()
    {
        return $this->belongsTo(DropdownOption::class);
    }
}
