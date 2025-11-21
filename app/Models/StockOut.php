<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StockOut extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'date',
        'product_id',
        'quantity',
        'price',
        'total',
        'type',
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
            $invoiceNumber = 'OUT-' . date('Ymd') . '-' . Str::random(6);
        } while (self::where('invoice_number', $invoiceNumber)->exists());

        return $invoiceNumber;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($stockOut) {
            // Generate invoice number jika tidak ada
            if (empty($stockOut->invoice_number)) {
                $stockOut->invoice_number = self::generateInvoiceNumber();
            }

            // Hitung total otomatis
            if (empty($stockOut->total)) {
                $stockOut->total = $stockOut->quantity * $stockOut->price;
            }

            // Set user_id jika tidak ada
            if (empty($stockOut->user_id) && auth()->check()) {
                $stockOut->user_id = auth()->id();
            }

            // JANGAN SET TYPE OTOMATIS! Biarkan dari form input
        });

        static::created(function ($stockOut) {
            $product = $stockOut->product;
            if ($product) {
                $product->stock -= $stockOut->quantity;
                $product->save();
            }
        });

        static::updated(function ($stockOut) {
            $product = $stockOut->product;
            if ($product) {
                $oldQuantity = $stockOut->getOriginal('quantity');
                $product->stock += $oldQuantity;
                $product->stock -= $stockOut->quantity;
                $product->save();
            }
        });

        static::deleted(function ($stockOut) {
            $product = $stockOut->product;
            if ($product) {
                $product->stock += $stockOut->quantity;
                $product->save();
            }
        });
    }
}