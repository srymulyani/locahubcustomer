<?php

namespace App\Http\Controllers\API;

use App\Models\Courier;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;

class CourierController extends Controller
{
    public function all(Request $request)
    {

        $id = $request->input('id');
           
        if($id){
            $courier = Courier::where('stores_id',$id)->first();
            if($courier){
                return ResponseFormatter::success(
                    $courier,
                    'Data Courier berhasil diambil',
                );
            }else{
                return ResponseFormatter::error(
                    null,
                    'Data Courier tidak ada',
                    200,
                );
            }
        } 
    }
}
