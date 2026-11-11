<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ActivityLog;
use Carbon\Carbon; // Tambahan untuk timezone
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('role:admin', except: ['index', 'show'])];
    }

    public function index(Request $request)
    {
        $products = Product::with('category')
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where(function ($x) use ($s) {
                    $x->where('name', 'like', "%$s%")->orWhere('sku', 'like', "%$s%");
                });
            })
            ->when($request->filled('category_id'), fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->filled('status'), fn($q) => $q->where('is_active', $request->status === '1'))
            ->latest()->paginate(10)->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        return view('products.form', [
            'product' => new Product(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'create',
            'model' => 'Product',
            'description' => 'Menambahkan produk: ' . $data['name'],
            'created_at' => Carbon::now('Asia/Jakarta'), // PAKSA WIB
        ]);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        $product->load('category');
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('products.form', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validateData($request, $product->id);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'update',
            'model' => 'Product',
            'description' => 'Mengubah produk: ' . $data['name'],
            'created_at' => Carbon::now('Asia/Jakarta'), // PAKSA WIB
        ]);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        if ($product->saleItems()->exists() || $product->purchaseItems()->exists()) {
            return back()->with('error', 'Produk sudah punya riwayat transaksi. Nonaktifkan saja, jangan dihapus.');
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete',
            'model' => 'Product',
            'description' => 'Menghapus produk: ' . $product->name,
            'created_at' => Carbon::now('Asia/Jakarta'), // PAKSA WIB
        ]);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return back()->with('success', 'Produk berhasil dihapus.');
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        $rules = [
            'category_id' => ['required', 'exists:categories,id'],
            'sku' => ['required', 'string', 'max:50', 'alpha_dash',
                Rule::unique('products', 'sku')->ignore($ignoreId)],
            'name' => ['required', 'string', 'max:150'],
            'unit' => ['required', 'string', 'max:20'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0', 'gte:purchase_price'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];

        if (! $ignoreId) {
            $rules['stock'] = ['required', 'integer', 'min:0'];
        }

        return $request->validate($rules, [
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori tidak valid.',
            'sku.required' => 'SKU wajib diisi.',
            'sku.unique' => 'SKU sudah dipakai produk lain.',
            'sku.alpha_dash' => 'SKU hanya boleh huruf, angka, strip, dan underscore.',
            'name.required' => 'Nama produk wajib diisi.',
            'unit.required' => 'Satuan wajib diisi.',
            'purchase_price.required' => 'Harga modal wajib diisi.',
            'selling_price.required' => 'Harga jual wajib diisi.',
            'selling_price.gte' => 'Harga jual tidak boleh di bawah modal.',
            'stock.required' => 'Stok awal wajib diisi.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus jpg, jpeg, png, atau webp.',
            'image.max' => 'Gambar maksimal 2 MB.',
        ]);
    }
}