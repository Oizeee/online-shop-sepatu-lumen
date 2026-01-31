<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json(
            Product::with('category')->get()
        );
    }

    public function store(Request $request)
    {
        $product = Product::create([
            'name' => $request->name,
            'brand' => $request->brand,
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id
        ]);

        return response()->json($product, 201);
    }
}