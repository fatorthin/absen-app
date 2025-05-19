<x-filament::page>
    <x-filament::card>
        <div class="flex items-center gap-4">
            @if($record->avatar)
                <img src="{{ Storage::url($record->avatar) }}" alt="{{ $record->name }}" class="w-24 h-24 rounded-full object-cover">
            @else
                <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center">
                    <span class="text-2xl font-bold text-gray-500">{{ substr($record->name, 0, 1) }}</span>
                </div>
            @endif
            
            <div>
                <h2 class="text-2xl font-bold">{{ $record->name }}</h2>
                <div class="grid grid-cols-2 gap-x-8 gap-y-2 mt-2">
                    <div>
                        <span class="font-medium">Group:</span> {{ $record->group->name ?? 'None' }}
                    </div>
                    <div>
                        <span class="font-medium">Class:</span> {{ $record->class ?? 'None' }}
                    </div>
                    <div>
                        <span class="font-medium">Gender:</span> {{ $record->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}
                    </div>
                    <div>
                        <span class="font-medium">Birthdate:</span> {{ $record->birthdate ? \Carbon\Carbon::parse($record->birthdate)->format('d F Y') : 'None' }}
                    </div>
                    <div>
                        <span class="font-medium">Age:</span> {{ $record->birthdate ? \Carbon\Carbon::parse($record->birthdate)->age . ' years' : 'Unknown' }}
                    </div>
                    <div>
                        <span class="font-medium">Contact:</span> {{ $record->no_wa }}
                    </div>
                </div>
            </div>
        </div>
    </x-filament::card>

    <div class="mt-6">
        @livewire(\App\Filament\Resources\StudentResource\Widgets\StudentClassroomsTable::class, ['record' => $record])
    </div>
    
    <div class="mt-6">
        @livewire(\App\Filament\Resources\StudentResource\Widgets\StudentEventsTable::class, ['record' => $record])
    </div>
</x-filament::page> 