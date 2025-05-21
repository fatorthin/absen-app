<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Event;
use App\Models\EventStaff;
use Filament\Tables\Table;
use Livewire\Attributes\Computed;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Route;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\SelectColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Illuminate\Database\Eloquent\Collection;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Enums\ActionsPosition;

class EventStaffTable extends BaseWidget
{
    protected static bool $isDiscovered = false;
    
    protected static string $view = 'filament.widgets.event-staff-table';
    
    public int|string|null $eventId = null;
    
    protected function getTableHeading(): string 
    {
        return 'Staff Members';
    }
    
    public function mount(): void
    {
        // Try multiple ways to extract the event ID if it's not already set
        if (!$this->eventId) {
            // Method 1: Get from route parameter directly
            $recordKey = request()->route('record');
            if ($recordKey) {
                $this->eventId = $recordKey;
                Log::info("[EventStaffTable] Extracted event ID from route parameter: {$this->eventId}");
                return;
            }
            
            // Method 2: Look through all route parameters
            $parameters = request()->route()->parameters();
            Log::info("[EventStaffTable] Route parameters: " . json_encode($parameters));
            
            foreach ($parameters as $param) {
                if (is_numeric($param)) {
                    $this->eventId = $param;
                    Log::info("[EventStaffTable] Found numeric parameter: {$this->eventId}");
                    return;
                }
            }
            
            // If we're still here, try to get info about the current route for debugging
            $routeName = Route::currentRouteName();
            $currentUrl = request()->url();
            Log::info("[EventStaffTable] Current route: {$routeName}, URL: {$currentUrl}");
            
            // If all else fails, log the issue
            Log::warning("[EventStaffTable] Could not extract event ID from current request");
        }
    }
    
    /**
     * Get the current event
     */
    #[Computed]
    public function getEvent()
    {
        if (!$this->eventId) {
            return null;
        }
        
        try {
            return Event::findOrFail($this->eventId);
        } catch (\Exception $e) {
            Log::error("[EventStaffTable] Error finding event: " . $e->getMessage());
            return null;
        }
    }

    public function table(Table $table): Table
    {
        $event = $this->getEvent();
        
        if (!$event) {
            Log::info('[EventStaffTable] No event available, returning empty query.');
            return $table->query(fn() => \App\Models\Staff::query()->where('id', 0));
        }
        
        try {
            // Kita akan menggunakan query biasa dengan join yang eksplisit
            $staffQuery = \App\Models\Staff::query()
                ->join('event_staff', 'staff.id', '=', 'event_staff.staff_id')
                ->where('event_staff.event_id', $event->id)
                ->select([
                    'staff.*',
                    'event_staff.status'
                ]);
                
            Log::info("[EventStaffTable] Built query for event ID: {$event->id}");
            
            return $table
            ->poll('10s')    
            ->query($staffQuery)
                ->columns([
                    TextColumn::make('name')
                        ->label('Name')
                        ->searchable()
                        ->sortable(),
                    TextColumn::make('status')
                        ->label('Status')
                        ->formatStateUsing(function ($state) {
                            return match($state) {
                                'hadir' => 'Hadir',
                                'izin' => 'Izin', 
                                'alfa' => 'Alfa',
                            };
                        })
                        ->badge()
                        ->color(function($state) {
                            return match($state) {
                                'hadir' => 'success',
                                'izin' => 'warning',
                                'alfa' => 'danger',
                            };
                        })
                ])
                ->actions([
                    // Update Status Action
                    Action::make('editStatus')
                        ->label('Edit Status')
                        ->icon('heroicon-o-pencil')
                        ->button()
                        ->form([
                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'hadir' => 'Hadir',
                                    'izin' => 'Izin',
                                    'alfa' => 'Alfa',
                                ])
                                ->default(function ($record) {
                                    return $record->status;
                                })
                                ->required(),
                        ])
                        ->action(function ($record, array $data) use ($event) {
                            try {
                                // Log data for debugging
                                Log::info('[EventStaffTable] Attempting to update status', [
                                    'staff_id' => $record->id,
                                    'event_id' => $event->id,
                                    'new_status' => $data['status']
                                ]);
                                
                                // Update pivot directly
                                EventStaff::where('event_id', $event->id)
                                    ->where('staff_id', $record->id)
                                    ->update(['status' => $data['status']]);
                                    
                                Notification::make()
                                    ->title('Status updated successfully')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Log::error("[EventStaffTable] Error updating status: " . $e->getMessage());
                                Notification::make()
                                    ->title('Error updating status: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                        
                    // Remove Staff Action
                    Action::make('remove')
                        ->label('Remove')
                        ->icon('heroicon-o-trash')
                        ->button()
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($record) use ($event) {
                            try {
                                $event->staff()->detach($record->id);
                                
                                Notification::make()
                                    ->title('Staff removed successfully')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Error removing staff')
                                    ->danger()
                                    ->send();
                            }
                        }),
                ])
                ->headerActions([
                    Action::make('add')
                        ->label('Add Staff')
                        ->button()
                        ->icon('heroicon-o-plus')
                        ->form([
                            Select::make('staff_id')
                                ->label('Staff')
                                ->options(function () use ($event) {
                                    $existingStaffIds = $event->staff->pluck('id')->toArray();
                                    return \App\Models\Staff::whereNotIn('id', $existingStaffIds)
                                        ->pluck('name', 'id');
                                })
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'hadir' => 'Hadir',
                                    'izin' => 'Izin',
                                    'alfa' => 'Alfa',
                                ])
                                ->default('alfa')
                                ->required(),
                        ])
                        ->action(function (array $data) use ($event) {
                            try {
                                $event->staff()->attach($data['staff_id'], [
                                    'status' => $data['status']
                                ]);
                                
                                Notification::make()
                                    ->title('Staff added successfully')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Error adding staff')
                                    ->danger()
                                    ->send();
                            }
                        }),
                ])
                ->defaultSort('name')
                ->striped()
                ->paginated(false);
                
        } catch (\Exception $e) {
            Log::error("[EventStaffTable] Error: " . $e->getMessage());
            return $table->query(fn() => \App\Models\Staff::query()->where('id', 0));
        }
    }
} 