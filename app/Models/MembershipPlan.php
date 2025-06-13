<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipPlan extends Model
{
    protected $fillable = [
        'title', 'icon', 'amount', 'member_count', 'en_ar_price', 'for_ar_price', 'job_post_count', 'is_active', 'live_online', 'specific_law_firm_choice', 'annual_legal_contract', 'annual_free_ad_days', 'unlimited_training_applications', 'welcome_gift', 'created_at'
    ];

    public function translations()
    {
        return $this->hasMany(MembershipPlanTranslation::class);
    }

    public function translation($lang = null)
    {
        $lang = $lang ?: app()->getLocale();
        return $this->translations->where('lang', $lang)->first();
    }
}
