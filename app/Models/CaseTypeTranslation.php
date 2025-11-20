<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseTypeTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['case_type_id', 'lang', 'title'];

    public function caseType() {
        return $this->belongsTo(CaseType::class);
    }
}
