<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'phone', 'email', 'address', 'total_debt'];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
