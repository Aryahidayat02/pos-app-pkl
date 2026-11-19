<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function create()
    {
        $products = Product::active()->orderBy('name')->get();
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
                // PAKSA TIMEZONE JAKARTA
                date_default_timezone_set('Asia/Jakarta');
                $waktuSekarang = date('Y-m-d H:i:s');

                $totalAmount = 0;
                $itemsToSave = [];

                foreach ($validated['items'] as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                    if ($product->stock < $item['quantity']) {
                        throw ValidationException::withMessages([
                            'items' => "Stok {$product->name} tidak cukup! Sisa: {$product->stock}",
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
                        'paid_amount' => 'Uang bayar kurang! Total: Rp '.number_format($totalAmount, 0, ',', '.'),
                    ]);
                }

                $date = date('Ymd', strtotime($waktuSekarang));
                $countToday = Sale::whereDate('created_at', date('Y-m-d', strtotime($waktuSekarang)))->count() + 1;
                $invoiceNumber = 'INV-'.$date.'-'.str_pad($countToday, 4, '0', STR_PAD_LEFT);

                $sale = Sale::create([
                    'invoice_number' => $invoiceNumber,
                    'user_id' => auth()->id(),
                    'customer_id' => $validated['customer_id'] ?? null,
                    'sale_date' => $waktuSekarang, // STRING WAKTU LOKAL
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
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function index()
    {
        $query = Sale::with('user', 'customer')->orderBy('created_at', 'desc');

        if (auth()->user()->role === 'kasir') {
            $query->where('user_id', auth()->id());
        }

        $sales = $query->get();

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

    public function receipt(Sale $sale)
    {
        if (auth()->user()->role === 'kasir' && $sale->user_id !== auth()->id()) {
            abort(403, 'Anda tidak berhak melihat struk ini.');
        }
        $sale->load('items', 'user', 'customer');

        return view('sales.receipt', compact('sale'));
    }

    public function void(Request $request, Sale $sale)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Hanya admin yang bisa void transaksi.');
        }

        if ($sale->status === 'void') {
            return back()->with('error', 'Transaksi sudah di-void sebelumnya.');
        }

        try {
            DB::transaction(function () use ($sale) {
                foreach ($sale->items as $item) {
                    Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                }
                $sale->update(['status' => 'void']);
            });

            return redirect()->route('sales.index')->with('success', 'Transaksi berhasil di-void dan stok dikembalikan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal void: '.$e->getMessage());
        }
    }
}
