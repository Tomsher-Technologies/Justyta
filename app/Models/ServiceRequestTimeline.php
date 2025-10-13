<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequestTimeline extends Model
{
    protected $fillable = [
        'service_request_id',
        'service_slug',
        'status',
        'label',
        'note',
        'changed_by',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function modifiedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
