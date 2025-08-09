<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'user_id', 'law_firm_name', 'law_firm_email', 'law_firm_phone', 'office_address', 'owner_name', 'owner_email', 'owner_phone', 'emirate_id', 'trn', 'logo', 'about', 'country', 'trade_license', 'trade_license_expiry', 'emirates_id_front', 'emirates_id_back', 'emirates_id_expiry', 'residence_visa', 'residence_visa_expiry', 'passport', 'passport_expiry', 'card_of_law', 'card_of_law_expiry','consultation_commission', 'ministry_of_justice_card', 'ministry_of_justice_card_expiry','website_url','payment_reference'
    ];

     public function location()
    {
        return $this->belongsTo(Emirate::class,'emirate_id');
    }

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

    protected static function booted()
    {
        static::creating(function ($vendor) {
            $vendor->ref_no = self::generateReferenceNumber();
        });
    }

    public static function generateReferenceNumber()
    {
        $prefix = 'LF';

        $lastCode = self::whereNotNull('ref_no')
            ->orderBy('id', 'desc')
            ->value('ref_no');

        $nextNumber = 1;
        if ($lastCode) {
            preg_match('/(\d+)$/', $lastCode, $matches);
            $nextNumber = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        }

        return $prefix . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    public function translations()
    {
        return $this->hasMany(VendorTranslation::class);
    }

    public function translate($lang = null)
    {
        $lang = $lang ?: app()->getLocale();
        return $this->translations->where('lang', $lang)->first();
    }

    public function getTranslation($field = '', $lang = false)
    {
        $lang = $lang == false ? getActiveLanguage() : $lang;
        $translations = $this->translations->where('lang', $lang)->first();
    
        if (!$translations || empty($translations->$field)) {
            $translations = $this->translations->where('lang', 'en')->first();
        }

        return $translations != null ? $translations->$field : $this->$field;
    }
}
