<?php

namespace App\Http\Controllers\API;

use App\Models\Transaction;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\Address;
use Illuminate\Http\Request;
use App\Models\TransactionDetail;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class TransactionController extends Controller
{
    //  public function all(Request $request)
    //  {
    //     $id = $request->input('id');
    //     $limit = $request->input('limit');
    //     $status = $request->input('status');
    //     $user_id = $request->input('user_id');
    //     $store_id = $request->store_id;

    //   //   dd("test");
    //     if ($id){
    //      $transaction = Transaction::with(['user','payment','details','store'])->find($id);

    //      if($transaction){
    //         return ResponseFormatter::success(
    //            $transaction,
    //            'Transaction data successfully retrieved'
    //         );
    //      }else {
    //         return ResponseFormatter::error(
    //            null,
    //            'No transaction data',404
    //         );
    //      }
    //     }

    //     $transaction = Transaction::with(['user', 'store', 'payment', 'details','details.product'])->where('user_id', Auth::user()->id);

    //     if($status){
    //      $transaction->where('status', $status);
    //   }
    //   return ResponseFormatter::success(
    //         $transaction->paginate($limit),
    //         'Transaction List data successfully retrieved'
    //     );
       
    //  }

   //   public function checkout(Request $request){
   //    try {
   //       $request->validate([
   //       'details' => 'required|array',
   //       'details.*.id' => 'exists:products.id',
   //       'total_shop' => 'required',
   //       'disc_total' => 'required',
   //       'shipping disc' =>'required',
   //       'shipping_total' => 'required',
   //       'shipment' => 'required',
   //       'status' => 'required|in:UNPAID,PAID,PACKED,DELIVERED,COMPLETED,CANCELLED,RETURNED'
   //    ]);

   //    $transaction = Transaction::find($request->id);

   //    $transaction->id = $transaction->id;
   //    $transaction->user_id = $transaction->user_id;
      
   //    if ($transaction->status == null) {
   //          $transaction->status = 'INV'. Carbon::createFromFormat('Y-m-d H:i:s', now())->format('dmYHis').random_int(100000, 999999). $request->user_id;
   //      } else {
   //          $transaction->status = $transaction->status;
   //      }

   //    // $paymentDue = (new\DateTime($transactionDate))->modify('+2 day')->format('Y-m-d H:i:s');
   //       $transaction = Transaction::create([
   //          'user_id' => Auth::user()->id,
   //          'code' => $request-> Transaction::generateCode(),
   //          'store_id' => $request->store_id,
   //          'address_id' =>$request->address_id,
   //          'pay_method_id' =>$request->pay_method_id,
   //          'payment_due' =>$request-> payment_due,
   //          'payment_status' =>$request->Transaction::UNPAID(),
   //          'jasa_antar' => $request->jasa_antar,
   //          'total_shop' => $request->total_shop,
   //          'disc_total' => $request->disc_total,
   //          'shipping_total' =>$request->shipping_total,
   //          'shipping_disc' => $request->shiping_disc,
   //          'price_total' => $request->price_total,
   //          'status' => $request->Tansaction::CREATED(),
   //          'note' => $request->note,
   //       ]);

   //       foreach($request->items as $product){
   //          TransactionDetail::create([
   //             'user_id' => Auth::user()->id,
   //             'products_id' => $product['id'],
   //             'transaction_id' => $transaction->id,
   //             'qty' => $product['qty'],
   //          ]);
   //       }

   //       return ResponseFormatter::success(
   //          $transaction->load('user','payment','store','details','details.product'),
   //          "Transaction successfully added"
   //       );
   //    } catch (\Throwable $th) {
   //       return ResponseFormatter::error([
   //          null,
   //          'errors' =>$th
   //       ], 'Transaction failed to add',404);
   //    }

   //   }
     
   //   public function edit(Request $request){
   //    $transaction = Transaction::find($request->id);

   //    $transaction->id = $transaction->id;
   //    $transaction->store_id = $transaction->store_id;
   //    // $transaction->pay_method_id = $transaction->pay_method_id;

   //    if ($transaction->invoice == null) {
   //          $transaction->invoice = 'INV'. Carbon::createFromFormat('Y-m-d H:i:s', now())->format('dmYHis').random_int(100000, 999999). $request->user_id.$request->store_id;
   //      } else {
   //          $transaction->invoice = $transaction->invoice;
   //      }

   //      if ($request->no_resi != null) {
   //          $transaction->no_resi = $request->no_resi;
   //      } else {
   //          $transaction->no_resi = $transaction->no_resi;
   //      }
      
   //      $transaction->total_shop = $transaction->total_shop;
   //      $transaction->disc_total = $transaction->disc_total;
   //      $transaction->shipping_total = $transaction->shipping_total;
   //      $transaction->shipping_disc =$transaction->shipping_disc;
   //      $transaction->price_total = $transaction->price_total;

   //       if ($request->status != null) {
   //          $transaction->status = $request->status;
   //      } else {
   //          $transaction->status = $transaction->status;
   //      }
        
   //      $transaction->note = $transaction->note;
   //      $transaction->save();

   //       return ResponseFormatter::success(
   //          $transaction->load('user','payment','details','store'),
   //          "Transaction successfully added"
   //       );
   //   }

   // NEW TRANSACTION METHODS
   public function index(Request $request)
   {
		$transactions = Transaction::with(['user', 'store', 'payment', 'details','details.product'])->where('user_id', auth()->user()->id);
		$limit = $request->limit ? intval($request->limit) : 10;
		$keyword = $request->keyword ? $request->keyword : null;

		if($keyword){
            $transaction = $transaction->where("code", "like", "%$keyword%")
                ->orWhere("no_resi", "like", "%$keyword%")
                ->orWhere("invoice", "like", "%$keyword%")
                ->orWhere("status", "like", "%$keyword%");
        }

		if($limit == -1){
            $transactions = [
                "data" => $transactions->get()
            ];
        }else{
            $transactions = $transactions->paginate($limit);
        }

        return response([
            "success" => true,
            "transactions" => $transactions,
			"message" => "Data successfully retrieved"
        ], 200);
   }

   public function show(Transaction $transaction)
   {
		$transaction = Transaction::with(['user', 'store', 'payment', 'details','details.product'])->find($transaction->id);
		
        return response([
            "success" => true,
            "transaction" => $transaction,
			"message" => "Data successfully retrieved"
        ], 200);
   }
}
