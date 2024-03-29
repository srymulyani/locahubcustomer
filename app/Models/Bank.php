<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
     use HasFactory, SoftDeletes;

           /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'users_id',
        'name',
        'rekening',
        'bank_name',
        'choice',
    ];
    public function user()
    {
        return $this->belongsTo(User::class,'id','users_id');
    }
}
