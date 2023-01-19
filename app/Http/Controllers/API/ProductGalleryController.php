<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProductGallery;


class ProductGalleryController extends Controller
{
    public function upload(Request $request)
    {
        if ($request->hasFile('image1')){
            $image = new ProductGallery();
            $image->products_id = $request ->id;
            $path = $request->file('image1')->store('productGalleries');
            $image->url = $path;
            $image->save();
        }

           if ($request->hasFile('image2')){
            $image = new ProductGallery();
            $image->products_id = $request ->id;
            $path = $request->file('image2')->store('productGalleries');
            $image->url = $path;
            $image->save();
        }

           if ($request->hasFile('image3')){
            $image = new ProductGallery();
            $image->products_id = $request ->id;
            $path = $request->file('image3')->store('productGalleries');
            $image->url = $path;
            $image->save();
        }

           if ($request->hasFile('image4')){
            $image = new ProductGallery();
            $image->products_id = $request ->id;
            $path = $request->file('image4')->store('productGalleries');
            $image->url = $path;
            $image->save();
        }

           if ($request->hasFile('image5')){
            $image = new ProductGallery();
            $image->products_id = $request ->id;
            $path = $request->file('image5')->store('productGalleries');
            $image->url = $path;
            $image->save();
        }

           if ($request->hasFile('image6')){
            $image = new ProductGallery();
            $image->products_id = $request ->id;
            $path = $request->file('image6')->store('productGalleries');
            $image->url = $path;
            $image->save();
        }

           if ($request->hasFile('image7')){
            $image = new ProductGallery();
            $image->products_id = $request ->id;
            $path = $request->file('image7')->store('productGalleries');
            $image->url = $path;
            $image->save();
        }

        return["result"=> $image];
    }
}
