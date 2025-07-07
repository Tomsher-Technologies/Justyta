<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestCompanySetup extends Model
{
    protected $table = 'request_company_setup';

    protected $fillable = [
        'service_request_id', 'user_id', 'applicant_type', 'emirate_id', 'zone', 'license_type', 'license_activity', 'company_type', 'industry', 'company_name', 'mobile', 'email', 'documents'
    ];

    protected $casts = [
        'documents' => 'array'
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

    public function zoneOption()
    {
        return $this->belongsTo(FreeZone::class, 'zone');
    }

    public function licenseType()
    {
        return $this->belongsTo(LicenseType::class, 'license_type');
    }
    
    public function licenseActivity()
    {
        return $this->belongsTo(LicenseType::class, 'license_activity');
    }

    public function companyType()
    {
        return $this->belongsTo(DropdownOption::class, 'company_type');
    }

    public function industryOption()
    {
        return $this->belongsTo(DropdownOption::class, 'industry');
    }


}
