<?php

namespace App\Exports;

use App\Models\VendorSubscription;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class SubscriptionSalesExport implements FromCollection, WithHeadings, WithMapping, WithStyles

{
    public function __construct($filters = [])
    {
        $this->filters = $filters;
        $this->rowNumber = 0;
    }

    public function collection()
    {
        $query = VendorSubscription::with(['vendor', 'plan'])
            ->whereHas('vendor', function ($q) {
                $q->where('is_default', 0);
            });

        // Status filter
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        } else {
            $query->whereIn('status', ['active', 'expired']);
        }

        // Vendor filter
        if (!empty($this->filters['vendor_id'])) {
            $query->where('vendor_id', $this->filters['vendor_id']);
        }

        // Plan filter
        if (!empty($this->filters['plan_id'])) {
            $query->where('membership_plan_id', $this->filters['plan_id']);
        }

        // Date range filter
        if (!empty($this->filters['daterange'])) {
            $dates = explode(' to ', $this->filters['daterange']);
            if (count($dates) === 2) {
                $query->whereBetween('created_at', [
                    Carbon::parse($dates[0])->startOfDay(),
                    Carbon::parse($dates[1])->endOfDay()
                ]);
            }
        }

        return $query->orderBy('id', 'desc')->get();
    }


    public function headings(): array
    {
        $exportDate = Carbon::now()->format('d M Y h:i A');
        $sheetHeading = "Subscription Sales (Exported on {$exportDate})";

        $headingRow = [
            'S.No',
            'Law Firm',
            'Law Firm Contact Email',
            'Law Firm Contact Phone',
            'Law Firm Owner Name',
            'Law Firm Owner Email',
            'Law Firm Owner Phone',
            'Plan',
            'Amount (AED)',
            'Subscription Start Date',
            'Subscription End Date',
            'Status',
            'Created At',
        ];
        
        return [
            [$sheetHeading],
            $headingRow,
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;

        $data = [
            $this->rowNumber,
            $row->vendor?->law_firm_name ?? '—',
            $row->vendor?->law_firm_email ?? '—',
            $row->vendor?->law_firm_phone ?? '—',
            $row->vendor?->owner_name ?? '—',
            $row->vendor?->owner_email ?? '—',
            $row->vendor?->owner_phone ?? '—',
            $row->plan?->title ?? '—',
            number_format($row->amount, 2),
            $row->subscription_start ? \Carbon\Carbon::parse($row->subscription_start)->format('d-m-Y') : '-',
            $row->subscription_end ? \Carbon\Carbon::parse($row->subscription_end)->format('d-m-Y') : '-',
            ucfirst($row->status ?? '-'),
            $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('d-m-Y h:i A') : '-',
        ];

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        // Merge title row
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // Header row
        $sheet->getStyle('A2:M2')->getFont()->setBold(true);
        $sheet->getStyle('A2:M2')->getAlignment()->setHorizontal('center');

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // Global center alignment
        $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
            ->getAlignment()
            ->setHorizontal('center')
            ->setVertical('center');

        // ---- FIXED WIDTH ----
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(35);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(40);
        $sheet->getColumnDimension('F')->setWidth(35);
        $sheet->getColumnDimension('G')->setWidth(25);
        $sheet->getColumnDimension('H')->setWidth(25);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(25);
        $sheet->getColumnDimension('K')->setWidth(25);
        $sheet->getColumnDimension('L')->setWidth(20);
        $sheet->getColumnDimension('M')->setWidth(30);

        // ---- WORD WRAP ----
        $sheet->getStyle("B1:B{$highestRow}")
            ->getAlignment()
            ->setWrapText(true)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP)
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        $sheet->getStyle("E1:E{$highestRow}")
            ->getAlignment()
            ->setWrapText(true)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP)
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        // ---- AUTO ROW HEIGHT (CRITICAL) ----
        for ($row = 1; $row <= $highestRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(-1);
        }
    }


}

