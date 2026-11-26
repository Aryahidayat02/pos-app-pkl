<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Support\ImageService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('role:admin', except: ['index', 'show'])];
    }

    public function index(Request $request): View|JsonResponse
    {
        $products = Product::query()
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->select([
                'products.id',
                'products.category_id',
                'products.sku',
                'products.barcode',
                'products.name',
                'products.unit',
                'products.selling_price',
                'products.stock',
                'products.min_stock',
                'products.image',
                'products.is_active',
                'categories.name as category_name',
            ])
            ->withCount(['saleItems', 'purchaseItems']);

        if (empty($request->input('order'))) {
            $products->orderByDesc('products.created_at')
                ->orderByDesc('products.id');
        }

        if ($request->ajax()) {
            return DataTables::eloquent($products)
                ->addIndexColumn()
                ->addColumn('image_display', function (Product $product): string {
                    return view('products.datatables.image', ['product' => $product])->render();
                })
                ->editColumn('selling_price', fn (Product $product): string => rupiah($product->selling_price))
                ->addColumn('stock_display', function (Product $product): string {
                    return view('products.datatables.stock', ['product' => $product])->render();
                })
                ->addColumn('status_display', function (Product $product): string {
                    return view('products.datatables.status', ['product' => $product])->render();
                })
                ->filterColumn('category_name', function (Builder $query, string $keyword): void {
                    $query->where('categories.name', 'like', "%{$keyword}%");
                })
                ->orderColumn('category_name', function (Builder $query, string $direction): void {
                    $query->orderBy('categories.name', $direction === 'desc' ? 'desc' : 'asc');
                })
                ->addColumn('actions', function (Product $product): string {
                    return view('products.datatables.actions', ['product' => $product])->render();
                })
                ->rawColumns(['image_display', 'stock_display', 'status_display', 'actions'])
                ->toJson();
        }

        return view('products.index', [
            'categories' => Category::query()->orderBy('name')->get(['id', 'name']),
            'units' => Product::query()->whereNotNull('unit')->where('unit', '!=', '')
                ->distinct()->orderBy('unit')->pluck('unit'),
        ]);
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('products.form', [
            'categories' => $categories,
            'product' => new Product,
            'units' => Product::query()->whereNotNull('unit')->where('unit', '!=', '')
                ->distinct()->orderBy('unit')->pluck('unit'),
        ]);
    }

    public function store(Request $request, ImageService $imageService)
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
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'extensions:jpg,jpeg,png', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $imageService->storeCover($request->file('image'), 'products');
        }

        $validated['is_active'] = $request->boolean('is_active');
        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product): View
    {
        $product->load('category');

        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();

        return view('products.form', [
            'product' => $product,
            'categories' => $categories,
            'units' => Product::query()->whereNotNull('unit')->where('unit', '!=', '')
                ->distinct()->orderBy('unit')->pluck('unit'),
        ]);
    }

    public function update(Request $request, Product $product, ImageService $imageService)
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
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'extensions:jpg,jpeg,png', 'max:2048'],
        ]);

        $oldImage = $product->image;
        if ($request->hasFile('image')) {
            $validated['image'] = $imageService->storeCover($request->file('image'), 'products');
        }

        $validated['is_active'] = $request->boolean('is_active');
        $product->update($validated);

        if ($request->hasFile('image') && $oldImage && ! filter_var($oldImage, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($oldImage);
        }

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
