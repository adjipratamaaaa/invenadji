<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_stock_ins' => StockIn::count(),
            'total_stock_outs' => StockOut::count(),
            'low_stock_products' => Product::whereRaw('stock <= min_stock')->count(),
        ];

        // Data untuk chart (contoh sederhana)
        $recentStockIns = StockIn::with('product')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentStockOuts = StockOut::with('product')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact('stats', 'recentStockIns', 'recentStockOuts'));
    }
}