<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorSubscription extends Model
{
    protected $fillable = [
        'vendor_id', 'membership_plan_id', 'amount', 'member_count', 'job_post_count', 'en_ar_price', 'for_ar_price', 'live_online', 'specific_law_firm_choice', 'annual_legal_contract', 'annual_free_ad_days', 'unlimited_training_applications', 'welcome_gift', 'subscription_start', 'subscription_end', 'status', 'created_at','payment_reference'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function plan()
    {
        return $this->belongsTo(MembershipPlan::class, 'membership_plan_id');
    }
}
