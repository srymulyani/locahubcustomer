<?php

namespace App\Http\Controllers\API;

use App\Models\BankAccount;
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

                'user_id' => 'required',
                'account_name' => 'required|string',
                'account_number' =>'required|numeric',
                'bank_account' => 'required|string',
                'choice' =>'numeric',
            ]);

            if ($validator->fails()){
            return ResponseFormatter::error([
                'message'=>'Validation fails',
                'errors' => $validator->errors()
            ],'Authentication Failed',422);
         }

         $bankUser = BankAccount::where('user_id', $request->user_id)->get();
         if($request->choice == 1){
            if($bankUser){
                BankAccount::where('user_id', $request->user_id)
                ->update(['choice' => 0]);
            }
         }

         $bank = BankAccount::create([
            'user_id' => $request->user_id,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'bank_account' => $request->bank_account,
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
    public function all(Request $request){
        
    }
}
