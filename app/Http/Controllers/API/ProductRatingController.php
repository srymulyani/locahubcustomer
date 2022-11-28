<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\ProductRating;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class ProductRatingController extends Controller
{
    public function show(Request $request){
        $id = $request->id;
        $products_id = $request->products_id;

        if($id){
        $review = ProductRating::where('id', $id)->first();
        if($review){
             return ResponseFormatter::success(
                $review,
                'Rating Product berhasil Ditampilkan',
             );
        }else{
           
            return ResponseFormatter::error(
                null,
                'Rating Product Gagal Ditampilkan',500
            );
        }
    }

      if($products_id){
        $review = ProductRating::where('products_id', $products_id)->first();
           if($review){
             return ResponseFormatter::success(
                $review,
                'Rating Product berhasil Ditampilkan',
             );
        }else{
           
            return ResponseFormatter::error(
                null,
                'Rating Product Gagal Ditampilkan',500
            );
        }
    }
}
    public function create(Request $request){
        try {
            $validator = Validator::make($request->all(),[
                'user_id'=>'required',
                'products_id' => 'required',
                'content' => 'required|string',
                'rating' => 'required',
            ]);
                if ($validator->fails()){
                     return ResponseFormatter::error([
                    'message'=>'Validation fails',
                    'errors' => $validator->errors()
              ],'Authentication Failed',422);
            }

            $review = ProductRating::create([
                'user_id' => $request->user_id,
                'products_id' => $request->products_id,
                'content' => $request->content,
                'rating' => $request->rating,
            ]);

            return ResponseFormatter::success(
                $review,
                'Terimakasih Telah Memberikan Penilaian'
            );
        } catch (\Throwable $th) {
            return ResponseFormatter::error(
                [
                    "message" => "Something went wrong",
                    "errors" => $th->getMessage()
                ],
                "Gagal Memberikan Penilaian", 500
            );
        }

    }
}