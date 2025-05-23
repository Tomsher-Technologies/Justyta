<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPost extends Model
{
    protected $fillable = [
        'type', 'salary_type', 'job_posted_date', 'deadline_date', 'user_id','user_type'
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
}
