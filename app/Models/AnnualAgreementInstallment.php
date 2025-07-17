<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnualAgreementInstallment extends Model
{
    protected $fillable = [
        'service_request_id',
        'installment_no',
        'amount',
        'status',
        'due_date',
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }
}