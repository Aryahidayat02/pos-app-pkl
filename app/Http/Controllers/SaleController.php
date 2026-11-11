<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Support\DocumentNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class SaleController extends Controller
{
    public function create()
    {
        return view('sales.create', [
            'products' => Product::active()->where('stock', '>', 0)->orderBy('name')->get(),
            'customers' => Customer::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:tunai,qris,transfer'],
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:255'],
        ], [
            'items.required' => 'Keranjang masih kosong.',
            'items.min' => 'Keranjang masih kosong.',
            'items.*.product_id.required' => 'Produk wajib dipilih.',
            'items.*.product_id.distinct' => 'Produk duplikat dalam keranjang.',
            'items.*.quantity.required' => 'Jumlah wajib diisi.',
            'items.*.quantity.min' => 'Jumlah minimal 1.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'paid_amount.required' => 'Uang diterima wajib diisi.',
        ]);

        $sale = DB::transaction(function () use ($data) {
            $ids = collect($data['items'])->pluck('product_id');
            $products = Product::whereIn('id', $ids)->lockForUpdate()->get()->keyBy('id');

            $subtotal = 0;
            $itemsToSave = [];

            foreach ($data['items'] as $i => $item) {
                $product = $products[$item['product_id']] ?? null;

                if (! $product || ! $product->is_active) {
                    throw ValidationException::withMessages(["items.$i.product_id" => 'Produk tidak tersedia.']);
                }

                if ($product->stock < $item['quantity']) {
                    throw ValidationException::withMessages([
                        "items.$i.quantity" => "Stok {$product->name} tidak cukup (sisa {$product->stock}).",
                    ]);
                }

                $lineSubtotal = $product->selling_price * $item['quantity'];
                $subtotal += $lineSubtotal;

                $itemsToSave[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->selling_price,
                    'purchase_price' => $product->purchase_price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $lineSubtotal,
                ];
            }

            $discount = (float) ($data['discount'] ?? 0);
            if ($discount > $subtotal) {
                throw ValidationException::withMessages(['discount' => 'Diskon tidak boleh melebihi subtotal.']);
            }
            $total = $subtotal - $discount;

            $method = $data['payment_method'];
            if (in_array($method, ['qris', 'transfer'])) {
                $paid = $total;
            } else {
                $paid = (float) $data['paid_amount'];
                if ($paid < $total) {
                    throw ValidationException::withMessages(['paid_amount' => 'Uang yang diterima kurang dari total.']);
                }
            }

            // PAKSA WAKTU JAKARTA (WIB)
            $sale = Sale::create([
                'invoice_number' => DocumentNumber::sale(),
                'user_id' => Auth::id(),
                'customer_id' => $data['customer_id'] ?? null,
                'sale_date' => Carbon::now('Asia/Jakarta'),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
                'payment_method' => $method,
                'paid_amount' => $paid,
                'change_amount' => $paid - $total,
                'status' => 'paid',
                'note' => $data['note'] ?? null,
            ]);

            foreach ($itemsToSave as $item) {
                $sale->items()->create($item);
                $products[$item['product_id']]->decrement('stock', $item['quantity']);
            }

            return $sale;
        });

        return redirect()->route('sales.show', $sale)->with('success', 'Transaksi berhasil disimpan.');
    }

    public function index(Request $request)
    {
        $sales = Sale::with('user', 'customer')
            ->when(Auth::user()->role === 'kasir', fn($q) => $q->where('user_id', Auth::id()))
            ->when($request->start_date, fn($q, $d) => $q->whereDate('sale_date', '>=', $d))
            ->when($request->end_date, fn($q, $d) => $q->whereDate('sale_date', '<=', $d))
            ->when($request->invoice, fn($q, $i) => $q->where('invoice_number', 'like', "%$i%"))
            ->latest('sale_date')
            ->paginate(15)
            ->withQueryString();

        return view('sales.index', compact('sales'));
    }

    public function show(Sale $sale)
    {
        if (Auth::user()->role === 'kasir' && $sale->user_id !== Auth::id()) {
            abort(403);
        }

        $sale->load('items', 'user', 'customer');
        return view('sales.show', compact('sale'));
    }

    public function receipt(Sale $sale)
    {
        if (Auth::user()->role === 'kasir' && $sale->user_id !== Auth::id()) {
            abort(403);
        }

        $sale->load('items', 'user', 'customer');
        return view('sales.receipt', compact('sale'));
    }

    public function void(Request $request, Sale $sale)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        if ($sale->status === 'void') {
            return back()->with('error', 'Transaksi sudah void.');
        }

        $request->validate([
            'note' => ['required', 'string', 'max:255'],
        ], [
            'note.required' => 'Alasan void wajib diisi.',
        ]);

        DB::transaction(function () use ($sale, $request) {
            foreach ($sale->items as $item) {
                Product::where('id', $item->product_id)->increment('stock', $item->quantity);
            }
            $sale->update([
                'status' => 'void',
                'note' => $request->note,
            ]);
        });

        return back()->with('success', 'Transaksi di-void, stok sudah dikembalikan.');
    }
}