<?php

namespace App\Http\Controllers\API;

use App\Models\{Transaction, Product, ProductVariation, StoreTransaction, StoreTransactionItem};
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Services\Midtrans\CreateSnapTokenService;

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
		$transactions = Transaction::with(['buyer', 'store_transactions', 'store_transactions.items', 'address'])->where('buyer_id', auth()->user()->id);
		$limit = $request->limit ? intval($request->limit) : 10;
		$keyword = $request->keyword ? $request->keyword : null;

		if($keyword){
            $transaction = $transaction->where("code", "like", "%$keyword%")
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

   public function store(Request $request)
   {
        $address_id = 1;

        $request->validate([
            // 'address_id' => 'required|exists:address,id',
            'note' => 'nullable|string|max:255',
            'products' => 'required|array|min:1',
            'products.*' => 'required|array|min:2|max:3',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.variation_id' => 'nullable|exists:products_variation,id',
        ],[
            'note.string' => 'Catatan tidak valid !',
            'note.max' => 'Catatan maksimal 255 karakter !',
            'products.required' => 'Pilih produk !',
            'products.array' => 'Produk tidak valid !',
            'products.min' => 'Pilih produk !',
            'products.*.required' => 'Pilih produk !',
            'products.*.array' => 'Produk tidak valid !',
            'products.*.min' => 'Pilih produk !',
            'products.*.product_id.required' => 'Pilih produk !',
            'products.*.product_id.exists' => 'Produk tidak ditemukan !',
            'products.*.quantity.required' => 'Pilih jumlah produk !',
            'products.*.quantity.numeric' => 'Jumlah produk tidak valid !',
            'products.*.quantity.min' => 'Jumlah produk minimal 1 !',
            'products.*.variation_id.exists' => 'Variasi produk tidak ditemukan !',
        ]);

        $transaction = Transaction::create([
            'buyer_id' => auth()->user()->id,
            'address_id' => $address_id,
            'code' => Transaction::generateCode(),
            'grand_total' => 0,
        ]);
        for ($i=0; $i < count($request->products); $i++) { 
            $product = Product::find($request->products[$i]['product_id']);

            $store_transaction = StoreTransaction::updateOrCreate([
                'store_id' => $product->store_id,
                'transaction_id' => $transaction->id,
            ]);

            $variation = null;
            $price = $product->price;

            if($request->products[$i]['variation_id']){
                $product_variation = ProductVariation::find($request->products[$i]['variation_id']);
                if($product_variation->product_id == $product->id){
                    $variation = $product_variation->name;
                    $price = $product_variation->products_price;
                }
            }

            StoreTransactionItem::create([
                'store_transaction_id' => $store_transaction->id,
                'product' => $product->name,
                'variation' => $variation,
                'price' => $price,
                'quantity' => $request->products[$i]['quantity'],
                'total' => $request->products[$i]['quantity']*$price,
            ]);
        }

        $store_transactions = StoreTransaction::where('transaction_id', $transaction->id)->get();

        foreach ($store_transactions as $store_transaction) {
            $total = StoreTransactionItem::where('store_transaction_id', $store_transaction->id)->sum('total');
            $store_transaction->total = $total;
            $store_transaction->save();
        }

        $grand_total = StoreTransaction::where('transaction_id', $transaction->id)->sum('total');
        $transaction->grand_total = $grand_total;
        $transaction->save();

        $snapToken = $transaction->snap_token;
        if (empty($snapToken)) {
 
            $midtrans = new CreateSnapTokenService($transaction);
            $snapToken = $midtrans->getSnapToken();
 
            $transaction->snap_token = $snapToken;
            $transaction->save();
        }

        $transaction = Transaction::with(['buyer', 'store_transactions', 'store_transactions.items', 'address'])->find($transaction->id);
        
        return response([
            "success" => true,
            "transaction" => $transaction,
			"message" => "Data successfully stored"
        ], 200);
   }

   public function show(Transaction $transaction)
   {
		$transaction = Transaction::with(['buyer', 'store_transactions', 'store_transactions.items', 'address'])->find($transaction->id);
		
        return response([
            "success" => true,
            "transaction" => $transaction,
			"message" => "Data successfully retrieved"
        ], 200);
   }
}
