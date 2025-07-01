<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    
    protected $fillable = [
        'job_post_id', 'user_id', 'full_name', 'email', 'phone', 'position', 'resume_path'
    ];

    public function job()
    {
        return $this->belongsTo(JobPost::class);
    }

}
