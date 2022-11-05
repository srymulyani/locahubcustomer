<?php

namespace App\Http\Controllers\API;

use App\Models\Address;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
class AddressController extends Controller
{
    public function create(Request $request)
    {
        try {
             $validator = Validator::make($request->all(),
             [
                'address_label' => 'required|string',
                'user_id' => 'required',
                'name' => 'required|string',
                'phone_number' =>'required|numeric',
                'address'=>'required|string',
                'complete_address'=> 'required|string',
                'address_detail' =>'required|string',
                'choice' => 'numeric',
                'kode' => 'required|numeric',
                'kecamatan' => 'required|string',
                'kota' => 'required|string',
                'provinsi' => 'required|string',

            ]);

            if ($validator->fails()){
            return ResponseFormatter::error([
                'message'=>'Validation fails',
                'errors' => $validator->errors()
            ],'Authentication Failed',422);
         }


            $addressAkun = Address::where('user_id', $request->user_id)->get();
            if($request->choice == 1){
                if($addressAkun){
                    Address::where('user_id', $request->user_id)
                    ->update(['choice' => 0]);
                }
            }
            $address = Address::create([

                'user_id' => $request->user_id,
                'choice' =>$request->choice,
                'address_label' => $request->address_label,
                'name' => $request->name,
                'phone_number' =>$request->phone_number,
                'address'=>$request->address,
                'complete_address'=>$request->complete_address,
                'address_detail' =>$request->address_detail,
                'kode' => $request->kode,
                'kecamatan' => $request->kecamatan,
                'kota' => $request->kota,
                'provinsi' => $request->provinsi,
                
            ]);

            return ResponseFormatter::success($address, 'Address Added Successfully');
        } catch (\Throwable $th) {
            print($th);
            return ResponseFormatter::error(null, "Address Added Failed", 404);
        }
    }

    public function all(Request $request)
    {

        $id = $request->input('id');
        $user_id = $request->user_id;
        $limit = $request->input('limit');
        $name = $request->input('name');
        // $start_date = $request->start_date; 
        // $end_date = $request->end_date; 
        
       
        if($id){
            $address =Address::find($id);
            if($address){
                return ResponseFormatter::success(
                    $address,
                    'Data Alamat berhasil diambil',
                );
            }else{
                return ResponseFormatter::error(
                    null,
                    'Data Alamat tidak ada',
                    404,
                );
            }
        }

            $address = Address::query();
            if($name){
                 $address->where('name', 'like', '$' . $name . '$')->where('user_id', $user_id);
            }
             if ($user_id) {
              $address->where('user_id', $user_id);
            }
             return ResponseFormatter::success(
            [
                'address' => $address->paginate($limit)
            ],
            'Data Alamat Berhasil Diambil' );    
    }
    public function edit(Request $request)
    {
         $request->all();

        $request->user_id = $request->user_id;

        $address = Address::find($request->id);
        // $address = update($data);

        $address->address_label =  $request->address_label;
        $address->name =  $request->name;
        $address->phone_number =  $request->phone_number;
        $address->address =  $request->address;
        $address->complete_address =  $request->complete_address;
        $address->address_detail =  $request->address_detail;
        $address->kode =  $request->kode;
        $address->kecamatan =  $request->kecamatan;
        $address->kota = $request->kota;
        $address->provinsi =  $request->provinsi;
        
        $address->save();

       $addressAkun = Address::where('user_id', $request->user_id)->get();
       if ($request->choice == 1) {
        if ($addressAkun){
            Address::where('user_id', $request->user_id)
            ->update(['choice' => 0]);
        }

        $address->choice = $request->choice;
       }else {
        $address->choice = $request->choice;
       }
       
         return ResponseFormatter::success($address, 'Alamat Berhasil Diubah');

    }
     public function destroy($id) //masih belum bisa
    {
        // $address = Address::find($id)->delete();
        
        // Address::where('id')->delete();
        $address = Address::find($id);
       
        $address->delete();
        // print_r($id);
        die();
        if($address){
            return ResponseFormatter::success([
                $address,
                'Berhasil Menghapus Alamat',
            ], 200);
        } else{
            return response()->json([
                'success' => false,
                'message' => 'Alamat tidak dapat dihapus',
            ], 400);
        }
        
    }
}
