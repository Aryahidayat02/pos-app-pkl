<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // PENTING: Pastikan ID Kategori di bawah ini sesuai dengan urutan di file CategorySeeder.php lu.
        // Asumsi urutan: 1=Alat Tulis, 2=Makanan, 3=Minuman, 4=Sembako, 5=Snack, 6=Rokok.
        
        $products = [
            // ==========================================
            // --- MINUMAN (Kategori 3) ---
            // ==========================================
            ['category_id' => 3, 'sku' => 'MNM-001', 'name' => 'Aqua Botol 600ml', 'unit' => 'pcs', 'purchase_price' => 2500, 'selling_price' => 4000, 'stock' => 50, 'min_stock' => 10, 'is_active' => true],
            ['category_id' => 3, 'sku' => 'MNM-002', 'name' => 'Teh Botol Sosro 350ml', 'unit' => 'pcs', 'purchase_price' => 3500, 'selling_price' => 5000, 'stock' => 40, 'min_stock' => 10, 'is_active' => true],
            ['category_id' => 3, 'sku' => 'MNM-003', 'name' => 'Kopi Kapal Api Sachet', 'unit' => 'pcs', 'purchase_price' => 1500, 'selling_price' => 2500, 'stock' => 100, 'min_stock' => 20, 'is_active' => true],
            ['category_id' => 3, 'sku' => 'MNM-004', 'name' => 'Ultra Milk Cokelat 250ml', 'unit' => 'pcs', 'purchase_price' => 4500, 'selling_price' => 6500, 'stock' => 20, 'min_stock' => 5, 'is_active' => true],

            // ==========================================
            // --- SNACK (Kategori 5) ---
            // ==========================================
            ['category_id' => 5, 'sku' => 'MKR-001', 'name' => 'Chitato Sapi Panggang 68g', 'unit' => 'pcs', 'purchase_price' => 8000, 'selling_price' => 11000, 'stock' => 15, 'min_stock' => 5, 'is_active' => true],
            ['category_id' => 5, 'sku' => 'MKR-002', 'name' => 'Taro Net 70g', 'unit' => 'pcs', 'purchase_price' => 7000, 'selling_price' => 9500, 'stock' => 3, 'min_stock' => 5, 'is_active' => true], // Stok Menipis
            ['category_id' => 5, 'sku' => 'MKR-003', 'name' => 'Oreo Vanilla 133g', 'unit' => 'pcs', 'purchase_price' => 8500, 'selling_price' => 12000, 'stock' => 20, 'min_stock' => 5, 'is_active' => true],

            // ==========================================
            // --- SEMBAKO (Kategori 4) ---
            // ==========================================
            ['category_id' => 4, 'sku' => 'SMK-001', 'name' => 'Beras Pandan Wangi 5kg', 'unit' => 'sak', 'purchase_price' => 60000, 'selling_price' => 70000, 'stock' => 20, 'min_stock' => 5, 'is_active' => true],
            ['category_id' => 4, 'sku' => 'SMK-002', 'name' => 'Gula Pasir Gulaku 1kg', 'unit' => 'kg', 'purchase_price' => 14000, 'selling_price' => 16000, 'stock' => 15, 'min_stock' => 5, 'is_active' => true],
            ['category_id' => 4, 'sku' => 'SMK-003', 'name' => 'Minyak Goreng Bimoli 1L', 'unit' => 'botol', 'purchase_price' => 15000, 'selling_price' => 18000, 'stock' => 25, 'min_stock' => 10, 'is_active' => true],
            ['category_id' => 4, 'sku' => 'SMK-004', 'name' => 'Telur Ayam Negeri', 'unit' => 'kg', 'purchase_price' => 25000, 'selling_price' => 28000, 'stock' => 30, 'min_stock' => 10, 'is_active' => true],
            ['category_id' => 4, 'sku' => 'SMK-005', 'name' => 'Indomie Goreng', 'unit' => 'pcs', 'purchase_price' => 2800, 'selling_price' => 3500, 'stock' => 0, 'min_stock' => 10, 'is_active' => true], // Stok Habis

            // ==========================================
            // --- MAKANAN & ALAT TULIS (Kategori 2 & 1) ---
            // ==========================================
            ['category_id' => 2, 'sku' => 'MKN-001', 'name' => 'Roti Tawar Sari Roti', 'unit' => 'bungkus', 'purchase_price' => 12000, 'selling_price' => 15000, 'stock' => 10, 'min_stock' => 3, 'is_active' => true],
            ['category_id' => 1, 'sku' => 'ATK-001', 'name' => 'Pulpen Standard AE7', 'unit' => 'pcs', 'purchase_price' => 2000, 'selling_price' => 3500, 'stock' => 50, 'min_stock' => 10, 'is_active' => true],
            ['category_id' => 1, 'sku' => 'ATK-002', 'name' => 'Buku Tulis Sidu 38 Lembar', 'unit' => 'pcs', 'purchase_price' => 3000, 'selling_price' => 4500, 'stock' => 40, 'min_stock' => 10, 'is_active' => true],

            // ==========================================
            // --- ROKOK (Kategori 6) ---
            // ==========================================
            ['category_id' => 6, 'sku' => 'ROK-001', 'name' => 'Sampoerna Mild 16', 'unit' => 'bungkus', 'purchase_price' => 28500, 'selling_price' => 31000, 'stock' => 40, 'min_stock' => 10, 'is_active' => true],
            ['category_id' => 6, 'sku' => 'ROK-002', 'name' => 'Sampoerna A Mild 16', 'unit' => 'bungkus', 'purchase_price' => 27000, 'selling_price' => 29500, 'stock' => 35, 'min_stock' => 10, 'is_active' => true],
            ['category_id' => 6, 'sku' => 'ROK-003', 'name' => 'Dji Sam Soe 234', 'unit' => 'bungkus', 'purchase_price' => 26000, 'selling_price' => 28500, 'stock' => 25, 'min_stock' => 10, 'is_active' => true],
            ['category_id' => 6, 'sku' => 'ROK-004', 'name' => 'Dji Sam Soe Magnum', 'unit' => 'bungkus', 'purchase_price' => 25000, 'selling_price' => 27500, 'stock' => 15, 'min_stock' => 5, 'is_active' => true],
            ['category_id' => 6, 'sku' => 'ROK-005', 'name' => 'Marlboro Merah 20', 'unit' => 'bungkus', 'purchase_price' => 38000, 'selling_price' => 42000, 'stock' => 12, 'min_stock' => 5, 'is_active' => true],
            ['category_id' => 6, 'sku' => 'ROK-006', 'name' => 'Dunhill Fine Cut', 'unit' => 'bungkus', 'purchase_price' => 26000, 'selling_price' => 28500, 'stock' => 15, 'min_stock' => 5, 'is_active' => true],
            ['category_id' => 6, 'sku' => 'ROK-007', 'name' => 'Gudang Garam Surya 12', 'unit' => 'bungkus', 'purchase_price' => 26000, 'selling_price' => 28500, 'stock' => 35, 'min_stock' => 10, 'is_active' => true],
            ['category_id' => 6, 'sku' => 'ROK-008', 'name' => 'Gudang Garam International', 'unit' => 'bungkus', 'purchase_price' => 25000, 'selling_price' => 27000, 'stock' => 25, 'min_stock' => 10, 'is_active' => true],
            ['category_id' => 6, 'sku' => 'ROK-009', 'name' => 'Gudang Garam Filter', 'unit' => 'bungkus', 'purchase_price' => 24000, 'selling_price' => 26000, 'stock' => 20, 'min_stock' => 5, 'is_active' => true],
            ['category_id' => 6, 'sku' => 'ROK-010', 'name' => 'GG Mild 16', 'unit' => 'bungkus', 'purchase_price' => 26000, 'selling_price' => 28500, 'stock' => 30, 'min_stock' => 10, 'is_active' => true],
            ['category_id' => 6, 'sku' => 'ROK-011', 'name' => 'Surya Pro Mild 16', 'unit' => 'bungkus', 'purchase_price' => 27000, 'selling_price' => 29000, 'stock' => 18, 'min_stock' => 5, 'is_active' => true],
            ['category_id' => 6, 'sku' => 'ROK-012', 'name' => 'Djarum Super 12', 'unit' => 'bungkus', 'purchase_price' => 24000, 'selling_price' => 26500, 'stock' => 20, 'min_stock' => 5, 'is_active' => true],
            ['category_id' => 6, 'sku' => 'ROK-013', 'name' => 'Djarum Black 16', 'unit' => 'bungkus', 'purchase_price' => 25000, 'selling_price' => 27500, 'stock' => 8, 'min_stock' => 10, 'is_active' => true], // Stok menipis
            ['category_id' => 6, 'sku' => 'ROK-014', 'name' => 'Djarum 76 12', 'unit' => 'bungkus', 'purchase_price' => 15000, 'selling_price' => 17000, 'stock' => 50, 'min_stock' => 20, 'is_active' => true],
            ['category_id' => 6, 'sku' => 'ROK-015', 'name' => 'LA Lights 16', 'unit' => 'bungkus', 'purchase_price' => 27000, 'selling_price' => 29500, 'stock' => 15, 'min_stock' => 5, 'is_active' => true],
            ['category_id' => 6, 'sku' => 'ROK-016', 'name' => 'LA Bold 16', 'unit' => 'bungkus', 'purchase_price' => 27000, 'selling_price' => 29500, 'stock' => 10, 'min_stock' => 5, 'is_active' => true],
            ['category_id' => 6, 'sku' => 'ROK-017', 'name' => 'Djarum Coklat 12', 'unit' => 'bungkus', 'purchase_price' => 20000, 'selling_price' => 22000, 'stock' => 25, 'min_stock' => 10, 'is_active' => true],
            ['category_id' => 6, 'sku' => 'ROK-018', 'name' => 'Clas Mild 16', 'unit' => 'bungkus', 'purchase_price' => 24000, 'selling_price' => 26500, 'stock' => 18, 'min_stock' => 5, 'is_active' => true],
            ['category_id' => 6, 'sku' => 'ROK-019', 'name' => 'Minak Djinggo 12', 'unit' => 'bungkus', 'purchase_price' => 18000, 'selling_price' => 20000, 'stock' => 20, 'min_stock' => 5, 'is_active' => true],
            ['category_id' => 6, 'sku' => 'ROK-020', 'name' => 'Kembang Djepun 12', 'unit' => 'bungkus', 'purchase_price' => 17000, 'selling_price' => 19000, 'stock' => 15, 'min_stock' => 5, 'is_active' => true],
            ['category_id' => 6, 'sku' => 'ROK-021', 'name' => 'Bentoel Biru 12', 'unit' => 'bungkus', 'purchase_price' => 20000, 'selling_price' => 22000, 'stock' => 20, 'min_stock' => 5, 'is_active' => true],
            ['category_id' => 6, 'sku' => 'ROK-022', 'name' => 'Bentoel Merah 12', 'unit' => 'bungkus', 'purchase_price' => 20000, 'selling_price' => 22000, 'stock' => 15, 'min_stock' => 5, 'is_active' => true],
            ['category_id' => 6, 'sku' => 'ROK-023', 'name' => 'Star Mild 16', 'unit' => 'bungkus', 'purchase_price' => 24000, 'selling_price' => 26500, 'stock' => 10, 'min_stock' => 5, 'is_active' => true],
            ['category_id' => 6, 'sku' => 'ROK-024', 'name' => 'Tali Jagat 12', 'unit' => 'bungkus', 'purchase_price' => 18000, 'selling_price' => 20000, 'stock' => 12, 'min_stock' => 5, 'is_active' => true],
            ['category_id' => 6, 'sku' => 'ROK-025', 'name' => 'Wismilak Diplomat 12', 'unit' => 'bungkus', 'purchase_price' => 23000, 'selling_price' => 25000, 'stock' => 20, 'min_stock' => 5, 'is_active' => true],
            ['category_id' => 6, 'sku' => 'ROK-026', 'name' => 'Wismilak Slim 12', 'unit' => 'bungkus', 'purchase_price' => 22000, 'selling_price' => 24000, 'stock' => 15, 'min_stock' => 5, 'is_active' => true],
            ['category_id' => 6, 'sku' => 'ROK-027', 'name' => 'Galang Mild 16', 'unit' => 'bungkus', 'purchase_price' => 23000, 'selling_price' => 25000, 'stock' => 15, 'min_stock' => 5, 'is_active' => true],
            ['category_id' => 6, 'sku' => 'ROK-028', 'name' => 'Apache 12', 'unit' => 'bungkus', 'purchase_price' => 15000, 'selling_price' => 17000, 'stock' => 30, 'min_stock' => 10, 'is_active' => true],
            ['category_id' => 6, 'sku' => 'ROK-029', 'name' => 'Juara 12', 'unit' => 'bungkus', 'purchase_price' => 16000, 'selling_price' => 18000, 'stock' => 25, 'min_stock' => 10, 'is_active' => true],
            ['category_id' => 6, 'sku' => 'ROK-030', 'name' => 'Sampoerna U Mild 16', 'unit' => 'bungkus', 'purchase_price' => 26000, 'selling_price' => 28500, 'stock' => 20, 'min_stock' => 5, 'is_active' => true],
            ['category_id' => 6, 'sku' => 'ROK-031', 'name' => 'Kansas 12', 'unit' => 'bungkus', 'purchase_price' => 18000, 'selling_price' => 20000, 'stock' => 15, 'min_stock' => 5, 'is_active' => true],
            ['category_id' => 6, 'sku' => 'ROK-032', 'name' => 'Vivo 12', 'unit' => 'bungkus', 'purchase_price' => 19000, 'selling_price' => 21000, 'stock' => 20, 'min_stock' => 5, 'is_active' => true],

            // ==========================================
            // --- PRODUK NONAKTIF (Buat ngetes filter) ---
            // ==========================================
            ['category_id' => 2, 'sku' => 'OLD-001', 'name' => 'Kue Basah (Kadaluarsa)', 'unit' => 'pcs', 'purchase_price' => 2000, 'selling_price' => 3000, 'stock' => 0, 'min_stock' => 0, 'is_active' => false],
        ];

        foreach ($products as $p) {
            Product::create($p);
        }
    }
}