<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('role:admin', only: ['destroy']),
        ];
    }

    public function index(Request $request): View|JsonResponse
    {
        $customers = Customer::query()
            ->select(['id', 'name', 'phone', 'email', 'address'])
            ->withCount('sales');

        if ($request->ajax()) {
            return DataTables::eloquent($customers)
                ->addIndexColumn()
                ->editColumn('email', fn (Customer $customer): string => $customer->email ?: '-')
                ->editColumn('sales_count', fn (Customer $customer): string => $customer->sales_count.'x')
                ->addColumn('actions', function (Customer $customer): string {
                    return view('customers.datatables.actions', ['customer' => $customer])->render();
                })
                ->rawColumns(['actions'])
                ->toJson();
        }

        return view('customers.index');
    }

    public function create(): View
    {
        return view('customers.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => ['required', 'string', 'regex:/^[0-9]{1,13}$/', 'unique:customers,phone'],
            'email' => ['nullable', 'email', 'max:150', 'unique:customers,email'],
            'address' => 'nullable|string|max:500',
        ], [
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.regex' => 'Nomor telepon hanya boleh berisi angka dan maksimal 13 digit.',
            'phone.unique' => 'Nomor HP ini sudah terdaftar.',
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function show(Customer $customer): View
    {
        $customer->load(['sales' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }]);

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        return view('customers.form', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => ['required', 'string', 'regex:/^[0-9]{1,13}$/', Rule::unique('customers', 'phone')->ignore($customer->id)],
            'email' => ['nullable', 'email', 'max:150', Rule::unique('customers', 'email')->ignore($customer->id)],
            'address' => 'nullable|string|max:500',
        ], [
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.regex' => 'Nomor telepon hanya boleh berisi angka dan maksimal 13 digit.',
            'phone.unique' => 'Nomor HP ini sudah terdaftar.',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil dihapus.');
    }
}
