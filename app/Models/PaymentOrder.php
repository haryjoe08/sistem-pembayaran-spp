<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'tagihan_id',
        'siswa_nis',
        'amount',
        'payment_type',
        'status',
        'transaction_id',
        'payment_code',
        'pdf_url',
        'snap_token',
        'payment_url',
        'midtrans_response',
        'paid_at',
        'expired_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'midtrans_response' => 'array',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    // Relations
    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_nis', 'nis');
    }

    // Helpers
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isSuccess()
    {
        return in_array($this->status, ['settlement', 'capture']);
    }

    public function isFailed()
    {
        return in_array($this->status, ['deny', 'cancel', 'expire', 'failure']);
    }

    public function isExpired()
    {
        return $this->expired_at && now()->isAfter($this->expired_at);
    }

    // Generate unique order ID
    public static function generateOrderId()
    {
        $date = date('Ymd');
        $lastOrder = self::whereDate('created_at', today())
                         ->orderBy('id', 'desc')
                         ->first();
        
        $number = $lastOrder ? intval(substr($lastOrder->order_id, -3)) + 1 : 1;
        
        return 'ORD-' . $date . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    // Status badge color
    public function statusBadgeClass()
    {
        return match($this->status) {
            'settlement', 'capture' => 'bg-success',
            'pending' => 'bg-warning',
            'deny', 'cancel', 'failure' => 'bg-danger',
            'expire' => 'bg-secondary',
            default => 'bg-info',
        };
    }

    // Payment type label
    public function paymentTypeLabel()
    {
        return match($this->payment_type) {
            'gopay' => 'GoPay',
            'qris' => 'QRIS',
            'bank_transfer' => 'Transfer Bank',
            'credit_card' => 'Kartu Kredit',
            'echannel' => 'Mandiri Bill',
            'bca_klikpay' => 'BCA KlikPay',
            'bri_epay' => 'BRI e-Pay',
            'cimb_clicks' => 'CIMB Clicks',
            'shopeepay' => 'ShopeePay',
            'alfamart' => 'Alfamart',
            'indomaret' => 'Indomaret',
            default => ucfirst($this->payment_type),
        };
    }
}