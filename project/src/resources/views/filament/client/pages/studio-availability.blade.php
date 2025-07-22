@php
    use Carbon\Carbon;
    $studio = App\Models\Studio::find($studioId);
@endphp

<x-filament::page>
    <div class="space-y-6">
        {{-- Search Form --}}
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-100 dark:border-gray-700">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    {{ $this->form }}
                </div>
                @if($studioId)
                    <div class="bg-primary-50 dark:bg-primary-900/30 p-3 rounded-lg border border-primary-100 dark:border-primary-800 flex items-center gap-3 min-w-[300px]">
                        <div class="bg-primary-100 dark:bg-primary-800 p-2 rounded-full">
                            <x-heroicon-o-photo class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                        </div>
                        <div>
                            <h4 class="font-bold text-primary-800 dark:text-primary-200">{{ $studio->nama_studio }}</h4>
                            <div class="flex items-center text-sm text-primary-600 dark:text-primary-300">
                                <x-heroicon-o-currency-dollar class="w-4 h-4 mr-1" />
                                Rp {{ number_format($studio->harga_per_jam, 0, ',', '.') }}/jam
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Results --}}
        @if($studioId)
            <div class="space-y-6">
                {{-- Summary Card --}}
                <div class="bg-gradient-to-r from-blue-50 to-primary-50 dark:from-blue-900/20 dark:to-primary-900/20 p-6 rounded-xl border border-blue-100 dark:border-blue-800/50 shadow-sm">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white">
                                Ketersediaan Studio pada {{ Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300">{{ $studio->nama_studio }}</p>
                        </div>

                        <div class="flex flex-wrap gap-4">
                            <div class="bg-white dark:bg-gray-800 px-4 py-2 rounded-lg border border-green-100 dark:border-green-800 shadow-xs flex items-center">
                                <span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span>
                                <span class="font-medium dark:text-white">Tersedia: {{ count($availableSlots) }} slot</span>
                            </div>
                            <div class="bg-white dark:bg-gray-800 px-4 py-2 rounded-lg border border-red-100 dark:border-red-800 shadow-xs flex items-center">
                                <span class="w-3 h-3 rounded-full bg-red-500 mr-2"></span>
                                <span class="font-medium dark:text-white">Booked: {{ count($bookedSlots) }} slot</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Visual Timeline --}}
                <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-white">Timeline Ketersediaan</h4>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <span class="inline-flex items-center gap-1 font-medium text-gray-800 dark:text-white">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $availabilityData ? reset($availabilityData)['start'] : '' }}
                            </span>
                            <span class="text-gray-400 dark:text-gray-500">→</span>
                            <span class="inline-flex items-center gap-1 font-medium text-gray-800 dark:text-white">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
                                {{ $availabilityData ? end($availabilityData)['end'] : '' }}
                            </span>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="h-2 bg-gray-100 dark:bg-gray-700 rounded-full w-full mb-2"></div>
                        <div class="flex justify-between absolute top-0 left-0 right-0 h-2">
                            @foreach($availabilityData as $slot)
                                @php
                                    $bgColor = $slot['status'] === 'booked' ? 'bg-red-400 dark:bg-red-600' : 'bg-green-400 dark:bg-green-600';
                                @endphp
                                <div 
                                    class="h-2 rounded-sm {{ $bgColor }}"
                                    style="width: calc(100%/{{ count($availabilityData) }} - 1px)"
                                    title="{{ $slot['start'] }} - {{ $slot['end'] }}: {{ $slot['status'] === 'available' ? 'Tersedia' : 'Dibooking' }}"
                                ></div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-700 dark:text-gray-300">
    @foreach(array_chunk($availabilityData, ceil(count($availabilityData)/4), true) as $chunk)
        <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-700 shadow-sm border border-gray-200 dark:border-gray-600">
            <div class="font-medium text-gray-800 dark:text-white flex items-center gap-1">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ reset($chunk)['start'] }}
            </div>
            <div class="text-gray-500 dark:text-gray-400 flex items-center gap-1">
                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ end($chunk)['end'] }}
            </div>
        </div>
    @endforeach
</div>

                </div>

                {{-- Available Slots --}}
                <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-white">
                            <x-heroicon-o-check-circle class="w-5 h-5 text-green-500 inline mr-2" />
                            Slot Tersedia
                        </h4>
                        <span class="text-sm bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-2 py-1 rounded-full">
                            {{ count($availableSlots) }} slot
                        </span>
                    </div>

                    @if(count($availableSlots) > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($availableSlots as $slot)
                                <a 
                                    href="{{ route('filament.client.resources.bookings.create', [
                                        'studio_id' => $studioId,
                                        'tanggal_booking' => $selectedDate,
                                        'jam_mulai' => $slot['start'],
                                        'jam_selesai' => $slot['end']
                                    ]) }}"
                                    class="group block p-4 border-2 border-green-100 dark:border-green-800 rounded-xl hover:border-green-300 dark:hover:border-green-600 hover:shadow-md transition-all duration-200"
                                >
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-lg text-gray-800 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400">
                                            {{ $slot['start'] }} - {{ $slot['end'] }}
                                        </span>
                                        <x-heroicon-o-arrow-right-circle 
                                            class="w-6 h-6 text-green-400 dark:text-green-500 group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors" />
                                    </div>
                                    <div class="mt-2 flex items-center justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">
                                            Durasi: 60 menit
                                        </span>
                                        <span class="text-sm font-medium bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-2 py-1 rounded">
                                            Rp {{ number_format($studio->harga_per_jam, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <x-heroicon-o-x-circle class="w-10 h-10 text-gray-400 dark:text-gray-500 mx-auto mb-3" />
                            <p class="text-gray-600 dark:text-gray-300 font-medium">Tidak ada slot tersedia pada tanggal ini</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Coba tanggal lain atau studio berbeda</p>
                        </div>
                    @endif
                </div>

                {{-- Booked Slots --}}
                @if(count($bookedSlots) > 0)
                    <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-white">
                                <x-heroicon-o-lock-closed class="w-5 h-5 text-red-500 inline mr-2" />
                                Slot Sudah Dibooking
                            </h4>
                            <span class="text-sm bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 px-2 py-1 rounded-full">
                                {{ count($bookedSlots) }} slot
                            </span>
                        </div>

                        <div class="space-y-3">
                            @foreach($bookedSlots as $slot)
                                @php
                                    $status = $slot['booking']['status'];
                                    $statusClasses = [
                                        'pending' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200',
                                        'confirmed' => 'bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200',
                                        'completed' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300',
                                    ];
                                    $statusClass = $statusClasses[$status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300';
                                @endphp
                                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <div class="font-bold text-gray-800 dark:text-white">
                                                {{ $slot['start'] }} - {{ $slot['end'] }}
                                            </div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                <span class="font-medium">Jadwal Asli:</span> 
                                                {{ $slot['booking']['jam_mulai'] }} - {{ $slot['booking']['jam_selesai'] }}
                                            </div>
                                        </div>
                                        <span class="text-xs px-2 py-1 rounded-full capitalize {{ $statusClass }}">
                                            {{ $status }}
                                        </span>
                                    </div>

                                    <div class="mt-3 pt-3 border-t border-dashed border-gray-200 dark:border-gray-700 flex items-center justify-between">
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                            <x-heroicon-o-user-circle class="w-5 h-5 mr-1.5" />
                                            {{ $slot['booking']['user'] ?? 'Unknown' }}
                                        </div>
                                        <div class="text-xs font-mono bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-1 rounded">
                                            #{{ $slot['booking']['booking_id'] }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="text-center py-12 bg-gradient-to-br from-blue-50 to-primary-50 dark:from-blue-900/20 dark:to-primary-900/20 rounded-xl border border-blue-100 dark:border-blue-800/50">
                <div class="max-w-md mx-auto">
                    <x-heroicon-o-calendar class="w-8 h-8 text-blue-400 mx-auto mb-4" />
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Cek Ketersediaan Studio</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">Pilih studio dan tanggal untuk melihat slot waktu yang tersedia</p>
                    <div class="animate-bounce">
                        <x-heroicon-o-arrow-down class="w-8 h-8 text-blue-400 mx-auto" />
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament::page>