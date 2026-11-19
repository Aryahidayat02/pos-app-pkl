<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ProductController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('role:admin', except: ['index', 'show'])];
    }

    public function index(Request $request)
    {
        $query = Product::with('category');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }
        $products = $query->latest()->get();
        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('products.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required|string|max:50|alpha_dash|unique:products,sku',
            'barcode' => 'nullable|numeric|unique:products,barcode',
            'name' => 'required|string|max:150',
            'unit' => 'required|string|max:20',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0|gte:purchase_price',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time().'.'.$image->getClientOriginalExtension();
            $path = storage_path('app/public/products/'.$filename);

            // SINTAKS VERSI 3
            $manager = new ImageManager(new Driver);
            $manager->read($image->getRealPath())
                ->cover(500, 500)
                ->save($path);

            $validated['image'] = 'products/'.$filename;
        }

        $validated['is_active'] = $request->boolean('is_active');
        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();

        return view('products.form', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required|string|max:50|alpha_dash|unique:products,sku,'.$product->id,
            'barcode' => 'nullable|numeric|unique:products,barcode,'.$product->id,
            'name' => 'required|string|max:150',
            'unit' => 'required|string|max:20',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0|gte:purchase_price',
            'min_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image && ! filter_var($product->image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($product->image);
            }
            $image = $request->file('image');
            $filename = time().'.'.$image->getClientOriginalExtension();
            $path = storage_path('app/public/products/'.$filename);

            // SINTAKS VERSI 3
            $manager = new ImageManager(new Driver);
            $manager->read($image->getRealPath())
                ->cover(500, 500)
                ->save($path);

            $validated['image'] = 'products/'.$filename;
        }

        $validated['is_active'] = $request->boolean('is_active');
        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        if ($product->saleItems()->count() > 0 || $product->purchaseItems()->count() > 0) {
            return redirect()->route('products.index')->with('error', 'Produk tidak bisa dihapus karena sudah memiliki riwayat transaksi.');
        }
        if ($product->image && ! filter_var($product->image, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
