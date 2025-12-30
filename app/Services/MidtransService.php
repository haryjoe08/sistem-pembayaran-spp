<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use App\Models\PaymentOrder;
use App\Models\Tagihan;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    public function __construct()
    {
        // Set Midtrans configuration
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Create Snap Token untuk pembayaran
     */
    public function createSnapToken(Tagihan $tagihan, $amount)
    {
        try {
            $orderId = PaymentOrder::generateOrderId();
            $siswa = $tagihan->siswa;
            
            // Prepare transaction details
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $amount,
                ],
                'customer_details' => [
                    'first_name' => $siswa->nama,
                    'email' => $siswa->login->email ?? 'noreply@school.com',
                    'phone' => $siswa->kontak ?? '081234567890',
                ],
                'item_details' => [
                    [
                        'id' => $tagihan->id,
                        'price' => (int) $amount,
                        'quantity' => 1,
                        'name' => $tagihan->jenisTagihan->nama,
                        'category' => 'SPP',
                    ],
                ],
                'callbacks' => [
                    'finish' => config('midtrans.finish_url'),
                    'unfinish' => config('midtrans.unfinish_url'),
                    'error' => config('midtrans.error_url'),
                ],
                'expiry' => [
                    'unit' => 'day',
                    'duration' => 1, // Expired dalam 1 hari
                ],
            ];

            // Get Snap Token
            $snapToken = Snap::getSnapToken($params);

            // Save to payment_orders
            $paymentOrder = PaymentOrder::create([
                'order_id' => $orderId,
                'tagihan_id' => $tagihan->id,
                'siswa_nis' => $tagihan->siswa_nis,
                'amount' => $amount,
                'snap_token' => $snapToken,
                'status' => 'pending',
                'expired_at' => now()->addDay(),
            ]);

            return [
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $orderId,
                'payment_order' => $paymentOrder,
            ];

        } catch (\Exception $e) {
            Log::error('Midtrans Create Snap Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check transaction status
     */
    public function checkTransactionStatus($orderId)
    {
        try {
            $status = Transaction::status($orderId);
            return [
                'success' => true,
                'data' => $status,
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans Check Status Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Handle notification from Midtrans webhook
     */
    public function handleNotification($notification)
    {
        try {
            $orderId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status ?? null;
            $paymentType = $notification->payment_type ?? null;

            // Get payment order
            $paymentOrder = PaymentOrder::where('order_id', $orderId)->first();
            
            if (!$paymentOrder) {
                Log::error('Payment order not found: ' . $orderId);
                return false;
            }

            // Update payment order data
            $paymentOrder->transaction_id = $notification->transaction_id ?? null;
            $paymentOrder->payment_type = $paymentType;
            $paymentOrder->midtrans_response = (array) $notification;

            // Determine status
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'accept') {
                    $paymentOrder->status = 'capture';
                    $this->processSuccessPayment($paymentOrder);
                } else {
                    $paymentOrder->status = 'pending';
                }
            } elseif ($transactionStatus == 'settlement') {
                $paymentOrder->status = 'settlement';
                $this->processSuccessPayment($paymentOrder);
            } elseif ($transactionStatus == 'pending') {
                $paymentOrder->status = 'pending';
                
                // Save payment code untuk VA/Store
                if (isset($notification->bill_key)) {
                    $paymentOrder->payment_code = $notification->bill_key;
                } elseif (isset($notification->va_numbers[0]->va_number)) {
                    $paymentOrder->payment_code = $notification->va_numbers[0]->va_number;
                } elseif (isset($notification->permata_va_number)) {
                    $paymentOrder->payment_code = $notification->permata_va_number;
                } elseif (isset($notification->payment_code)) {
                    $paymentOrder->payment_code = $notification->payment_code;
                }

                // Save PDF URL untuk VA
                if (isset($notification->pdf_url)) {
                    $paymentOrder->pdf_url = $notification->pdf_url;
                }
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                $paymentOrder->status = $transactionStatus;
            } elseif ($transactionStatus == 'refund') {
                $paymentOrder->status = 'refund';
            }

            $paymentOrder->save();

            Log::info('Payment notification processed: ' . $orderId . ' - ' . $transactionStatus);
            
            return true;

        } catch (\Exception $e) {
            Log::error('Midtrans Handle Notification Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Process successful payment
     */
    private function processSuccessPayment(PaymentOrder $paymentOrder)
    {
        // Update payment order
        $paymentOrder->paid_at = now();
        $paymentOrder->save();

        // Get tagihan
        $tagihan = $paymentOrder->tagihan;
        
        // Update tagihan
        $tagihan->sudah_dibayar += $paymentOrder->amount;
        
        // Auto set status lunas
        if ($tagihan->sudah_dibayar >= $tagihan->total_tagihan) {
            $tagihan->status = 'lunas';
            $tagihan->sudah_dibayar = $tagihan->total_tagihan; // Normalisasi
        }
        
        $tagihan->save();

        // Create transaksi record
        \App\Models\Transaksi::create([
            'tagihan_id' => $tagihan->id,
            'siswa_nis' => $tagihan->siswa_nis,
            'tanggal' => now(),
            'jumlah_bayar' => $paymentOrder->amount,
            'metode' => $this->mapPaymentMethod($paymentOrder->payment_type),
            'keterangan' => 'Pembayaran via ' . $paymentOrder->paymentTypeLabel() . ' - Order ID: ' . $paymentOrder->order_id,
        ]);

        Log::info('Payment processed successfully: ' . $paymentOrder->order_id);
    }

    /**
     * Map Midtrans payment type to our metode
     */
    private function mapPaymentMethod($midtransType)
    {
        return match($midtransType) {
            'gopay', 'shopeepay', 'qris' => 'qris',
            'bank_transfer', 'echannel', 'bca_klikpay', 'bri_epay', 'cimb_clicks' => 'va',
            'credit_card' => 'transfer',
            default => 'transfer',
        };
    }
}