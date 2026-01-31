<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // contoh user sementara (nanti diganti auth)
        $userId = 1;

        $total = 0;

        // 1. buat order dulu
        $order = Order::create([
            'user_id' => $userId,
            'total_price' => 0,
            'status' => 'pending'
        ]);

        // 2. loop items
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);

            $subtotal = $product->price * $item['quantity'];
            $total += $subtotal;

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $product->price
            ]);
        }

        // 3. update total harga
        $order->update([
            'total_price' => $total
        ]);

        return response()->json([
            'message' => 'Order berhasil dibuat',
            'order_id' => $order->id,
            'total_price' => $total
        ], 201);
    }

    public function show($id)
    {
        $order = Order::with(['items.product', 'user'])->find($id);

        if (!$order) {
            return response()->json([
                'message' => 'Order tidak ditemukan'
            ], 404);
        }

        return response()->json($order);
    }
}