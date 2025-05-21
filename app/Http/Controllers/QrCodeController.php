<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Staff;
use App\Models\Report;
use App\Models\Event;
use App\Models\EventStaff;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class QrCodeController extends Controller
{
    // Scanner password - you should move this to .env in production
    protected $scannerPassword = 'absenApp2023';
    
    public function scanner(Request $request)
    {
        // Check if already authenticated for scanner
        if ($request->session()->get('scanner_auth', false)) {
            return view('qr-scanner');
        }
        
        // If not authenticated, show the password form
        return view('qr-scanner-password');
    }
    
    public function verifyPassword(Request $request)
    {
        $password = $request->input('password');
        
        // Verify password
        if ($password === $this->scannerPassword) {
            // Set session flag for authentication
            $request->session()->put('scanner_auth', true);
            
            // Redirect to scanner
            return redirect()->route('qrcode.scanner');
        }
        
        // Password incorrect
        return redirect()->route('qrcode.scanner')
            ->with('error', 'Incorrect password. Please try again.');
    }
    
    public function process(Request $request)
    {
        try {
            $uuid = $request->uuid;
            
            Log::info("Processing QR code: UUID={$uuid}");
            
            if (!$uuid) {
                return response()->json([
                    'success' => false,
                    'message' => 'UUID is required'
                ]);
            }
            
            // Find student or staff by UUID
            $student = Student::where('uuid', $uuid)->first();
            $staff = Staff::where('uuid', $uuid)->first();
            
            // Process based on who was found
            if ($student) {
                return $this->processStudentAttendance($student);
            } elseif ($staff) {
                return $this->processStaffAttendance($staff);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No student or staff found with this UUID'
                ]);
            }
        } catch (\Exception $e) {
            Log::error("QR code processing error: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error processing QR code: ' . $e->getMessage()
            ]);
        }
    }
    
    private function processStudentAttendance($student)
    {
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
            'type' => 'student',
            'person' => [
                'id' => $student->id,
                'name' => $student->name
            ],
            'events' => $updatedEvents
        ]);
    }
    
    private function processStaffAttendance($staff)
    {
        // Find today's events for this staff member
        $today = Carbon::today();
        $todaysEvents = Event::whereDate('date', $today)
            ->whereHas('staff', function ($query) use ($staff) {
                $query->where('staff.id', $staff->id);
            })
            ->get();
        
        if ($todaysEvents->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => "No events found today for staff {$staff->name}"
            ]);
        }
        
        // Update attendance for all today's events
        $updatedEvents = [];
        
        foreach ($todaysEvents as $event) {
            $eventStaff = EventStaff::where('staff_id', $staff->id)
                                   ->where('event_id', $event->id)
                                   ->first();
            
            if ($eventStaff) {
                $eventStaff->status = 'hadir';
                $eventStaff->save();
                $updatedEvents[] = $event->name;
            }
        }
        
        // Format the response message
        $eventNames = implode(', ', $updatedEvents);
        $message = "Staff {$staff->name} marked as present for event(s): {$eventNames}";
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'type' => 'staff',
            'person' => [
                'id' => $staff->id,
                'name' => $staff->name
            ],
            'events' => $updatedEvents
        ]);
    }
    
    public function logout(Request $request)
    {
        // Remove scanner authentication
        $request->session()->forget('scanner_auth');
        
        // Redirect back to scanner (which will show password form)
        return redirect()->route('qrcode.scanner');
    }
} 