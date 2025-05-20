<div>
    <button wire:click="initializePayment" class="btn btn-primary">Bayar Sekarang</button>

    @if ($isPaymentReady)
        <script>
            window.addEventListener('payment-ready', function(event) {
                snap.pay(event.detail.snapToken, {
                    onSuccess: function(result) {
                        window.location.href = '{{ route('filament.client.resources.bookings.edit', ['record' => $booking->id]) }}';
                    }
                });
            });
        </script>
    @endif
</div>

<!-- <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
 -->