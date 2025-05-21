<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Group;
use App\Models\Staff;
use App\Models\EventStaff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StaffAttendanceExport;

class StaffAttendanceReportController extends Controller
{
    public function index(Request $request)
    {
        $groups = Group::all();
        $selectedGroup = $request->input('group_id');
        $selectedMonth = $request->input('month', Carbon::now()->month);
        $selectedYear = $request->input('year', Carbon::now()->year);

        $query = Staff::query();
        
        if ($selectedGroup) {
            $query->where('group_id', $selectedGroup);
        }
        
        $staffMembers = $query->with('group')->get();

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
        foreach ($staffMembers as $staff) {
            $eventStaffRecords = EventStaff::whereHas('event', function($query) use ($selectedMonth, $selectedYear) {
                $query->whereMonth('date', $selectedMonth)
                      ->whereYear('date', $selectedYear);
            })->where('staff_id', $staff->id)->get();
            
            $staffAttendance = [
                'id' => $staff->id,
                'name' => $staff->name,
                'role' => $staff->role,
                'group' => $staff->group->name ?? '',
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
                    $eventStaff = $eventStaffRecords->filter(function($es) use ($event) {
                        return $es->event_id == $event->id;
                    })->first();
                    
                    $status = $eventStaff ? $eventStaff->status : null;
                    $staffAttendance['attendance'][$date] = $status;
                    
                    if ($status) {
                        $staffAttendance['summary'][$status]++;
                    }
                } else {
                    $staffAttendance['attendance'][$date] = null;
                }
            }
            
            $attendanceData[] = $staffAttendance;
        }

        // Get month name for title
        $monthName = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->locale('id')->monthName;
        
        return view('reports.staff-attendance', compact(
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
        
        $query = Staff::query();
        
        if ($selectedGroup) {
            $query->where('group_id', $selectedGroup);
        }
        
        $staffMembers = $query->with('group')->get();

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
        foreach ($staffMembers as $staff) {
            $eventStaffRecords = EventStaff::whereHas('event', function($query) use ($selectedMonth, $selectedYear) {
                $query->whereMonth('date', $selectedMonth)
                      ->whereYear('date', $selectedYear);
            })->where('staff_id', $staff->id)->get();
            
            $staffAttendance = [
                'id' => $staff->id,
                'name' => $staff->name,
                'role' => $staff->role,
                'group' => $staff->group->name ?? '',
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
                    $eventStaff = $eventStaffRecords->filter(function($es) use ($event) {
                        return $es->event_id == $event->id;
                    })->first();
                    
                    $status = $eventStaff ? $eventStaff->status : null;
                    $staffAttendance['attendance'][$date] = $status;
                    
                    if ($status) {
                        $staffAttendance['summary'][$status]++;
                    }
                } else {
                    $staffAttendance['attendance'][$date] = null;
                }
            }
            
            $attendanceData[] = $staffAttendance;
        }

        // Get month name for title
        $monthName = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->locale('id')->monthName;
        
        $fileName = "rekap_kehadiran_staff_{$monthName}_{$selectedYear}.xlsx";
        
        return Excel::download(new StaffAttendanceExport($attendanceData, $eventDates, $monthName, $selectedYear), $fileName);
    }
} 