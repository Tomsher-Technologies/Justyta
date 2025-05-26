<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPost extends Model
{
    protected $fillable = [
        'user_id', 'user_type', 'ref_no', 'type', 'emirate', 'job_posted_date', 'deadline_date', 'status'
    ];

    public function translations()
    {
        return $this->hasMany(JobPostTranslation::class);
    }

    public function translation($lang = null)
    {
        $lang = $lang ?: app()->getLocale();
        return $this->translations->where('lang', $lang)->first();
    }

     protected static function booted()
    {
        static::creating(function ($jobpost) {
            $jobpost->ref_no = self::generateReferenceNumber();
        });
    }

    public static function generateReferenceNumber()
    {
        $lastId = self::max('id') ?? 0;
        $nextId = $lastId + 1;
        return 'JOB-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }
}
