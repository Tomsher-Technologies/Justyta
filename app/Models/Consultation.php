<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    protected $fillable = [
        'user_id','ref_code','applicant_type','litigation_type','consultant_type',
        'emirate_id','you_represent','case_type','case_stage','language',
        'duration','amount','lawyer_id','status','zoom_meeting_id','zoom_join_url','meeting_end_time','is_completed'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function lawyer() {
        return $this->belongsTo(Lawyer::class);
    }

    public function emirate() {
        return $this->belongsTo(Emirate::class);
    }

    public function caseType()
    {
        return $this->belongsTo(DropdownOption::class, 'case_type');
    }

    public function youRepresent()
    {
        return $this->belongsTo(DropdownOption::class, 'you_represent');
    }

    public function caseStage()
    {
        return $this->belongsTo(DropdownOption::class, 'case_stage');
    }

    public function languageValue()
    {
        return $this->belongsTo(DropdownOption::class, 'language');
    }

    public function assignments() {
        return $this->hasMany(ConsultationAssignment::class);
    }

    protected static function booted()
    {
        static::creating(function ($lawyer) {
            $lawyer->ref_code = self::generateReferenceNumber();
        });
    }

    public static function generateReferenceNumber()
    {
        $prefix = 'ONC';

        $lastCode = self::whereNotNull('ref_code')
            ->orderBy('id', 'desc')
            ->value('ref_code');

        $nextNumber = 1;
        if ($lastCode) {
            preg_match('/(\d+)$/', $lastCode, $matches);
            $nextNumber = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        }

        return $prefix . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}

