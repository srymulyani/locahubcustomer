<?php

namespace App\Http\Controllers\API;

use App\Models\Day;
use App\Models\Store;
use App\Models\Courier;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Darbaoui\Avatar\Facades\Avatar;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class StoreController extends Controller
{
    // FETCHING STORE DATA
        public function show(Request $request){
        
        $id = $request->id;
        // $city_name = $request->city_name;
        // $name = $request->name;
        // $limit =$request->input('limit');

        if($id){
        $store=Store::where('user_id',$id)->first();
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
        }
     
        // $stores = Store::with('city', 'products') //FILTERING BY CITY NAME
        //     ->when($city_name, function ($query, $city_name) {
        //         return $query->whereHas('city', function ($query) use ($city_name) {
        //             $query->where('name', 'like', '%' . $city_name . '%');
        //         });
        //     })
        //     ->when($name, function ($query, $name) {
        //         return $query->where('name', 'like', '%' . $name . '%');
        //     })
        //     ->get();

        // $response = [];

        // foreach ($stores as $store) {
        //     $city = $store->city->name;
        //     $name = $store->name;
        //     $products = $store->products;

        //     $response[] = [
        //         'city' => $city,
        //         'name' => $name,
        //         'products' => $products
        //     ];
        // }

        // return response()->json($response);

    }

    // CREATE STORE
    public function create(Request $request){
        try {
               $validator= Validator::make($request->all(),[
                'user_id'=> 'required',
                'name' => 'required|string',
                'username' => 'required|string',
                'address' =>'required|string'
               ]);

                if ($validator->fails()){
                     return ResponseFormatter::error([
                    'message'=>'Validation fails',
                    'errors' => $validator->errors()
              ],'Authentication Failed',422);
            }
       

            $store=Store::create([
                // 'user_id'=>$request->user_id, //HARUS DIGANTI JADI Auth::user()->id
                'user_id'=>Auth::user()->id,
                'name' =>$request->name,
                'username' =>$request->username,
                'address' => $request->address,
                'profile' => 'default.jpg'
            ]);

            // if ($request->hasFile('profile')) {
            // $path = $request->file('profile')->storeAs('ProfileStore', $request->file('profile')->getClientOriginalName(),'public');
            // $store->profile = 'storage/' . $path;
            // $store->save();
            // }


            // $courier = Courier::create([
            //     'jne_kilat' => false,
            //     'sicepat_kilat' => false,
            //     'jnt_kilat' => false,
            //     'jne_reguler' =>false,
            //     'sicepat_reguler' => false,
            //     'jnt_reguler' => false,
            //     'jne_ekonomis' => false,
            //     'sicepat_ekonomis' => false,
            //     'jnt_ekonomis' => false,
            //     'jne_kargo' => false,
            //     'sicepat_kargo' => false,
            //     'jnt_kargo' => false,
            // ]);

            // $day = Day::create([
            //     'sunday' => false,
            //     'monday' => false,
            //     'tuesday' => false,
            //     'wednesday' => false,
            //     'thursday' => false,
            //     'friday' => false,
            //     'saturday' => false, 
            // ]);

            // $store->couriers_id = $courier->id;
            // $store->day_id = $day->id;
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
                "Toko Gagal dibuat", 500
            );
        }

    }

    // UPDATE STORE
    public function update(Request $request){
        try {
            $request->all();
            $id = $request->id;

            $store=Store::where('id',$id)->first();

            // $store->user_id = $request->user_id;
          

            if($request->username !=null){
                $request->validate([
                    'username' => 'unique:store'
                ]);
                $username = $request->username;
            }else{
                $username = $store['username'];
            }

            if($request->hasFile('profile')){
                $request->validate([
                    'profile' => 'mimes:jpeg,jpg,png,gif',
                ]);

                $image = Image::make($request->file('profile'))->fit(300);

                if (Storage::disk('public')->exists($store['profile'])) {
                    Storage::disk('public')->delete($store['profile']);
                }

                $path = Str::random(28).".jpg";
                $image->save(storage_path('app/public/'.$path));
            }else{
                $path = $store['profile'];
            }

            if($request->name !=null){
                $name = $request->name;
            }else{
                $name = $store['name'];
            }

            if($request->address != null){
                $address = $request->address;
            }else{
                $address = $store['address'];
            }

            if($request->description !=null){
                $description = $request->description;
            }else{
                $description = $store['description'];
            }

            if($request->store_note !=null){
                $store_note = $request->store_note;
            }else{
                $store_note = $store['store_note'];
            }

            $store->name = $name;
            $store->profile = $path;
            $store->username = $username;
            $store->address = $address;
            $store->description = $description;
            $store->store_note = $store_note;

            $store->save();
            return ResponseFormatter::success(
                $store,
                'Toko Berhasil diubah',
            );


        } catch (\Throwable $th) {
              return ResponseFormatter::error(
                [
                    "message" => "Something went wrong",
                    "errors" => $th->getMessage()
                ],
                "Toko Gagal  diubah", 500
            );
        }
    }
}
