<?php

namespace App\Filament\Resources\StudentResource\Widgets;

use App\Models\Classroom;
use App\Models\Student;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class StudentClassroomsTable extends BaseWidget
{
    public Student $record;

    protected int | string | array $columnSpan = 'full';

    protected function getTableHeading(): string
    {
        return 'Enrolled Classrooms';
    }

    public function mount(Student $record): void
    {
        $this->record = $record;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Classroom::query()
                    ->join('classroom_student', 'classrooms.id', '=', 'classroom_student.classroom_id')
                    ->where('classroom_student.student_id', $this->record->id)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('classroom_student.created_at')
                    ->label('Joined Date')
                    ->date()
                    ->sortable(),
                // Add more classroom columns as needed
            ]);
    }
} 