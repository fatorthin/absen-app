<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class AttendanceExport implements FromCollection, WithHeadings, WithStyles, WithTitle
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
        
        foreach ($this->attendanceData as $index => $student) {
            $row = [
                $index + 1,
                $student['name'],
                $student['class'],
            ];
            
            foreach ($this->eventDates as $date) {
                $status = $student['attendance'][$date] ?? null;
                $statusDisplay = null;
                
                if ($status === 'hadir') {
                    $statusDisplay = 'H';  // Attendance
                } elseif ($status === 'alfa') {
                    $statusDisplay = 'A';  // Alpha
                } elseif ($status === 'izin') {
                    $statusDisplay = 'I';  // Izin
                }
                
                $row[] = $statusDisplay;
            }
            
            // Add summary columns
            $row[] = $student['summary']['hadir'];
            $row[] = $student['summary']['alfa'];
            $row[] = $student['summary']['izin'];
            
            $data->push($row);
        }
        
        return $data;
    }

    public function headings(): array
    {
        $headers = [
            'No',
            'Nama',
            'Kelas',
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
        $sheet->getColumnDimension('A')->setWidth(5);  // No
        $sheet->getColumnDimension('B')->setWidth(25); // Nama
        $sheet->getColumnDimension('C')->setWidth(10); // Kelas
        
        // Date columns
        for ($i = 0; $i < count($this->eventDates); $i++) {
            $column = chr(68 + $i); // Start from column D
            if (68 + $i > 90) {
                // For columns after Z (AA, AB, etc.)
                $column = 'A' . chr(65 + ($i - 23));
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
        return "Rekap Kehadiran - {$this->monthName} {$this->year}";
    }
} 