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
        'name',
        'profile',
        'image',
        'url',
        'username',
        'addres',
        'description',
        'store_note',
    ];
    public function store()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
    // public function product()
    // {
    //     return $this->hasMany(Product::class,'store_id','id');
    // }
    public function courier ()
    {
        return $this->belongsTo(Courier::class,'couriers_id','id');
    }
    public function voucher()
    {
        return $this->hasMany(Voucher::class,'store_id','id');
    }
    public function day()
    {
        return $this->hasMany(Day::class,'day_id','id');
    }
   

    
    // public function getUrlAttribute($url)
    // {
    //     return config('app.url') . Storage::url($url);
    // }

}
