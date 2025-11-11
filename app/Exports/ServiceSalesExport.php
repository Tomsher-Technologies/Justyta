<?php

namespace App\Exports;

use App\Models\ServiceRequest;
use App\Models\Consultation;
use App\Models\Service;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class ServiceSalesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $serviceSlug;
    protected $dates;
    protected $rowNumber = 0;
    protected $serviceName;

    public function __construct($serviceSlug = null, $dates = null)
    {
        $this->serviceSlug = $serviceSlug;
        $this->dates = $dates;

        $service = Service::where('slug', $serviceSlug)->first();
        $this->serviceName = $service->name ?? 'Service Requests';
    }

    public function collection()
    {
        if ($this->serviceSlug === 'online-live-consultancy') {
            $query = Consultation::with(['user', 'lawyer'])
                ->where('request_success', 1);

            if ($this->dates && count($this->dates) === 2) {
                $query->whereBetween('created_at', [
                    Carbon::parse($this->dates[0])->startOfDay(),
                    Carbon::parse($this->dates[1])->endOfDay(),
                ]);
            }

            return $query->orderBy('id', 'desc')->get();
        }

        $query = ServiceRequest::with(['user', 'service', 'legalTranslation.assignedTranslator'])
            ->where('request_success', 1);

        if ($this->serviceSlug) {
            $query->where('service_slug', $this->serviceSlug);
        }

        if ($this->dates && count($this->dates) === 2) {
            $query->whereBetween('submitted_at', [
                Carbon::parse($this->dates[0])->startOfDay(),
                Carbon::parse($this->dates[1])->endOfDay(),
            ]);
        }

        return $query->orderBy('id', 'desc')->get();
    }

    public function headings(): array
    {
        $exportDate = Carbon::now()->format('d M Y h:i A');
        $serviceHeading = "{$this->serviceName} (Exported on {$exportDate})";

        $headingRow = [
            'S.No',
            'Reference Code',
            'User Name',
            'User Email',
            'User Phone',
        ];

        if ($this->serviceSlug === 'online-live-consultancy') {
            $headingRow = array_merge($headingRow, [
                'Lawyer Name',
                'Consultation Type',
                'Status',
                'Duration (mins)',
                'Amount (AED)',
                'Created At',
            ]);
        } else {
            if ($this->serviceSlug === 'legal-translation') {
                $headingRow[] = 'Translator Name';
            }

            $headingRow = array_merge($headingRow, [
                'Amount (AED)',
                'Payment Status',
                'Request Status',
                'Submitted At',
            ]);
        }

        // The export will have 2 header rows
        return [
            [$serviceHeading],
            $headingRow,
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;

        if ($this->serviceSlug === 'online-live-consultancy') {
            return [
                $this->rowNumber,
                $row->ref_code,
                $row->user?->name ?? '—',
                $row->user?->email ?? '—',
                $row->user?->phone ?? '—',
                $row->lawyer?->full_name ?? '—',
                ucfirst($row->consultant_type ?? '-'),
                ucfirst(str_replace('_', ' ', $row->status ?? '-')),
                $row->duration ?? 0,
                number_format($row->amount, 2),
                $row->created_at->format('d, M Y h:i A'),
            ];
        }

        $data = [
            $this->rowNumber,
            $row->reference_code ?? '',
            $row->user?->name ?? '—',
            $row->user?->email ?? '—',
            $row->user?->phone ?? '—',
        ];

        if ($this->serviceSlug === 'legal-translation') {
            $data[] = $row->legalTranslation?->assignedTranslator?->name ?? '—';
        }

        if($row->payment_status === 'success') {
            $paymentStatus = 'Paid';
        }elseif($row->payment_status === 'partial') {
            $paymentStatus = 'Partial';
        }else{
            $paymentStatus = 'Unpaid';
        }
        $data[] = number_format($row->amount ?? 0, 2);
        $data[] = $paymentStatus ?? '-';
        $data[] = ucfirst($row->status ?? '-');
        $data[] = $row->submitted_at ? $row->submitted_at->format('d, M Y h:i A') : '';

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        // Make the first row (service info) bold and merged
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // Make heading row bold and centered
        $sheet->getStyle('A2:J2')->getFont()->setBold(true);
        $sheet->getStyle('A2:J2')->getAlignment()->setHorizontal('center');

        // Center align all cells
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
            ->getAlignment()
            ->setHorizontal('center')
            ->setVertical('center');
    }
}
