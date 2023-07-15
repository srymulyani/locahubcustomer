<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Courier extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $softDelete = true;
    protected $table="couriers";
    protected $fillable = [
        'stores_id',
        'jne_kilat',
        'sicepat_kilat',
        'jnt_kilat',
        'jne_reguler',
        'sicepat_reg',
        'jnt_reg',
        'jne_ekonomis',
        'sicepat_ekonomis',
        'jne_kargo',
        'siceppat_kargo',
        'jnt_kargo',
        'province_id',
    ];

}
