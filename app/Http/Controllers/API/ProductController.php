<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Models\ProductGallery;
use App\Models\ProductVariation;
use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function all(Request $request)
    {
        $id =$request->input('id');
        $limit =$request->input('limit');
        // $user_id =$request->input('user_id');
        $name =$request->name;
        $price =$request->input('price');
        $products_information=$request->input('products_information');
        $categories=$request->input('categories');
        $tags=$request->input('tags');
        $status=$request->input('status');
        $variation=$request->input('variation');
        $rating=$request->input('rating');
        $store=$request->input('store');

        $price_from = $request->input('price_from');
        $price_to=$request->input('price_to');
    
    if($id) //Ambil data berdasarkan ID
    {
        $product =Product::with(['category','galleries','variation','rating','store'])->find($id);
        if($product)
        {
            return ResponseFormatter::success(  
                $product,
                'Data produk berhasil diambil'     
            );        
      
        }
        else {
            return ResponseFormatter::error(
                null,
                'Data Produk Tidak Dapat di Tampilkan',
                404
            );
        }
    }


    $product=Product::with(['category','galleries','variation','rating','store']); //Filltering Data

    if ($name){
        $product->where('products.name','like', '%' .$name. '%');
    }
    if ($price_from){
        $product->where('price', '>=' . $price_from);
    }
    if ($price_to){
        $product->where('price','<='. $price_to);
    }
    if ($products_information){
        $product->where('product_information', 'like', '%'. $products_information. '%');
    }
    if ($categories){
        $product->where('categories_id'.$categories);   
    }
    if ($tags){
        $product->where('tags','like', '%' . $tags . '%');
    }
    if ($status){
        $product->where('status','%', 'like' . $status . '%');
    }
    if($variation){
        $product->where('variation','%', 'like'.$variation. '%');
    }
    if($rating){
        $product->where('star'.$rating);
    }
    if($store){
        $product->where('name'.$store);
    }

    return ResponseFormatter::success(
        $product->paginate($limit),
        'Data Produk Berhasil Diambil'
    );
    }

    public function create(Request $request) //blm bisa
    {
        try{
            $request->validate([

                'user_id' => 'required',
                'name' => 'required|string|max:255',
                'price' => 'required',
                'products_information' => 'required|string',
                'categories_id' =>'required',
                'store_id' => 'required',
                'tags' => 'required|string',
                'url_disc' => 'array',
                'variation_id' => 'required',
                'weight' => 'required',
                'long' => 'required',
                'wide' => 'required',
                'height' => 'required',

            ]);

            $product = Product::create([
                
                'user_id' => $request->user_id,
                'name' => $request->name,
                'price' => $request->price,
                'products_information' => $request->products_information,
                'categories_id' => $request->categories_id,
                'store_id' => $request->store_id,
                'tags' => $request->tags,

                'variation_id' => $request->variation_id,
                'weight' => $request->weight,
                'long' => $request->long,
                'wide' => $request->wide,
                'height' => $request->height,
            ]);

            
            if ($request->hasFile('image1')) {
                $image = new ProductGallery();
                $image->products_id = $product->id;
                $path = $request->file('image1')->store('productGalleries');
                $image->url = $path;
                $image->save();
            }

            if ($request->hasFile('image2')) {
                $image = new ProductGallery();
                $image->products_id = $product->id;
                $path = $request->file('image2')->store('productGalleries');
                $image->url = $path;
                $image->save();
            }

            if ($request->hasFile('image3')) {
                $image = new ProductGallery();
                $image->products_id = $product->id;
                $path = $request->file('image3')->store('productGalleries');
                $image->url = $path;
                $image->save();
            }

            if ($request->hasFile('image4')) {
                $image = new ProductGallery();
                $image->products_id = $product->id;
                $path = $request->file('image4')->store('productGalleries');
                $image->url = $path;
                $image->save();
            }

            if ($request->hasFile('image5')) {
                $image = new ProductGallery();
                $image->products_id = $product->id;
                $path = $request->file('image5')->store('productGalleries');
                $image->url = $path;
                $image->save();
            }

            if ($request->hasFile('image6')) {
                $image = new ProductGallery();
                $image->products_id = $product->id;
                $path = $request->file('image6')->store('productGalleries');
                $image->url = $path;
                $image->save();
            }
    
            if ($request->variations != null) {
                foreach ($request->variations as $item) {
                    ProductVariation::create([
                        'products_id' => $product->id,
                        'name' => $item['name'],
                        'detail' => $item['detail'],
                        'price' => $item['price'],
                    ]);
                }
            }       

            return ResponseFormatter::success(
                
                    // 'product' => $product,
                $product->load('variations', 'galleries')
                    // 'product' => $product->load('variations', 'galleries')
                ,
                'Produk berhasil ditambah'
            );
        } catch (\Throwable $th) {
            return ResponseFormatter::error(
                [
                    "message" => "Something went wrong",
                    "errors" => $th
                ],
                "Produk Gagal ditambah", 404
            );
        };
    }
    // public function update(){

    // }
}