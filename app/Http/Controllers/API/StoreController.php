<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Courier;
use App\Models\Day;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Validator;
use Darbaoui\Avatar\Facades\Avatar;
use Illuminate\Http\Request;

class StoreController extends Controller
{
        public function show(Request $request){
        $id = $request->id;
        $limit =$request->input('limit');
        $city_id = $request->city_id;
        $name = $request->name;

        
        $store = Store::where('id', $id)->find($id);
        if($store){
            return ResponseFormatter::success(
                $store,
                'Store Berhasil',
            );
        }else{
            return ResponseFormatter::error(
                null,
                'Store Gagal tidak dapat ditampilkan',404
            );
        }


        $store=Store::with(['city','products'])->first(); 

        if($name){
             $store->where('store.name','like', '%' .$name. '%');
        }
        if($city_id){
            $store->where('city_id','=', $city_id);
        }
        return ResponseFormatter::success(
        $store->paginate($limit),
        'Data Produk Berhasil Diambil'
    );

}

    public function create(Request $request){
        try {
               $validator= Validator::make($request->all(),[
                'user_id'=> 'required',
                'city_id' => 'required',
                'name' => 'required|string',
                'username' => 'required|string',
                'addres' =>'required|string',
                'description' => 'required|string',
                'store_note' => 'required|string',
               ]);

                if ($validator->fails()){
                     return ResponseFormatter::error([
                    'message'=>'Validation fails',
                    'errors' => $validator->errors()
              ],'Authentication Failed',422);
            }
            $profile ="StoreImage/image_profile_toko.png";

            $store=Store::create([
                'user_id'=>$request->user_id,
                'city_id' => $request -> city_id,
                'name' =>$request->name,
                'username' =>$request->username,
                'addres' => $request->addres,
                'description' =>$request->description,
                'store_note' =>$request->store_note,
                'profile' =>$profile,
            ]);


            $courier = Courier::create([
                'jne_kilat' => false,
                'sicepat_kilat' => false,
                'jnt_kilat' => false,
                'jne_reguler' =>false,
                'sicepat_reguler' => false,
                'jnt_reguler' => false,
                'jne_ekonomis' => false,
                'sicepat_ekonomis' => false,
                'jnt_ekonomis' => false,
                'jne_kargo' => false,
                'sicepat_kargo' => false,
                'jnt_kargo' => false,
            ]);

            $day = Day::create([
                'sunday' => false,
                'monday' => false,
                'tuesday' => false,
                'wednesday' => false,
                'thursday' => false,
                'friday' => false,
                'saturday' => false, 
            ]);

            $store->couriers_id = $courier->id;
            $store->day_id = $day->id;
            $store->save();

           return ResponseFormatter::success(
            $store,
            'Tokoh Berhasil dibuat, Selamat Berjualan'
           );
        } catch (\Throwable $th) {
             return ResponseFormatter::error(
                [
                    "message" => "Something went wrong",
                    "errors" => $th->getMessage()
                ],
                "Tokoh Gagal dibuat", 500
            );
        }

    }

    public function update(Request $request){
        try {
            $request->all();
            $id = $request->id;

            $store=Store::where('id',$id)->first();

            $store->user_id = $request->user_id;
          

            if($request->username !=null){
                $request->validate([
                    'username' => 'unique:stores'
                ]);
                $username = $request->username;
            }else{
                $username = $store['username'];
            }

            if($request->hasFile('profile')){
                $request->validate([
                    'profile' => 'mimes:jpeg,jpg,png,gif',
                ]);

                $path = $request->file('profile')->store('StoreImage');
            }else{
                $path = $store['profile'];
            }

            if($request->name !=null){
                $name = $request->name;
            }else{
                $name = $store['name'];
            }

            if($request->addres != null){
                $addres = $request->addres;
            }else{
                $addres = $store['addres'];
            }

            if($request->store_note !=null){
                $store_note = $request->store_note;
            }else{
                $store_note = $store['store_note'];
            }

            $store->name = $name;
            $store->profile = $path;
            $store->username = $username;
            $store->addres = $addres;
            $store->store_note = $store_note;

            $store->save();
            return ResponseFormatter::success(
                $store,
                'Tokoh Berhasil diubah',
            );


        } catch (\Throwable $th) {
              return ResponseFormatter::error(
                [
                    "message" => "Something went wrong",
                    "errors" => $th->getMessage()
                ],
                "Tokoh Gagal  diubah", 500
            );
        }
    }
}
