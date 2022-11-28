<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ResponseFormatter;

class VoucherController extends Controller
{
    public function create(Request $request){
        try {
            $validator= Validator::make($request->all(),[
                'store_id'=> 'required',
                'name' => 'required|string',
                'code' =>'required',
                'type' => 'required',
                'start_date' => 'required',
                'end_date'=> 'required',
                'minimum' => 'required',
                'quota' =>'required',
                'choice'=>'required',
                'description' => 'required|string',
                'status' => 'required|in:FREE_SHIPPING,CASHBACK',
               ]);

                if ($validator->fails()){
                     return ResponseFormatter::error([
                    'message'=>'Validation fails',
                    'errors' => $validator->errors()
              ],'Authentication Failed',422);
            }

            $voucher = Voucher::create([
                'store_id' => $request->store_id,
                'name' => $request->name,
                'code' => $request->code,
                'type' => $request->type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'minimum' =>$request->minimum,
                'quota' => $request->quota,
                'description' =>$request->description,
                'choice' => $request->choice,
                'status' => $request->status,
   
            ]);

            return ResponseFormatter::success(
                $voucher,
                "Voucher Berhasil Ditambahkan"
            );
        } catch (\Throwable $th) {
            return ResponseFormatter::error([
                null,
                'errors' =>$th
            ], "Voucher Gagal Ditambahkan", 500);
        }
    }

    public function all(Request $request)
    {
        $id = $request->id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $store_id = $request->store_id;


        if($id){
        $vouchers = Voucher::where('id', $id)->find($id);
        if($vouchers)
        {
            return ResponseFormatter::success(  
                $vouchers,
                'Voucher tersedia'     
            );        
      
        }
        else {
            return ResponseFormatter::error(
                null,
                'Voucher tidak tersedia',
                404
            );
        }
    }   

        if($store_id){
            $vouchers = Voucher::where('store_id', $store_id)->get();
            if($vouchers){
                return ResponseFormatter::success(
                    $vouchers,
                    'Voucher berhasil diambil ',
                );
            }else{
                  return ResponseFormatter::error(
                    null,
                    'Voucher tidak berhasil diambil ',
                );
            }
        }

        if($start_date){ 
            $vouchers = Voucher::where('start_date', $start_date)->orderBy('id', 'DESC')->get();
            if($vouchers){
                return ResponseFormatter::success(
                    $vouchers,
                    'Voucher berhasil diambil ',
                );
            }else{
                  return ResponseFormatter::error(
                    null,
                    'Voucher tidak berhasil diambil ',
                );
            }
        }
        if($end_date){
            $vouchers = Voucher::where('end_date', $end_date)->orderBy('id', 'DESC')->get(); 
            if($vouchers){
             return ResponseFormatter::success(
                    $vouchers,
                    'Voucher berhasil diambil',
                );
            }else{
                   return ResponseFormatter::error(
                    null,
                    'Voucher tidak berhasil diambil ',
                );
            }
        }
    }
}
