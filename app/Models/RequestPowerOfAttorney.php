<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestPowerOfAttorney extends Model
{
    use HasFactory;

    protected $table = 'request_power_of_attorney';

    protected $fillable = [
        'service_request_id', 'user_id', 'applicant_type', 'appointer_name', 'id_number', 'appointer_mobile', 'emirate_id', 'poa_type', 'name_of_authorized', 'authorized_mobile', 'id_number_authorized', 'authorized_address', 'relationship', 'appointer_id', 'authorized_id', 'authorized_passport'
    ];

    protected $casts = [
        'appointer_id' => 'array',
        'authorized_id' => 'array',
        'authorized_passport' => 'array',
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

    public function powerOfAttorneyType()
    {
        return $this->belongsTo(DropdownOption::class, 'poa_type');
    }

    public function relationshipOption()
    {
        return $this->belongsTo(DropdownOption::class, 'relationship');
    }
}
