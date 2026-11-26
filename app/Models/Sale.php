<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'customer_id',
        'sale_date',
        'subtotal',
        'discount',
        'total',
        'payment_method',
        'paid_amount',
        'change_amount',
        'status',
        'note',
        'void_reason',
    ];

    protected function casts(): array
    {
        return [
            'sale_date' => 'datetime',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'change_amount' => 'decimal:2',
        ];
    }

    // Relasi ke Kasir/User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Pelanggan
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relasi ke Detail Barang (SaleItem)
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}
