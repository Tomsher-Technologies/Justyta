<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestExpertReport extends Model
{
    protected $table = 'request_expert_reports';

    protected $fillable = [
        'service_request_id', 'user_id', 'applicant_type', 'applicant_place', 'emirate_id', 'expert_report_type', 'expert_report_language', 'about_case', 'documents', 'eid', 'trade_license'
    ];

    protected $casts = [
        'documents' => 'array',
        'eid' => 'array',
        'trade_license' => 'array',
    ];

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
        return $this->belongsTo(Emirate::class,'emirate_id');
    }

    public function expertReportType()
    {
        return $this->belongsTo(DropdownOption::class, 'expert_report_type');
    }
    public function expertReportLanguage()
    {
        return $this->belongsTo(DropdownOption::class, 'expert_report_language');
    }
}
