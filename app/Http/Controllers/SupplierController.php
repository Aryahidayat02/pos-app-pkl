<?php

namespace App\Http\Controllers;

use App\Models\PurchaseItem;
use App\Models\Supplier;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('role:admin')];
    }

    public function index(Request $request): View|JsonResponse
    {
        $suppliers = Supplier::query()
            ->select(['id', 'name', 'contact_person', 'phone', 'address'])
            ->selectSub(
                PurchaseItem::query()
                    ->join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
                    ->whereColumn('purchases.supplier_id', 'suppliers.id')
                    ->selectRaw('COALESCE(SUM(purchase_items.quantity), 0)'),
                'supplied_quantity',
            );

        if ($request->ajax()) {
            return DataTables::eloquent($suppliers)
                ->addIndexColumn()
                ->editColumn('contact_person', fn (Supplier $supplier): string => $supplier->contact_person ?: '-')
                ->editColumn('address', fn (Supplier $supplier): string => Str::limit($supplier->address ?? '', 30) ?: '-')
                ->editColumn('supplied_quantity', fn (Supplier $supplier): int => (int) $supplier->supplied_quantity)
                ->addColumn('actions', function (Supplier $supplier): string {
                    return view('suppliers.datatables.actions', ['supplier' => $supplier])->render();
                })
                ->rawColumns(['actions'])
                ->toJson();
        }

        return view('suppliers.index');
    }

    public function create(): View
    {
        return view('suppliers.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:suppliers,name'],
            'contact_person' => 'nullable|string|max:100',
            'phone' => ['required', 'string', 'regex:/^[0-9]{1,13}$/', 'unique:suppliers,phone'],
            'address' => 'nullable|string|max:500',
        ], [
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.regex' => 'Nomor telepon hanya boleh berisi angka dan maksimal 13 digit.',
            'name.unique' => 'Nama supplier ini sudah terdaftar.',
            'phone.unique' => 'Nomor telepon supplier ini sudah terdaftar.',
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function edit(Supplier $supplier): View
    {
        return view('suppliers.form', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150', Rule::unique('suppliers', 'name')->ignore($supplier->id)],
            'contact_person' => 'nullable|string|max:100',
            'phone' => ['required', 'string', 'regex:/^[0-9]{1,13}$/', Rule::unique('suppliers', 'phone')->ignore($supplier->id)],
            'address' => 'nullable|string|max:500',
        ], [
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.regex' => 'Nomor telepon hanya boleh berisi angka dan maksimal 13 digit.',
            'name.unique' => 'Nama supplier ini sudah terdaftar.',
            'phone.unique' => 'Nomor telepon supplier ini sudah terdaftar.',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->purchases()->count() > 0) {
            return redirect()->route('suppliers.index')->with('error', 'Supplier tidak bisa dihapus karena sudah memiliki riwayat stok masuk.');
        }
        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil dihapus.');
    }
}
