<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use HasFactory,SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'store_id',
        'name',
        'code',
        'type',
        'start_date',
        'end_date',
        'minimum',
        'quota',
        'description',
        'choice',
        'status'
    ];

    public function store(){
         return $this->belongsTo(Store::class,'store_id', 'id');
    }

}
