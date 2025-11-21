<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
    'code',
    'name',
    'category_id',
    'description',
    'purchase_price',
    'selling_price',
    'stock',
    'min_stock',
    'unit',
    'image',
    'is_active',
    'barcode',        // TAMBAHKAN INI
    'barcode_type'    // TAMBAHKAN INI
];
    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'stock' => 'integer',
        'min_stock' => 'integer',
        'is_active' => 'boolean'
    ];

    // Relationship - handle nullable category_id
    public function category()
    {
        return $this->belongsTo(Category::class)->withDefault([
            'name' => 'Uncategorized',
            'description' => 'Produk tanpa kategori'
        ]);
    }

    public function stockIns()
    {
        return $this->hasMany(StockIn::class);
    }

    public function stockOuts()
    {
        return $this->hasMany(StockOut::class);
    }

    // Scope
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('stock <= min_stock');
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    // Method untuk mencari atau membuat produk baru (untuk workflow stok masuk)
    public static function findOrCreateProduct($name, $categoryId, $data = [])
    {
        $product = self::where('name', $name)
                      ->where('category_id', $categoryId)
                      ->first();

        if (!$product) {
            $product = self::create(array_merge([
                'code' => 'PCP' . date('Ymd') . strtoupper(uniqid()),
                'name' => $name,
                'category_id' => $categoryId,
                'description' => $data['description'] ?? '',
                'purchase_price' => $data['purchase_price'] ?? 0,
                'selling_price' => $data['selling_price'] ?? 0,
                'stock' => 0,
                'min_stock' => $data['min_stock'] ?? 5,
                'unit' => $data['unit'] ?? 'pcs',
                'is_active' => true
            ], $data));
        }

        return $product;
    }

    // Method untuk update stok (digunakan oleh StockIn/StockOut events)
    public function updateStock($quantity, $operation = 'in')
    {
        if ($operation === 'in') {
            $this->increment('stock', $quantity);
        } else {
            $this->decrement('stock', $quantity);
        }
        
        $this->save();
    }

    // Method untuk calculate harga jual otomatis (15% markup)
    public function calculateSellingPrice($purchasePrice)
    {
        return $purchasePrice * 1.15;
    }

    // Accessor untuk status stok
    public function getStockStatusAttribute()
    {
        if ($this->stock == 0) {
            return 'out-of-stock';
        } elseif ($this->stock <= $this->min_stock) {
            return 'low-stock';
        } else {
            return 'sufficient';
        }
    }

    public function getStockStatusColorAttribute()
    {
        switch ($this->stock_status) {
            case 'out-of-stock':
                return 'danger';
            case 'low-stock':
                return 'warning';
            default:
                return 'success';
        }
    }

    // Accessor untuk nama kategori (handle nullable)
    public function getCategoryNameAttribute()
    {
        return $this->category->name;
    }

    // Accessor untuk profit margin
    public function getProfitMarginAttribute()
    {
        if ($this->purchase_price > 0) {
            return (($this->selling_price - $this->purchase_price) / $this->purchase_price) * 100;
        }
        return 0;
    }

    // Accessor untuk total nilai stok
    public function getStockValueAttribute()
    {
        return $this->stock * $this->purchase_price;
    }

    // Event handlers untuk maintain data consistency
    protected static function boot()
    {
        parent::boot();

        // Auto-generate code jika tidak diisi
        static::creating(function ($product) {
            if (empty($product->code)) {
                $product->code = 'PCP' . date('Ymd') . strtoupper(uniqid());
            }
            
            // Auto-calculate selling price jika tidak diisi
            if (empty($product->selling_price) && $product->purchase_price > 0) {
                $product->selling_price = $product->calculateSellingPrice($product->purchase_price);
            }
        });

        // Update selling price jika purchase price berubah
        static::updating(function ($product) {
            if ($product->isDirty('purchase_price') && $product->purchase_price > 0) {
                $product->selling_price = $product->calculateSellingPrice($product->purchase_price);
            }
        });
    }
}