<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Models\Report;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
    
    protected function afterCreate(): void
    {
        // Get count of attendance records created for this event
        $attendanceCount = Report::where('event_id', $this->record->id)->count();
        
        // Show notification about the number of attendance records created
        Notification::make()
            ->title('Event created successfully')
            ->body("{$attendanceCount} student attendance records were automatically created with status 'alfa'.")
            ->success()
            ->send();
    }
}
