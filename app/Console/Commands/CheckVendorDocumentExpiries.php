<?php

namespace App\Console\Commands;

use App\Models\Vendor;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Notifications\VendorDocumentExpiryNotification;

class CheckVendorDocumentExpiries extends Command
{
    protected $signature = 'check:vendor-expiry';
    protected $description = 'Notify vendors about expiring documents';

    public function handle()
    {
        $vendors = Vendor::whereHas('user', function ($query) {
                        $query->where('banned', 0);
                    })->with('user')->get();
        $now = Carbon::now();
       
        foreach ($vendors as $vendor) {
            $documents = [
                'trade_license_expiry' => $vendor->trade_license_expiry,
                'emirates_id_expiry' => $vendor->emirates_id_expiry,
                'residence_visa_expiry' => $vendor->residence_visa_expiry,
                'passport_expiry' => $vendor->passport_expiry,
                'card_of_law_expiry' => $vendor->card_of_law_expiry,
            ];
            foreach ($documents as $name => $date) {

                if ($date && Carbon::parse($date)->isSameDay(now()->addDays(3))) {
                    $doc = '';
                    if($name === 'trade_license_expiry' ){
                        $doc = 'Trade License';
                    }elseif($name === 'emirates_id_expiry' ){
                        $doc = 'Emirates ID';
                    }elseif($name === 'residence_visa_expiry' ){
                        $doc = 'Residence Visa';
                    }elseif($name === 'passport_expiry' ){
                        $doc = 'Passport';
                    }elseif($name === 'card_of_law_expiry' ){
                        $doc = 'Card of Law';
                    }
                    $vendor->user->notify(new VendorDocumentExpiryNotification($doc, Carbon::parse($date)));
                }
            }
        }

        $this->info('Vendor expiry notifications checked.');
    }
}
