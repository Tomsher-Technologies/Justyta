<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AnnualRetainerBaseFee;
use App\Models\AnnualRetainerInstallment;

class AnnualRetainerFeeSeeder extends Seeder
{
    public function run()
    {
        $calls = [1, 2, 3, 4, 5];
        $visits = [0, 1, 2, 3, 4];
        $installments = [1, 2, 4];

        foreach ($calls as $call) {
            foreach ($visits as $visit) {
                $serviceFee = $call * 100; // Example logic
                $govtFee = $visit * 50;
                $tax = ($serviceFee + $govtFee) * 0.05;
                $baseTotal = $serviceFee + $govtFee + $tax;

                $base = AnnualRetainerBaseFee::create([
                    'calls_per_month' => $call,
                    'visits_per_year' => $visit,
                    'service_fee' => $serviceFee,
                    'govt_fee' => $govtFee,
                    'tax' => $tax,
                    'base_total' => $baseTotal,
                ]);

                foreach ($installments as $inst) {
                    AnnualRetainerInstallment::create([
                        'base_fee_id' => $base->id,
                        'installments' => $inst,
                        'extra_percent' => 0,
                        'final_total' => $baseTotal,
                    ]);
                }
            }
        }
    }
}
