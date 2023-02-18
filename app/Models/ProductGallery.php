<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ProductGallery extends Model
{
    use HasFactory,SoftDeletes;
     /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $table ="products_gallery";
    protected $fillable = [
        'products_id',
        'url',
    ];

    public function getUrlAttribute()
    {
        if (!$this->url) return "";
        return url($this->url);
    }

    public function products(){
        return $this->belongsTo(Products::class);
    }
    
}
