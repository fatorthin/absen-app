<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\Report;
use App\Models\EventStaff;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AttendanceChart extends ChartWidget
{
    protected static ?string $heading = 'Rekap Kehadiran 7 Hari Terakhir';
    
    protected static ?string $pollingInterval = '30s';
    
    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        // Get dates for the last 7 days
        $dates = collect();
        $labels = collect();
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dates->push($date->format('Y-m-d'));
            $labels->push($date->locale('id')->format('d M'));
        }
        
        // Get events for those dates
        $events = Event::whereIn(DB::raw('DATE(date)'), $dates->toArray())
            ->orderBy('date')
            ->get()
            ->groupBy(function($event) {
                return Carbon::parse($event->date)->format('Y-m-d');
            });
        
        // Initialize data arrays
        $studentHadir = array_fill(0, 7, 0);
        $studentIzin = array_fill(0, 7, 0);
        $studentAlfa = array_fill(0, 7, 0);
        
        $staffHadir = array_fill(0, 7, 0);
        $staffIzin = array_fill(0, 7, 0);
        $staffAlfa = array_fill(0, 7, 0);
        
        // Calculate attendance for each day
        foreach ($dates as $index => $date) {
            if (isset($events[$date])) {
                $dayEvents = $events[$date];
                $eventIds = $dayEvents->pluck('id')->toArray();
                
                // Student attendance
                $studentStats = Report::whereIn('event_id', $eventIds)
                    ->select('status', DB::raw('count(*) as total'))
                    ->groupBy('status')
                    ->pluck('total', 'status')
                    ->toArray();
                
                $studentHadir[$index] = $studentStats['hadir'] ?? 0;
                $studentIzin[$index] = $studentStats['izin'] ?? 0;
                $studentAlfa[$index] = $studentStats['alfa'] ?? 0;
                
                // Staff attendance
                $staffStats = EventStaff::whereIn('event_id', $eventIds)
                    ->select('status', DB::raw('count(*) as total'))
                    ->groupBy('status')
                    ->pluck('total', 'status')
                    ->toArray();
                
                $staffHadir[$index] = $staffStats['hadir'] ?? 0;
                $staffIzin[$index] = $staffStats['izin'] ?? 0;
                $staffAlfa[$index] = $staffStats['alfa'] ?? 0;
            }
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Siswa Hadir',
                    'data' => $studentHadir,
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#10b981',
                ],
                [
                    'label' => 'Siswa Izin',
                    'data' => $studentIzin,
                    'backgroundColor' => '#f59e0b',
                    'borderColor' => '#f59e0b',
                ],
                [
                    'label' => 'Siswa Alfa',
                    'data' => $studentAlfa,
                    'backgroundColor' => '#ef4444',
                    'borderColor' => '#ef4444',
                ],
                [
                    'label' => 'Staff Hadir',
                    'data' => $staffHadir,
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#3b82f6',
                ],
                [
                    'label' => 'Staff Izin',
                    'data' => $staffIzin,
                    'backgroundColor' => '#8b5cf6',
                    'borderColor' => '#8b5cf6',
                ],
                [
                    'label' => 'Staff Alfa',
                    'data' => $staffAlfa,
                    'backgroundColor' => '#ec4899',
                    'borderColor' => '#ec4899',
                ],
            ],
            'labels' => $labels->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
} 