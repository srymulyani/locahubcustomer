<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Models\ProductVariation;
use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    // FETCHING DATA
    public function all(Request $request)
    {

        $id = $request->input('id');
        $limit = $request->input('limit');
        $user_id = $request->input('user_id');
        $store_id = $request->input('store_id');
        $name = $request->name;
        $price = $request->input('price');
        $products_information = $request->input('products_information');
        $categories = $request->input('categories');
        $tags = $request->input('tags');
        $status = $request->input('status');
        $variation = $request->input('variation');
        $rating = $request->input('rating');
        $store = $request->input('store');
        $price_from = $request->input('price_from');
        $price_to = $request->input('price_to');

        if ($id) //Ambil data berdasarkan ID
        {
            $product = Product::with(['category', 'galleries', 'variation', 'rating', 'store'])->find($id);
            if ($product) {
                return ResponseFormatter::success(
                    $product,
                    'Data produk berhasil diambil'
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'Data Produk Tidak Dapat di Tampilkan',
                    404
                );
            }
        }

        $product = Product::with(['category', 'galleries', 'variation', 'rating', 'store.city:id,name'])
            // jika mengirimkan parameter best_seller HARUS bernilai 'asc' or 'desc'
            ->when($request->best_seller, function ($query) use ($request) {
                $query->orderBy('product_sold', $request->best_seller);
            });
        if ($store_id){
            $product = $product->where('store_id', $store_id);
        }
        
        if ($name) {
            $product->where('products.name', 'like', '%' . $name . '%');
        }
        if ($price_from) {
            $product->where('price', '>=', $price_from);
        }
        if ($price_to) {
            $product->where('price', '<=', $price_to);
        }
        if ($products_information) {
            $product->where('product_information', 'like', '%' . $products_information . '%');
        }
        if ($tags) {
            $product->where('tags', 'like', '%' . $tags . '%');
        }
        if ($status) {
            $product->where('status', '%', 'like' . $status . '%');
        }
        if ($variation) {
            $product->where('variation', '%', 'like' . $variation . '%');
        }
        if ($rating) {
            $product->where('star' . $rating);
        }
        if ($store) {
            $product->where('name' . $store);
        }


        return ResponseFormatter::success(
            $product->paginate($limit),
            'Data Produk Berhasil Diambil'
        );
    }

    //CREATE PRODUCT
    public function create(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'name' => 'required|string|max:255',
                'price' => 'required|string',
                'products_information' => 'required|string',
                'categories_id' => 'required',
                'store_id' => 'required',
                'tags' => 'required|string',
                'galleries' => 'array',
                'variation' => 'array',
                'weight' => 'required',
                'long' => 'required',
                'wide' => 'required',
                'height' => 'required',
                'status' => 'required',
                'stock' => 'required',
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error([
                    'message' => 'Validation fails',
                    'errors' => $validator->errors()
                ], 'Authentication Failed', 422);
            }

            $product = Product::create([

                'user_id' => $request->user_id,
                'name' => $request->name,
                'price' => $request->price,
                'products_information' => $request->products_information,
                'categories_id' => $request->categories_id,
                'store_id' => $request->store_id,
                'tags' => $request->tags,
                'weight' => $request->weight,
                'long' => $request->long,
                'wide' => $request->wide,
                'height' => $request->height,
                'status' => $request->status,
                'stock' => $request->stock,
            ]);

            foreach ($request->variation as $item) {
                ProductVariation::create([
                    'products_id' => $product->id,
                    'name' => $item['name'],
                    'detail' => $item['detail'],
                    'products_price' => $product->price,
                ]);
            }

            return ResponseFormatter::success(

                $product->load('variation', 'galleries'),
                'Produk berhasil ditambah'
            );
        } catch (\Throwable $th) {
            return ResponseFormatter::error(
                [
                    "message" => "Something went wrong",
                    "errors" => $th->getMessage()
                ],
                "Produk Gagal ditambah",
                500
            );
        };
    }

    // UPDATE PRODUCT
    public function updateAll(Request $request)
    {
        try {
            $request->all();
            $id = $request->id;
            $product = Product::where('id', $id)->first();

            $product->name = $request->name;
            $product->price = $request->price;
            $product->products_information = $request->products_information;
            $product->categories_id = $request->categories_id;
            $product->store_id = $request->store_id;
            $product->tags = $request->tags;
            $product->wide = $request->wide;
            $product->long = $request->long;
            $product->weight = $request->weight;
            $product->status = $request->status;
            $product->stock = $product->stock + $request->status ?? 0;

            $product->save();

            ProductVariation::where('products_id', $request->id)
                ->update([
                    "products_id" => $request->id,
                    "name" => $request->name,
                    "detail" => $request->products_information,
                    "products_price" => $request->price,
                ]);

            return ResponseFormatter::success(
                // $product->load('variation', 'galleries'),
                'Produk berhasil diubah'
            );
        } catch (\Throwable $th) {
            return ResponseFormatter::error(
                [
                    "message" => "Something went wrong",
                    "errors" => $th->getMessage()
                ],
                "Produk Gagal diubah",
                500
            );
        };
    }

    // DELETE PRODUCT
    public function delete($id)
    {
        try {
            $product = Product::where('id', $id)->first();

            if ($product != null) {
                $product->delete();
            }

            return ResponseFormatter::success([
                $product,
                'Berhasil Menghapus Produk',
            ], 200);
        } catch (\Throwable $th) {
            return ResponseFormatter::error(
                [
                    "message" => "Something went wrong",
                    "errors" => $th->getMessage()
                ],
                "Produk Gagal dihapus",
                500
            );
        }
    }
}
