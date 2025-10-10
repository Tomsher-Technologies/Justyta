<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Consultation;

class ReleaseExpiredReservations extends Command
{
    protected $signature = 'consultations:release-expired';
    protected $description = 'Release lawyers from unpaid reserved consultations';

    public function handle()
    {
        $expired = Consultation::where('status', 'reserved')
            ->where('created_at', '<', now()->subMinutes(15))
            ->get();

        foreach ($expired as $consultation) {
            unreserveLawyer($consultation->lawyer_id);
            $consultation->delete();
        }

        $this->info('Expired reservations released successfully.');
    }
}
