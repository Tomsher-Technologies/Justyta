<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultationDuration extends Model
{
    protected $fillable = ['type', 'duration', 'amount'];
}