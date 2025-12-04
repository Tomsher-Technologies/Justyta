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
    protected $canViewSales;

    public function __construct($requests, $serviceName, $serviceSlug, $customFields = [], $canViewSales = false)
    {
        $this->requests = $requests;
        $this->customFields = $customFields;
        $this->serviceName = $serviceName;
        $this->serviceSlug = $serviceSlug;
        $this->canViewSales = $canViewSales;
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
                    ];

                    if ($this->canViewSales) {
                        $row['Admin Amount'] = number_format((float) $request->legalTranslation?->admin_amount, 2) ?? '0.00';
                        $row['Translator Amount'] = number_format((float) $request->legalTranslation?->translator_amount, 2) ?? '0.00';
                        $row['Delivery Amount'] = number_format((float) $request->legalTranslation?->delivery_amount, 2) ?? '0.00';
                        $row['Tax Amount'] = number_format((float) $request->legalTranslation?->tax, 2) ?? '0.00';
                        $row['Total Amount'] = number_format((float) $request->legalTranslation?->total_amount, 2) ?? '0.00';
                        $row['Paid Date'] = $request->paid_at ? date('d, M Y h:i A', strtotime($request->paid_at)) : '';
                    }

                    $row['User'] = $request->user?->name ?? '';
                    $row['Submitted At'] = date('d, M Y h:i A', strtotime($request->submitted_at));
                    $row['Translator'] = $request->legalTranslation?->assignedTranslator?->name ?? '';
                }else{
                    $row = [
                        'Sl No.' => $i+1,
                        'Reference Code' => $request->reference_code,
                        'Request Status' => ucfirst($request->status),
                        'Payment Status' => $paymentStatus,
                    ];

                    if ($this->canViewSales) {
                        $row['Total Amount'] = number_format((float) $request->amount, 2) ?? '0.00';
                        $row['Paid Date'] = $request->paid_at ? date('d, M Y h:i A', strtotime($request->paid_at)) : '';
                    }

                    $row['User'] = $request->user?->name ?? '';
                    $row['Submitted At'] = date('d, M Y h:i A', strtotime($request->submitted_at));

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
                $base = [
                    'Sl No.',
                    'Reference Code',
                    'Request Status',
                    'Payment Status',
                ];

                if ($this->canViewSales) {
                    $base[] = 'Admin Amount';
                    $base[] = 'Translator Amount';
                    $base[] = 'Delivery Amount';
                    $base[] = 'Tax Amount';
                    $base[] = 'Total Amount';
                    $base[] = 'Paid Date';
                }

                $base[] = 'User';
                $base[] = 'Submitted At';
                $base[] = 'Translator';
                return array_merge($base, $this->customFields);
            }
            
            $base = [
                'Sl No.',
                'Reference Code',
                'Request Status',
                'Payment Status',
            ];

            if ($this->canViewSales) {
                $base[] = 'Total Amount';
                $base[] = 'Paid Date';
            }

            $base[] = 'User';
            $base[] = 'Submitted At';

            return array_merge($base, $this->customFields);

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

        $styles = [
            1 => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ],
            2 => [
                'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
            ],
            'A' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];

        $amountColumn = $this->getAmountColumnLetter();
        if ($amountColumn) {
            $styles[$amountColumn] = [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ];
        }

        return $styles;
    }


    private function getAmountColumnLetter()
    {
        $headings = $this->headings();
        $index = array_search('Total Amount', $headings, true);

        // If "Total Amount" is not present, do nothing
        if ($index === false) {
            return null;
        }

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
                    'Admin Amount' => 15,
                    'Translator Amount' => 15,
                    'Delivery Amount' => 15,
                    'Tax Amount' => 15,
                    'Total Amount' => 15,
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

                $colIndex = 1;

                foreach ($headings as $heading) {
                    $width = $widthsByHeading[$heading] ?? 20;
                    $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                    $sheet->getColumnDimension($column)->setWidth($width);

                    $colIndex++;
                }


                // Optional: enable wrap text on whole sheet or specific range
                $sheet->getStyle('A1:' . $column . $sheet->getHighestRow())
                    ->getAlignment()->setWrapText(true);
                $sheet->getRowDimension(1)->setRowHeight(30);

                $columnsToCenter = ['A', 'B', 'C', 'D', 'E','F', 'G', 'H','I', 'J',  'K','L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S','T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
                foreach ($columnsToCenter as $col) {
                    $sheet->getStyle("{$col}:{$col}")
                          ->getAlignment()
                          ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                          ->setVertical(Alignment::VERTICAL_CENTER);
                }
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
