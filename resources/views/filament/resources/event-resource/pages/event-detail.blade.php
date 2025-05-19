<x-filament-panels::page>
    <div class="mb-6">
        <div class="rounded-xl shadow p-6 dark:bg-gray-800" >
            <h2 class="text-xl font-bold mb-3">Event Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="mb-4">
                        <span class="font-medium ">Event Name:</span>
                        <span class="ml-2">{{ $event->name }}</span>
                    </div>
                    
                    <div class="mb-4">
                        <span class="font-medium">Date:</span>
                        <span class="ml-2">{{ \Carbon\Carbon::parse($event->date)->format('d F Y') }}</span>
                    </div>
                    
                    <div class="mb-4">
                        <span class="font-medium">Classroom:</span>
                        <span class="ml-2">{{ $event->classroom->name ?? 'N/A' }}</span>
                    </div>
                </div>
                
                <div>
                    <div class="mb-4">
                        <span class="font-medium">Description:</span>
                        <p class="mt-1">{{ $event->description }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <span class="font-medium">Total Students:</span>
                        <span class="ml-2">{{ $attendanceStats['total'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mb-6">
        <div class="rounded-xl shadow p-6 dark:bg-gray-800">
            <h2 class="text-xl font-bold mb-4">Attendance Statistics</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <h3 class="font-medium">Present (Hadir)</h3>
                    <div class="mt-2 text-2xl font-bold ">{{ $attendanceStats['hadir']['count'] }}</div>
                    <div class="text-sm">{{ $attendanceStats['hadir']['percentage'] }}% of students</div>
                </div>
                
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <h3 class="font-medium text-yellow-800">Excused (Izin)</h3>
                    <div class="mt-2 text-2xl font-bold text-yellow-600">{{ $attendanceStats['izin']['count'] }}</div>
                    <div class="text-sm text-yellow-700">{{ $attendanceStats['izin']['percentage'] }}% of students</div>
                </div>
                
                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                    <h3 class="font-medium text-red-800">Absent (Alfa)</h3>
                    <div class="mt-2 text-2xl font-bold text-red-600">{{ $attendanceStats['alfa']['count'] }}</div>
                    <div class="text-sm text-red-700">{{ $attendanceStats['alfa']['percentage'] }}% of students</div>
                </div>
            </div>
        </div>
    </div>
    
    <div>
        <h2 class="text-xl font-bold">Attendance List</h2>
    </div>

    {{ $this->table }}

</x-filament-panels::page>
