<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@pos.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Kasir',
            'email' => 'kasir@pos.test',
            'password' => Hash::make('password'),
            'role' => 'kasir',
            'is_active' => true,
        ]);

        $categories = [
            ['name' => 'Minuman', 'description' => 'Minuman ringan dan air mineral'],
            ['name' => 'Makanan', 'description' => 'Makanan siap saji'],
            ['name' => 'Snack', 'description' => 'Camilan'],
            ['name' => 'Sembako', 'description' => 'Kebutuhan pokok'],
            ['name' => 'Alat Tulis', 'description' => 'Peralatan kantor'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        $products = [
            ['Minuman', 'AIR001', 'Aqua 600ml', 'pcs', 2500, 4000, 50, 5],
            ['Minuman', 'TEH001', 'Teh Botol Sosro', 'pcs', 3500, 5000, 3, 5],
            ['Makanan', 'ROT001', 'Roti Tawar', 'pcs', 12000, 15000, 10, 3],
            ['Snack', 'SNK001', 'Chitato', 'pcs', 8000, 11000, 2, 5],
            ['Sembako', 'BRS001', 'Beras 1kg', 'kg', 10000, 13000, 20, 5],
            ['Minuman', 'KOP001', 'Kopi Kapal Api', 'pcs', 1500, 2500, 100, 10],
            ['Snack', 'SNK002', 'Taro Net', 'pcs', 6000, 9000, 15, 5],
        ];

        foreach ($products as $p) {
            Product::create([
                'category_id' => Category::where('name', $p[0])->first()->id,
                'sku' => $p[1],
                'name' => $p[2],
                'unit' => $p[3],
                'purchase_price' => $p[4],
                'selling_price' => $p[5],
                'stock' => $p[6],
                'min_stock' => $p[7],
                'is_active' => true,
            ]);
        }

        Supplier::create([
            'name' => 'PT Supplier Jaya',
            'contact_person' => 'Budi Santoso',
            'phone' => '0211234567',
            'address' => 'Jl. Industri No. 10 Jakarta',
        ]);

        Customer::create([
            'name' => 'Pelanggan Umum',
            'phone' => '081234567890',
            'email' => null,
            'address' => null,
        ]);

        $settings = [
            ['key' => 'store_name', 'value' => 'POS App'],
            ['key' => 'store_address', 'value' => 'Jl. Contoh No. 1 Jakarta'],
            ['key' => 'store_phone', 'value' => '021-1234567'],
            ['key' => 'receipt_footer', 'value' => 'Terima kasih telah berbelanja'],
        ];

        foreach ($settings as $s) {
            Setting::create($s);
        }
    }
}