<?php

namespace App\Exports;

use App\Models\ServiceRequest;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\Color;

class ServiceRequestExport implements FromArray, WithHeadings, WithStyles, WithEvents
{
    protected $requests;
    protected $customFields;
    protected $serviceName;
    protected $serviceSlug;

    public function __construct($requests, $serviceName, $serviceSlug, $customFields = [])
    {
        $this->requests = $requests;
        $this->customFields = $customFields;
        $this->serviceName = $serviceName;
        $this->serviceSlug = $serviceSlug;
    }

    public function array(): array
    {
        $data = [];

        foreach ($this->requests as $i => $request) {
            $paymentStatus = '';
            if($request->payment_status === 'pending'){
                $paymentStatus = 'Unpaid';
            }elseif($request->payment_status === 'success'){
                $paymentStatus = 'Paid';
            }elseif($request->payment_status === 'failed'){
                $paymentStatus = 'Failed';
            }

            if(in_array($request->service_slug, ['expert-report','annual-retainer-agreement','legal-translation','immigration-requests','request-submission']) ){
                if($request->service_slug === 'legal-translation'){
                    $row = [
                        'Sl No.' => $i+1,
                        'Reference Code' => $request->reference_code,
                        'Request Status' => ucfirst($request->status),
                        'Payment Status' => $paymentStatus,
                        'Amount' => number_format((float) $request->amount, 2) ?? '0.00',
                        'Paid Date ' => $request->paid_at ? date('d, M Y h:i A', strtotime($request->paid_at)) : '',
                        'User' => $request->user?->name ?? '',
                        'Submitted At' => date('d, M Y h:i A', strtotime($request->submitted_at)),
                        'Translator' => $request->legalTranslation?->assignedTranslator?->name ?? '',
                    ];
                }else{
                    $row = [
                        'Sl No.' => $i+1,
                        'Reference Code' => $request->reference_code,
                        'Request Status' => ucfirst($request->status),
                        'Payment Status' => $paymentStatus,
                        'Amount' => number_format((float) $request->amount, 2) ?? '0.00',
                        'Paid Date ' => $request->paid_at ? date('d, M Y h:i A', strtotime($request->paid_at)) : '',
                        'User' => $request->user?->name ?? '',
                        'Submitted At' => date('d, M Y h:i A', strtotime($request->submitted_at)),
                    ];
                }
                
            }else{
                $row = [
                    'Sl No.' => $i+1,
                    'Reference Code' => $request->reference_code,
                    'Request Status' => ucfirst($request->status),
                    'User' => $request->user?->name ?? '',
                    'Submitted At' => date('d, M Y h:i A', strtotime($request->submitted_at)),
                ];
            }
            
            $relation = getServiceRelationName($request->service_slug);

            if (!$relation || !$request->relationLoaded($relation)) {
                $request->load($relation);
            }

            $serviceDetails = $request->$relation;
            $translatedData = getServiceHistoryTranslatedFields($request->service_slug, $serviceDetails, 'en');

            // Merge with service-specific fields
            foreach ($this->customFields as $field => $label) {
                $value = $translatedData[$field] ?? ''; // `details` = relation or attribute

                if (is_array($value)) {
                    $row[$label] = implode("\n", $value);
                }elseif (Str::startsWith($value, '[') && Str::endsWith($value, ']')) {
                    $decodedValue = json_decode($value, true);

                    if (is_array($decodedValue)) {
                        $row[$label] = implode(', ', $decodedValue);
                    } else {
                        $row[$label] = $value;
                    }
                }else {
                    $row[$label] = $value;
                }
            }

            $data[] = $row;
        }

        return $data;
    }

    public function headings(): array
    {
         if(in_array($this->serviceSlug, ['expert-report','annual-retainer-agreement','legal-translation','immigration-requests','request-submission']) ){
            if($this->serviceSlug === 'legal-translation'){
                return array_values(array_merge([
                    'Sl No.',
                    'Reference Code',
                    'Request Status',
                    'Payment Status',
                    'Amount',
                    'Paid Date',
                    'User',
                    'Submitted At',
                    'Translator',
                ], $this->customFields));
            }
            return array_values(array_merge([
                'Sl No.',
                'Reference Code',
                'Request Status',
                'Payment Status',
                'Amount',
                'Paid Date',
                'User',
                'Submitted At',
            ], $this->customFields));

         }else{
            return array_values(array_merge([
                'Sl No.',
                'Reference Code',
                'Request Status',
                'User',
                'Submitted At',
            ], $this->customFields));
         }
    }
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:Z1000')->getAlignment()->setWrapText(true);
        
        return [
            1 => [ // First row (exported date + service name)
                // 'font' => [
                //     'bold' => true,
                //     'size' => 13,
                //     // 'color' => ['rgb' => '000000'],
                // ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
            2 => [
                'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
                // 'alignment' => ['horizontal' => 'center'],
            ],
            
            'A' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],

            // Center align "Amount" = You need to find the column letter for it dynamically if headings change
            $this->getAmountColumnLetter() => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],
        ];
    }

    private function getAmountColumnLetter()
    {
        $headings = $this->headings();
        $index = array_search('Amount', $headings);

        // Convert index to column letter (e.g., 0 = A, 1 = B...)
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
    }
    
    public function registerEvents(): array
    {
        return [
            
            AfterSheet::class => function($event) {
                $sheet = $event->sheet->getDelegate();

                // Define widths for specific headings
                $widthsByHeading = [
                    'Sl No.' => 10,
                    'Reference Code' => 25,
                    'Request Status' => 15,
                    'Payment Status' => 15,
                    'Law firm' => 30,
                    'Address' => 30,
                    'Zone' => 30,
                    'Licence Type' => 30,
                    'Licence Activity' => 30,
                    'Amount' => 15,
                    'About Case' => 60,
                    'About Deal' => 60,
                    'Documents' => 50,
                    'Memo' => 50,
                    'Trade License' => 50,
                    'Emirates ID' => 50,
                    'CV' => 50,
                    'Certificates' => 50,
                    'Passport' => 50,
                    'Photo' => 50,
                    'Account Statement' => 50,
                    'Appointer ID' => 50,
                    'Authorized ID' => 50,
                    'Authorized Passport' => 50,
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
                $serviceName = $this->serviceName;

                $richText = new RichText();
                $text1 = $richText->createTextRun('Service: ');
                $text1->getFont()->setBold(true)->setSize(12);
                $serviceRun = $richText->createTextRun($serviceName);
                $serviceRun->getFont()
                    ->setItalic(true)
                    ->setBold(true)
                    ->setSize(12)
                    ->setColor(new Color(Color::COLOR_DARKRED));
                $text3 = $richText->createTextRun(' | Exported on: ');
                $text3->getFont()->setBold(true)->setSize(12);
                $dateRun = $richText->createTextRun($timestamp);
                $dateRun->getFont()
                    ->setItalic(true)
                    ->setBold(true)
                    ->setSize(12)
                    ->setColor(new Color('00008B'));
                $event->sheet->setCellValue('A1', $richText);
                $event->sheet->mergeCells('A1:I1');
            },

        ];
    }
}
