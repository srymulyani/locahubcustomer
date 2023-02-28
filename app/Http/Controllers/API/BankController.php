<?php

namespace App\Http\Controllers\API;

use App\Models\Bank;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BankController extends Controller
{
    public function create(Request $request){
        try {

            $validator = Validator::make($request->all(),
             [

                'users_id' => 'required',
                'name' => 'required|string',
                'rekening' =>'required|numeric',
                'bank_name' => 'required|string',
                'choice' =>'numeric',
            ]);

            if ($validator->fails()){
            return ResponseFormatter::error([
                'message'=>'Validation fails',
                'errors' => $validator->errors()
            ],'Authentication Failed',422);
         }

         $bankUser = Bank::where('users_id', $request->users_id)->get();
         if($request->choice == 1){
            if($bankUser){
                Bank::where('users_id', $request->users_id)
                ->update(['choice' => 0]);
            }
         }

         $bank = Bank::create([
            'users_id' => $request->users_id,
            'name' => $request->name,
            'rekening' => $request->rekening,
            'bank_name' => $request->bank_name,
            'choice' => $request->choice,
         ]);

         return ResponseFormatter::success(
            $bank,
            'Bank Account Berhasil ditambah',
         );
        } catch (\Throwable $th) {
            return ResponseFormatter::error(
                [
                    'message' => 'Something went Wrong',
                    'errors'=>$th,
                ], 'Bank Gagal ditambahkan', 404,
            );
        }
    }
    public function all(Request $request)
    {
        $users_id = $request->input('users_id');
        $limit = $request->input('limit');
        $name = $request->input('name');

        $bank = Bank::query();

        if ($users_id) {
            $bank->where('users_id', $users_id);
        }

        if ($name) {
            $bank->where('name', 'like', '%' . $name . '%');
        }

        $bank = $bank->paginate($limit);

        if ($bank->count() > 0) {
            return ResponseFormatter::success(
                $bank,
                'Data Bank berhasil diambil',
            );
        } else {
            return ResponseFormatter::error(
                null,
                'Data Bank tidak ditemukan',
                404,
            );
        }
    }

    public function edit(Request $request)
    {
        $bank = Bank::find($request->id);

        if (!$bank) {
            return ResponseFormatter::error(
                null,
                'Bank tidak ditemukan',
                404
            );
        }

        $bank->name = $request->name;
        $bank->rekening = $request->rekening;
        $bank->bank_name = $request->bank_name;
        $bank->users_id = $request->users_id;

        if ($request->choice == 1) {
            // Jika choice == 1, maka ubah semua record yang memiliki users_id yang sama menjadi 0
            Bank::where('users_id', $request->users_id)->update(['choice' => 0]);
        }

        $bank->choice = $request->choice;

        $bank->save();

        return ResponseFormatter::success(
            $bank,
            'Berhasil mengubah data bank'
        );
    }
    public function delete($id)
    {
        $category = Bank::find($id);
 
        $category->delete();

        return ResponseFormatter::success(
            $category,
            'Berhasil Menghapus Bank',
        );
    }
}
