<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestLastWill extends Model
{
    use HasFactory;

    protected $table = 'request_last_will';

    protected $fillable = [
        'service_request_id', 'user_id', 'testament_place', 'emirate_id', 'you_represent', 'nationality', 'religion', 'about_case', 'eid'
    ];

    protected $casts = [
        'eid' => 'array',
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

    public function nationality()
    {
        return $this->belongsTo(Country::class);
    }

    public function youRepresent()
    {
        return $this->belongsTo(DropdownOption::class, 'you_represent');
    }

    public function religion()
    {
        return $this->belongsTo(DropdownOption::class, 'religion');
    }
}
