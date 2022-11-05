<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariation extends Model
{
    use HasFactory, SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $table ="products_variation";
    protected $fillable = [
        'products_id',
        'name',
        'detail',
        'products_price',
    
    ];
    public function products ()
    {
        return $this->hasOne(Product::class,'products_price','price');
    }
}
