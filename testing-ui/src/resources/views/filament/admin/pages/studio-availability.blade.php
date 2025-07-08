@php
    use Carbon\Carbon;
    $studio = App\Models\Studio::find($studioId);
@endphp

<x-filament::page>
    <div class="space-y-6">
        {{-- Search Form --}}
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex flex-col md:flex-row gap-6">
                <div class="flex-1">
                    {{ $this->form }}
                </div>
                @if($studioId)
                    <div class="bg-primary-50/50 dark:bg-primary-900/20 p-4 rounded-xl border border-primary-200 dark:border-primary-800 flex items-center gap-4 min-w-[300px] transition-all duration-300 hover:shadow-md">
                        <div class="bg-primary-100/80 dark:bg-primary-800 p-3 rounded-xl">
                            <x-heroicon-o-photo class="w-7 h-7 text-primary-600 dark:text-primary-400" />
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-lg text-primary-800 dark:text-primary-200">{{ $studio->nama_studio }}</h4>
                            <div class="flex items-center text-sm text-primary-600 dark:text-primary-300 mt-1">
                                <x-heroicon-o-currency-dollar class="w-4 h-4 mr-1.5" />
                                Rp {{ number_format($studio->harga_per_jam, 0, ',', '.') }}/jam
                            </div>
                            <div class="mt-2 flex items-center text-xs text-primary-500 dark:text-primary-400">
                                <x-heroicon-o-information-circle class="w-3.5 h-3.5 mr-1" />
                                Kapasitas: {{ $studio->kapasitas }} orang
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
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                <x-heroicon-o-calendar class="w-5 h-5 text-blue-500" />
                                <span>Ketersediaan Studio pada {{ Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}</span>
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mt-1 flex items-center gap-1.5">
                                <x-heroicon-o-building-office class="w-4 h-4" />
                                {{ $studio->nama_studio }}
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <div class="bg-white/80 dark:bg-gray-800 px-4 py-2 rounded-lg border border-green-100 dark:border-green-800 shadow-xs flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-green-500"></span>
                                <span class="font-medium dark:text-white">Tersedia: {{ count($availableSlots) }} slot</span>
                            </div>
                            <div class="bg-white/80 dark:bg-gray-800 px-4 py-2 rounded-lg border border-red-100 dark:border-red-800 shadow-xs flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-red-500"></span>
                                <span class="font-medium dark:text-white">Booked: {{ count($bookedSlots) }} slot</span>
                            </div>
                            @if($studio->is_active)
                                <div class="bg-white/80 dark:bg-gray-800 px-4 py-2 rounded-lg border border-blue-100 dark:border-blue-800 shadow-xs flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                                    <span class="font-medium dark:text-white">Status: Aktif</span>
                                </div>
                            @else
                                <div class="bg-white/80 dark:bg-gray-800 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 shadow-xs flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-gray-500"></span>
                                    <span class="font-medium dark:text-white">Status: Tidak Aktif</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Visual Timeline --}}
                <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5">
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                            <x-heroicon-o-chart-bar class="w-5 h-5 text-primary-500" />
                            <span>Timeline Ketersediaan</span>
                        </h4>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex items-center gap-1 font-medium text-gray-800 dark:text-white">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $availabilityData ? reset($availabilityData)['start'] : '' }}
                            </div>
                            <span class="hidden sm:block text-gray-400 dark:text-gray-500">→</span>
                            <div class="flex items-center gap-1 font-medium text-gray-800 dark:text-white">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $availabilityData ? end($availabilityData)['end'] : '' }}
                            </div>
                        </div>
                    </div>

                    <div class="relative mb-8">
                        <div class="h-3 bg-gray-100 dark:bg-gray-700 rounded-full w-full"></div>
                        <div class="flex justify-between absolute top-0 left-0 right-0 h-3">
                            @foreach($availabilityData as $slot)
                                @php
                                    $bgColor = $slot['status'] === 'booked' ? 'bg-red-500 dark:bg-red-600' : 'bg-green-500 dark:bg-green-600';
                                    $hoverColor = $slot['status'] === 'booked' ? 'hover:bg-red-600 dark:hover:bg-red-700' : 'hover:bg-green-600 dark:hover:bg-green-700';
                                @endphp
                                <div 
                                    class="h-3 rounded-sm {{ $bgColor }} {{ $hoverColor }} transition-all duration-200 cursor-help"
                                    style="width: calc(100%/{{ count($availabilityData) }} - 1px)"
                                    x-tooltip="'{{ $slot['start'] }} - {{ $slot['end'] }}: {{ $slot['status'] === 'available' ? 'Tersedia' : 'Dibooking' }}'"
                                ></div>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                        @foreach(array_chunk($availabilityData, ceil(count($availabilityData)/4), true) as $chunk)
                            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50 shadow-sm border border-gray-200 dark:border-gray-600 hover:shadow-md transition-all duration-300">
                                <div class="font-medium text-gray-800 dark:text-white flex items-center gap-2 mb-2">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ reset($chunk)['start'] }}
                                </div>
                                <div class="text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ end($chunk)['end'] }}
                                </div>
                                <div class="mt-3 pt-3 border-t border-dashed border-gray-200 dark:border-gray-600 text-xs text-gray-500 dark:text-gray-400">
                                    {{ count(array_filter($chunk, fn($item) => $item['status'] === 'available')) }} slot tersedia
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Available Slots --}}
                <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5">
                        <div class="flex items-center gap-3">
                            <div class="bg-green-100 dark:bg-green-900/30 p-2 rounded-lg">
                                <x-heroicon-o-check-circle class="w-6 h-6 text-green-600 dark:text-green-400" />
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-white">Slot Tersedia</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Pilih slot waktu untuk melanjutkan booking</p>
                            </div>
                        </div>
                        <span class="text-sm bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 px-3 py-1.5 rounded-full font-medium">
                            {{ count($availableSlots) }} slot tersedia
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
                                    class="group block p-5 border-2 border-green-100 dark:border-green-800 rounded-xl hover:border-green-300 dark:hover:border-green-600 hover:shadow-md transition-all duration-300 bg-gradient-to-br from-green-50/50 to-white dark:from-green-900/10 dark:to-gray-800"
                                >
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-lg text-gray-800 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400">
                                            {{ $slot['start'] }} - {{ $slot['end'] }}
                                        </span>
                                        <x-heroicon-o-arrow-right-circle 
                                            class="w-6 h-6 text-green-400 dark:text-green-500 group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors" />
                                    </div>
                                    <div class="mt-3 flex items-center justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-1.5">
                                            <x-heroicon-o-clock class="w-4 h-4" />
                                            Durasi: 60 menit
                                        </span>
                                        <span class="text-sm font-medium bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-2.5 py-1 rounded">
                                            Rp {{ number_format($studio->harga_per_jam, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="mt-3 pt-3 border-t border-dashed border-green-100 dark:border-green-800/50">
                                        <span class="text-xs text-green-600 dark:text-green-400 font-medium">
                                            <x-heroicon-o-bolt class="w-3.5 h-3.5 inline mr-1" />
                                            Slot tersedia - Klik untuk booking
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-dashed border-gray-200 dark:border-gray-600">
                            <x-heroicon-o-x-circle class="w-12 h-12 text-gray-400 dark:text-gray-500 mx-auto mb-4" />
                            <h5 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">Tidak ada slot tersedia</h5>
                            <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">Maaf, tidak ada slot waktu yang tersedia untuk studio ini pada tanggal {{ Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}. Silakan coba tanggal lain.</p>
                            <button 
                                wire:click="$set('selectedDate', now()->format('Y-m-d'))" 
                                class="mt-4 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition-colors duration-200 inline-flex items-center gap-1.5"
                            >
                                <x-heroicon-o-calendar class="w-4 h-4" />
                                Cek Hari Ini
                            </button>
                        </div>
                    @endif
                </div>

                {{-- Booked Slots --}}
                @if(count($bookedSlots) > 0)
                    <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5">
                            <div class="flex items-center gap-3">
                                <div class="bg-red-100 dark:bg-red-900/30 p-2 rounded-lg">
                                    <x-heroicon-o-lock-closed class="w-6 h-6 text-red-600 dark:text-red-400" />
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-800 dark:text-white">Slot Sudah Dibooking</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Detail booking yang sudah ada</p>
                                </div>
                            </div>
                            <span class="text-sm bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 px-3 py-1.5 rounded-full font-medium">
                                {{ count($bookedSlots) }} slot terisi
                            </span>
                        </div>

                        <div class="space-y-4">
                            @foreach($bookedSlots as $slot)
                                @php
                                    $status = $slot['booking']['status'];
                                    $statusClasses = [
                                        'pending' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200',
                                        'confirmed' => 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200',
                                        'completed' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300',
                                        'cancelled' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200',
                                    ];
                                    $statusClass = $statusClasses[$status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300';
                                    $statusIcon = [
                                        'pending' => 'heroicon-o-clock',
                                        'confirmed' => 'heroicon-o-check-circle',
                                        'completed' => 'heroicon-o-check-badge',
                                        'cancelled' => 'heroicon-o-x-circle',
                                    ][$status] ?? 'heroicon-o-question-mark-circle';
                                @endphp
                                <div class="p-5 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                        <div class="flex-1">
                                            <div class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                                <x-dynamic-component :component="$statusIcon" class="w-5 h-5" />
                                                {{ $slot['start'] }} - {{ $slot['end'] }}
                                            </div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400 mt-2 flex items-center gap-2">
                                                <x-heroicon-o-user-circle class="w-4 h-4" />
                                                {{ $slot['booking']['user'] ?? 'Unknown' }}
                                            </div>
                                        </div>
                                        <div class="flex flex-col sm:items-end gap-2">
                                            <span class="text-xs px-3 py-1 rounded-full capitalize {{ $statusClass }} flex items-center gap-1.5">
                                                <x-dynamic-component :component="$statusIcon" class="w-3.5 h-3.5" />
                                                {{ $status }}
                                            </span>
                                            <div class="text-xs font-mono bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2.5 py-1 rounded">
                                                #{{ $slot['booking']['booking_id'] }}
                                            </div>
                                        </div>
                                    </div>

                                    @if($slot['booking']['notes'])
                                        <div class="mt-3 pt-3 border-t border-dashed border-gray-200 dark:border-gray-700">
                                            <div class="text-sm text-gray-600 dark:text-gray-400 flex items-start gap-2">
                                                <x-heroicon-o-chat-bubble-left-ellipsis class="w-4 h-4 mt-0.5 flex-shrink-0" />
                                                <span class="italic">"{{ $slot['booking']['notes'] }}"</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="text-center py-16 bg-gradient-to-br from-blue-50 to-primary-50 dark:from-blue-900/20 dark:to-primary-900/20 rounded-xl border border-dashed border-blue-200 dark:border-blue-800/50">
                <div class="max-w-md mx-auto">
                    <div class="bg-white dark:bg-gray-800 w-16 h-16 mx-auto rounded-full shadow-md flex items-center justify-center mb-6 border border-blue-100 dark:border-blue-800">
                        <x-heroicon-o-calendar class="w-8 h-8 text-blue-500" />
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-3">Cek Ketersediaan Studio</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">Pilih studio dan tanggal untuk melihat slot waktu yang tersedia untuk booking</p>
                    <div class="animate-bounce">
                        <x-heroicon-o-arrow-down class="w-8 h-8 text-blue-400 mx-auto" />
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script src="https://unpkg.com/@ryangjchandler/alpine-tooltip@1.2.0/dist/cdn.min.js"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.plugin(Tooltip);
            });
        </script>
    @endpush
</x-filament::page>