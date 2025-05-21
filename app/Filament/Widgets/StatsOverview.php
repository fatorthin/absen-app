<?php

namespace App\Filament\Widgets;

use App\Models\Staff;
use App\Models\Event;
use App\Models\Student;
use App\Models\Report;
use App\Models\EventStaff;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';
    
    protected function getStats(): array
    {
        // Get total students
        $totalStudents = Student::count();
        
        // Get total staff
        $totalStaff = Staff::count();
        
        // Get today's date
        $today = Carbon::today();
        
        // Get today's events
        $todayEvents = Event::whereDate('date', $today)->get();
        $eventIds = $todayEvents->pluck('id')->toArray();
        
        // Initialize attendance counters
        $studentHadir = 0;
        $studentIzin = 0;
        $studentAlfa = 0;
        $totalStudentAttendance = 0;
        
        $staffHadir = 0;
        $staffIzin = 0;
        $staffAlfa = 0;
        $totalStaffAttendance = 0;
        
        // Calculate student attendance for today
        if (!empty($eventIds)) {
            $studentAttendance = Report::whereIn('event_id', $eventIds)
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();
                
            $studentHadir = $studentAttendance['hadir'] ?? 0;
            $studentIzin = $studentAttendance['izin'] ?? 0;
            $studentAlfa = $studentAttendance['alfa'] ?? 0;
            $totalStudentAttendance = $studentHadir + $studentIzin + $studentAlfa;
        }
        
        // Calculate staff attendance for today
        if (!empty($eventIds)) {
            $staffAttendance = EventStaff::whereIn('event_id', $eventIds)
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();
                
            $staffHadir = $staffAttendance['hadir'] ?? 0;
            $staffIzin = $staffAttendance['izin'] ?? 0;
            $staffAlfa = $staffAttendance['alfa'] ?? 0;
            $totalStaffAttendance = $staffHadir + $staffIzin + $staffAlfa;
        }
        
        // Calculate percentages
        $studentHadirPercent = $totalStudentAttendance > 0 ? round(($studentHadir / $totalStudentAttendance) * 100) : 0;
        $studentIzinPercent = $totalStudentAttendance > 0 ? round(($studentIzin / $totalStudentAttendance) * 100) : 0;
        $studentAlfaPercent = $totalStudentAttendance > 0 ? round(($studentAlfa / $totalStudentAttendance) * 100) : 0;
        
        $staffHadirPercent = $totalStaffAttendance > 0 ? round(($staffHadir / $totalStaffAttendance) * 100) : 0;
        $staffIzinPercent = $totalStaffAttendance > 0 ? round(($staffIzin / $totalStaffAttendance) * 100) : 0;
        $staffAlfaPercent = $totalStaffAttendance > 0 ? round(($staffAlfa / $totalStaffAttendance) * 100) : 0;
        
        return [
            Stat::make('Jumlah Siswa', $totalStudents)
                ->description('Total siswa terdaftar')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success'),
                
            Stat::make('Jumlah Staff', $totalStaff)
                ->description('Total staff terdaftar')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
                
            Stat::make('Kehadiran Siswa Hari Ini', $studentHadirPercent . '%')
                ->description("Hadir: $studentHadir, Izin: $studentIzin, Alfa: $studentAlfa")
                ->descriptionIcon('heroicon-m-calendar')
                ->chart([
                    $studentHadirPercent, 
                    $studentIzinPercent, 
                    $studentAlfaPercent > 0 ? $studentAlfaPercent : (100 - $studentHadirPercent - $studentIzinPercent)
                ])
                ->color('success'),
                
            Stat::make('Kehadiran Staff Hari Ini', $staffHadirPercent . '%')
                ->description("Hadir: $staffHadir, Izin: $staffIzin, Alfa: $staffAlfa")
                ->descriptionIcon('heroicon-m-calendar')
                ->chart([
                    $staffHadirPercent, 
                    $staffIzinPercent, 
                    $staffAlfaPercent > 0 ? $staffAlfaPercent : (100 - $staffHadirPercent - $staffIzinPercent)
                ])
                ->color('info'),
        ];
    }
} 