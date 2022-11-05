<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
class ProductRating extends Model
{
    use HasFactory, SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $table ="products_rating";
    protected $fillable = [
        'user_id',
        'name',
        'star',
        'photo_testi',
        'comentar',
    ];
    public function getUrlAttribute($photo_testi)
    {
        return config('photo_testi.url') . Storage::url($photo_testi);
    }
   
}
