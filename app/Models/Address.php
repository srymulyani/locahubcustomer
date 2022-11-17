<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory, SoftDeletes;
     /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $softDelete = true;
    protected $table="address";
    protected $fillable = [
        'user_id',
        'address_label',
        'name',
        'phone_number',
        'address',
        'complete_address',
        'address_detail',
        'choice',
        'postcode',
        'district',
        'city_id',
        'province_id',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id','id');
    }
    public function city(){
        return $this->hasOne(City::class,'city_id','id');
    }
    public function province(){
        return $this->hasOne(Province::class,'province_id','id');
    }
}
