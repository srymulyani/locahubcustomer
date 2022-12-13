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
