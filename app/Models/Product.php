<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'barcode',
        'cost_price',
        'selling_price',
        'wholesale_price',
        'stock_quantity',
        'low_stock_threshold',
        'image_path',
        'description',
        'is_active'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
