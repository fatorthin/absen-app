<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\ReportController;

//storage-link
Route::get('/storage-link', function () {
    Artisan::call('storage:link');
    return 'Storage link created successfully';
});

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
