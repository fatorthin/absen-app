<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Group;
use App\Models\Report;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;

class AttendanceReportController extends Controller
{
    public function index(Request $request)
    {
        $groups = Group::all();
        $selectedGroup = $request->input('group_id');
        $selectedMonth = $request->input('month', Carbon::now()->month);
        $selectedYear = $request->input('year', Carbon::now()->year);

        $query = Student::query();
        
        if ($selectedGroup) {
            $query->where('group_id', $selectedGroup);
        }
        
        $students = $query->with('group')->get();

        // Get all events for the selected month
        $events = Event::whereMonth('date', $selectedMonth)
            ->whereYear('date', $selectedYear)
            ->orderBy('date')
            ->get();
        
        // Get all dates for the selected month
        $eventDates = $events->pluck('date')->map(function($date) {
            return Carbon::parse($date)->format('d');
        })->unique()->sort()->values();

        // Get attendance data
        $attendanceData = [];
        foreach ($students as $student) {
            $reports = Report::whereHas('event', function($query) use ($selectedMonth, $selectedYear) {
                $query->whereMonth('date', $selectedMonth)
                      ->whereYear('date', $selectedYear);
            })->where('student_id', $student->id)->get();
            
            $studentAttendance = [
                'id' => $student->id,
                'name' => $student->name,
                'class' => $student->class,
                'group' => $student->group->name ?? '',
                'attendance' => [],
                'summary' => [
                    'hadir' => 0,
                    'alfa' => 0,
                    'izin' => 0
                ]
            ];
            
            foreach ($eventDates as $date) {
                $event = $events->filter(function($e) use ($date) {
                    return Carbon::parse($e->date)->format('d') == $date;
                })->first();
                
                if ($event) {
                    $report = $reports->where('event_id', $event->id)->first();
                    $status = $report ? $report->status : null;
                    $studentAttendance['attendance'][$date] = $status;
                    
                    if ($status) {
                        $studentAttendance['summary'][$status]++;
                    }
                } else {
                    $studentAttendance['attendance'][$date] = null;
                }
            }
            
            $attendanceData[] = $studentAttendance;
        }

        // Get month name for title
        $monthName = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->locale('id')->monthName;
        
        return view('reports.attendance', compact(
            'attendanceData', 
            'eventDates', 
            'groups', 
            'selectedGroup', 
            'selectedMonth', 
            'selectedYear',
            'monthName'
        ));
    }
    
    public function export(Request $request)
    {
        $selectedGroup = $request->input('group_id');
        $selectedMonth = $request->input('month', Carbon::now()->month);
        $selectedYear = $request->input('year', Carbon::now()->year);
        
        $query = Student::query();
        
        if ($selectedGroup) {
            $query->where('group_id', $selectedGroup);
        }
        
        $students = $query->with('group')->get();

        // Get all events for the selected month
        $events = Event::whereMonth('date', $selectedMonth)
            ->whereYear('date', $selectedYear)
            ->orderBy('date')
            ->get();
        
        // Get all dates for the selected month
        $eventDates = $events->pluck('date')->map(function($date) {
            return Carbon::parse($date)->format('d');
        })->unique()->sort()->values();

        // Get attendance data
        $attendanceData = [];
        foreach ($students as $student) {
            $reports = Report::whereHas('event', function($query) use ($selectedMonth, $selectedYear) {
                $query->whereMonth('date', $selectedMonth)
                      ->whereYear('date', $selectedYear);
            })->where('student_id', $student->id)->get();
            
            $studentAttendance = [
                'id' => $student->id,
                'name' => $student->name,
                'class' => $student->class,
                'group' => $student->group->name ?? '',
                'attendance' => [],
                'summary' => [
                    'hadir' => 0,
                    'alfa' => 0,
                    'izin' => 0
                ]
            ];
            
            foreach ($eventDates as $date) {
                $event = $events->filter(function($e) use ($date) {
                    return Carbon::parse($e->date)->format('d') == $date;
                })->first();
                
                if ($event) {
                    $report = $reports->where('event_id', $event->id)->first();
                    $status = $report ? $report->status : null;
                    $studentAttendance['attendance'][$date] = $status;
                    
                    if ($status) {
                        $studentAttendance['summary'][$status]++;
                    }
                } else {
                    $studentAttendance['attendance'][$date] = null;
                }
            }
            
            $attendanceData[] = $studentAttendance;
        }

        // Get month name for title
        $monthName = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->locale('id')->monthName;
        
        $fileName = "rekap_kehadiran_{$monthName}_{$selectedYear}.xlsx";
        
        return Excel::download(new AttendanceExport($attendanceData, $eventDates, $monthName, $selectedYear), $fileName);
    }
} 