<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\StockIn;
use App\Models\StockOut;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->get();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:products',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:20',
            'description' => 'nullable|string',
        ]);

        // Auto-calculate selling price jika tidak diisi
        $sellingPrice = $request->selling_price;
        if (!$sellingPrice && $request->purchase_price > 0) {
            $sellingPrice = $request->purchase_price * 1.15;
        }

        Product::create([
            'code' => $request->code,
            'name' => $request->name,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'purchase_price' => $request->purchase_price,
            'selling_price' => $sellingPrice,
            'stock' => $request->stock,
            'min_stock' => $request->min_stock,
            'unit' => $request->unit,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dibuat.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'stockIns' => function($query) {
            $query->latest()->limit(5);
        }, 'stockOuts' => function($query) {
            $query->latest()->limit(5);
        }]);

        // Hitung profit margin
        $profit = $product->selling_price - $product->purchase_price;
        $profitMargin = $product->purchase_price > 0 ? ($profit / $product->purchase_price) * 100 : 0;
        $totalProfitPotential = $profit * $product->stock;

        // Tentukan status stok
        if ($product->stock == 0) {
            $stockStatus = 'out-of-stock';
            $stockStatusColor = 'danger';
        } elseif ($product->stock <= $product->min_stock) {
            $stockStatus = 'low-stock';
            $stockStatusColor = 'warning';
        } else {
            $stockStatus = 'safe';
            $stockStatusColor = 'success';
        }

        return view('products.show', compact(
            'product', 
            'profitMargin', 
            'totalProfitPotential',
            'stockStatus',
            'stockStatusColor'
        ));
    }

    public function edit(Product $product)
    {
        $categories = Category::active()->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'code' => 'required|unique:products,code,' . $product->id,
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:20',
            'description' => 'nullable|string',
        ]);

        // Auto-calculate selling price jika purchase price berubah
        $sellingPrice = $request->selling_price;
        if ($product->purchase_price != $request->purchase_price && !$request->has('selling_price_manual')) {
            $sellingPrice = $request->purchase_price * 1.15;
        }

        $product->update([
            'code' => $request->code,
            'name' => $request->name,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'purchase_price' => $request->purchase_price,
            'selling_price' => $sellingPrice,
            'stock' => $request->stock,
            'min_stock' => $request->min_stock,
            'unit' => $request->unit,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('products.show', $product->id)
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    public function generateCode()
    {
        $code = 'PCP' . date('Ymd') . Str::random(3);
        return response()->json(['code' => $code]);
    }

    public function toggleStatus(Product $product)
    {
        $product->update([
            'is_active' => !$product->is_active
        ]);

        $status = $product->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('products.index')
            ->with('success', "Produk berhasil $status.");
    }

    public function getByCategory(Category $category)
    {
        $products = $category->products()->active()->get();
        return response()->json($products);
    }

    public function lowStock()
    {
        $lowStockProducts = Product::with('category')
            ->whereRaw('stock <= min_stock')
            ->where('is_active', true)
            ->orderBy('stock', 'asc')
            ->get();

        return view('products.low-stock', compact('lowStockProducts'));
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id'
        ]);

        switch ($request->action) {
            case 'activate':
                Product::whereIn('id', $request->product_ids)->update(['is_active' => true]);
                $message = 'Produk berhasil diaktifkan.';
                break;

            case 'deactivate':
                Product::whereIn('id', $request->product_ids)->update(['is_active' => false]);
                $message = 'Produk berhasil dinonaktifkan.';
                break;

            case 'delete':
                Product::whereIn('id', $request->product_ids)->delete();
                $message = 'Produk berhasil dihapus.';
                break;
        }

        return redirect()->route('products.index')
            ->with('success', $message);
    }

    public function search(Request $request)
    {
        $search = $request->get('search');
        
        $products = Product::with('category')
            ->where('name', 'like', "%{$search}%")
            ->orWhere('code', 'like', "%{$search}%")
            ->orWhereHas('category', function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->get();

        if ($request->wantsJson()) {
            return response()->json($products);
        }

        return view('products.index', compact('products'));
    }

    public function apiLowStock()
    {
        $lowStockProducts = Product::with('category')
            ->whereRaw('stock <= min_stock')
            ->where('is_active', true)
            ->orderBy('stock', 'asc')
            ->get();

        return response()->json($lowStockProducts);
    }

    public function quickUpdateStock(Request $request, Product $product)
    {
        $request->validate([
            'type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string'
        ]);

        if ($request->type === 'in') {
            StockIn::create([
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'date' => now(),
                'note' => $request->note ?? 'Quick stock in',
                'price' => $product->purchase_price
            ]);
        } else {
            StockOut::create([
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'date' => now(),
                'note' => $request->note ?? 'Quick stock out',
                'price' => $product->selling_price
            ]);
        }

        return redirect()->back()
            ->with('success', 'Stok berhasil diperbarui.');
    }
}