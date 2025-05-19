<?php

namespace App\Filament\Resources\StudentResource\Widgets;

use App\Models\Event;
use App\Models\Student;
use App\Models\Report;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class StudentEventsTable extends BaseWidget
{
    public Student $record;

    protected int | string | array $columnSpan = 'full';

    protected function getTableHeading(): string
    {
        return 'Attended Events';
    }

    public function mount(Student $record): void
    {
        $this->record = $record;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Convert the relationship to a query builder
                Event::query()
                    ->join('reports', 'events.id', '=', 'reports.event_id')
                    ->where('reports.student_id', $this->record->id)
                    ->whereNull('reports.deleted_at')
                    ->select('events.*', 'reports.status')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('location')
                //     ->searchable(),
                // Show the status directly from the join
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'hadir' => 'success',
                        'izin' => 'warning',
                        'alfa' => 'danger',
                        default => 'gray',
                    }),
            ]);
    }
} 