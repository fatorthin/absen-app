<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Models\Report;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
    
    protected function afterSave(): void
    {
        // Check if classroom was changed
        if ($this->record->wasChanged('classroom_id')) {
            // Get the count of new reports created for this event
            $initialReportsCount = session()->pull('event_initial_reports_count', 0);
            $newReportsCount = Report::where('event_id', $this->record->id)->count() - $initialReportsCount;
            
            if ($newReportsCount > 0) {
                // Show notification about new student records
                Notification::make()
                    ->title('Classroom changed')
                    ->body("{$newReportsCount} new student attendance records were automatically created with status 'alfa'.")
                    ->success()
                    ->send();
            }
        }
    }
    
    protected function beforeSave(): void
    {
        // Store the current count of reports before saving
        session()->put(
            'event_initial_reports_count', 
            Report::where('event_id', $this->record->id)->count()
        );
    }
}
