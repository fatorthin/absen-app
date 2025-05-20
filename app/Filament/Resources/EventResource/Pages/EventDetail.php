<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use Filament\Actions;
use Filament\Resources\Pages\Page;
use App\Models\Event;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Models\Report;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Forms\Components\Select as FormSelect;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Filament\Tables\Actions\BulkAction as FilamentBulkAction;
use Filament\Tables\Actions\BulkActionGroup as FilamentBulkActionGroup;
use Filament\Tables\Actions\Action as FilamentTableAction;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log as LaravelLog;
use Illuminate\Contracts\Support\Htmlable;

class EventDetail extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = EventResource::class;

    protected static string $view = 'filament.resources.event-resource.pages.event-detail';

    public Event $event;
    public $attendanceStats = [];

    public function getTitle(): string | Htmlable
    {
        return __($this->event->name);
    }

    public function mount($record): void
    {
        $this->event = Event::findOrFail($record);
        $this->calculateAttendanceStats();
        LaravelLog::info('[EventDetail MOUNT] Event ID: ' . $this->event->id);
    }

    public function calculateAttendanceStats(): void
    {
        $stats = Report::where('event_id', $this->event->id)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
            
        $totalStudents = array_sum($stats);
        
        $hadirCount = $stats['hadir'] ?? 0;
        $izinCount = $stats['izin'] ?? 0;
        $alfaCount = $stats['alfa'] ?? 0;
        
        $this->attendanceStats = [
            'hadir' => [
                'count' => $hadirCount,
                'percentage' => $totalStudents > 0 ? round(($hadirCount / $totalStudents) * 100, 1) : 0
            ],
            'izin' => [
                'count' => $izinCount,
                'percentage' => $totalStudents > 0 ? round(($izinCount / $totalStudents) * 100, 1) : 0
            ],
            'alfa' => [
                'count' => $alfaCount,
                'percentage' => $totalStudents > 0 ? round(($alfaCount / $totalStudents) * 100, 1) : 0
            ],
            'total' => $totalStudents
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn() => Report::where('event_id', $this->event->id))
            ->columns([
                TextColumn::make('student.name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('student.group.name')
                    ->label('Group')
                    ->searchable()
                    ->sortable(),
                SelectColumn::make('status')->options([
                    'hadir' => 'Hadir',
                    'izin' => 'Izin',
                    'alfa' => 'Alfa',
                ])->selectablePlaceholder(false)
                  ->afterStateUpdated(function () {
                      $this->calculateAttendanceStats();
                  }),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->sortable()
                    ->formatStateUsing(function ($state, $record) {
                        return $record->status !== 'hadir' ? 'N/A' : date_format($state, 'H:i:s');
                    }),
            ])
            ->filters([
                // Define your filters here
            ])
            ->actions([
                // Define your actions here
            ])
            ->bulkActions([
                FilamentBulkActionGroup::make([
                    FilamentBulkAction::make('hadir')
                        ->requiresConfirmation()
                        ->action(function (EloquentCollection $records) {
                            $records->each->update(['status' => 'hadir']);
                            $this->calculateAttendanceStats();
                        }),
                    FilamentBulkAction::make('izin')
                        ->requiresConfirmation()
                        ->action(function (EloquentCollection $records) {
                            $records->each->update(['status' => 'izin']);
                            $this->calculateAttendanceStats();
                        }),
                    FilamentBulkAction::make('alfa')
                        ->requiresConfirmation()
                        ->action(function (EloquentCollection $records) {
                            $records->each->update(['status' => 'alfa']);
                            $this->calculateAttendanceStats();
                        }),
                ]),
            ])->headerActions([
                FilamentTableAction::make('add-student')
                    ->form([
                        FormSelect::make('student_id')
                            ->label('Name')
                            ->options(function () {
                                $classroom = $this->event->classroom;
                                if (!$classroom) return [];
                                $existingStudentIds = Report::where('event_id', $this->event->id)
                                    ->pluck('student_id')
                                    ->toArray();
                                return $classroom->students()
                                    ->whereNotIn('students.id', $existingStudentIds)
                                    ->pluck('name', 'students.id');
                            })
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        $record = new Report();
                        $record->student_id = $data['student_id'];
                        $record->event_id = $this->event->id;
                        $record->status = 'alfa';
                        $record->save();
                        $this->calculateAttendanceStats();
                        FilamentNotification::make()
                            ->title('Student added')
                            ->success()
                            ->send();
                    })->modalWidth(MaxWidth::Medium),
                FilamentTableAction::make('scanQrCode')
                    ->label('Scan QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->action(function (): void {
                        $url = route('qrcode.scanner');
                        $this->js("window.open('{$url}', 'qrScanner', 'width=800,height=700')");
                    }),
            ])
            ->paginated(false)
            ->poll('10s');
    }
}
