@extends('layouts.app')

@section('title', 'Rekap Kehadiran - Bulan ' . $monthName . ' ' . $selectedYear)

@push('styles')
<style>
    .attendance-table {
        width: 100%;
        border-collapse: collapse;
    }
    .attendance-table th, .attendance-table td {
        border: 1px solid #dee2e6;
        padding: 8px;
        text-align: center;
    }
    .attendance-table th {
        background-color: #f2f2f2;
    }
    .attendance-table .name-cell {
        text-align: left;
        font-weight: bold;
    }
    .status-hadir {
        background-color: #d4edda;
    }
    .status-alfa {
        background-color: #f8d7da;
    }
    .status-izin {
        background-color: #fff3cd;
    }
    .status-sakit {
        background-color: #cff4fc;
    }
    .filters {
        margin-bottom: 20px;
    }
    .summary-col {
        background-color: #e9ecef;
    }
    .month-year-header {
        font-weight: bold;
        text-transform: uppercase;
    }
</style>
@endpush

@section('content')
<div class="row mb-4">
    <div class="col-12 text-center">
        <h2 class="mb-3">Laporan Kehadiran - Bulan {{ $monthName }} {{ $selectedYear }}</h2>
    </div>
</div>

<form class="row g-3 filters no-print" method="get" action="{{ route('attendance.report') }}">
    <div class="col-md-4">
        <label for="group_id" class="form-label">Group</label>
        <select name="group_id" id="group_id" class="form-select">
            <option value="">All Groups</option>
            @foreach($groups as $group)
                <option value="{{ $group->id }}" {{ $selectedGroup == $group->id ? 'selected' : '' }}>
                    {{ $group->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label for="month" class="form-label">Month</label>
        <select name="month" id="month" class="form-select">
            @foreach(range(1, 12) as $month)
                <option value="{{ $month }}" {{ $selectedMonth == $month ? 'selected' : '' }}>
                    {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label for="year" class="form-label">Year</label>
        <select name="year" id="year" class="form-select">
            @foreach(range(date('Y') - 2, date('Y') + 1) as $year)
                <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                    {{ $year }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-primary me-2">Filter</button>
        <a href="{{ route('attendance.export', ['group_id' => $selectedGroup, 'month' => $selectedMonth, 'year' => $selectedYear]) }}" class="btn btn-info me-2">Export</a>
        <button type="button" class="btn btn-success" onclick="window.print()">Print</button>
    </div>
</form>

<div class="row">
    <div class="col-12 table-responsive">
        <table class="attendance-table">
            <thead>
                <tr>
                    <th rowspan="2" class="align-middle">No</th>
                    <th rowspan="2" class="align-middle">Nama</th>
                    <th rowspan="2" class="align-middle">Kelompok</th>
                    <th rowspan="2" class="align-middle">Kelas</th>
                    @foreach($eventDates as $date)
                        <th rowspan="2">{{ $date }}</th>
                    @endforeach
                    <th colspan="4" class="text-center">Jumlah Kehadiran</th>
                </tr>
                <tr>
                    {{-- @foreach($eventDates as $date)
                        <th></th>
                    @endforeach --}}
                    <th class="summary-col">Hadir</th>
                    <th class="summary-col">Alfa</th>
                    <th class="summary-col">Izin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendanceData as $index => $student)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="name-cell">{{ $student['name'] }}</td>
                        <td class="name-cell">{{ $student['group'] }}</td>
                        <td>{{ $student['class'] }}</td>
                        
                        @foreach($eventDates as $date)
                            @php
                                $status = $student['attendance'][$date] ?? null;
                                $statusClass = $status ? "status-{$status}" : "";
                                $statusDisplay = null;
                                
                                if ($status === 'hadir') {
                                    $statusDisplay = 'H';  // Attendance
                                } elseif ($status === 'alfa') {
                                    $statusDisplay = 'A';  // Alpha
                                } elseif ($status === 'izin') {
                                    $statusDisplay = 'I';  // Izin
                                }
                            @endphp
                            
                            <td class="{{ $statusClass }}">{{ $statusDisplay }}</td>
                        @endforeach
                        
                        <td class="summary-col">{{ $student['summary']['hadir'] }}</td>
                        <td class="summary-col">{{ $student['summary']['alfa'] }}</td>
                        <td class="summary-col">{{ $student['summary']['izin'] }}</td>
                    </tr>
                    
                @empty
                    <tr>
                        <td colspan="{{ count($eventDates) + 7 }}" class="text-center">No attendance records found</td>
                    </tr>
                @endforelse
                
                @if(count($attendanceData) > 0)
                    <tr class="font-weight-bold" style="background-color: #f8f9fa; font-weight: bold;">
                        <td colspan="4" class="text-end">TOTAL</td>
                        @foreach($eventDates as $date)
                            @php
                                $dateHadir = 0;
                                $dateAlfa = 0;
                                $dateIzin = 0;
                                
                                foreach($attendanceData as $student) {
                                    $status = $student['attendance'][$date] ?? null;
                                    if ($status === 'hadir') $dateHadir++;
                                    if ($status === 'alfa') $dateAlfa++;
                                    if ($status === 'izin') $dateIzin++;
                                }
                                
                                $totalAttendance = $dateHadir + $dateAlfa + $dateIzin;
                                if ($totalAttendance > 0) {
                                    $percentHadir = round(($dateHadir / $totalAttendance) * 100);
                                    $display = "{$dateHadir}/{$totalAttendance}";
                                } else {
                                    $display = "0/0";
                                }
                            @endphp
                            <td>{{ $display }}</td>
                        @endforeach
                        
                        @php
                            $totalHadir = 0;
                            $totalAlfa = 0;
                            $totalIzin = 0;
                            
                            foreach($attendanceData as $student) {
                                $totalHadir += $student['summary']['hadir'];
                                $totalAlfa += $student['summary']['alfa'];
                                $totalIzin += $student['summary']['izin'];
                            }
                        @endphp
                        
                        <td class="summary-col">{{ $totalHadir }}</td>
                        <td class="summary-col">{{ $totalAlfa }}</td>
                        <td class="summary-col">{{ $totalIzin }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection 