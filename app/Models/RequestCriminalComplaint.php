<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestCriminalComplaint extends Model
{
    use HasFactory;

    protected $table = 'request_criminal_complaints';

    protected $fillable = [
        'service_request_id', 'user_id', 'applicant_type', 'litigation_type', 'emirate_id', 'case_type', 'you_represent', 'about_case', 'memo', 'documents', 'eid', 'trade_license'
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
        return $this->belongsTo(CaseType::class, 'case_type');
    }

    public function youRepresent()
    {
        return $this->belongsTo(DropdownOption::class, 'you_represent');
    }
}
