<?php

namespace App\Http\Controllers\API;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransactionDetail;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class TransactionController extends Controller
{
     public function all(Request $request)
     {
        $id = $request->input('id');
        $limit = $request->input('limit');
        $status = $request->input('status');
        $user_id = $request->input('user_id');
        $store_id = $request->store_id;

      //   dd("test");
        if ($id){
         $transaction = Transaction::with(['user','payment','details','store'])->find($id);

         if($transaction){
            return ResponseFormatter::success(
               $transaction,
               'Transaction data successfully retrieved'
            );
         }else {
            return ResponseFormatter::error(
               null,
               'No transaction data',404
            );
         }
        }

        $transaction = Transaction::with(['user', 'store', 'payment', 'details','details.product'])->where('user_id', Auth::user()->id);

        if($status){
         $transaction->where('status', $status);
      }
      return ResponseFormatter::success(
            $transaction->paginate($limit),
            'Transaction List data successfully retrieved'
        );
       
     }

     public function create(Request $request){
      try {
         $request->all();

         $transaction = Transaction::create([
            'user_id' => $request->user_id,
            'store_id' => $request->store_id,
            'address_id' =>$request->address_id,
            'pay_method_id' =>$request->pay_method_id,

            'jasa_antar' => $request->jasa_antar, 
            'total_shop' => $request->total_shop,
            'disc_total' => $request->disc_total,
            'shipping_total' =>$request->shipping_total,
            'shipping_disc' => $request->shiping_disc,
            'price_total' => $request->price_total,
            'status' => $request->status,
            'note' => $request->note,
         ]);

         foreach($request->items as $product){
            TransactionDetail::create([
               'user_id' => $request->user_id,
               'products_id' => $product['id'],
               'transaction_id' => $transaction->id,
               'qty' => $product['qty'],
            ]);
         }

         return ResponseFormatter::success(
            $transaction->load('user','payment','store','details','details.product'),
            "Transaction successfully added"
         );
      } catch (\Throwable $th) {
         return ResponseFormatter::error([
            null,
            'errors' =>$th
         ], 'Transaction failed to add',404);
      }
     }
     
     public function edit(Request $request){
      $transaction = Transaction::find($request->id);

      $transaction->id = $transaction->id;
      $transaction->store_id = $transaction->store_id;
      $transaction->pay_method_id = $transaction->pay_method_id;

      if ($transaction->invoice == null) {
            $transaction->invoice = 'INV'. Carbon::createFromFormat('Y-m-d H:i:s', now())->format('dmYHis').random_int(100000, 999999). $request->user_id.$request->store_id;
        } else {
            $transaction->invoice = $transaction->invoice;
        }

        if ($request->nomor_resi != null) {
            $transaction->no_resi = $request->no_resi;
        } else {
            $transaction->no_resi = $transaction->no_resi;
        }
      
        $transaction->total_shop = $transaction->total_shop;
        $transaction->disc_total = $transaction->disc_total;
        $transaction->shipping_total = $transaction->shipping_total;
        $transaction->shipping_disc =$transaction->shipping_disc;
        $transaction->price_total = $transaction->price_total;

         if ($request->status != null) {
            $transaction->status = $request->status;
        } else {
            $transaction->status = $transaction->status;
        }
        
        $transaction->note = $transaction->note;
        $transaction->save();

         return ResponseFormatter::success(
            $transaction->load('user','payment','details','store'),
            "Transaction successfully added"
         );
     }
}
