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
    protected $table = "products_category";
    protected $fillable = [
        'name',
        'store_id',
    ];
    public function products()
    {
        return $this->hasMany(Product::class, 'categories_id', 'id');
    }

}
