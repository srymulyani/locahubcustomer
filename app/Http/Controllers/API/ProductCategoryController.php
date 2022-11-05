<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Helpers\ResponseFormatter;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function all (Request $request)
    {
        $id =$request->input('id');
        $store_id=$request->store_id;
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

         if ($store_id){
            $category->where('store_id', $store_id);
         }

         
         return ResponseFormatter::success(
            $category->paginate($limit),
            'Data Kategori Produk Berhasil Diambil'
        );

    }
}
