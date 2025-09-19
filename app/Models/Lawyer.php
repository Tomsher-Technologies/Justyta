<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lawyer extends Model
{
    protected $fillable = [
        'lawfirm_id','is_busy', 'full_name', 'email', 'phone', 'gender', 'date_of_birth', 'emirate_id', 'nationality', 'years_of_experience', 'profile_photo', 'emirate_id_front', 'emirate_id_back', 'emirate_id_expiry', 'passport', 'passport_expiry', 'residence_visa', 'residence_visa_expiry', 'bar_card', 'bar_card_expiry', 'practicing_lawyer_card', 'practicing_lawyer_card_expiry','working_hours'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lawfirm()
    {
        return $this->belongsTo(Vendor::class,'lawfirm_id');
    }
    public function emirate()
    {
        return $this->belongsTo(Emirate::class,'emirate_id');
    }

    public function yearsExperienceOption()
    {
        return $this->belongsTo(DropdownOption::class, 'years_of_experience');
    }

    public function nationalityCountry()
    {
        return $this->belongsTo(Country::class,'nationality');
    }
 
    public function dropdownOptions()
    {
        return $this->belongsToMany(DropdownOption::class, 'lawyer_dropdown_options')
                    ->withPivot('type');
    }

    public function specialities()
    {
        return $this->hasMany(LawyerDropdownOption::class)
                    ->where('type', 'specialities')->with(['dropdownOption']); 
    }

    public function languages()
    {
        return $this->hasMany(LawyerDropdownOption::class)
                    ->where('type', 'languages')->with(['dropdownOption']); 
    }

    protected static function booted()
    {
        static::creating(function ($lawyer) {
            $lawyer->ref_no = self::generateReferenceNumber();
        });
    }

    public static function generateReferenceNumber()
    {
        $prefix = 'LFM';

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
        return $this->hasMany(LawyerTranslation::class);
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
