<?php

namespace App\Http\Controllers;

use App\Models\StockOut;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class StockOutController extends Controller
{
    public function index()
    {
        $stockOuts = StockOut::with(['product', 'user'])->latest()->get();
        return view('stock-outs.index', compact('stockOuts'));
    }

    public function create()
    {
        $products = Product::active()->get();
        
        // Ambil product_id dari request jika ada
        $selectedProductId = request('product_id');
        $autoPrice = null;
        
        // Jika ada product_id, ambil harga jual otomatis
        if ($selectedProductId) {
            $product = Product::find($selectedProductId);
            $autoPrice = $product ? $product->selling_price : null;
        }
        
        return view('stock-outs.create', compact('products', 'autoPrice'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_number' => 'required|unique:stock_outs',
            'date' => 'required|date',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'type' => 'required|in:sale,return,damage,other',
            'note' => 'nullable',
        ]);

        // Cek stok tersedia
        $product = Product::find($request->product_id);
        if ($product->stock < $request->quantity) {
            return back()->with('error', 'Stok tidak mencukupi. Stok tersedia: ' . $product->stock)
                        ->withInput();
        }

        // Auto price jika tidak diisi atau 0
        $price = $request->price;
        if (!$price || $price == 0) {
            $price = $product->selling_price;
        }

        try {
            DB::beginTransaction();

            StockOut::create([
                'invoice_number' => $request->invoice_number,
                'date' => $request->date,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'price' => $price,
                'type' => $request->type,
                'note' => $request->note,
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('stock-outs.index')
                ->with('success', 'Stok keluar berhasil dicatat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal mencatat stok keluar: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(StockOut $stockOut)
    {
        return view('stock-outs.show', compact('stockOut'));
    }

    public function edit(StockOut $stockOut)
    {
        $products = Product::active()->get();
        return view('stock-outs.edit', compact('stockOut', 'products'));
    }

    public function update(Request $request, StockOut $stockOut)
    {
        $request->validate([
            'invoice_number' => 'required|unique:stock_outs,invoice_number,' . $stockOut->id,
            'date' => 'required|date',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'type' => 'required|in:sale,return,damage,other',
            'note' => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            $stockOut->update([
                'invoice_number' => $request->invoice_number,
                'date' => $request->date,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'price' => $request->price,
                'type' => $request->type,
                'note' => $request->note,
            ]);

            DB::commit();

            return redirect()->route('stock-outs.index')
                ->with('success', 'Stok keluar berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui stok keluar: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(StockOut $stockOut)
    {
        try {
            DB::beginTransaction();

            $stockOut->delete();

            DB::commit();

            return redirect()->route('stock-outs.index')
                ->with('success', 'Stok keluar berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('stock-outs.index')
                ->with('error', 'Gagal menghapus stok keluar: ' . $e->getMessage());
        }
    }

    public function generateInvoice()
    {
        $invoice = 'OUT-' . date('Ymd') . '-' . Str::random(6);
        return response()->json(['invoice_number' => $invoice]);
    }
}