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
                'postcode' => 'required|numeric',
                'district' => 'required|string',
                'city_id' => 'required',
                'province_id' => 'required',

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
                'postcode' => $request->postcode,
                'district' => $request->district,
                'city_id' => $request->city_id,
                'province_id' => $request->province_id,
                
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
        $request->user_id; //$request->user_id;
        $address = Address::find($request->id);

        $address->address_label =  $request->address_label;
        $address->name =  $request->name;
        $address->phone_number =  $request->phone_number;
        $address->address =  $request->address;
        $address->complete_address =  $request->complete_address;
        $address->address_detail =  $request->address_detail;
        $address->postcode =  $request->postcode;
        $address->district =  $request->district;
        $address->city_id = $request->city_id;
        $address->province_id =  $request->province_id;
        
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
        try {
        $address = Address::where('id',$id)->first();
         if ($address !=null){
            $address->delete();
        }
            return ResponseFormatter::success([
            $address,
            'Berhasil Menghapus Alamat',
            ], 200);
       
        } catch (\Throwable $th) {
            return ResponseFormatter::error(
                [
                    "message" => "Something went wrong",
                    "errors" => $th->getMessage()
                ],
                "Alamat Tidak dapat dihapus", 500
            );
        }
      
        
    }
}
