<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConsultationDuration;

class ConsultationDurationSeeder extends Seeder
{
    public function run(): void
    {
        $durations = [15, 30, 45, 60];
        $types = ['normal', 'vip'];

        foreach ($types as $type) {
            foreach ($durations as $duration) {
                ConsultationDuration::firstOrCreate(
                    [
                        'type' => $type,
                        'duration' => $duration,
                    ],
                    [
                        'amount' => 0.00,
                    ]
                );
            }
        }
    }
}

