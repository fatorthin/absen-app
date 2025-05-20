<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\QrCodeController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/scan', function () {
    return view('scan');
});

// Route::post('/update_report', [ReportController::class, 'update_report'])->name('update.report');

Route::post('/update_report', function (Request $request) {
    // dd($request);


    return back()->with('masuk', 'masuk');
});

// Add QR code scanner routes
Route::get('/qrcode/scanner', [QrCodeController::class, 'scanner'])->name('qrcode.scanner');
Route::post('/qrcode/process', [QrCodeController::class, 'process'])->name('qrcode.process');
