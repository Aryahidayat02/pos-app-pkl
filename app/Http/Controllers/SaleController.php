<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Support\DocumentNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    // 1. Halaman Utama Kasir
    public function create()
    {
        $products = Product::where('is_active', true)->where('stock', '>', 0)->orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        return view('sales.create', compact('products', 'customers'));
    }

    // 2. Proses Simpan Transaksi
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|distinct|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'discount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:tunai,qris,transfer',
            'paid_amount' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:255',
        ], [
            'items.required' => 'Keranjang masih kosong.',
        ]);

        $sale = DB::transaction(function () use ($validated) {
            $productIds = collect($validated['items'])->pluck('product_id');
            
            // Kunci baris produk agar tidak bentrok antar kasir
            $products = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $index => $item) {
                $product = $products[$item['product_id']];

                if (!$product->is_active || $product->stock < $item['quantity']) {
                    throw ValidationException::withMessages([
                        "items.$index.quantity" => "Stok {$product->name} tidak cukup (sisa {$product->stock})."
                    ]);
                }

                $itemSubtotal = $product->selling_price * $item['quantity'];
                $subtotal += $itemSubtotal;

                $itemsData[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->selling_price,
                    'purchase_price' => $product->purchase_price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $itemSubtotal,
                ];
            }

            $discount = $validated['discount'] ?? 0;
            $total = $subtotal - $discount;

            if ($validated['payment_method'] === 'tunai' && $validated['paid_amount'] < $total) {
                throw ValidationException::withMessages([
                    'paid_amount' => 'Uang yang dibayarkan kurang dari total.'
                ]);
            }

            if (in_array($validated['payment_method'], ['qris', 'transfer'])) {
                $validated['paid_amount'] = $total;
            }

            // Simpan Header
            $sale = Sale::create([
                'invoice_number' => DocumentNumber::sale(),
                'user_id' => auth()->id(),
                'customer_id' => $validated['customer_id'] ?? null,
                'sale_date' => now(),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
                'payment_method' => $validated['payment_method'],
                'paid_amount' => $validated['paid_amount'],
                'change_amount' => $validated['paid_amount'] - $total,
                'status' => 'paid',
                'note' => $validated['note'] ?? null,
            ]);

            // Simpan Detail & Potong Stok
            foreach ($itemsData as $data) {
                $sale->items()->create($data);
                $product = $products[$data['product_id']];
                $product->decrement('stock', $data['quantity']);
            }

            return $sale;
        });

        return redirect()->route('sales.show', $sale)->with('success', 'Transaksi berhasil disimpan.');
    }

    // 3. Halaman Riwayat Transaksi
    public function index(Request $request)
    {
        $query = Sale::with(['user', 'customer']);

        if (auth()->user()->role === 'kasir') {
            $query->where('user_id', auth()->id());
        }

        if ($request->filled('start_date')) {
            $query->whereDate('sale_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('sale_date', '<=', $request->end_date);
        }
        if ($request->filled('invoice')) {
            $query->where('invoice_number', 'like', '%' . $request->invoice . '%');
        }

        $sales = $query->latest()->paginate(10)->withQueryString();

        return view('sales.index', compact('sales'));
    }

    // 4. Halaman Struk
    public function show(Sale $sale)
    {
        if (auth()->user()->role === 'kasir' && $sale->user_id !== auth()->id()) {
            abort(403, 'Anda tidak boleh melihat transaksi kasir lain.');
        }

        $sale->load(['items.product', 'user', 'customer']);
        return view('sales.show', compact('sale'));
    }

    // 5. FITUR BONUS: Void (Pembatalan) Transaksi
    public function void(Request $request, Sale $sale)
    {
        // Hanya admin yang boleh void
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Hanya admin yang bisa membatalkan transaksi.');
        }

        if ($sale->status === 'void') {
            return redirect()->back()->withErrors(['error' => 'Transaksi ini sudah dibatalkan sebelumnya.']);
        }

        $request->validate([
            'void_reason' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($sale, $request) {
            // 1. Ubah status jadi void
            $sale->update([
                'status' => 'void',
                'note' => $sale->note . ' | VOID: ' . $request->void_reason,
            ]);

            // 2. Kembalikan stok
            foreach ($sale->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
            }
        });

        return redirect()->route('sales.show', $sale)->with('success', 'Transaksi berhasil dibatalkan dan stok telah dikembalikan.');
    }
}