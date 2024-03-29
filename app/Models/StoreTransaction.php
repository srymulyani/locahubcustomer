<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'transaction_id',
        'status',
        'total',
        'shipping_cost',
        'courier',
        'courier_name',
        'shipping_service',
        'shipping_etd',
        'receipt',
        'cancellation_note',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function items()
    {
        return $this->hasMany(StoreTransactionItem::class);
    }

    public function shipment()
    {
        return $this->hasOne(StoreTransactionShipment::class);
    }
}
