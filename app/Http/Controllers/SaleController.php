<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

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
            'items.*.product_id' => 'required|distinct|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.discount_type' => 'nullable|in:amount,percent',
            'items.*.discount_value' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:tunai,qris,transfer',
            'paid_amount' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:255',
        ]);

        $productsForDiscountValidation = Product::query()
            ->whereKey(collect($validated['items'])->pluck('product_id'))
            ->get()
            ->keyBy('id');

        foreach ($validated['items'] as $index => $item) {
            $product = $productsForDiscountValidation->get($item['product_id']);
            if (! $product) {
                continue;
            }

            $discountType = $item['discount_type'] ?? 'amount';
            $discountValue = (float) ($item['discount_value'] ?? 0);
            $lineSubtotal = (float) $product->selling_price * (int) $item['quantity'];

            if ($discountType === 'percent' && $discountValue > 100) {
                throw ValidationException::withMessages([
                    "items.{$index}.discount_value" => 'Diskon persentase maksimal 100%.',
                ]);
            }

            if ($discountType === 'amount' && $discountValue > $lineSubtotal) {
                throw ValidationException::withMessages([
                    "items.{$index}.discount_value" => 'Diskon nominal tidak boleh melebihi subtotal produk.',
                ]);
            }
        }

        try {
            DB::transaction(function () use ($validated) {
                $transactionTime = Carbon::now('Asia/Jakarta');

                $subtotalAmount = 0;
                $discountAmount = 0;
                $itemsToSave = [];

                foreach ($validated['items'] as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                    if ($product->stock < $item['quantity']) {
                        throw ValidationException::withMessages([
                            'items' => "Stok {$product->name} tidak cukup! Sisa: {$product->stock}",
                        ]);
                    }

                    $lineSubtotal = round((float) $product->selling_price * $item['quantity'], 2);
                    $discountType = $item['discount_type'] ?? 'amount';
                    $discountValue = round((float) ($item['discount_value'] ?? 0), 2);
                    $lineDiscount = $discountType === 'percent'
                        ? round($lineSubtotal * $discountValue / 100, 2)
                        : $discountValue;

                    if ($lineDiscount > $lineSubtotal) {
                        throw ValidationException::withMessages([
                            'items' => "Diskon {$product->name} tidak boleh melebihi subtotal produk.",
                        ]);
                    }

                    $subtotalAmount += $lineSubtotal;
                    $discountAmount += $lineDiscount;

                    $itemsToSave[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'price' => $product->selling_price,
                        'purchase_price' => $product->purchase_price,
                        'quantity' => $item['quantity'],
                        'subtotal' => $lineSubtotal,
                        'discount_type' => $discountType,
                        'discount_value' => $discountValue,
                        'discount' => $lineDiscount,
                    ];

                    $product->decrement('stock', $item['quantity']);
                }

                $totalAmount = $subtotalAmount - $discountAmount;

                if ($validated['paid_amount'] < $totalAmount) {
                    throw ValidationException::withMessages([
                        'paid_amount' => 'Uang bayar kurang! Total: Rp '.number_format($totalAmount, 0, ',', '.'),
                    ]);
                }

                $date = $transactionTime->format('Ymd');
                $countToday = Sale::whereDate('created_at', $transactionTime->toDateString())->count() + 1;
                $invoiceNumber = 'INV-'.$date.'-'.str_pad($countToday, 4, '0', STR_PAD_LEFT);

                $sale = new Sale([
                    'invoice_number' => $invoiceNumber,
                    'user_id' => auth()->id(),
                    'customer_id' => $validated['customer_id'] ?? null,
                    'subtotal' => $subtotalAmount,
                    'discount' => $discountAmount,
                    'total' => $totalAmount,
                    'payment_method' => $validated['payment_method'],
                    'paid_amount' => $validated['paid_amount'],
                    'change_amount' => $validated['paid_amount'] - $totalAmount,
                    'status' => 'paid',
                    'note' => $validated['note'] ?? null,
                ]);
                $sale->timestamps = false;
                $sale->forceFill([
                    'sale_date' => $transactionTime,
                    'created_at' => $transactionTime,
                    'updated_at' => $transactionTime,
                ]);
                $sale->save();

                $sale->items()->createMany($itemsToSave);
            });

            return redirect()->route('sales.index')->with('success', 'Transaksi berhasil disimpan!');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function index(Request $request): View|JsonResponse
    {
        $sales = Sale::query()
            ->leftJoin('users as cashiers', 'sales.user_id', '=', 'cashiers.id')
            ->leftJoin('customers', 'sales.customer_id', '=', 'customers.id')
            ->select([
                'sales.id',
                'sales.invoice_number',
                'sales.sale_date',
                'sales.user_id',
                'sales.customer_id',
                'sales.total',
                'sales.status',
                'cashiers.name as cashier_name',
                'customers.name as customer_name',
            ])
            ->orderByDesc('sales.sale_date')
            ->orderByDesc('sales.id');

        if (auth()->user()->role === 'kasir') {
            $sales->where('sales.user_id', auth()->id());
        }

        if ($request->ajax()) {
            return DataTables::eloquent($sales)
                ->addIndexColumn()
                ->editColumn('sale_date', fn (Sale $sale): string => $sale->sale_date->format('d/m/Y H:i'))
                ->editColumn('customer_name', fn (Sale $sale): string => $sale->customer_name ?: 'Umum')
                ->editColumn('total', fn (Sale $sale): string => rupiah($sale->total))
                ->filterColumn('cashier_name', function (Builder $query, string $keyword): void {
                    $query->where('cashiers.name', 'like', "%{$keyword}%");
                })
                ->filterColumn('customer_name', function (Builder $query, string $keyword): void {
                    $query->where('customers.name', 'like', "%{$keyword}%");
                })
                ->orderColumn('cashier_name', function (Builder $query, string $direction): void {
                    $query->orderBy('cashiers.name', $direction === 'desc' ? 'desc' : 'asc');
                })
                ->orderColumn('customer_name', function (Builder $query, string $direction): void {
                    $query->orderBy('customers.name', $direction === 'desc' ? 'desc' : 'asc');
                })
                ->addColumn('status_display', function (Sale $sale): string {
                    return view('sales.datatables.status', ['sale' => $sale])->render();
                })
                ->addColumn('actions', function (Sale $sale): string {
                    return view('sales.datatables.actions', ['sale' => $sale])->render();
                })
                ->rawColumns(['status_display', 'actions'])
                ->toJson();
        }

        return view('sales.index');
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

        $validated = $request->validate([
            'note' => 'required|string|max:255',
        ]);

        if ($sale->status === 'void') {
            return back()->with('error', 'Transaksi sudah di-void sebelumnya.');
        }

        try {
            DB::transaction(function () use ($sale, $validated) {
                foreach ($sale->items as $item) {
                    Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                }
                $sale->update([
                    'status' => 'void',
                    'void_reason' => $validated['note'],
                ]);
            });

            return redirect()->route('sales.index')->with('success', 'Transaksi berhasil di-void dan stok dikembalikan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal void: '.$e->getMessage());
        }
    }
}
