<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestImmigration extends Model
{
    protected $table = 'request_immigrations';

    protected $fillable = [
        'service_request_id', 'user_id', 'preferred_country', 'position', 'age', 'nationality', 'years_of_experience', 'address', 'residency_status', 'current_salary', 'application_type', 'cv', 'certificates', 'passport', 'photo', 'account_statement'
    ];

    protected $casts = [
        'cv'                => 'array',
        'certificates'      => 'array',
        'passport'          => 'array',
        'photo'             => 'array',
        'account_statement' => 'array',
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function preferredCountry()
    {
        return $this->belongsTo(Country::class,'preferred_country');
    }

    public function nationalityOption()
    {
        return $this->belongsTo(Country::class,'nationality');
    }

    public function currentPosition()
    {
        return $this->belongsTo(DropdownOption::class, 'position');
    }
    public function residencyStatus()
    {
        return $this->belongsTo(DropdownOption::class, 'residency_status');
    }

    public function applicationType()
    {
        return $this->belongsTo(DropdownOption::class, 'application_type');
    }
}
