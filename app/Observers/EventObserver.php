<?php

namespace App\Observers;

use App\Models\Event;
use App\Models\Report;

class EventObserver
{
    /**
     * Handle the Event "created" event.
     */
    public function created(Event $event): void
    {
        $this->addStudentsFromClassroom($event);
    }
    
    /**
     * Handle the Event "updated" event - only add new students if classroom_id changed.
     */
    public function updated(Event $event): void
    {
        // If classroom_id was changed, add students from the new classroom
        if ($event->isDirty('classroom_id')) {
            $this->addStudentsFromClassroom($event);
        }
    }
    
    /**
     * Common method to add students from a classroom to an event
     */
    private function addStudentsFromClassroom(Event $event): void
    {
        // Get the classroom associated with this event
        $classroom = $event->classroom;
        
        if ($classroom) {
            // Get all students from this classroom
            $students = $classroom->students;
            
            // Get existing student IDs for this event
            $existingStudentIds = Report::where('event_id', $event->id)
                ->pluck('student_id')
                ->toArray();
            
            // Create report entries for each student that doesn't already have one
            foreach ($students as $student) {
                if (!in_array($student->id, $existingStudentIds)) {
                    Report::create([
                        'event_id' => $event->id,
                        'student_id' => $student->id,
                        'status' => 'alfa', // Default status is alfa (absent)
                    ]);
                }
            }
        }
    }
} 