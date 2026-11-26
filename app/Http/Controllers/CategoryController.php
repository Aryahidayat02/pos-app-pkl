<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('role:admin')];
    }

    public function index(Request $request): View|JsonResponse
    {
        $categories = Category::query()
            ->select(['id', 'name', 'description'])
            ->withCount('products');

        if ($request->ajax()) {
            return DataTables::eloquent($categories)
                ->addIndexColumn()
                ->editColumn('description', fn (Category $category): string => $category->description ?: '-')
                ->editColumn('products_count', fn (Category $category): string => $category->products_count.' Produk')
                ->addColumn('actions', function (Category $category): string {
                    return view('categories.datatables.actions', ['category' => $category])->render();
                })
                ->rawColumns(['actions'])
                ->toJson();
        }

        return view('categories.index');
    }

    public function create()
    {
        return view('categories.form', ['category' => new Category]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        Category::create($data);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        return view('categories.form', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('categories', 'name')->ignore($category->id)],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $category->update($data);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->with('error', "Kategori masih dipakai oleh {$category->products()->count()} produk.");
        }

        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
