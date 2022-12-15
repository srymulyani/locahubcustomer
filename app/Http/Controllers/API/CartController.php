<?php

namespace App\Http\Controllers\API;

use App\Models\{Cart, ProductVariation, Store};
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
   {
		// $carts = Cart::select('quantity','product_id')->with('product.store')->where('user_id', auth()->user()->id)->get()->groupBy('product.store.username');
        $carts = Store::with('carts.product','carts.variation')->with(['carts' => function($query){
            $query->where('user_id', auth()->user()->id);
        }])->get();

        return response([
            "success" => true,
            "cart_stores" => $carts,
			"message" => "Data successfully retrieved"
        ], 200);
   }

   public function store(Request $request)
   {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variation_id' => 'nullable|exists:products_variation,id',
        ],[
            'product_id.required' => 'Pilih produk !',
            'product_id.exists' => 'Produk tidak ditemukan !',
            'variation_id.exists' => 'Variasi produk tidak ditemukan !',
        ]);

        $product_variation = ProductVariation::find($request->variation_id);
        if($product_variation && $product_variation->products_id != $request->product_id){
            return response([
                "message" => "The given data was invalid.",
                "errors"=> [
                    "variation_id" => [
                        "Variasi Produk tidak valid !"
                    ]
                ]
            ], 422);
        }

        $cart = Cart::firstOrNew([
            'user_id' => auth()->user()->id,
            'product_id' => $request->product_id,
            'variation_id' => $request->variation_id,
        ]);

        $cart->quantity = $cart->quantity+1;
        $cart->save();

        return response([
            'success' => true,
            'cart' => $cart,
            'message' => 'Data successfully stored'
        ], 200);
   }

   public function decrease(Cart $cart)
   {
        if(auth()->user()->id != $cart->user_id){
            return response([
                "message" => "The given data was invalid.",
                "errors"=> [
                    "permission" => [
                        "Anda tidak bisa mengubah data ini !"
                    ]
                ]
            ], 422);
        }

        if ($cart->quantity == 1) {
            return response([
                "message" => "The given data was invalid.",
                "errors"=> [
                    "quantity" => [
                        "Jumlah produk tidak bisa kurang dari 1 !"
                    ]
                ]
            ], 422);
        }

        $cart->quantity = $cart->quantity-1;
        $cart->save();

        return response([
            'success' => true,
            'message' => 'Data updated successfully'
        ], 200);
   }

   public function destroy(Cart $cart)
   {
        $cart->delete();

        return response([
            'success' => true,
            'message' => 'Data successfully deleted'
        ], 200);
   }

   public function bulkDestroy(Request $request)
   {
        $request->validate([
            'cart_ids' => 'required|array|min:1',
            'cart_ids.*' => 'required|exists:carts,id',
        ]);

        Cart::whereIn('id', $request->cart_ids)->delete();

        return response([
            'success' => true,
            'message' => 'Data successfully deleted'
        ], 200);
   }

   public function clear()
   {
        Cart::where('user_id', auth()->user()->id)->delete();

        return response([
            'success' => true,
            'message' => 'Data successfully deleted'
        ], 200);
   }
}
