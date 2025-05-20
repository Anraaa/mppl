<x-filament::page>
    <x-filament::card>
        <div class="space-y-6">

            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Pembayaran Booking Studio
                </h2>

                <div class="mt-4 bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                    <p class="font-medium">{{ $this->booking->studio->nama_studio }}</p>
                    <p class="text-gray-600 dark:text-gray-300">{{ \Carbon\Carbon::parse($this->booking->tanggal_booking)->translatedFormat('l, d F Y') }}</p>
                    <p class="text-gray-600 dark:text-gray-300">
                        {{ \Carbon\Carbon::parse($this->booking->jam_mulai)->format('H:i') }} - 
                        {{ \Carbon\Carbon::parse($this->booking->jam_selesai)->format('H:i') }}
                    </p>
                    <p class="mt-2 text-2xl font-bold text-primary-600">
                        Rp {{ number_format($this->booking->total_bayar, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            @if (!$isPaymentReady)
            <div class="text-center">
                <button 
                    wire:click="initializePayment" 
                    wire:loading.attr="disabled"
                    class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition"
                    id="pay-button"
                >
                    <span wire:loading.remove>Lanjutkan ke Pembayaran</span>
                    <span wire:loading>
                        <svg class="animate-spin h-5 w-5 text-white mx-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </div>
            @else
            <div class="mt-6 text-center">
                <p class="mb-4 text-gray-700 dark:text-gray-300">Sedang membuka pembayaran...</p>
            </div>
            @endif

        </div>

        @push('scripts')
            

        {{-- Load snap.js --}}
        <script>
            function loadSnapScript() {
                const script = document.createElement('script');
                script.src = 'https://app.sandbox.midtrans.com/snap/snap.js';
                script.setAttribute('data-client-key', '{{ config("services.midtrans.client_key") }}');
                script.onload = () => {
                    const snapToken = @json($snapToken); // Pastikan ini tidak null
                    console.log('Snap.js loaded! Token:', snapToken);
                    snap.pay(snapToken, {
            onSuccess: function(result) {
                console.log('success', result);
                window.livewire.emit('paymentSuccess', result);
            },
            onPending: function(result) {
                console.log('pending', result);
                window.livewire.emit('paymentPending', result);
            },
            onError: function(result) {
                console.log('error', result);
                window.livewire.emit('paymentError', result);
            },
            onClose: function() {
                console.log('customer closed the popup without finishing the payment');
                window.livewire.emit('paymentClosed');
            }
        });
                };
            document.body.appendChild(script);
            }

            // Jalankan saat halaman siap
            document.addEventListener('DOMContentLoaded', loadSnapScript);

            
        </script>
        
        {{-- End Load snap.js --}}
        
        @endpush


    </x-filament::card>
</x-filament::page>
