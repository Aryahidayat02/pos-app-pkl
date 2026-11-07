<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with('supplier', 'user')->latest()->paginate(10);
        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::where('is_active', 1)->orderBy('name')->get();
        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date|before_or_equal:today',
            'note' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|distinct|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.purchase_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // 1. Generate No. Dokumen (PB-YYYYMMDD-0001)
                $date = \Carbon\Carbon::now('Asia/Jakarta')->format('Ymd');
                $countToday = Purchase::whereDate('created_at', \Carbon\Carbon::now('Asia/Jakarta')->toDateString())->count() + 1;
                $purchaseNumber = 'PB-' . $date . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

                // 2. Hitung Total & Simpan Header
                $totalAmount = 0;
                $itemsToSave = [];

                foreach ($validated['items'] as $item) {
                    $subtotal = $item['quantity'] * $item['purchase_price'];
                    $totalAmount += $subtotal;

                    $itemsToSave[] = [
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'purchase_price' => $item['purchase_price'],
                        'subtotal' => $subtotal,
                    ];
                }

                $purchase = Purchase::create([
                    'purchase_number' => $purchaseNumber,
                    'supplier_id' => $validated['supplier_id'],
                    'user_id' => auth()->id(),
                    'purchase_date' => $validated['purchase_date'],
                    'total_amount' => $totalAmount,
                    'note' => $validated['note'] ?? null,
                ]);

                // 3. Simpan Detail & Tambah Stok
                foreach ($itemsToSave as $data) {
                    $purchase->items()->create($data);
                    
                    // TAMBAH STOK PRODUK (KUNCI UTAMA)
                    Product::where('id', $data['product_id'])->increment('stock', $data['quantity']);
                    
                    // Update harga modal terbaru di produk
                    Product::where('id', $data['product_id'])->update(['purchase_price' => $data['purchase_price']]);
                }
            });

            return redirect()->route('purchases.index')->with('success', 'Stok masuk berhasil dicatat! Stok produk telah diperbarui.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Purchase $purchase)
    {
        $purchase->load('items.product', 'supplier', 'user');
        return view('purchases.show', compact('purchase'));
    }
}