<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Product;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request, XenditService $xenditService)
    {
        // 1. Validasi request
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'email' => 'required|email'
        ]);

        // 2. Hitung total harga
        $total = 0;
        foreach ($request->items as $item) {
            $total += $item['qty'] * $item['price'];
        }

        // 3. Simpan order
        $order = Order::create([
            'total_price' => $total,
            'status' => 'PENDING',
            'email' => $request->email
        ]);

        // Generate INV- (transaction_id)
        $order->transaction_id = 'INV-' . $order->id;
        $order->save();

        // 4. Simpan item
        foreach ($request->items as $item) {
            $order->items()->create($item);
        }

        // 5. Response ke client
        return response()->json([
            'message' => 'Order created successfully',
            'data' => [
                'order_id' => $order->id,
                'transaction_id' => $order->transaction_id,
                'total_price' => $order->total_price,
                'status' => $order->status,
                'email' => $order->email
            ]
        ]);
    }

    public function index()
    {
        return Order::with('items', 'payments')->get();
    }

    public function show($id)
    {
        return Order::with('items', 'payments')->findOrFail($id);
    }

    public function destroy($id)
    {
        Order::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
