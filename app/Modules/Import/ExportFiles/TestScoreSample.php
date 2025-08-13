<?php

namespace App\Modules\Import\ExportFiles;

use Maatwebsite\Excel\Concerns\{Exportable,WithEvents,FromCollection,ShouldAutoSize,WithHeadings};
use Maatwebsite\Excel\Events\AfterSheet;

class TestScoreSample implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $headings = [];//['Submission ID'];
    }

    public function registerEvents(): array
    {
    	return [
    		AfterSheet::class    => function(AfterSheet $event) {
    			$event->sheet->getDelegate()->getRowDimension(1)->setRowHeight(25);
    			$styleArray = [
                    'font' => [
                        'family' => 'Open Sans',
                        'size' =>  13,
                        'bold' => true,
                        ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ];
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:Z1')->applyFromArray($styleArray);
    		}
        ];
    }

}
