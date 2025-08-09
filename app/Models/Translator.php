<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Translator extends Model
{
    protected $fillable = [
        'user_id', 'ref_no', 'name', 'email', 'phone', 'company_name',
        'emirate_id', 'image', 'country', 'trade_license', 'trade_license_expiry',
        'emirates_id_front', 'emirates_id_back', 'emirates_id_expiry',
        'residence_visa', 'residence_visa_expiry', 'passport', 'passport_expiry','type','is_default'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->whereHas('user', fn ($q) => $q->where('banned', 0));
    }

     public function emirate()
    {
        return $this->belongsTo(Emirate::class,'emirate_id');
    }

    public function nationality()
    {
        return $this->belongsTo(Country::class,'country');
    }

    public function dropdownOptions()
    {
        return $this->belongsToMany(DropdownOption::class, 'translator_dropdown_options');
    }

    public function languages()
    {
        return $this->belongsToMany(DropdownOption::class, 'translator_dropdown_options')
                    ->wherePivot('type', 'languages')
                    ->with('translations'); 
    }

    
    protected static function booted()
    {
        static::creating(function ($translator) {
            $translator->ref_no = self::generateReferenceNumber();
        });
    }

    public static function generateReferenceNumber()
    {
        $lastId = self::max('id') ?? 0;
        $nextId = $lastId + 1;
        return 'TR-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }

    public function defaultHistory()
    {
        return $this->hasMany(DefaultTranslatorHistory::class);
    }

    public function languageRates()
    {
        return $this->hasMany(TranslatorLanguageRate::class)->orderBy('id');
    }

}
