<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;

class CustomerController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            // Kasir boleh semua kecuali hapus
            new Middleware('role:admin,kasir'),
            new Middleware('role:admin', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $search = $request->query('search');

        $customers = Customer::query()
            ->withCount('sales')
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('customers.index', compact('customers', 'search'));
    }

    public function create()
    {
        return view('customers.form', ['customer' => new Customer()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^08[0-9]{8,11}$/', 'unique:customers,phone'],
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama pelanggan wajib diisi.',
            'phone.regex' => 'Format nomor HP harus diawali 08 dan 10-13 digit.',
            'phone.unique' => 'Nomor HP sudah terdaftar.',
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['sales' => function($q) {
            $q->where('status', 'paid')->latest()->limit(10);
        }]);
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.form', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^08[0-9]{8,11}$/', Rule::unique('customers', 'phone')->ignore($customer->id)],
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:500',
        ], [
            'phone.regex' => 'Format nomor HP harus diawali 08 dan 10-13 digit.',
            'phone.unique' => 'Nomor HP sudah terdaftar.',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil dihapus.');
    }
}