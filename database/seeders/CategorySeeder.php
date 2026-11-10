<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Urutan ini penting, ID 1 sampai 6
        $cats = ['Alat Tulis', 'Makanan', 'Minuman', 'Sembako', 'Snack', 'Rokok'];
        
        foreach ($cats as $c) {
            Category::create(['name' => $c, 'description' => "Kategori $c"]);
        }
    }
}