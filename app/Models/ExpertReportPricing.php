<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpertReportPricing extends Model
{
    protected $table = 'expert_report_pricing';

    protected $fillable = [
        'litigation_type',
        'expert_report_type_id',
        'language_id',
        'admin_fee',
        'status'
    ];

    protected $appends = ['vat', 'total'];

    public function reportType()
    {
        return $this->belongsTo(DropdownOption::class, 'expert_report_type_id');
    }

    public function language()
    {
        return $this->belongsTo(DropdownOption::class, 'language_id');
    }

    public function getVatAttribute()
    {
        return round($this->admin_fee * 0.05, 2);
    }

    public function getTotalAttribute()
    {
        return round($this->admin_fee + $this->vat, 2);
    }
}
