<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultationPayment extends Model
{
    protected $fillable = [
        'consultation_id', 'user_id', 'type', 'amount', 'payment_reference', 'status'
    ];

    public function consultation() {
        return $this->belongsTo(Consultation::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
