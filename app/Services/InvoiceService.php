<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Invoice;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    public static function generate(Invoice $invoice, $user, $description)
    {
        $pdf = Pdf::loadView('emails.invoice', compact(
            'invoice', 'user', 'description'
        ));

        $path = 'invoices/' . $invoice->invoice_no . '.pdf';

        Storage::disk('public')->put($path, $pdf->output());
        $path = Storage::url($path);

        $invoice->update(['pdf_path' => $path]);

        $storagePath = str_replace('/storage/', 'public/', $path);
       
        return storage_path("app/{$storagePath}");
    }
}

