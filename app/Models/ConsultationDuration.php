<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultationDuration extends Model
{
    protected $fillable = ['type', 'duration', 'amount'];

    public function translations()
    {
        return $this->hasMany(ConsultationDurationTranslation::class);
    }

    public function translation($lang = null)
    {
        $lang = $lang ?? app()->getLocale();
        return $this->hasOne(ConsultationDurationTranslation::class)->where('lang', $lang);
    }
}