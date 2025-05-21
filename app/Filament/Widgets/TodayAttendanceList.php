<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\Report;
use App\Models\Student;
use App\Models\Staff;
use App\Models\EventStaff;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TodayAttendanceList extends BaseWidget
{
    protected static ?string $heading = 'Kehadiran Hari Ini';
    
    protected int|string|array $columnSpan = 'full';
    
    protected function getTableQuery(): Builder
    {
        $today = Carbon::today();
        
        // Get today's events
        $todayEvents = Event::whereDate('date', $today)->pluck('id')->toArray();
        
        if (empty($todayEvents)) {
            return Student::query()->where('id', 0); // Return empty query if no events
        }
        
        return Report::query()
            ->with(['student', 'event'])
            ->whereIn('event_id', $todayEvents)
            ->latest();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('created_at')
                ->label('Waktu')
                ->dateTime('H:i:s')
                ->sortable(),
            Tables\Columns\TextColumn::make('student.name')
                ->label('Nama')
                ->searchable(),
            Tables\Columns\TextColumn::make('student.class')
                ->label('Kelas'),
            Tables\Columns\TextColumn::make('student.group.name')
                ->label('Kelompok'),
            Tables\Columns\TextColumn::make('event.name')
                ->label('Event'),
            Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'hadir' => 'success',
                    'izin' => 'warning',
                    'alfa' => 'danger',
                }),
        ];
    }
    
    protected function isTablePaginationEnabled(): bool
    {
        return true;
    }
    
    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [10, 25, 50];
    }
} 