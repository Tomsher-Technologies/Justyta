<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestDebtCollection extends Model
{
    protected $table = 'request_debt_collections';

    protected $fillable = [
        'service_request_id', 'user_id', 'applicant_type', 'emirate_id', 'debt_type', 'debt_amount', 'debt_category', 'eid', 'documents', 'trade_license'
    ];

    protected $casts = [
        'documents' => 'array',
        'eid' => 'array',
        'trade_license' => 'array',
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function emirate()
    {
        return $this->belongsTo(Emirate::class);
    }

    public function debtType()
    {
        return $this->belongsTo(DropdownOption::class, 'debt_type');
    }

    public function debtCategory()
    {
        return $this->belongsTo(DropdownOption::class, 'debt_category');
    }
}
