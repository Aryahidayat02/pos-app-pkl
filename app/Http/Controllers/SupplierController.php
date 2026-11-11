<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('role:admin')];
    }

    public function index(Request $request)
    {
        $q = $request->get('search');
        
        $suppliers = Supplier::withCount('purchases')
            ->when($q, fn($x) => $x->where('name', 'like', "%$q%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('suppliers.index', compact('suppliers', 'q'));
    }

    public function create()
    {
        return view('suppliers.form', ['supplier' => new Supplier()]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        Supplier::create($data);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'create',
            'model' => 'Supplier',
            'description' => 'Menambahkan supplier: ' . $data['name']
        ]);

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.form', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $supplier->update($this->validateData($request));

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'update',
            'model' => 'Supplier',
            'description' => 'Mengubah supplier: ' . $request->name
        ]);

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->purchases()->exists()) {
            return back()->with('error', 'Supplier sudah punya riwayat stok masuk. Tidak bisa dihapus.');
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete',
            'model' => 'Supplier',
            'description' => 'Menghapus supplier: ' . $supplier->name
        ]);

        $supplier->delete();

        return back()->with('success', 'Supplier berhasil dihapus.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'contact_person' => ['nullable', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Nama supplier wajib diisi.',
            'name.max' => 'Nama maksimal 150 karakter.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'address.max' => 'Alamat maksimal 500 karakter.',
        ]);
    }
}