<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'description',
        'amount',
        'expense_date',
        'category',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
