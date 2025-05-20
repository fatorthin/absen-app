<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Models\Event;
use App\Models\Report;
use App\Models\Student;
use Filament\Tables\Table;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use App\Filament\Resources\EventResource;
use Filament\Tables\Columns\SelectColumn;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Tables\Actions\BulkActionGroup;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Concerns\InteractsWithTable;
use DesignTheBox\BarcodeField\Forms\Components\BarcodeInput;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventDetail extends Page implements HasTable
{
    use InteractsWithTable;
    protected static string $resource = EventResource::class;

    protected static ?string $model = Report::class;

    protected static string $view = 'filament.resources.event-resource.pages.event-detail';

    public Event $event;
    public $reports;
    public $attendanceStats = [];
    
    public function getTitle(): string | Htmlable
    {
        return __($this->event->name);
    }
    
    public function mount($record): void
    {
        $this->event = Event::findorFail($record);
        $this->reports = Report::where('event_id', $record)->get();
        $this->calculateAttendanceStats();
    }
    
    public function calculateAttendanceStats(): void
    {
        // Get statistics for chart
        $stats = Report::where('event_id', $this->event->id)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
            
        $totalStudents = array_sum($stats);
        
        // Ensure all statuses are represented
        $hadirCount = $stats['hadir'] ?? 0;
        $izinCount = $stats['izin'] ?? 0;
        $alfaCount = $stats['alfa'] ?? 0;
        
        // Calculate percentages
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
                BulkActionGroup::make([
                    BulkAction::make('hadir')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each->update(['status' => 'hadir']);
                            $this->calculateAttendanceStats();
                        }),
                    BulkAction::make('izin')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each->update(['status' => 'izin']);
                            $this->calculateAttendanceStats();
                        }),
                    BulkAction::make('alfa')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each->update(['status' => 'alfa']);
                            $this->calculateAttendanceStats();
                        }),
                ]),
            ])->headerActions([
                Action::make('add-student')
                    ->form([
                        Select::make('student_id')
                            ->label('Name')
                            ->options(function () {
                                // Get the event's classroom
                                $classroom = $this->event->classroom;
                                
                                if (!$classroom) {
                                    return [];
                                }
                                
                                // Get IDs of students already added to this event
                                $existingStudentIds = Report::where('event_id', $this->event->id)
                                    ->pluck('student_id')
                                    ->toArray();
                                
                                // Get students from the classroom who are not already in the event
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
                        
                        // Recalculate stats after adding a student
                        $this->calculateAttendanceStats();
                        
                        // Show notification
                        Notification::make()
                            ->title('Student added')
                            ->success()
                            ->send();
                    })->modalWidth(MaxWidth::Medium),
                Action::make('scanQrCode')
                    ->label('Scan QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->action(function (): void {
                        // Open the scanner in a new window - no event_id needed anymore
                        $url = route('qrcode.scanner');
                        
                        // The JS to open the new window will be handled by Filament
                        $this->js("window.open('{$url}', 'qrScanner', 'width=800,height=700')");
                    }),
            ])
            ->paginated(false)
            ->poll('1s');
    }
}
