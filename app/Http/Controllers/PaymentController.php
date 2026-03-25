<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\PaymentOrder;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Show payment page
     */
    public function index($tagihanId)
    {
        $tagihan = Tagihan::with(['siswa', 'jenisTagihan'])->findOrFail($tagihanId);

        // Check authorization (siswa hanya bisa bayar tagihan sendiri)
        if (Auth::user()->role == 'siswa') {
            if ($tagihan->siswa_nis != Auth::user()->siswa->nis) {
                abort(403, 'Unauthorized');
            }
        }

        // Check if already lunas
        if ($tagihan->status == 'lunas') {
            return redirect()->back()->with('error', 'Tagihan sudah lunas!');
        }

        $sisaTagihan = $tagihan->total_tagihan - $tagihan->sudah_dibayar;

        return view('siswa.payment.index', compact('tagihan', 'sisaTagihan'));
    }

    /**
     * Create payment
     */
    public function create(Request $request, $tagihanId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
        ]);

        DB::beginTransaction();
        try {
            $tagihan = Tagihan::findOrFail($tagihanId);

            // Validate amount
            $sisaTagihan = $tagihan->total_tagihan - $tagihan->sudah_dibayar;
            $amount = $request->amount;

            if ($amount > $sisaTagihan) {
                return back()->withErrors(['amount' => 'Jumlah pembayaran melebihi sisa tagihan!']);
            }

            if ($amount < 10000) {
                return back()->withErrors(['amount' => 'Minimal pembayaran Rp 10.000']);
            }

            // Create snap token
            $result = $this->midtransService->createSnapToken($tagihan, $amount);

            if (!$result['success']) {
                throw new \Exception($result['message']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'snap_token' => $result['snap_token'],
                'order_id' => $result['order_id'],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment Create Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Payment finish (redirect dari Midtrans)
     */
    public function finish(Request $request)
    {
        $orderId = $request->order_id;

        $paymentOrder = PaymentOrder::where('order_id', $orderId)->first();

        if (!$paymentOrder) {
            return redirect()->route('siswa.dashboard')
                ->with('error', 'Pembayaran tidak ditemukan!');
        }

        // Fetch status from Midtrans
        $statusCheck = $this->midtransService->checkTransactionStatus($orderId);

        if ($statusCheck['success']) {
            $status = $statusCheck['data'];

            // Update dasar
            $paymentOrder->transaction_id = $status->transaction_id ?? $paymentOrder->transaction_id;
            $paymentOrder->payment_type   = $status->payment_type ?? $paymentOrder->payment_type;
            $paymentOrder->status         = $status->transaction_status ?? $paymentOrder->status;
            $paymentOrder->midtrans_response = (array) $status;

            // === VA / PAYMENT CODE ===
            if (!$paymentOrder->payment_code) {
                if (isset($status->va_numbers[0]->va_number)) {
                    $paymentOrder->payment_code = $status->va_numbers[0]->va_number;
                } elseif (isset($status->permata_va_number)) {
                    $paymentOrder->payment_code = $status->permata_va_number;
                } elseif (isset($status->bill_key)) {
                    $paymentOrder->payment_code = $status->bill_key;
                }
            }

            // === PDF URL ===
            if (!$paymentOrder->pdf_url && isset($status->pdf_url)) {
                $paymentOrder->pdf_url = $status->pdf_url;
            }

            $paymentOrder->save();
        }

        return view('siswa.payment.finish', compact('paymentOrder'));
    }


    /**
     * Payment unfinish
     */
    public function unfinish(Request $request)
    {
        $orderId = $request->order_id;

        return view('siswa.payment.unfinish', compact('orderId'));
    }

    /**
     * Payment error
     */
    public function error(Request $request)
    {
        $orderId = $request->order_id;

        return view('siswa.payment.error', compact('orderId'));
    }

    /**
     * Webhook notification dari Midtrans
     */
    public function notification(Request $request)
    {
        try {
            $notification = $request->all();

            Log::info('Midtrans Notification Received', $notification);

            // Process notification
            $this->midtransService->handleNotification((object) $notification);

            return response()->json(['message' => 'Notification handled'], 200);
        } catch (\Exception $e) {
            Log::error('Notification Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error'], 500);
        }

        Log::error('=== MIDTRANS WEBHOOK MASUK ===');
        Log::error(json_encode($request->all(), JSON_PRETTY_PRINT));

        return response()->json(['ok' => true]);
    }

    /**
     * Check payment status (AJAX)
     */
    public function checkStatus($orderId)
    {
        $paymentOrder = PaymentOrder::where('order_id', $orderId)->first();

        if (!$paymentOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'status' => $paymentOrder->status,
            'payment_order' => $paymentOrder,
        ]);
    }

    /**
     * Payment history (untuk siswa)
     */
    public function history()
    {
        $siswa = Auth::user()->siswa;

        $payments = PaymentOrder::where('siswa_nis', $siswa->nis)
            ->with(['tagihan.jenisTagihan'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('siswa.payment.history', compact('payments'));
    }

    /**
     * Payment detail
     */
    public function detail($orderId)
    {
        $paymentOrder = PaymentOrder::with(['tagihan.jenisTagihan', 'siswa'])
            ->where('order_id', $orderId)
            ->firstOrFail();

        // Authorization check
        if (Auth::user()->role == 'siswa') {
            if ($paymentOrder->siswa_nis != Auth::user()->siswa->nis) {
                abort(403);
            }
        }

        return view('siswa.payment.detail', compact('paymentOrder'));
    }
}
