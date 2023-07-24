<?php

namespace App\Http\Controllers\API;

use App\Models\{Address, City, Product, ProductVariation, Store, StoreTransaction, StoreTransactionItem, StoreTransactionShipment, Transaction};
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Services\Midtrans\CreateSnapTokenService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = Transaction::with(['buyer', 'store_transactions', 'store_transactions.store', 'store_transactions.items', 'store_transactions.items.product.galleries', 'address'])->where('buyer_id', auth()->user()->id);
        $limit = $request->limit ? intval($request->limit) : 10;
        $keyword = $request->keyword ? $request->keyword : null;
        $status = $request->status ? $request->status : null;

        if ($keyword) {
            $transactions = $transactions->where("code", "like", "%$keyword%")
                ->orWhere("invoice", "like", "%$keyword%")
                ->orWhere("status", "like", "%$keyword%");
        }

        if ($status && in_array($status, ['menunggu_konfirmasi', 'diproses', 'dikirim', 'selesai', 'dibatalkan', 'menunggu-pembayaran', 'expired'])) {
            $transactions = $transactions->whereHas('store_transactions', function ($q) use ($status) {
                $q->where('status', $status);
            });
        }

        if ($limit == -1) {
            $transactions = [
                "data" => $transactions->get()
            ];
        } else {
            $transactions = $transactions->paginate($limit);
        }

        return response([
            "success" => true,
            "transactions" => $transactions,
            "message" => "Data successfully retrieved"
        ], 200);
    }

    public function indexStoreTrans(Request $request)
    {
        $storeTransactions = StoreTransaction::with(['transaction', 'transaction.buyer', 'store', 'transaction.address' ,'items', 'items.product', 'items.product.category', 'items.product.galleries', 'items.product.variation']);
        $limit = $request->limit ? intval($request->limit) : 10;
        $status = $request->status ? $request->status : null;
        $store_id = $request->store_id ? $request->store_id : null;
        $invoice = $request->invoice ? $request->invoice : null;

        if ($status && in_array($status, ['menunggu_konfirmasi', 'diproses', 'dikirim', 'selesai', 'dibatalkan', 'menunggu-pembayaran', 'expired'])) {
            $storeTransactions = $storeTransactions->where('status', $status);
            if ($store_id) {
                $storeTransactions = $storeTransactions->where('store_id', $store_id);
            }
        }

        if ($invoice) {
            $storeTransactions = $storeTransactions->whereHas('transaction', function ($q) use ($invoice) {
                $q->where("code", "like", "%$invoice%");
            });
        }

        // if ($limit == -1) {
        //     $storeTransactions = [
        //         "data" => $storeTransactions->get()
        //     ];
        // } else {
        //     $storeTransactions = $storeTransactions->paginate($limit);
        // }

        return ResponseFormatter::success(
            $storeTransactions->get(),
            'Data successfully retrieved.',
         );
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
        ], [
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

        try {
            // INIT DB TRANSACTION
            DB::beginTransaction();

            // GET PRODUCTS BASED ON REQUEST PRODUCT ID
            // $product_ids = array_column($request->products, 'product_id');
            // $products = Product::whereIn('id', $product_ids)->get();

            // CALCULATE TOTAL WEIGHTS FOR EVERY STORE TRANSACTION
            $weights = [];
            foreach ($request->products as $request_product) {
                $product = Product::find($request_product['product_id']);

                $key = array_search($product->id, array_column($request->products, 'product_id'));
                $quantity = $request->products[$key]['quantity'];

                if (isset($weights[$product->store_id])) {
                    $weights[$product->store_id] = $weights[$product->store_id] + ($product->weight * $quantity);
                } else {
                    $weights[$product->store_id] = $product->weight * $quantity;
                }
            }

            // GET SHIPMENT COST FOR EVERY STORE TRANSACTION FROM RAJAONGKIR
            $destination = Address::find($request->address_id)->city_id;
            foreach ($weights as $store_id => $weight) {
                $shipping_key = array_search($store_id, array_column($request->shippings, 'store_id'));
                $origin[$store_id] = Store::find($store_id)->city_id;

                $response = Http::post('https://api.rajaongkir.com/starter/cost', [
                    'key' => env('RAJAONGKIR_API_KEY'),
                    'origin' => $origin[$store_id],
                    'destination' => $destination,
                    'weight' => $weight,
                    'courier' => $request->shippings[$shipping_key]['code']
                ]);

                $costs_key = array_search($request->shippings[$shipping_key]['service'], array_column($response->json()['rajaongkir']['results'][0]['costs'], 'service'));

                $prices[$store_id] = $response->json()['rajaongkir']['results'][0]['costs'][$costs_key]['cost'][0]['value'];
                $courier_codes[$store_id] = $response->json()['rajaongkir']['results'][0]['code'];
                $courier_names[$store_id] = $response->json()['rajaongkir']['results'][0]['name'];
                $services[$store_id] = $response->json()['rajaongkir']['results'][0]['costs'][$costs_key]['service'];
                $etds[$store_id] = $response->json()['rajaongkir']['results'][0]['costs'][$costs_key]['cost'][0]['etd'];
            }

            // CREATE TRANSACTION
            $transaction = Transaction::create([
                'buyer_id' => auth()->user()->id,
                'address_id' => $request->address_id,
                'code' => Transaction::generateCode(),
                'grand_total' => 0,
            ]);

            // CREATE STORE TRANSACTION AND THE ITEMS
            foreach ($request->products as $key => $request_product) {
                $product = Product::find($request_product['product_id']);

                //CHECK IF PRODUCT EXIST
                if (!$product) {
                    continue;
                }

                // CHECK PRODUCT QUANTITY
                if ($request->products[$key]['quantity'] > $product->stock) {
                    throw new Exception("Insufficient product stock ({$product->name})");
                }

                // UPDATE PRODUK STOCK && PRODUCT SOLD
                $product->update([
                    'stock' => $product->stock - $request->products[$key]['quantity'],
                    'product_sold' => $product->product_sold - $request->products[$key]['quantity'],
                ]);

                $store_transaction = StoreTransaction::updateOrCreate([
                    'store_id' => $product->store_id,
                    'transaction_id' => $transaction->id,
                    'shipping_cost' => $prices[$product->store_id],
                    'courier' => $courier_codes[$product->store_id],
                    'courier_name' => $courier_names[$product->store_id],
                    'shipping_service' => $services[$product->store_id],
                    'shipping_etd' => $etds[$product->store_id],
                ]);

                $variation = null;
                $price = $product->price;

                if ($request->products[$key]['variation_id']) {
                    $product_variation = ProductVariation::find($request->products[$key]['variation_id']);
                    if ($product_variation->product_id == $product->id) {
                        $variation = $product_variation->name;
                        $price = $product_variation->products_price;
                    }
                }

                StoreTransactionItem::create([
                    'store_transaction_id' => $store_transaction->id,
                    'product_id' => $product->id,
                    'product' => $product->name,
                    'variation' => $variation,
                    'price' => $price,
                    'quantity' => $request->products[$key]['quantity'],
                    'total' => $request->products[$key]['quantity'] * $price,
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
            $total = StoreTransaction::where('transaction_id', $transaction->id)->sum('total');
            $shipping_cost = StoreTransaction::where('transaction_id', $transaction->id)->sum('shipping_cost');
            $transaction->grand_total = $total + $shipping_cost;
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

            // COMMIT
            DB::commit();

            // RETURN RESPONSE
            return response([
                "success" => true,
                "transaction" => $transaction,
                "message" => "Data successfully stored"
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseFormatter::error(null, $th->getMessage(), 500);
        }
    }

    public function show(Transaction $transaction)
    {
        $transaction = Transaction::with(['buyer', 'store_transactions', 'store_transactions.items', 'store_transactions.items.product.galleries', 'address'])->find($transaction->id);

        return response([
            "success" => true,
            "transaction" => $transaction,
            "message" => "Data successfully retrieved"
        ], 200);
    }

    // UPDATE RESI & STATUS STORE TRANSACTION & TRANSACTION => DIKIRIM
    public function updateShipment(Request $request, $store_transaction_id)
    {
        try {
            DB::beginTransaction();

            $storeTransaction = StoreTransaction::findOrFail($store_transaction_id);
            $storeTransaction->status = 'dikirim';
            $storeTransaction->receipt = $request->track_number;
            $storeTransaction->save();

            $shipment = $storeTransaction->shipment()->firstOrFail();
            $shipment->track_number = $request->track_number;
            $shipment->save();

            $transaction = $storeTransaction->transaction()->firstOrFail();
            $storeTransactions = $transaction->store_transactions()->get();

            $allSent = true;
            foreach ($storeTransactions as $st) {
                if ($st->status != 'dikirim' && !$st->receipt) {
                    $allSent = false;
                }
            }

            if ($allSent) {
                $transaction->payment_status = 'dikirim';
                $transaction->save();
            }

            DB::commit();
            return ResponseFormatter::success($shipment, 'Resi Berhasil Di Ubah');
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseFormatter::error(null, $th->getMessage(), 500);
        }
    }

    // TRACK STORE TRANSACTION
    public function trackShipment($store_transaction_id)
    {
        $storeTransaction = StoreTransaction::findOrFail($store_transaction_id);

        if (!$storeTransaction->receipt) {
            return ResponseFormatter::error(null, 'Resi belum diinput', 422);
        }

        $response = Http::asForm()
            ->withHeaders([
                'key' => env('RAJAONGKIR_API_KEY'),
                'content-type' => 'application/x-www-form-urlencoded',
            ])
            ->post('https://pro.rajaongkir.com/api/waybill', [
                'waybill' => $storeTransaction->receipt,
                'courier' => $storeTransaction->courier,
            ])
            ->json();

        if ($response['rajaongkir']['status']['code'] != 200) {
            return ResponseFormatter::error(
                null,
                $response['rajaongkir']['status']['description'],
                $response['rajaongkir']['status']['code']
            );
        }

        $data['store_transaction'] = $storeTransaction;
        $data['rajaongkir'] = $response['rajaongkir']['result'];

        return ResponseFormatter::success($data, 'Resi Berhasil Di Ubah');
    }

    // UPDATE STATUS STORE TRANSACTION & TRANSACTION => SELESAI
    public function finishShipment(Request $request, $store_transaction_id)
    {
        try {
            DB::beginTransaction();

            $storeTransaction = StoreTransaction::findOrFail($store_transaction_id);
            $storeTransaction->status = 'selesai';
            $storeTransaction->save();

            $transaction = $storeTransaction->transaction()->firstOrFail();
            $storeTransactions = $transaction->store_transactions()->get();

            $isDone = true;
            foreach ($storeTransactions as $st) {
                if ($st->status != 'selesai') {
                    $isDone = false;
                }
            }

            if ($isDone) {
                $transaction->payment_status = 'selesai';
                $transaction->save();
            }

            DB::commit();
            return ResponseFormatter::success($storeTransaction, 'Pesanan diselesaikan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseFormatter::error(null, $th->getMessage(), 500);
        }
    }
}
