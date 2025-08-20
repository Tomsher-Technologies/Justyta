<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestSubmissionPricing extends Model
{
    protected $table = 'request_submission_pricing';

    protected $fillable = [
        'litigation_type',
        'litigation_place',
        'case_type_id',
        'request_type_id',
        'request_title_id',
        'govt_fee',
        'admin_fee',
        'vat',
        'total_amount',
        'status',
    ];

    // Relationships
    public function requestTitle()
    {
        return $this->belongsTo(RequestTitle::class, 'request_title_id');
    }

    public function requestType()
    {
        return $this->belongsTo(RequestType::class, 'request_type_id');
    }

    public function caseType()
    {
        return $this->belongsTo(CaseType::class, 'case_type_id');
    }
}
