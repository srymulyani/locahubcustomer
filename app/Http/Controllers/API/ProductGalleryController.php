<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductGallery;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;


class ProductGalleryController extends Controller
{
    public function upload(Request $request)
    {
        
    $productId= $request->id;
    $images = [];

    $validatedData = $request->validate([
        'image1' => 'required|mimes:jpg,jpeg,png,svg|max:2048',
        'image2' => 'nullable|mimes:jpg,jpeg,png,svg|max:2048',
    ], [
        'image1.required' => 'The image1 field is required.',
    ]);

    $product = Product::find($productId);

    if (!$product) {
        return response()->json(['error' => 'Product not found'], 404);
    }

    if ($request->hasFile('image1')) {
        $image = new ProductGallery();
        $image->products_id = $productId;
        $path = $request->file('image1')->storeAs('ProductGalleries', $request->file('image1')->getClientOriginalName(),'public');
        $image->url = 'storage/' . $path;
        $image->save();
        $images[] = $image;
    }

    if ($request->hasFile('image2')) {
        $image = new ProductGallery();
        $image->products_id = $productId;
        $path = $request->file('image2')->storeAs('ProductGalleries', $request->file('image2')->getClientOriginalName(),'public');
        $image->url = 'storage/' . $path;
        $image->save();
        $images[] = $image;
    }

    // return response()->json(['result' => $images]);
    return ResponseFormatter::success(response()->json(['result' => $images]), 'Image Added Successfully', 200);
    }
}


    

