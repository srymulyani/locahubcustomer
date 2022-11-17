<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class StoreController extends Controller
{
    public function all(Request $request){
        try {
        $id =$request->input('id');
        $limit =$request->input('limit');
        $couriers_id =$request->input('couriers_id');
        $day_id =$request->input('day_id');
        $name =$request->input('name');
        
            
        } catch (\Throwable $th) {
            //throw $th;
        }

    }

    public function create(Request $request){

    }
}
