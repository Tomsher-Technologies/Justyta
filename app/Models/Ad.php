<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $fillable = [
        'title', 'page_id', 'start_date', 'end_date', 'status',
        'customer_name', 'customer_email', 'customer_phone',
        'cta_text', 'cta_url', 'impressions', 'clicks',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => 'boolean',
    ];

    public function files()
    {
        return $this->hasMany(AdFile::class);
    }

    public function page()
    {
        return $this->belongsTo(AdsPage::class, 'page_id');
    }
}
