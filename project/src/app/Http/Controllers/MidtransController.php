<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;

class MidtransController extends Controller
{
    public function handleNotification(Request $request)
    {
        $payload = $request->all();
        
        // Verifikasi signature jika diperlukan
        $booking = Booking::where('payment_order_id', $payload['order_id'])->first();
        
        if (!$booking) {
            return response()->json(['status' => 'error', 'message' => 'Booking not found'], 404);
        }

        $transactionStatus = $payload['transaction_status'];
        $fraudStatus = $payload['fraud_status'] ?? null;

        // Update status berdasarkan notifikasi
        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'accept') {
                $booking->update([
                    'payment_status' => 'paid',
                    'payment_method' => $payload['payment_type'],
                    'paid_at' => now(),
                    'payment_metadata' => $payload
                ]);
            }
        } elseif ($transactionStatus == 'settlement') {
            $booking->update([
                'payment_status' => 'paid',
                'payment_method' => $payload['payment_type'],
                'paid_at' => now(),
                'payment_metadata' => $payload
            ]);
        } elseif ($transactionStatus == 'pending') {
            $booking->update([
                'payment_status' => 'pending',
                'payment_metadata' => $payload
            ]);
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            $booking->update([
                'payment_status' => 'failed',
                'payment_metadata' => $payload
            ]);
        }

        return response()->json(['status' => 'success']);
    }
}