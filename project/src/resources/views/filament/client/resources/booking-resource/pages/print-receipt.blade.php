<x-filament::page>
    <div class="max-w-4xl mx-auto bg-white dark:bg-gray-900 p-6 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 print:border-none text-gray-800 dark:text-gray-200">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold">Struk Booking Studio Foto</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">No. Transaksi: <span class="font-semibold">#{{ $record->id }}</span></p>
            <p class="text-sm text-gray-400 dark:text-gray-500">
                Tanggal Cetak: {{ now()->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }}
            </p>
        </div>

        <div class="space-y-4 text-sm">
            <div class="border-b pb-3 border-gray-200 dark:border-gray-700">
                <h2 class="text-base font-semibold mb-2">Detail Booking</h2>
                <div class="flex justify-between border-b py-2 border-gray-200 dark:border-gray-700">
                    <span>Tanggal Booking:</span>
                    <span>{{ \Carbon\Carbon::parse($record->tanggal_booking)->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between border-b py-2 border-gray-200 dark:border-gray-700">
                    <span>Jam:</span>
                    <span>{{ \Carbon\Carbon::parse($record->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($record->jam_selesai)->format('H:i') }}</span>
                </div>
                <div class="flex justify-between border-b py-2 border-gray-200 dark:border-gray-700">
                    <span>Durasi:</span>
                    <span>{{ \Carbon\Carbon::parse($record->jam_mulai)->diffInHours($record->jam_selesai) }} Jam</span>
                </div>
                <div class="flex justify-between border-b py-2 border-gray-200 dark:border-gray-700">
                    <span>Studio:</span>
                    <span>{{ $record->studio->nama_studio }}</span>
                </div>
            </div>

            <div class="border-b pb-3 border-gray-200 dark:border-gray-700">
                <h2 class="text-base font-semibold mb-2">Biaya</h2>
                <div class="flex justify-between border-b py-2 border-gray-200 dark:border-gray-700">
                    <span>Harga per Jam:</span>
                    <span>Rp {{ number_format($record->studio->harga_per_jam, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between border-b py-2 border-gray-200 dark:border-gray-700">
                    <span>Total Durasi:</span>
                    <span>{{ \Carbon\Carbon::parse($record->jam_mulai)->diffInHours($record->jam_selesai) }} Jam</span>
                </div>
                <div class="flex justify-between font-bold text-lg pt-2 border-t border-gray-300 dark:border-gray-600">
                    <span>Total Bayar:</span>
                    <span class="text-blue-600 dark:text-blue-400">Rp {{ number_format($record->total_bayar, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="text-center text-xs mt-6 border-t pt-4 border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400">
                <p>Terima kasih telah menggunakan layanan studio kami.</p>
                <p>Struk ini berlaku sebagai bukti pembayaran resmi.</p>
            </div>
        </div>

        <div class="text-center mt-8 print:hidden">
            <button onclick="window.print()" class="px-5 py-2 bg-blue-600 text-black dark:text-white font-semibold rounded-md hover:bg-blue-700 transition">
                Cetak Struk
            </button>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden !important;
            }
            .max-w-4xl, .max-w-4xl * {
                visibility: visible !important;
            }
            .max-w-4xl {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                background: white !important;  /* Override background jadi putih saat cetak */
                color: black !important;       /* Override text jadi hitam */
            }
            /* Sembunyikan tombol cetak */
            button {
                display: none !important;
            }
            /* Override warna border jadi lebih terang */
            .border-gray-200 {
                border-color: #e5e7eb !important;
            }
            .text-gray-500 {
                color: #6b7280 !important;
            }
            .text-gray-400 {
                color: #9ca3af !important;
            }
            .text-gray-700 {
                color: #374151 !important;
            }
            .text-gray-800 {
                color: #1f2937 !important;
            }
            /* Override warna link dan tombol */
            .text-blue-600 {
                color: #2563eb !important;
            }
            .bg-blue-600 {
                background-color: #2563eb !important;
                color: white !important;
            }
        }
    </style>
</x-filament::page>
