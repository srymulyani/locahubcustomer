<?php

namespace App\Http\Controllers\API;

use App\Models\{Address, City, Product, ProductVariation, Store, StoreTransaction, StoreTransactionItem, StoreTransactionShipment, Transaction};
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Services\Midtrans\CreateSnapTokenService;
use Illuminate\Support\Facades\Http;

class TransactionController extends Controller
{
   public function index(Request $request)
   {
		$transactions = Transaction::with(['buyer', 'store_transactions', 'store_transactions.items', 'address'])->where('buyer_id', auth()->user()->id);
		$limit = $request->limit ? intval($request->limit) : 10;
		$keyword = $request->keyword ? $request->keyword : null;

		if($keyword){
            $transactions = $transactions->where("code", "like", "%$keyword%")
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
        // VALIDATE REQUEST DATA
        $request->validate([
            'address_id' => 'required|exists:address,id',
            'shippings' => 'required|array|min:1',
            'shippings.*' => 'required|array|min:3|max:3',
            'shippings.*.store_id' => 'required|exists:store,id',
            'shippings.*.code' => 'required|in:jne,pos,tiki',
            'shippings.*.service' => 'required',
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

        // GET PRODUCTS BASED ON REQUEST PRODUCT ID
        $product_ids = array_column($request->products, 'product_id');
        $products = Product::whereIn('id', $product_ids)->get();

        // CALCULATE TOTAL WEIGHTS FOR EVERY STORE TRANSACTION
        $weights = [];
        foreach ($products as $product) {
            $key = array_search($product->id, array_column($request->products, 'product_id'));
            $quantity = $request->products[$key]['quantity'];
            
            $weights = [$product->store_id => (isset($weights[$product->store_id]) ? $weights[$product->store_id] : 0) + ($product->weight * $quantity)] ;
        }

        // GET SHIPMENT COST FOR EVERY STORE TRANSACTION FROM RAJAONGKIR
        $destination = Address::find($request->address_id)->city_id;
        foreach ($weights as $store_id => $weight) {
            $shipping_key = array_search($store_id, array_column($request->shippings, 'store_id'));
            $origin[$store_id] = Store::find($store_id)->city_id;
            
            $response = Http::post('https://api.rajaongkir.com/starter/cost', [
                'key' => env('RAJA_ONGKIR_KEY'),
                'origin' => $origin[$store_id],
                'destination' => $destination,
                'weight' => $weight,
                'courier' => $request->shippings[$shipping_key]['code']
            ]);
            
            $costs_key = array_search($request->shippings[$shipping_key]['service'], array_column($response->json()['rajaongkir']['results'][0]['costs'], 'service'));
            $prices[$store_id] = $response->json()['rajaongkir']['results'][0]['costs'][$costs_key]['cost'][0]['value'];
        }

        // CREATE TRANSACTION
        $transaction = Transaction::create([
            'buyer_id' => auth()->user()->id,
            'address_id' => $request->address_id,
            'code' => Transaction::generateCode(),
            'grand_total' => 0,
        ]);

        // CREATE STORE TRANSACTION AND THE ITEMS
        foreach ($products as $key => $product) {
            $store_transaction = StoreTransaction::updateOrCreate([
                'store_id' => $product->store_id,
                'transaction_id' => $transaction->id,
            ]);

            $variation = null;
            $price = $product->price;

            if($request->products[$key]['variation_id']){
                $product_variation = ProductVariation::find($request->products[$key]['variation_id']);
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
                'quantity' => $request->products[$key]['quantity'],
                'total' => $request->products[$key]['quantity']*$price,
            ]);
        }

        // UPDATE DATA TOTAL STORE TRANSACTION AFTER STORING ALL THE ITEMS
        $store_transactions = StoreTransaction::where('transaction_id', $transaction->id)->get();

        foreach ($store_transactions as $store_transaction) {
            $total = StoreTransactionItem::where('store_transaction_id', $store_transaction->id)->sum('total');
            $store_transaction->total = $total;
            $store_transaction->save();

            // CREATE STORE TRANSACTION SHIPMENT DATA
            StoreTransactionShipment::create([
                'store_transaction_id' => $store_transaction->id,
                'origin' => City::find($origin[$store_transaction->store_id])->name,
                'destination' => City::find($destination)->name,
                'weight' => $weights[$store_transaction->store_id],
            ]);
        }

        // UPDATE GRAND TOTAL TRANSACTION AFTER UPDATING TOTAL PER STORE TRANSACTION
        $grand_total = StoreTransaction::where('transaction_id', $transaction->id)->sum('total');
        $transaction->grand_total = $grand_total;
        $transaction->save();

        // CREATE SNAP TOKEN FOR MIDTRANS
        $snapToken = $transaction->snap_token;
        if (empty($snapToken)) {
 
            $midtrans = new CreateSnapTokenService($transaction);
            $snapToken = $midtrans->getSnapToken();
 
            $transaction->snap_token = $snapToken;
            $transaction->save();
        }

        // GET TRANSACTION DATA WITH SOME OF THE RELATIONS
        $transaction = Transaction::with(['buyer', 'store_transactions', 'store_transactions.items', 'address'])->find($transaction->id);
        
        // RETURN RESPONSE
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
