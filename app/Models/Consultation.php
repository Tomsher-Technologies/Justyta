<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    protected $fillable = [
        'user_id','applicant_type','litigation_type','consultant_type',
        'emirate_id','you_represent','case_type','case_stage','language',
        'duration','amount','lawyer_id','status','zoom_meeting_id','zoom_join_url'
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

    public function language()
    {
        return $this->belongsTo(DropdownOption::class, 'language');
    }

    public function attempts() {
        return $this->hasMany(ConsultationLawyerAttempt::class);
    }
}

