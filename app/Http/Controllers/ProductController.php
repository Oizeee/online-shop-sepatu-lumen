<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        $imageName = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension();

            $destinationPath = base_path('public/uploads/products');
            $image->move($destinationPath, $imageName);
        }

        $product = Product::create([
            'name' => $request->name,
            'brand' => $request->brand,
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
            'image' => $imageName
        ]);

        return response()->json([
            'message' => 'Product berhasil ditambahkan',
            'data' => $product
        ], 201);
    }
}