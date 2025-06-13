<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipPlanTranslation extends Model
{
    protected $fillable = ['membership_plan_id', 'lang', 'title'];
    public $timestamps = true;

    public function membershipPlan()
    {
        return $this->belongsTo(MembershipPlan::class);
    }
}