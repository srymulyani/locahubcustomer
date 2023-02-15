<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\ProductGallery;


class ProductGalleryController extends Controller
{
    public function upload(Request $request)
    {
             if ($request->hasFile('image1')) {
                $image = new ProductGallery();
                $image->products_id = $request->id;
                $path = $request->file('image1')->store('productGalleries');
                $image->url = $path;
                $image->save();
            }

            if ($request->hasFile('image2')) {
                $image = new ProductGallery();
                $image->products_id = $request->id;
                $path = $request->file('image2')->store('productGalleries');
                $image->url = $path;
                $image->save();
            }
             return ["result" => $image];
    }
    
    }

    

