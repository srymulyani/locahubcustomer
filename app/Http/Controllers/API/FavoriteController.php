<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{

    //Store favorite products
    public function store(Request $request)
{
    $favorite = new Favorite;
    $favorite->user_id = $request->input('user_id');
    $favorite->product_id = $request->input('product_id');
    $favorite->save();

    return response()->json([
        'message' => 'Favorite created successfully.'
    ], 201);
}

    // Fetch Favorite Products

    public function index($user_id)
{
    $favorites = Favorite::where('user_id', $user_id)->get();

    return response()->json($favorites);
}
    //destroy favorite products
    public function destroy($id){
    $favorite = Favorite::find($id);

    if (!$favorite) {
        return response()->json([
            'message' => 'Favorite not found.'
        ], 404);
    }

    $favorite->delete();

    return response()->json([
        'message' => 'Favorite deleted successfully.'
    ]);
    }


}
