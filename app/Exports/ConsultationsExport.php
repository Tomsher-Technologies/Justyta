<?php

namespace App\Exports;

use App\Models\Consultation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ConsultationsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithEvents
{
    protected $filters;
    protected $exportedAt;

    public function __construct($filters)
    {
        $this->filters = $filters;
        $this->exportedAt = Carbon::now()->format('Y-m-d H:i:s');
    }

    public function collection()
    {
        $query = Consultation::with([
            'user',
            'lawyer',
            'caseType.translations',
            'caseStage.translations',
            'languageValue.translations',
            'emirate.translations',
        ]);

        // Apply filters
        if (!empty($this->filters['lawyer_id'])) {
            $query->where('lawyer_id', $this->filters['lawyer_id']);
        }

        if (!empty($this->filters['consultation_type'])) {
            $query->where('consultant_type', $this->filters['consultation_type']);
        }

        if (!empty($this->filters['specialities'])) {
            $query->where('case_type', $this->filters['specialities']);
        }

        if (!empty($this->filters['language'])) {
            $query->where('language', $this->filters['language']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['daterange'])) {
            $dates = explode(' to ', $this->filters['daterange']);
            if (count($dates) == 2) {
                $query->whereBetween('created_at', [
                    Carbon::parse($dates[0])->startOfDay(),
                    Carbon::parse($dates[1])->endOfDay()
                ]);
            }
        }

        if (!empty($this->filters['keyword'])) {
            $keyword = $this->filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('ref_code', 'like', "%{$keyword}%")
                    ->orWhereHas('user', function ($userQuery) use ($keyword) {
                        $userQuery->where('name', 'like', "%{$keyword}%")
                                  ->orWhere('email', 'like', "%{$keyword}%")
                                  ->orWhere('phone', 'like', "%{$keyword}%");
                    });
            });
        }

        return $query->orderBy('id', 'desc')->where('request_success', 1)
            ->get()
            ->map(function ($consultation, $key) {
                return [
                    'Sl No.' => $key + 1,
                    'Reference Code' => $consultation->ref_code ?? '-',
                    'Status' => ucwords(str_replace('_', ' ', $consultation->status ?? '-')),
                    'User' => $consultation->user?->name ?? '-',
                    'Lawyer' => $consultation->lawyer?->full_name ?? '-',
                    'Applicant Type' => ucfirst($consultation->applicant_type ?? '-'),
                    'Litigation Type' => ucfirst($consultation->litigation_type ?? '-'),
                    'Consultant Type' => ucfirst($consultation->consultant_type ?? '-'),
                    'Case Type' => $consultation->caseType?->getTranslation('name', 'en') ?? '-',
                    'Case Stage' => $consultation->caseStage?->getTranslation('name', 'en') ?? '-',
                    'Language' => ucfirst($consultation->languageValue?->getTranslation('name', 'en') ?? '-'),
                    'Emirate' => $consultation->emirate?->getTranslation('name', 'en') ?? '-',
                    'Duration (mins)' => $consultation->duration ?? '-',
                    'Amount (AED)' => number_format($consultation->amount ?? 0, 2),
                    'Meeting Start' => $consultation->meeting_start_time ? Carbon::parse($consultation->meeting_start_time)->format('Y-m-d h:i A') : '-',
                    'Meeting End' => $consultation->meeting_end_time ? Carbon::parse($consultation->meeting_end_time)->format('Y-m-d h:i A') : '-',
                    'Date' => optional($consultation->created_at)->format('Y-m-d h:i A') ?? '-',
                ];
            });
    }

    public function headings(): array
    {
        return [
            ['Exported Date & Time: ' . Carbon::now()->format('d-m-Y h:i A')], // Row 1
            [
                'Sl No.',
                'Reference Code',
                'Status',
                'User',
                'Lawyer',
                'Applicant Type',
                'Litigation Type',
                'Consultant Type',
                'Case Type',
                'Case Stage',
                'Language',
                'Emirate',
                'Duration (mins)',
                'Amount (AED)',
                'Meeting Start',
                'Meeting End',
                'Date',
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Make main header row (row 2) bold
        $sheet->getStyle('A2:Q2')->getFont()->setBold(true);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Center alignment for specific columns (A,B,C,G,H,M,N,O,P,Q)
                $columnsToCenter = ['A', 'B', 'C','F', 'G', 'H','K','L', 'M', 'N', 'O', 'P', 'Q'];
                foreach ($columnsToCenter as $col) {
                    $sheet->getStyle("{$col}:{$col}")
                          ->getAlignment()
                          ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                          ->setVertical(Alignment::VERTICAL_CENTER);
                }

                // Merge and format first row (Export info)
                $sheet->mergeCells('A1:I1');
                $sheet->getStyle('A1')->getFont()->setBold(true);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
