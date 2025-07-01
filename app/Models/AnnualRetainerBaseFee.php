<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnualRetainerBaseFee extends Model
{
    protected $fillable = [
        'calls_per_month',
        'visits_per_year',
        'service_fee',
        'govt_fee',
        'tax',
        'base_total',
    ];

    public function installments()
    {
        return $this->hasMany(AnnualRetainerInstallment::class, 'base_fee_id');
    }
}
