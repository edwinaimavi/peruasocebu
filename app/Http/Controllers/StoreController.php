<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;

class StoreController extends Controller
{
    

    public function index()
    {
        $products = Product::with('images')
            ->where('status', 'published')
            ->latest()
            ->take(8)
            ->get();

        // 🔥 NUEVO
        $categories = Category::with('images')
            ->where('status', 1)
            ->latest()
          
            ->get();

        return view('store.home', compact('products', 'categories'));
    }

    public function show($slug)
    {
        $product = Product::with('images')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return view('store.product', compact('product'));
    }
}
