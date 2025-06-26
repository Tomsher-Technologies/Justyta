<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestEscrowAccount extends Model
{
    protected $table = 'request_escrow_accounts';

    protected $fillable = [
        'service_request_id', 'user_id', 'applicant_type', 'company_name', 'company_activity', 'company_origin', 'amount', 'about_deal'
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function companyOrigin()
    {
        return $this->belongsTo(Country::class,'company_origin');
    }

    public function companyActivity()
    {
        return $this->belongsTo(DropdownOption::class, 'company_activity');
    }

}
