@php
    use Carbon\Carbon;
    $studio = App\Models\Studio::find($studioId);
    $booking = App\Models\Booking::where('studio_id', $studioId)
        ->whereDate('tanggal_booking', $selectedDate)
        ->get();
@endphp

<x-filament::page>
    <div class="space-y-4 md:space-y-6">
        {{-- Search Form --}}
        <div class="p-4 md:p-6 bg-white dark:bg-gray-800 rounded-lg md:rounded-xl shadow border border-gray-200 dark:border-gray-700">
            <div class="flex flex-col lg:flex-row gap-4 md:gap-6">
                <div class="flex-1">
                    <div class="mb-3 flex items-center gap-2">
                        <x-heroicon-o-calendar class="w-5 h-5 text-primary-500" />
                        <h2 class="text-lg font-bold text-gray-800 dark:text-white">Booking Studio</h2>
                    </div>
                    <div class="space-y-3">
                        {{ $this->form }}
                    </div>
                </div>
                
                @if($studioId)
                <div class="bg-gradient-to-br from-primary-50 to-blue-50 dark:from-primary-900/20 dark:to-blue-900/20 p-4 md:p-5 rounded-lg border border-primary-100 dark:border-primary-800">
                    <div class="flex items-start gap-3">
                        @if($studio->foto)
                        <div class="flex-shrink-0 w-14 h-14 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600">
                            <img src="{{ asset('storage/'.$studio->foto) }}" alt="{{ $studio->nama_studio }}" class="w-full h-full object-cover">
                        </div>
                        @endif
                        <div class="min-w-0">
                            <h3 class="font-bold text-gray-800 dark:text-white">{{ $studio->nama_studio }}</h3>
                            <div class="mt-1 flex items-center text-sm text-primary-600 dark:text-primary-300">
                                <x-heroicon-o-currency-dollar class="w-4 h-4 mr-1" />
                                Rp {{ number_format($studio->harga_per_jam, 0, ',', '.') }}/jam
                            </div>
                            <div class="mt-2 flex flex-wrap gap-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <x-heroicon-o-check-circle class="w-3 h-3 mr-1" />
                                    Tersedia
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    <x-heroicon-o-clock class="w-3 h-3 mr-1" />
                                    {{ $studio->jam_operasional }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-dashed border-primary-200 dark:border-primary-700">
                        <h4 class="text-sm font-semibold text-primary-700 dark:text-primary-300 mb-1">Fasilitas:</h4>
                        <div class="flex flex-wrap gap-1">
                            @foreach(explode(',', $studio->fasilitas) as $facility)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                <x-heroicon-o-check class="w-3 h-3 mr-1 text-green-500" />
                                {{ trim($facility) }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Results --}}
        @if($studioId)
            <div class="space-y-4 md:space-y-6">
                {{-- Summary Card --}}
                <div class="bg-gradient-to-r from-blue-50 to-primary-50 dark:from-blue-900/20 dark:to-primary-900/20 p-4 md:p-6 rounded-lg border border-blue-200 dark:border-blue-800/50 shadow">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                        <div>
                            <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                <x-heroicon-o-calendar class="w-5 h-5 text-primary-500" />
                                <span>Ketersediaan Studio pada {{ Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}</span>
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mt-1">{{ $studio->nama_studio }}</p>
                        </div>

                        <div class="flex flex-wrap gap-2 mt-2 md:mt-0">
                            <div class="bg-white dark:bg-gray-800 px-3 py-1.5 rounded border border-green-200 dark:border-green-800 flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-green-500"></span>
                                <span class="text-sm font-medium dark:text-white">Tersedia: {{ count($availableSlots) }}</span>
                            </div>
                            @if(count($bookedSlots) > 0)
                            <div class="bg-white dark:bg-gray-800 px-3 py-1.5 rounded border border-red-200 dark:border-red-800 flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-red-500"></span>
                                <span class="text-sm font-medium dark:text-white">Booked: {{ count($bookedSlots) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Visual Timeline --}}
                <div class="p-4 md:p-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                        <h4 class="font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                            <x-heroicon-o-chart-bar class="w-5 h-5 text-blue-500" />
                            <span>Timeline Ketersediaan</span>
                        </h4>
                        <div class="flex items-center gap-3 text-sm">
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="relative h-3 bg-gray-100 dark:bg-gray-700 rounded-full w-full">
                            <div class="flex justify-between absolute top-0 left-0 right-0 h-3">
                                @foreach($availabilityData as $slot)
                                    @php
                                        $bgColor = $slot['status'] === 'booked' ? 'bg-red-500' : 'bg-green-500';
                                        $hoverColor = $slot['status'] === 'booked' ? 'hover:bg-red-600' : 'hover:bg-green-600';
                                    @endphp
                                    <div 
                                        class="h-3 rounded-sm {{ $bgColor }} {{ $hoverColor }} transition-all duration-200 cursor-help"
                                        style="width: calc(100%/{{ count($availabilityData) }} - 1px)"
                                        x-tooltip="'{{ $slot['start'] }} - {{ $slot['end'] }}: {{ $slot['status'] === 'available' ? 'Tersedia' : 'Dibooking' }}'"
                                    ></div>
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="flex justify-between mt-2 text-xs text-gray-500 dark:text-gray-400">
                            <span>{{ $availabilityData ? reset($availabilityData)['start'] : '' }}</span>
                            <span>{{ $availabilityData ? end($availabilityData)['end'] : '' }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach(array_chunk($availabilityData, ceil(count($availabilityData)/4), true) as $chunk)
                        <div class="p-3 rounded bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
                            <div class="flex items-center gap-2 mb-1">
                                <x-heroicon-o-clock class="w-4 h-4 text-blue-500" />
                                <span class="font-medium text-gray-800 dark:text-white">{{ reset($chunk)['start'] }} - {{ end($chunk)['end'] }}</span>
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">
                                @php
                                    $availableCount = count(array_filter($chunk, fn($slot) => $slot['status'] === 'available'));
                                    $totalCount = count($chunk);
                                @endphp
                                <span class="font-medium">{{ $availableCount }}/{{ $totalCount }}</span> slot tersedia
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Available Slots --}}
                <div class="p-4 md:p-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                                <x-heroicon-o-check-circle class="w-5 h-5" />
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800 dark:text-white">Slot Tersedia</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Pilih slot waktu untuk melanjutkan booking</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            {{ count($availableSlots) }} slot
                        </span>
                    </div>

                    @if(count($availableSlots) > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($availableSlots as $slot)
                                <a 
                                    href="{{ route('filament.client.resources.bookings.create', [
                                        'studio_id' => $studioId,
                                        'tanggal_booking' => $selectedDate,
                                        'jam_mulai' => $slot['start'],
                                        'jam_selesai' => $slot['end']
                                    ]) }}"
                                    class="group block p-4 border border-green-200 dark:border-green-800 rounded-lg hover:border-green-400 dark:hover:border-green-600 hover:shadow-md transition-all"
                                >
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-gray-800 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400">
                                            {{ $slot['start'] }} - {{ $slot['end'] }}
                                        </span>
                                        <x-heroicon-o-arrow-right-circle class="w-5 h-5 text-green-500 group-hover:text-green-600" />
                                    </div>
                                    <div class="mt-2 flex items-center justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-1">
                                            <x-heroicon-o-clock class="w-4 h-4" />
                                            <span>60 menit</span>
                                        </span>
                                        <span class="text-sm font-medium bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-2 py-1 rounded">
                                            Rp {{ number_format($studio->harga_per_jam, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="mt-3 pt-2 border-t border-dashed border-gray-200 dark:border-gray-700">
                                        <button class="w-full py-1.5 bg-green-500 hover:bg-green-600 text-white rounded text-sm font-medium flex items-center justify-center gap-2">
                                            <x-heroicon-o-bookmark class="w-4 h-4" />
                                            <span>Pesan Sekarang</span>
                                        </button>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 bg-gray-50 dark:bg-gray-700 rounded border border-dashed border-gray-300 dark:border-gray-600">
                            <div class="max-w-md mx-auto">
                                <x-heroicon-o-x-circle class="w-10 h-10 text-gray-400 dark:text-gray-500 mx-auto mb-3" />
                                <h3 class="font-bold text-gray-800 dark:text-white mb-1">Tidak Ada Slot Tersedia</h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-3">Maaf, tidak ada slot tersedia pada tanggal ini. Silakan coba tanggal lain.</p>
                                <button @click="$wire.set('selectedDate', '{{ Carbon::tomorrow()->format('Y-m-d') }}')" class="inline-flex items-center px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded text-sm font-medium">
                                    <x-heroicon-o-calendar class="w-4 h-4 mr-2" />
                                    Coba Besok
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Booked Slots --}}
                @if(count($bookedSlots) > 0)
                    <div class="p-4 md:p-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200">
                                    <x-heroicon-o-lock-closed class="w-5 h-5" />
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 dark:text-white">Slot Sudah Dibooking</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Berikut adalah slot yang sudah dipesan</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                {{ count($bookedSlots) }} slot
                            </span>
                        </div>

                        <div class="space-y-3">
                            @foreach($bookedSlots as $slot)
                                @php
                                    $status = $slot['booking']['status'];
                                    $statusClasses = [
                                        'pending' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200',
                                        'confirmed' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200',
                                        'completed' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300',
                                        'canceled' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200',
                                    ];
                                    $statusClass = $statusClasses[$status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300';
                                    $statusIcons = [
                                        'pending' => 'heroicon-o-clock',
                                        'confirmed' => 'heroicon-o-check-circle',
                                        'completed' => 'heroicon-o-check-badge',
                                        'canceled' => 'heroicon-o-x-circle',
                                    ];
                                    $statusIcon = $statusIcons[$status] ?? 'heroicon-o-question-mark-circle';
                                @endphp
                                <div class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:shadow-md transition-shadow">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-gray-800 dark:text-white">
                                                    {{ $slot['start'] }} - {{ $slot['end'] }}
                                                </span>
                                                <span class="text-xs px-2 py-0.5 rounded-full capitalize {{ $statusClass }} flex items-center gap-1">
                                                    <x-dynamic-component :component="$statusIcon" class="w-3 h-3" />
                                                    <span>{{ $status }}</span>
                                                </span>
                                            </div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                <span class="font-medium">Durasi:</span> 
                                                {{ Carbon::parse($slot['booking']['jam_mulai'])->diffInHours(Carbon::parse($slot['booking']['jam_selesai'])) }} jam
                                            </div>
                                        </div>
                                        <div class="text-xs font-mono bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-0.5 rounded">
                                            #{{ $slot['booking']['booking_id'] }}
                                        </div>
                                    </div>

                                    <div class="mt-2 pt-2 border-t border-dashed border-gray-200 dark:border-gray-700 flex items-center justify-between">
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                            <x-heroicon-o-user-circle class="w-4 h-4 mr-1.5 text-gray-400" />
                                            {{ $slot['booking']['user'] ?? 'Unknown' }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="text-center py-12 bg-gradient-to-br from-blue-50 to-primary-50 dark:from-blue-900/20 dark:to-primary-900/20 rounded-lg border-2 border-dashed border-blue-200 dark:border-blue-800/50">
                <div class="max-w-md mx-auto">
                    <div class="relative w-16 h-16 mx-auto mb-4">
                        <div class="absolute inset-0 bg-blue-100 dark:bg-blue-900 rounded-full opacity-20 animate-pulse"></div>
                        <x-heroicon-o-calendar class="w-10 h-10 text-blue-500 mx-auto relative" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Cek Ketersediaan Studio</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Pilih studio dan tanggal untuk melihat slot waktu yang tersedia untuk booking</p>
                    <div class="animate-bounce">
                        <x-heroicon-o-arrow-down class="w-6 h-6 text-blue-400 mx-auto" />
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament::page>