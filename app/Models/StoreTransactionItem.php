<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreTransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_transaction_id',
        'product_id',
        'name',
        'product',
        'variation',
        'price',
        'quantity',
        'total',
    ];

    public function store_transaction()
    {
        return $this->belongsTo(StoreTransaction::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
