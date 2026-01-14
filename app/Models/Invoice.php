<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_no', 'amount', 'tax', 'total',
        'currency', 'pdf_path', 'paid_at','billable_type','billable_id'
    ];

    public function billable()
    {
        return $this->morphTo();
    }
}
