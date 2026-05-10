<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'shift_id',
        'customer_id',
        'receipt_number',
        'subtotal',
        'discount',
        'total',
        'cash_received',
        'change_amount',
        'payment_method',
        'status',
        'notes'
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
