<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\StaffAttendanceReportController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    return view('welcome');
});

// Add QR code scanner routes
Route::get('/qrcode/scanner', [QrCodeController::class, 'scanner'])->name('qrcode.scanner');
Route::post('/qrcode/verify', [QrCodeController::class, 'verifyPassword'])->name('qrcode.verify');
Route::get('/qrcode/logout', [QrCodeController::class, 'logout'])->name('qrcode.logout');
Route::post('/qrcode/process', [QrCodeController::class, 'process'])->name('qrcode.process');

// Add attendance report routes
Route::get('/attendance/report', [AttendanceReportController::class, 'index'])->name('attendance.report');
Route::get('/attendance/export', [AttendanceReportController::class, 'export'])->name('attendance.export');

// Add staff attendance report routes
Route::get('/staff/attendance/report', [StaffAttendanceReportController::class, 'index'])->name('staff.attendance.report');
Route::get('/staff/attendance/export', [StaffAttendanceReportController::class, 'export'])->name('staff.attendance.export');
