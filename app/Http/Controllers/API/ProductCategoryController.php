<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ProductCategoryController extends Controller
{
    public function all (Request $request)
    {
        $id =$request->input('id');
        $limit =$request->input('limit');
        $name =$request->name;
        $show_product=$request->input('show_product');

        if($id) //Ambil data berdasarkan ID
        {
            $category =ProductCategory::with(['products'])->find($id);
            if($category)
            {
                return ResponseFormatter::success(  
                    $category,
                    'Data Kategori Produk berhasil diambil'     
                );        
          
            }
            else {
                return ResponseFormatter::error(
                    null,
                    'Data Kategori Produk di Tampilkan',
                    404
                );
            }
        }
    
        $category=ProductCategory::query(); //Filltering Data

         if ($name)
         {
         $category->where('name','like', '%' .$name. '%');
         }

         if($show_product){
            $category->with('products');
         }

         
         return ResponseFormatter::success(
            $category->paginate($limit),
            'Data Kategori Produk Berhasil Diambil'
        );
    }

    public function create(Request $request)
    {
        try {
             $validator = Validator::make($request->all(),
             [
                'name' => 'required|string',
            ]);

            if ($validator->fails()){
            return ResponseFormatter::error([
                'message'=>'Validation fails',
                'errors' => $validator->errors()
            ],'Authentication Failed',422);
         }

            $product_category = ProductCategory::create([

                'name' => $request->name,
            ]);

            return ResponseFormatter::success($product_category, 'Kategori produk berhasil ditambahkan!');
        } catch (\Throwable $th) {
            print($th);
            return ResponseFormatter::error(null, "Kategori produk gagal ditambahkan!", 404);
        }
    }

    public function edit(Request $request)
    {
        try {
        $request->all();

        $id = $request->id;

        $product_category = ProductCategory::where('id',$id)->first();

        $product_category->name =  $request->name;
        $product_category->store_id =  $request->store_id;
        
        $product_category->save();
       
        return ResponseFormatter::success($product_category, 'Kategori produk berhasil diubah');
        } catch (\Throwable $th) {
            return ResponseFormatter::error(
                [
                    "message" => "Terjadi sebuah kesalahan.",
                    "errors" => $th->getMessage()
                ],
                "Kategori produk gagal diubah", 500
            );
        }
    }

    public function delete($id){
        try {
        $product_category = ProductCategory::where('id',$id)->first();
       
        if ($product_category !=null){
            $product_category->delete();
        }
       
        return ResponseFormatter::success([
            $product_category,
            'Kategori produk berhasil dihapus.',
            ], 200);
        } catch (\Throwable $th) {
            return ResponseFormatter::error(
                [
                    "message" => "Terjadi sebuah kesalahan.",
                    "errors" => $th->getMessage()
                ],
                "Gagal menghapus kategori produk!", 500
            );}
    }
}
