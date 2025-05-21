<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\EventStaff;
use App\Models\Staff;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TodayStaffAttendanceList extends BaseWidget
{
    protected static ?string $heading = 'Kehadiran Staff Hari Ini';
    
    protected int|string|array $columnSpan = 'full';
    
    protected function getTableQuery(): Builder
    {
        $today = Carbon::today();
        
        // Get today's events
        $todayEvents = Event::whereDate('date', $today)->pluck('id')->toArray();
        
        if (empty($todayEvents)) {
            return Staff::query()->where('id', 0); // Return empty query if no events
        }
        
        return EventStaff::query()
            ->with(['staff', 'event'])
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
            Tables\Columns\TextColumn::make('staff.name')
                ->label('Nama')
                ->searchable(),
            Tables\Columns\TextColumn::make('staff.role')
                ->label('Jabatan')
                ->formatStateUsing(fn (string $state): string => ucfirst($state)),
            Tables\Columns\TextColumn::make('staff.group.name')
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