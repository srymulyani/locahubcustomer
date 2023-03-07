<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{
    use HasFactory;

     protected $table="banner";
     protected $fillable = [
        'url'
    ];

     public function getUrlAttribute()
    {
        if (!$this->attributes["url"]) return "";
        return url($this->attributes["url"]);
    }
}
