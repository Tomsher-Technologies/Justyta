<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultationLawyerAttempt extends Model
{
    protected $fillable = ['consultation_id', 'lawyer_id', 'status'];

    public function consultation() {
        return $this->belongsTo(Consultation::class);
    }

    public function lawyer() {
        return $this->belongsTo(Lawyer::class);
    }
}

