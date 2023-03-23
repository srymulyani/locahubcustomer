<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    use HasFactory;

        /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $softDelete = true;
    protected $table ="products_category";
    protected $fillable = [
        'name',
    ];
    public function products ()
    {
        return $this->hasMany(Product::class,'categories_id','id');
    }

    // public function store()
    // {
    //     return $this->hasOne(Store::class,'store_id','id');
    // }
}
