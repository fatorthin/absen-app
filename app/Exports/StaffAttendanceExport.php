<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class StaffAttendanceExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $attendanceData;
    protected $eventDates;
    protected $monthName;
    protected $year;

    public function __construct($attendanceData, $eventDates, $monthName, $year)
    {
        $this->attendanceData = $attendanceData;
        $this->eventDates = $eventDates;
        $this->monthName = $monthName;
        $this->year = $year;
    }

    public function collection()
    {
        $data = new Collection();
        
        foreach ($this->attendanceData as $index => $staff) {
            $row = [
                $index + 1,
                $staff['name'],
                $staff['role'],
                $staff['group'],
            ];
            
            foreach ($this->eventDates as $date) {
                $status = $staff['attendance'][$date] ?? null;
                $statusDisplay = null;
                
                if ($status === 'hadir') {
                    $statusDisplay = 'H';  // Hadir
                } elseif ($status === 'alfa') {
                    $statusDisplay = 'A';  // Alfa
                } elseif ($status === 'izin') {
                    $statusDisplay = 'I';  // Izin
                }
                
                $row[] = $statusDisplay;
            }
            
            // Add summary columns
            $row[] = $staff['summary']['hadir'];
            $row[] = $staff['summary']['alfa'];
            $row[] = $staff['summary']['izin'];
            
            $data->push($row);
        }
        
        return $data;
    }

    public function headings(): array
    {
        $headers = [
            'No',
            'Nama',
            'Jabatan',
            'Kelompok',
        ];
        
        foreach ($this->eventDates as $date) {
            $headers[] = $date;
        }
        
        // Add summary headers
        $headers[] = 'Hadir';
        $headers[] = 'Alfa';
        $headers[] = 'Izin';
        
        return $headers;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'E9ECEF',
                ],
            ],
        ]);
        
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);   // No
        $sheet->getColumnDimension('B')->setWidth(25);  // Nama
        $sheet->getColumnDimension('C')->setWidth(15);  // Jabatan
        $sheet->getColumnDimension('D')->setWidth(15);  // Kelompok
        
        // Date columns
        for ($i = 0; $i < count($this->eventDates); $i++) {
            $column = chr(69 + $i); // Start from column E (after A,B,C,D)
            if (69 + $i > 90) {
                // For columns after Z (AA, AB, etc.)
                $column = 'A' . chr(65 + ($i - 22));
            }
            $sheet->getColumnDimension($column)->setWidth(4);
        }
        
        // Summary columns - calculate the letters for the last 3 columns
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestColumn());
        $summaryColumns = [
            \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($highestColumnIndex - 2),
            \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($highestColumnIndex - 1),
            \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($highestColumnIndex)
        ];
        
        foreach ($summaryColumns as $column) {
            $sheet->getColumnDimension($column)->setWidth(8);
            $sheet->getStyle($column . '1:' . $column . $sheet->getHighestRow())->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'E9ECEF',
                    ],
                ],
            ]);
        }
        
        return $sheet;
    }

    public function title(): string
    {
        return "Rekap Kehadiran Staff - {$this->monthName} {$this->year}";
    }
} 