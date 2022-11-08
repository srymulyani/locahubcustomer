<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    
        /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $softDelete = true;
    protected $table ="transaction";
    protected $fillable = [
        'user_id',
        'store_id',
        'address_id',
        'pay_method_id',
        'no_resi',
        'jasa_antar',
        'total_shop',
        'disc_total',
        'shipping_total',
        'shippin_disc',
        'price_total',
        'status',
        'note',
        'invoice',
    ];
    public function  user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function payment()
    {
        return $this->belongsTo(PaymentMethod::class,'pay_method_id','id');
    }
    public function details()
    {
        return $this->hasMany(TransactionDetail::class,'transaction_id', 'id');
    } 
    public function store()
    {
        return $this->belongsTo(Store::class,'store_id','id');
    }

}
