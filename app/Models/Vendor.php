<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'user_id', 'logo', 'office_address', 'city', 'country', 
        'trade_license', 'trade_license_expiry',
        'emirates_id_front', 'emirates_id_back', 'emirates_id_expiry',
        'residence_visa', 'residence_visa_expiry',
        'passport', 'passport_expiry',
        'card_of_law', 'card_of_law_expiry',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(VendorSubscription::class);
    }

    public function currentSubscription()
    {
        return $this->hasOne(VendorSubscription::class)->where('status', 'active')->latestOfMany();
    }

    public function latestSubscription()
    {
        return $this->hasOne(VendorSubscription::class)->latestOfMany();
    }

}
