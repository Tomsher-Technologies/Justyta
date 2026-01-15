<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Consultation;
use App\Models\ConsultationAssignment;
use Carbon\Carbon;

class AutoCancelConsultations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consultation:auto-cancel';
    protected $description = 'Auto cancel consultations not accepted within 5 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $oneMinuteAgo = Carbon::now()->subMinute();

        $pending = Consultation::whereIn('status', ['reserved','waiting_lawyer'])
                                ->where('created_at', '<=', $oneMinuteAgo)
                                ->get();

        foreach ($pending as $consultation) {

            $assignment = ConsultationAssignment::where('consultation_id', $consultation->id)
                                                ->where('status', 'assigned')
                                                ->where('created_at', '<=', $oneMinuteAgo)
                                                ->first();

            if (!$assignment) {
                continue;
            }

            $assignment->update([
                'status' => 'rejected',
                'auto_rejection' => 1
            ]);

            unreserveLawyer($assignment->lawyer_id);

            $nextLawyer = findBestFitLawyer($consultation);

            if ($nextLawyer) {
                assignLawyer($consultation, $nextLawyer->id);
                $consultation->update([
                    'status' => 'waiting_lawyer'
                ]);

            } else {
                $consultation->update([
                    'status' => 'cancelled'
                ]);
            }
        }

        return Command::SUCCESS;
    }
}
