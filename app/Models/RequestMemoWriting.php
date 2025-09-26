<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestMemoWriting extends Model
{
    protected $table = 'request_memo_writings';

    protected $fillable = [
        'service_request_id', 'user_id', 'applicant_type', 'litigation_type', 'emirate_id', 'case_type', 'you_represent', 'full_name', 'about_case', 'eid', 'trade_license', 'document'
    ];

    protected $casts = [
        'eid' => 'array',
        'trade_license' => 'array',
        'document' => 'array',
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
