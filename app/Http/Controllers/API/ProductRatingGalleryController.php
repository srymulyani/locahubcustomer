<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProductRatingGallery;

class ProductRatingGalleryController extends Controller
{
    public function upload(Request $request){
         if ($request->hasFile('image1')){
            $image = new ProductRatingGallery();
            $image->products_id = $request ->products_id;
            $path = $request->file('image1');
            $image->url = $path;
            $image->save();
        }
        if ($request->hasFile('image2')){
            $image = new ProductRatingGallery();
            $image->products_id = $request ->products_id;
            $path = $request->file('image2');
            $image->url = $path;
            $image->save();
        }
        if ($request->hasFile('image3')){
            $image = new ProductRatingGallery();
            $image->products_id = $request ->products_id;
        $path = $request->file('image3');
            $image->url = $path;
            $image->save();
        }
        if ($request->hasFile('image4')){
            $image = new ProductRatingGallery();
            $image->products_id = $request ->products_id;
            $path = $request->file('image4');
            $image->url = $path;
            $image->save();
        }
         if ($request->hasFile('image5')){
            $image = new ProductRatingGallery();
            $image->products_id = $request ->products_id;
            $path = $request->file('image5');
            $image->url = $path;
            $image->save();
        }
         if ($request->hasFile('image6')){
            $image = new ProductRatingGallery();
            $image->products_id = $request ->products_id;
            $path = $request->file('image6');
            $image->url = $path;
            $image->save();
        }
        return["result"=> $image];
    }
}
