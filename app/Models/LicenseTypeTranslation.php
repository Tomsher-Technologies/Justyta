<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicenseTypeTranslation extends Model
{
    protected $fillable = ['license_type_id', 'lang', 'name'];

    public function licenseType()
    {
        return $this->belongsTo(LicenseType::class);
    }
}