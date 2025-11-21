<?php

namespace App\Http\Controllers;

use App\Models\StockIn;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class StockInController extends Controller
{
    public function index()
    {
        $stockIns = StockIn::with(['product', 'user'])->latest()->get();
        return view('stock-ins.index', compact('stockIns'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        return view('stock-ins.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_number' => 'required|unique:stock_ins',
            'date' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'product_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'note' => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            // Cari atau buat produk
            $product = Product::where('name', $request->product_name)
                             ->where('category_id', $request->category_id)
                             ->first();

            if (!$product) {
                $product = Product::create([
                    'code' => 'PCP' . date('Ymd') . Str::random(3),
                    'name' => $request->product_name,
                    'category_id' => $request->category_id,
                    'description' => $request->note,
                    'purchase_price' => $request->price,
                    'selling_price' => $request->price * 1.15,
                    'stock' => $request->quantity,
                    'min_stock' => 5,
                    'unit' => 'pcs',
                    'is_active' => true
                ]);
            } else {
                // Jika produk sudah ada, update stok
                $product->increment('stock', $request->quantity);
                // Update harga jika perlu
                $product->update([
                    'purchase_price' => $request->price,
                    'selling_price' => $request->price * 1.15,
                ]);
            }

            // HITUNG TOTAL
            $total = $request->quantity * $request->price;

            // Buat stok masuk - PASTIKAN TOTAL DIISI
            StockIn::create([
                'invoice_number' => $request->invoice_number,
                'date' => $request->date,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $request->price,
                'total' => $total, // ✅ TAMBAHKAN INI
                'note' => $request->note,
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            $message = $product->wasRecentlyCreated ? 
                'Stok masuk berhasil dan produk baru dibuat.' : 
                'Stok masuk berhasil ditambahkan ke produk yang sudah ada.';

            return redirect()->route('stock-ins.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal mencatat stok masuk: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(StockIn $stockIn)
    {
        return view('stock-ins.show', compact('stockIn'));
    }

    public function edit(StockIn $stockIn)
    {
        $categories = Category::active()->get();
        return view('stock-ins.edit', compact('stockIn', 'categories'));
    }

    public function update(Request $request, StockIn $stockIn)
    {
        $request->validate([
            'invoice_number' => 'required|unique:stock_ins,invoice_number,' . $stockIn->id,
            'date' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'product_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'note' => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            // Simpan quantity lama untuk adjustment stok
            $oldQuantity = $stockIn->quantity;
            $newQuantity = $request->quantity;

            // HITUNG TOTAL BARU
            $newTotal = $newQuantity * $request->price;

            // Cek apakah produk berubah (nama atau kategori)
            $productChanged = (
                $stockIn->product->name != $request->product_name ||
                $stockIn->product->category_id != $request->category_id
            );

            if ($productChanged) {
                // Jika produk berubah, cari/buat produk baru
                $newProduct = Product::where('name', $request->product_name)
                                    ->where('category_id', $request->category_id)
                                    ->first();

                if (!$newProduct) {
                    $newProduct = Product::create([
                        'code' => 'PCP' . date('Ymd') . Str::random(3),
                        'name' => $request->product_name,
                        'category_id' => $request->category_id,
                        'description' => $request->note,
                        'purchase_price' => $request->price,
                        'selling_price' => $request->price * 1.15,
                        'stock' => $newQuantity,
                        'min_stock' => 5,
                        'unit' => 'pcs',
                        'is_active' => true
                    ]);
                } else {
                    // Jika produk sudah ada, tambah stok
                    $newProduct->increment('stock', $newQuantity);
                    $newProduct->update([
                        'purchase_price' => $request->price,
                        'selling_price' => $request->price * 1.15,
                    ]);
                }

                // Kurangi stok dari produk lama
                $stockIn->product->decrement('stock', $oldQuantity);

                // Update stok masuk dengan produk baru - PASTIKAN TOTAL DIUPDATE
                $stockIn->update([
                    'invoice_number' => $request->invoice_number,
                    'date' => $request->date,
                    'product_id' => $newProduct->id,
                    'quantity' => $newQuantity,
                    'price' => $request->price,
                    'total' => $newTotal, // ✅ UPDATE TOTAL
                    'note' => $request->note,
                ]);

            } else {
                // Jika produk tidak berubah, hanya update data
                $product = $stockIn->product;

                // Adjust stok berdasarkan perubahan quantity
                if ($oldQuantity != $newQuantity) {
                    $difference = $newQuantity - $oldQuantity;
                    if ($difference > 0) {
                        $product->increment('stock', $difference);
                    } else {
                        $product->decrement('stock', abs($difference));
                    }
                }

                // Update data produk
                $product->update([
                    'name' => $request->product_name,
                    'category_id' => $request->category_id,
                    'description' => $request->note,
                    'purchase_price' => $request->price,
                    'selling_price' => $request->price * 1.15,
                ]);

                // Update stok masuk - PASTIKAN TOTAL DIUPDATE
                $stockIn->update([
                    'invoice_number' => $request->invoice_number,
                    'date' => $request->date,
                    'quantity' => $newQuantity,
                    'price' => $request->price,
                    'total' => $newTotal, // ✅ UPDATE TOTAL
                    'note' => $request->note,
                ]);
            }

            DB::commit();

            return redirect()->route('stock-ins.index')
                ->with('success', 'Stok masuk berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui stok masuk: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(StockIn $stockIn)
    {
        try {
            DB::beginTransaction();

            // Kurangi stok produk sebelum hapus
            $stockIn->product->decrement('stock', $stockIn->quantity);
            
            $stockIn->delete();

            DB::commit();

            return redirect()->route('stock-ins.index')
                ->with('success', 'Stok masuk berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('stock-ins.index')
                ->with('error', 'Gagal menghapus stok masuk: ' . $e->getMessage());
        }
    }

    public function generateInvoice()
    {
        $invoice = 'IN-PC-' . date('Ymd') . '-' . Str::random(6);
        return response()->json(['invoice_number' => $invoice]);
    }
}