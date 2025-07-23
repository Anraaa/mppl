<x-filament::page>
    <div class="space-y-6">
        <!-- Welcome Section -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Selamat Datang, {{ auth()->user()->name }}!</h2>
            <p>Anda memiliki {{ $this->upcomingBookings->count() }} booking yang akan datang</p>
        </div>

        <!-- Upcoming Bookings -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Booking Mendatang</h3>
            @if($this->upcomingBookings->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($this->upcomingBookings as $booking)
                        <div class="border rounded-lg p-4">
                            <h4 class="font-medium">{{ $booking->studio->nama_studio }}</h4>
                            <p class="text-sm text-gray-600">
                                {{ Carbon::parse($booking->tanggal_booking)->translatedFormat('l, d F Y') }}
                            </p>
                            <p class="text-sm">
                                {{ Carbon::parse($booking->jam_mulai)->format('H:i') }} - 
                                {{ Carbon::parse($booking->jam_selesai)->format('H:i') }}
                            </p>
                            <p class="text-sm font-medium mt-2">
                                Total: Rp {{ number_format($booking->total_bayar, 0, ',', '.') }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">Anda tidak memiliki booking yang akan datang</p>
            @endif
        </div>

        <!-- Available Studios -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Studio Tersedia</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @foreach($this->availableStudios as $studio)
                    <div class="border rounded-lg overflow-hidden">
                        @if($studio->foto)
                            <img src="{{ asset('storage/' . $studio->foto) }}" alt="{{ $studio->nama_studio }}" class="w-full h-32 object-cover">
                        @endif
                        <div class="p-4">
                            <h4 class="font-medium">{{ $studio->nama_studio }}</h4>
                            <p class="text-sm text-gray-600">Rp {{ number_format($studio->harga_per_jam, 0, ',', '.') }}/jam</p>
                            <p class="text-xs mt-2">{{ $studio->jam_operasional }}</p>
                            <a href="{{ route('filament.client.resources.bookings.create') }}" class="mt-2 inline-block text-sm text-primary-500 hover:underline">
                                Booking Sekarang
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Riwayat Booking Terakhir</h3>
            <div class="space-y-3">
                @foreach($this->recentBookings as $booking)
                    <div class="border-b pb-3 last:border-b-0">
                        <div class="flex justify-between">
                            <div>
                                <h4 class="font-medium">{{ $booking->studio->nama_studio }}</h4>
                                <p class="text-sm text-gray-600">
                                    {{ Carbon::parse($booking->tanggal_booking)->translatedFormat('d M Y') }} | 
                                    {{ Carbon::parse($booking->jam_mulai)->format('H:i') }}-{{ Carbon::parse($booking->jam_selesai)->format('H:i') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium">Rp {{ number_format($booking->total_bayar, 0, ',', '.') }}</p>
                                <span class="inline-block px-2 py-1 text-xs rounded-full 
                                    {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $booking->status === 'canceled' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-filament::page>