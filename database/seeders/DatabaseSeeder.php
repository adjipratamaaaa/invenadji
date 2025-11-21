<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // User Seeder - Gunakan firstOrCreate
        User::firstOrCreate(
            ['email' => 'admin@inventory.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'gudang@inventory.com'],
            [
                'name' => 'Staff Gudang',
                'password' => Hash::make('password'),
                'role' => 'gudang',
            ]
        );

        User::firstOrCreate(
            ['email' => 'kasir@inventory.com'],
            [
                'name' => 'Kasir',
                'password' => Hash::make('password'),
                'role' => 'kasir',
            ]
        );

        // Category Seeder - KHUSUS TOKO PC PARTS
        $categories = [
            ['name' => 'Processor (CPU)', 'description' => 'Processor Intel, AMD, berbagai seri'],
            ['name' => 'Motherboard', 'description' => 'Mainboard berbagai socket dan chipset'],
            ['name' => 'Memory (RAM)', 'description' => 'RAM DDR4, DDR5, berbagai speed dan kapasitas'],
            ['name' => 'Storage', 'description' => 'SSD, HDD, NVMe, berbagai kapasitas'],
            ['name' => 'Graphics Card (VGA)', 'description' => 'GPU NVIDIA, AMD, berbagai series'],
            ['name' => 'Power Supply (PSU)', 'description' => 'Power supply berbagai wattage dan rating'],
            ['name' => 'Casing & Cooling', 'description' => 'Casing, CPU Cooler, Fan, Thermal Paste'],
            ['name' => 'Monitor', 'description' => 'Monitor gaming, office, professional'],
            ['name' => 'Peripheral', 'description' => 'Keyboard, Mouse, Headset, Webcam'],
            ['name' => 'Network & Accessories', 'description' => 'WiFi card, cable, adapter, converter'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }

        // Product Seeder - CONTOH PRODUK PC PARTS
        $products = [
            // Processor
            [
                'code' => 'PCP001',
                'name' => 'Intel Core i5-12400F',
                'category_id' => 1,
                'description' => 'Processor Intel Core i5-12400F 6 Core 12 Thread',
                'purchase_price' => 2200000,
                'selling_price' => 2530000,
                'stock' => 8,
                'min_stock' => 2,
                'unit' => 'pcs'
            ],
            [
                'code' => 'PCP002',
                'name' => 'AMD Ryzen 5 5600X',
                'category_id' => 1,
                'description' => 'Processor AMD Ryzen 5 5600X 6 Core 12 Thread',
                'purchase_price' => 2450000,
                'selling_price' => 2817500,
                'stock' => 6,
                'min_stock' => 2,
                'unit' => 'pcs'
            ],

            // Motherboard
            [
                'code' => 'PCP003',
                'name' => 'ASUS Prime B660M-A',
                'category_id' => 2,
                'description' => 'Motherboard ASUS Prime B660M-A DDR4',
                'purchase_price' => 1850000,
                'selling_price' => 2127500,
                'stock' => 5,
                'min_stock' => 1,
                'unit' => 'pcs'
            ],
            [
                'code' => 'PCP004',
                'name' => 'MSI B550 Tomahawk',
                'category_id' => 2,
                'description' => 'Motherboard MSI B550 Tomahawk MAX',
                'purchase_price' => 2100000,
                'selling_price' => 2415000,
                'stock' => 4,
                'min_stock' => 1,
                'unit' => 'pcs'
            ],

            // Memory RAM
            [
                'code' => 'PCP005',
                'name' => 'Corsair Vengeance LPX 16GB DDR4 3200MHz',
                'category_id' => 3,
                'description' => 'RAM Corsair Vengeance LPX 16GB (2x8GB) DDR4 3200MHz',
                'purchase_price' => 850000,
                'selling_price' => 977500,
                'stock' => 15,
                'min_stock' => 5,
                'unit' => 'kit'
            ],
            // ... (sisanya sama seperti sebelumnya)
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['code' => $product['code']],
                $product
            );
        }

        $this->command->info('Database seeded successfully for PC Parts Store!');
        $this->command->info('Admin Login: admin@inventory.com / password');
        $this->command->info('Gudang Login: gudang@inventory.com / password');
        $this->command->info('Kasir Login: kasir@inventory.com / password');
    }
}