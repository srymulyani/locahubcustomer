<?php

namespace App\Http\Controllers\API;

use App\Models\Courier;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

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

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),
            [
               'stores_id' => 'required',
               'jne_kilat' => 'numeric',
               'sicepat_kilat' => 'numeric',
               'jnt_kilat' =>'numeric',
               'jne_reguler'=>'numeric',
               'sicepat_reg'=> 'numeric',
               'jnt_reg' =>'numeric',
               'jne_ekonomis' => 'numeric',
               'sicepat_ekonomis' => 'numeric',
               'jne_kargo' => 'numeric',
               'sicepat_kargo' => 'numeric',
               'jnt_kargo' => 'numeric',
               'jnt_ekonomis' => 'numeric'
           ]);

           if ($validator->fails()){
           return ResponseFormatter::error([
               'message'=>'Validation fails',
               'errors' => $validator->errors()
           ],'Authentication Failed',422);
        }
           $findCourier = Courier::where('stores_id',$request->stores_id)->first();
           if($findCourier){
            $findCourier->jne_kilat = $request->jne_kilat;
            $findCourier->sicepat_kilat = $request->sicepat_kilat;
            $findCourier->jnt_kilat = $request->jnt_kilat;
            $findCourier->jne_reguler = $request->jne_reguler;
            $findCourier->sicepat_reg = $request->sicepat_reg;
            $findCourier->jnt_reg = $request->jnt_reg;
            $findCourier->jne_ekonomis = $request->jne_ekonomis;
            $findCourier->sicepat_ekonomis = $request->sicepat_ekonomis;
            $findCourier->jne_kargo = $request->jne_kargo;
            $findCourier->sicepat_kargo = $request->sicepat_kargo;
            $findCourier->jnt_kargo = $request->jnt_kargo;
            $findCourier->jnt_ekonomis = $request->jnt_ekonomis;

            $findCourier->save();

            return ResponseFormatter::success($findCourier, 'Courier Edited Successfully', 200);
           }else{
            $createCourier = Courier::create([
                'stores_id' => $request->stores_id,
                'jne_kilat' =>$request->jne_kilat,
                'sicepat_kilat' => $request->sicepat_kilat,
                'jnt_kilat' => $request->jnt_kilat,
                'jne_reguler' =>$request->jne_reguler,
                'sicepat_reg'=>$request->sicepat_reg,
                'jnt_reg'=>$request->jnt_reg,
                'jne_ekonomis' =>$request->jne_ekonomis,
                'sicepat_ekonomis' => $request->sicepat_ekonomis,
                'jne_kargo' => $request->jne_kargo,
                'sicepat_kargo' => $request->sicepat_kargo,
                'jnt_kargo' => $request->jnt_kargo,
                'jnt_ekonomis' => $request->jnt_ekonomis           
            ]);

            return ResponseFormatter::success($createCourier, 'Courier Added Successfully', 200);
           }
       } catch (\Throwable $th) {
           print($th);
           return ResponseFormatter::error(null, "Courier Added Failed", 404);
       }
    }
}
