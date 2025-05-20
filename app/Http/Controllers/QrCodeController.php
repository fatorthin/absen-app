<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Report;
use App\Models\Event;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class QrCodeController extends Controller
{
    public function scanner(Request $request)
    {
        // We no longer need an event_id parameter
        return view('qr-scanner');
    }
    
    public function process(Request $request)
    {
        try {
            $uuid = $request->uuid;
            
            Log::info("Processing QR code: UUID={$uuid}");
            
            if (!$uuid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student UUID is required'
                ]);
            }
            
            // Find student by UUID
            $student = Student::where('uuid', $uuid)->first();
            
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found with this UUID'
                ]);
            }
            
            // Find today's events for this student
            $today = Carbon::today();
            $todaysEvents = Event::whereDate('date', $today)
                ->whereHas('reports', function ($query) use ($student) {
                    $query->where('student_id', $student->id);
                })
                ->get();
            
            if ($todaysEvents->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => "No events found today for student {$student->name}"
                ]);
            }
            
            // Update attendance for all today's events
            $updatedEvents = [];
            
            foreach ($todaysEvents as $event) {
                $report = Report::where('student_id', $student->id)
                               ->where('event_id', $event->id)
                               ->first();
                
                if ($report) {
                    $report->status = 'hadir';
                    $report->save();
                    $updatedEvents[] = $event->name;
                }
            }
            
            // Format the response message
            $eventNames = implode(', ', $updatedEvents);
            $message = "Student {$student->name} marked as present for event(s): {$eventNames}";
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name
                ],
                'events' => $updatedEvents
            ]);
            
        } catch (\Exception $e) {
            Log::error("QR code processing error: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error processing QR code: ' . $e->getMessage()
            ]);
        }
    }
} 