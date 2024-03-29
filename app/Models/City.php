<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'province_id',
        'type',
        'name',
        'postal_code',
    ];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function store(){
        return $this->hasOne(Store::class,'city_id', 'id');
    }
}
