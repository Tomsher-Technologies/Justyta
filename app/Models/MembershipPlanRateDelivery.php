<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipPlanRateDelivery extends Model
{
    protected $table = 'membership_plan_rate_deliveries';

    protected $fillable = [
        'rate_id',
        'priority_type',
        'delivery_type',
        'delivery_amount',
        'admin_amount',
        'translator_amount',
        'tax',
        'total_amount',
    ];

    public function rate()
    {
        return $this->belongsTo(MembershipPlanLanguageRate::class, 'rate_id');
    }

    public function getFormattedTotalAttribute()
    {
        return number_format($this->total_amount, 2);
    }

    public function scopeForPriority($query, $priority)
    {
        return $query->where('priority_type', $priority);
    }

    public function scopeForDeliveryType($query, $type)
    {
        return $query->where('delivery_type', $type);
    }


}
