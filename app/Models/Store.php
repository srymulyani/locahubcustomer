<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// use Illuminate\Support\Facades\Storage;

class Store extends Model
{
    use HasFactory, SoftDeletes;

    
        /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $table ="store";
    protected $fillable = [
        'user_id',
        'couriers_id',
        'day_id',
        'city_id',
        'name',
        'profile',
        'image',
        'url',
        'username',
        'addres',
        'description',
        'store_note',
    ];

    public function courier ()
    {
        return $this->hasMany(Courier::class,'couriers_id','id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function bank(){
        return $this->hasMany(Bank::class,'store_id', 'id');
    }
    public function voucher()
    {
        return $this->hasMany(Voucher::class,'store_id','id');
    }
    public function day()
    {
        return $this->hasMany(Day::class,'day_id','id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function carts()
    {
        return $this->hasManyThrough(Cart::class, Product::class);
    }
    
    // public function getUrlAttribute($url)
    // {
    //     return config('app.url') . Storage::url($url);
    // }

}
