<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'price',
        'products_information',
        'categories_id',
        'weight',
        'long',
        'wide',
        'height',
        'store_id',
        'tags',
        'status',
        'stock',
    ];
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'categories_id', 'id');
    }
    public function galleries()
    {
        return $this->hasMany(ProductGallery::class, 'products_id', 'id');
    }
    public function variation()
    {
        return $this->hasMany(ProductVariation::class, 'products_id', 'id');
    }
    public function rating()
    {
        return $this->hasMany(ProductRating::class, 'products_id', 'id');
    }
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function favorites()
    {
        return $this->hasMany(ProductFavorite::class, 'products_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(StoreTrasanctionItem::class);
    }
}
