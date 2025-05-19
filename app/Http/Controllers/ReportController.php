<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function createFromQrCode(Request $request)
    {
        $data = $request->validate([
            'qrcode' => 'required|string',
        ]);

        // Assuming the QR code contains the student name
        // dd($request);
        // return  view('scan', ['data' => $data]);

        return response()->json(['success' => true, 'data' => $data]);
    }
}
