<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreTransactionShipment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'store_transaction_id',
        'track_number',
        'origin',
        'destination',
        'weight',
    ];

    public function store_transaction()
    {
        return $this->belongsTo(StoreTransaction::class);
    }
}
