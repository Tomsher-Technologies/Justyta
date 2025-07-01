<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnualRetainerInstallment extends Model
{
    protected $fillable = [
        'base_fee_id',
        'installments',
        'extra_percent',
        'final_total',
    ];

    public function baseFee()
    {
        return $this->belongsTo(AnnualRetainerBaseFee::class, 'base_fee_id');
    }
}

