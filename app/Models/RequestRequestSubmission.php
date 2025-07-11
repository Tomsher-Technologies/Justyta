<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestRequestSubmission extends Model
{
    protected $table = 'request_request_submissions';

    protected $fillable = [
       'service_request_id', 'user_id', 'applicant_type', 'litigation_type', 'litigation_place', 'emirate_id', 'case_type', 'request_type', 'request_title', 'case_number', 'trade_license', 'documents', 'memo', 'eid'
    ];

    protected $casts = [
        'memo' => 'array',
        'documents' => 'array',
        'eid' => 'array',
        'trade_license' => 'array',
    ];
    /**
     * Relationships
     */

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function emirate()
    {
        return $this->belongsTo(Emirate::class);
    }

    public function caseType()
    {
        return $this->belongsTo(DropdownOption::class, 'case_type');
    }

    public function requestType()
    {
        if ($this->litigation_place === 'court') {
            return $this->belongsTo(CourtRequest::class, 'request_type');
        } elseif ($this->litigation_place === 'public_prosecution') {
            return $this->belongsTo(PublicProsecution::class, 'request_type');
        }

        return null;
    }

    public function requestTitle()
    {
        if ($this->litigation_place === 'court') {
            return $this->belongsTo(CourtRequest::class, 'request_title');
        } elseif ($this->litigation_place === 'public_prosecution') {
            return $this->belongsTo(PublicProsecution::class, 'request_title');
        }

        return null;
    }
}
