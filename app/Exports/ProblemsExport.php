<?php

namespace App\Exports;

use App\Models\ProblemReport;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Str;
use Illuminate\Http\Request; 
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Carbon\Carbon;

class ProblemsExport implements FromArray, WithHeadings, WithStyles, WithEvents
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function array(): array
    {
        $query = ProblemReport::query()->orderBy('id', 'desc');

        if ($this->request->filled('daterange')) {
            $dates = explode(' to ', $this->request->daterange);
            if (count($dates) === 2) {
                $query->whereBetween('created_at', [
                    Carbon::parse($dates[0])->startOfDay(),
                    Carbon::parse($dates[1])->endOfDay()
                ]);
            }
        }

        return $query->get([
                    'id', 'user_id', 'subject', 'message','image', 'created_at'
                ])->map(function ($prbm, $index) {
                    return [
                        'Sl No'      => $index+1,
                        'Name'       => $prbm->user?->name,
                        'Email'      => $prbm->user?->email,
                        'Phone'      => $prbm->user?->phone,
                        'Subject'    => $prbm->subject,
                        'Message'    => $prbm->message,
                        'Date'  => \Carbon\Carbon::parse($prbm->created_at)->format('d-m-Y h:i A'),
                    ];
                })->toArray();
    }

    public function headings(): array
    {
        return ['Sl No.', 'Name', 'Email', 'Phone', 'Subject', 'Message', 'Date'];
    }

     public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:Z1000')->getAlignment()->setWrapText(true);
        
        return [
            1 => [ 
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
            2 => [
                'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
            ],
            
            'A' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],

        ];
    }

    public function registerEvents(): array
    {
        return [
            
            AfterSheet::class => function($event) {
                $sheet = $event->sheet->getDelegate();

                // Define widths for specific headings
                $widthsByHeading = [
                    'Sl No.' => 10,
                    'Name' => 25,
                    'Email' => 20,
                    'Phone' => 20,
                    'Subject' => 50,
                    'Message' => 70,
                    'Date' => 30
                ];

                $headings = $this->headings(); // your headings array

                foreach ($headings as $index => $heading) {
                    $width = $widthsByHeading[$heading] ?? 20;  // default width 20 if not defined
                    $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
                    $sheet->getColumnDimension($column)->setWidth($width);
                }

                // Optional: enable wrap text on whole sheet or specific range
                $sheet->getStyle('A1:' . $column . $sheet->getHighestRow())
                    ->getAlignment()->setWrapText(true);
                $sheet->getRowDimension(1)->setRowHeight(30);
            },
            BeforeSheet::class => function(BeforeSheet $event) {
                $timestamp = now()->format('d M Y h:i A');
               
                $richText = new RichText();
               
                $text3 = $richText->createTextRun('Exported on: ');
                $text3->getFont()->setBold(true)->setSize(12);
                $dateRun = $richText->createTextRun($timestamp);
                $dateRun->getFont()
                    ->setItalic(true)
                    ->setBold(true)
                    ->setSize(12)
                    ->setColor(new Color('00008B'));
                $event->sheet->setCellValue('A1', $richText);
                $event->sheet->mergeCells('A1:G1');
            },

        ];
    }
}
