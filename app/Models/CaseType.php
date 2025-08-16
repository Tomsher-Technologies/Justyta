<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseType extends Model
{
    protected $fillable = ['litigation_place', 'litigation_type', 'title', 'status', 'sort_order'];
    public function translations() {
        return $this->hasMany(CaseTypeTranslation::class);
    }
}
