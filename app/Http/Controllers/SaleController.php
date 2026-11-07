<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function create()
    {
        $products = Product::where('is_active', 1)
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();
            
        $customers = Customer::orderBy('name')->get();

        return view('sales.create', compact('products', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:tunai,qris,transfer',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // JURUS BRUTAL: Paksa PHP pakai waktu Jakarta
                date_default_timezone_set('Asia/Jakarta');
                $waktuSekarang = date('Y-m-d H:i:s'); 

                $totalAmount = 0;
                $itemsToSave = [];

                foreach ($validated['items'] as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                    if ($product->stock < $item['quantity']) {
                        throw ValidationException::withMessages([
                            'items' => "Stok {$product->name} tidak cukup! Sisa: {$product->stock}"
                        ]);
                    }

                    $subtotal = $product->selling_price * $item['quantity'];
                    $totalAmount += $subtotal;

                    $itemsToSave[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'price' => $product->selling_price,
                        'purchase_price' => $product->purchase_price,
                        'quantity' => $item['quantity'],
                        'subtotal' => $subtotal,
                    ];

                    $product->decrement('stock', $item['quantity']);
                }

                if ($validated['paid_amount'] < $totalAmount) {
                    throw ValidationException::withMessages([
                        'paid_amount' => "Uang bayar kurang! Total: Rp " . number_format($totalAmount, 0, ',', '.')
                    ]);
                }

                // Generate Invoice pakai tanggal yang udah dipaksa WIB
                $date = date('Ymd', strtotime($waktuSekarang));
                $countToday = Sale::whereDate('created_at', date('Y-m-d', strtotime($waktuSekarang)))->count() + 1;
                $invoiceNumber = 'INV-' . $date . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

                // Simpan Header Sales (PAKSA STRING WAKTU LOKAL)
                $sale = Sale::create([
                    'invoice_number' => $invoiceNumber,
                    'user_id' => auth()->id(),
                    'customer_id' => $validated['customer_id'] ?? null,
                    'sale_date' => $waktuSekarang, // <--- PAKE VARIABEL INI
                    'subtotal' => $totalAmount,
                    'discount' => 0,
                    'total' => $totalAmount,
                    'payment_method' => $validated['payment_method'],
                    'paid_amount' => $validated['paid_amount'],
                    'change_amount' => $validated['paid_amount'] - $totalAmount,
                    'status' => 'paid',
                ]);

                $sale->items()->createMany($itemsToSave);
            });

            return redirect()->route('sales.index')->with('success', 'Transaksi berhasil disimpan!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function index()
    {
        $query = Sale::with('user', 'customer')->orderBy('created_at', 'desc');

        if (auth()->user()->role === 'kasir') {
            $query->where('user_id', auth()->id());
        }

        $sales = $query->paginate(10);
        return view('sales.index', compact('sales'));
    }

    public function show(Sale $sale)
    {
        if (auth()->user()->role === 'kasir' && $sale->user_id !== auth()->id()) {
            abort(403, 'Anda tidak berhak melihat transaksi ini.');
        }

        $sale->load('items', 'user', 'customer');
        return view('sales.show', compact('sale'));
    }
}