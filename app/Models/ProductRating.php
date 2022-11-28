<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
class ProductRating extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $softDelete = true;
    protected $table ="products_rating";
    protected $fillable = [
        'id',
        'user_id',
        'products_id',
        'content',
        'rating',
        'url_image',
    ];

    public function getStatusAttribute(){
        return $this->attributes['status'] == 0 ? 'Inactive' : 'Active';
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }
   
}
