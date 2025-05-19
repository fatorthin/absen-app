<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Database\Seeder;

class ClassroomAndStudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create classrooms with specific names
        $classrooms = [
            '1 SMP', '2 SMP', '3 SMP', 
            '1 SMA', '2 SMA', '3 SMA'
        ];

        // Clear existing data to avoid duplication
        Student::query()->forceDelete();
        Classroom::query()->forceDelete();

        $createdClassrooms = [];
        foreach ($classrooms as $name) {
            $createdClassrooms[] = Classroom::factory()
                ->withName($name)
                ->create();
        }

        // Define exactly how many students per classroom (16-17)
        $studentsPerClassroom = [17, 17, 17, 17, 16, 16]; // 100 students total
        
        // Create and assign students to each classroom
        foreach ($createdClassrooms as $index => $classroom) {
            // Determine class value based on classroom name
            $className = $classroom->name;
            
            // Create students for this classroom
            $students = Student::factory()
                ->count($studentsPerClassroom[$index])
                ->inClass($className) // Set the class to match the classroom
                ->create();
            
            // Attach all these students to this classroom
            foreach ($students as $student) {
                $student->classrooms()->attach($classroom->id);
            }
            
            $this->command->info("Created {$studentsPerClassroom[$index]} students for classroom '{$className}'");
        }
        
        $this->command->info('Total students created: ' . Student::count());
    }
} 