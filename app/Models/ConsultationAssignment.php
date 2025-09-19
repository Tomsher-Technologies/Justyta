<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultationAssignment extends Model
{
    protected $fillable = [
        'consultation_id','lawyer_id','status','assigned_at','responded_at'
    ];

    public function consultation() {
        return $this->belongsTo(Consultation::class);
    }

    public function lawyer() {
        return $this->belongsTo(Lawyer::class, 'lawyer_id');
    }
}
