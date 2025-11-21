<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockIn;
use App\Models\StockOut;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function stockReport()
    {
        $products = Product::with('category')
            ->orderBy('name')
            ->get();

        return view('reports.stock', compact('products'));
    }

    public function stockMovement(Request $request)
    {
        $stockIns = StockIn::with('product.category')
            ->selectRaw('date, product_id, quantity, "in" as type, invoice_number, NULL as stock_out_type')
            ->when($request->start_date, function($query) use ($request) {
                return $query->where('date', '>=', $request->start_date);
            })
            ->when($request->end_date, function($query) use ($request) {
                return $query->where('date', '<=', $request->end_date);
            });

        $stockOuts = StockOut::with('product.category')
            ->selectRaw('date, product_id, quantity, "out" as type, invoice_number, type as stock_out_type')
            ->when($request->start_date, function($query) use ($request) {
                return $query->where('date', '>=', $request->start_date);
            })
            ->when($request->end_date, function($query) use ($request) {
                return $query->where('date', '<=', $request->end_date);
            });

        $movements = $stockIns->unionAll($stockOuts)
            ->orderBy('date', 'desc')
            ->get();

        return view('reports.movement', compact('movements'));
    }

    public function incomeReport(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth()->format('Y-m-d');

        $stockIns = StockIn::whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy(function($item) {
                return $item->date->format('Y-m-d');
            });

        $stockOuts = StockOut::whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy(function($item) {
                return $item->date->format('Y-m-d');
            });

        return view('reports.income', compact('stockIns', 'stockOuts', 'startDate', 'endDate'));
    }
}