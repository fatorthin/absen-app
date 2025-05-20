<x-filament-panels::page>
    {{-- Filament will automatically render header widgets (like EventStaffTable) here or at the top by default --}}

    <div class="space-y-6">
        {{-- Event Information Section --}}
        <x-filament::section>
            <x-slot name="heading">Event Information</x-slot>
            
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-base font-medium text-gray-500">Date</h3>
                            <p class="mt-1 text-lg font-medium">{{ \Carbon\Carbon::parse($event->date)->format('d F Y') }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-base font-medium text-gray-500">Class</h3>
                            <p class="mt-1 text-lg font-medium">{{ $event->classroom ? $event->classroom->name : 'Not assigned' }}</p>
                        </div>
                    </div>
                </div>
                
                <div>
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-base font-medium text-gray-500">Description</h3>
                            <p class="mt-1 text-lg font-medium">{{ $event->description ?: 'No description' }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-base font-medium text-gray-500">Created At</h3>
                            <p class="mt-1 text-lg font-medium">{{ $event->created_at->format('d F Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::section>

        {{-- Attendance Statistics --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <x-filament::card>
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Total Students</h3>
                        <p class="text-3xl font-bold text-primary-600">{{ $attendanceStats['total'] }}</p>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Hadir</h3>
                        <p class="text-3xl font-bold text-success-600">{{ $attendanceStats['hadir']['count'] }}</p>
                        <p class="text-sm text-gray-500">{{ $attendanceStats['hadir']['percentage'] }}%</p>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Izin</h3>
                        <p class="text-3xl font-bold text-warning-600">{{ $attendanceStats['izin']['count'] }}</p>
                        <p class="text-sm text-gray-500">{{ $attendanceStats['izin']['percentage'] }}%</p>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Alfa</h3>
                        <p class="text-3xl font-bold text-danger-600">{{ $attendanceStats['alfa']['count'] }}</p>
                        <p class="text-sm text-gray-500">{{ $attendanceStats['alfa']['percentage'] }}%</p>
                    </div>
                </div>
            </x-filament::card>
        </div>

        {{-- Staff Table Widget - Directly include the widget --}}
        @livewire(\App\Filament\Widgets\EventStaffTable::class, ['eventId' => $event->id])

        {{-- Student Attendance Table --}}
        <x-filament::section>
            <x-slot name="heading">
                Student Attendance
            </x-slot>
            {{-- This renders the table defined in EventDetail.php's table() method --}}
            {{ $this->table }}
        </x-filament::section>
    </div>
</x-filament-panels::page>
