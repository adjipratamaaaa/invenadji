<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StockIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'date',
        'product_id',
        'quantity',
        'price',
        'total',
        'note',
        'user_id'
    ];

    protected $attributes = [
        'total' => 0,
        'note' => null,
    ];

    protected $casts = [
        'date' => 'datetime',
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generateInvoiceNumber()
    {
        do {
            $invoiceNumber = 'IN-' . date('Ymd') . '-' . Str::random(6);
        } while (self::where('invoice_number', $invoice_number)->exists());

        return $invoiceNumber;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($stockIn) {
            // Generate invoice number jika tidak ada
            if (empty($stockIn->invoice_number)) {
                $stockIn->invoice_number = self::generateInvoiceNumber();
            }

            // HITUNG TOTAL OTOMATIS
            $stockIn->total = $stockIn->quantity * $stockIn->price;

            // Set user_id jika tidak ada
            if (empty($stockIn->user_id) && auth()->check()) {
                $stockIn->user_id = auth()->id();
            }

            // Set price dari product jika tidak ada
            if (empty($stockIn->price) && $stockIn->product) {
                $stockIn->price = $stockIn->product->purchase_price;
            }
        });

        static::updating(function ($stockIn) {
            // Hitung total otomatis saat update
            $stockIn->total = $stockIn->quantity * $stockIn->price;
        });

        static::created(function ($stockIn) {
            // Update stok produk
            $product = $stockIn->product;
            if ($product) {
                $product->stock += $stockIn->quantity;
                $product->save();
            }
        });

        static::updated(function ($stockIn) {
            $product = $stockIn->product;
            if ($product) {
                // Kembalikan stok lama
                $oldQuantity = $stockIn->getOriginal('quantity');
                $product->stock -= $oldQuantity;
                
                // Tambahkan stok baru
                $product->stock += $stockIn->quantity;
                $product->save();
            }
        });

        static::deleted(function ($stockIn) {
            $product = $stockIn->product;
            if ($product) {
                $product->stock -= $stockIn->quantity;
                $product->save();
            }
        });
    }

    // Accessor untuk format total
    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }
}