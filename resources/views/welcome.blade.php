@extends('layouts.app')

@section('title', 'Welcome - Absensi App')

@section('content')
<div class="row">
    <div class="col-12 text-center">
        <h1 class="mb-4">Absensi App</h1>
        <p class="lead">Sistem Manajemen Kehadiran Siswa</p>
        
        <div class="mt-5">
            <a href="{{ route('attendance.report') }}" class="btn btn-primary btn-lg">Lihat Rekap Kehadiran</a>
            <a href="{{ route('staff.attendance.report') }}" class="btn btn-info btn-lg ms-2">Lihat Rekap Staff</a>
            <a href="{{ route('qrcode.scanner') }}" class="btn btn-success btn-lg ms-2">Scan Kehadiran</a>
        </div>
    </div>
</div>
@endsection