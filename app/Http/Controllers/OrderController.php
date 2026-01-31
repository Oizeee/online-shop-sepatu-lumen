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
        // user dari auth middleware
        $user = $request->auth;

        // ambil product
        $product = Product::find($request->product_id);

        if (!$product) {
            return response()->json([
                'message' => 'Product tidak ditemukan'
            ], 404);
        }

        if (!$request->product_id || !$request->quantity) {
            return response()->json([
                'message' => 'product_id dan quantity wajib diisi'
            ], 400);
        }

        if ($request->quantity < 1) {
            return response()->json([
                'message' => 'Quantity minimal 1'
            ], 400);
        }

        $quantity = $request->quantity;
        $total = $product->price * $quantity;

        if ($product->stock < $quantity) {
            return response()->json([
                'message' => 'Stok tidak mencukupi'
            ], 400);
        }

        $product->update([
            'stock' => $product->stock - $quantity
        ]);

        // 1. buat order
        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => $total,
            'status' => 'pending'
        ]);

        // 2. buat order item
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price' => $product->price
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

    public function destroy(Request $request, $id)
    {
        $user = $request->auth;
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'message' => 'Order tidak ditemukan'
            ], 404);
        }

        // user hanya bisa cancel order miliknya
        if ($order->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        // hanya pending yang boleh dibatalkan
        if ($order->status !== 'pending') {
            return response()->json([
                'message' => 'Order tidak bisa dibatalkan'
            ], 400);
        }

        $order->update([
            'status' => 'cancelled'
        ]);

        return response()->json([
            'message' => 'Order berhasil dibatalkan'
        ]);
    }

    public function markAsPaid($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'message' => 'Order tidak ditemukan'
            ], 404);
        }

        if ($order->status !== 'pending') {
            return response()->json([
                'message' => 'Order sudah diproses'
            ], 400);
        }

        $order->update([
            'status' => 'paid'
        ]);

        return response()->json([
            'message' => 'Order berhasil dibayar'
        ]);
    }
}