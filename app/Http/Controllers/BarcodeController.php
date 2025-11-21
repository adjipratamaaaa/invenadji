<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Milon\Barcode\DNS1D;

class BarcodeController extends Controller
{
    // Generate barcode untuk product yang sudah ada
    public function generateBarcode($productId)
    {
        $product = Product::findOrFail($productId);
        
        // Jika product belum punya barcode, generate baru
        if (empty($product->barcode)) {
            $product->barcode = $this->generateUniqueBarcode();
            $product->barcode_type = 'C128';
            $product->save();
        }
        
        $d = new DNS1D();
        $barcodeImage = $d->getBarcodeHTML($product->barcode, 'C128', 2, 33);

        return view('barcode.show', compact('product', 'barcodeImage'));
    }

    // Download barcode sebagai PNG - VERSI DIPERBAIKI
    public function downloadBarcode($productId)
    {
        $product = Product::findOrFail($productId);
        $d = new DNS1D();
        
        // Generate barcode PNG dengan parameter yang benar
        $barcodePNG = $d->getBarcodePNG($product->barcode, 'C128', 2, 33, [0,0,0], true);
        
        // Pastikan ini adalah PNG yang valid
        if (empty($barcodePNG)) {
            abort(500, 'Failed to generate barcode');
        }

        // Decode base64 ke binary
        $barcodeBinary = base64_decode($barcodePNG);

        return response($barcodeBinary)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="barcode_' . $product->name . '_' . $product->barcode . '.png"')
            ->header('Content-Length', strlen($barcodeBinary))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    // Generate barcode unik
    private function generateUniqueBarcode()
    {
        do {
            $barcode = 'PC' . rand(10000000, 99999999);
        } while (Product::where('barcode', $barcode)->exists());

        return $barcode;
    }
}