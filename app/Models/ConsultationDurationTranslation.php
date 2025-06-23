<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultationDurationTranslation extends Model
{
    protected $fillable = ['consultation_duration_id', 'lang', 'name'];
}