<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestContractDrafting extends Model
{
    protected $table = 'request_contract_draftings';

    protected $fillable = [
        'service_request_id', 'user_id', 'applicant_type', 'emirate_id', 'contract_type', 'sub_contract_type', 'contract_language', 'company_name', 'industry', 'email', 'priority', 'documents', 'trade_license', 'eid'
    ];

    protected $casts = [
        'documents' => 'array',
        'trade_license' => 'array',
        'eid' => 'array'
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

    public function contractType()
    {
        return $this->belongsTo(ContractType::class, 'contract_type');
    }

    public function subContractType()
    {
        return $this->belongsTo(ContractType::class, 'sub_contract_type');
    }

    public function contractLanguage()
    {
        return $this->belongsTo(DropdownOption::class, 'contract_language');
    }

    public function industryOption()
    {
        return $this->belongsTo(DropdownOption::class, 'industry');
    }
}
