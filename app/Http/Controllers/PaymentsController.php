<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payments;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PaymentsController extends Controller
{
    public function create(Request $request, XenditService $xenditService)
    {
        $order = Order::findOrFail($request->order_id);

        // 1. Create payment record
        $payment = Payments::create([
            'order_id' => $order->id,
            'amount' => $order->total_price,
            'status' => 'PENDING'
        ]);

        // 2. Generate invoice ke Xendit
        $invoice = $xenditService->createInvoice($order);

        // 3. Simpan ke payment
        $payment->update([
            'transaction_id' => $invoice['external_id'], // untuk webhook
            'payment_url' => $invoice['invoice_url']
        ]);

        return response()->json([
            'payment_url' => $invoice['invoice_url'],
            'payment_id' => $payment->id
        ]);
    }

    public function webhook(Request $request)
    {
        Log::info('Xendit Webhook', $request->all());

        // optional: validasi token (jika dipakai)
        $token = $request->header('X-CALLBACK-TOKEN');
        if ($token && $token !== config('xendit.callback_token')) {
            return response()->json(['message' => 'Invalid token'], 403);
        }

        // ambil external_id (format: order-10)
        $externalId = $request->external_id;

        // ambil ID order (hapus "order-")
        $orderId = str_replace('INV-', '', $externalId);

        // ambil payment berdasarkan external_id
        $payment = Payments::where('transaction_id', $request->external_id)->first();

        // cari order
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // idempotency (hindari double update)
        if ($order->status === 'PAID') {
            return response()->json(['message' => 'Already processed']);
        }

        // update order
        $status = $request->status;

        $order->update([
            'status' => $status
        ]);

        if ($payment) {
            $payment->update([
                'status' => $status,
                'payment_method' => $request->payment_method,
                'payment_channel' => $request->payment_channel,
                'paid_amount' => $request->paid_amount,
                'paid_at' => $request->paid_at ? Carbon::parse($request->paid_at) : null,
            ]);
        }

        return response()->json(['message' => 'Webhook processed']);
    }

    public function show($order_id)
    {
        return Payments::where('order_id', $order_id)->first();
    }
}
