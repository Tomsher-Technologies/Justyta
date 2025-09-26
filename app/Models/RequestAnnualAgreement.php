<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestAnnualAgreement extends Model
{
    protected $table = 'request_annual_agreements';

    protected $fillable = [
        'service_request_id', 'user_id', 'company_name', 'emirate_id', 'license_type', 'license_activity', 'industry', 'no_of_employees', 'case_type', 'no_of_calls', 'no_of_visits', 'no_of_installment', 'final_total', 'amount_paid', 'lawfirm'
    ];

    protected $casts = [
        'case_type' => 'array',
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

    public function licenseType()
    {
        return $this->belongsTo(LicenseType::class, 'license_type');
    }
    
    public function licenseActivity()
    {
        return $this->belongsTo(LicenseType::class, 'license_activity');
    }

    public function noOfEmployees()
    {
        return $this->belongsTo(DropdownOption::class, 'no_of_employees');
    }

    public function industryOption()
    {
        return $this->belongsTo(DropdownOption::class, 'industry');
    }

    public function lawFirm()
    {
        return $this->belongsTo(Vendor::class, 'lawfirm');
    }

    public function caseTypes()
    {
        return CaseType::whereIn('id', $this->case_type ?? []);
    }

    public function getCaseTypeNamesAttribute()
    {
        $lang = app()->getLocale(); 
        return CaseType::whereIn('id', $this->case_type ?? [])
            ->get()
            ->map(function ($item) use ($lang) {
                return $item->getTranslation('title', $lang);
            });
    }
}
