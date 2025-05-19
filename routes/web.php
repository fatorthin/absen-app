<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;

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
