<?php

namespace App\Http\Controllers\API;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
{
    public function show($id)
    {
          
        $image = Banner::findOrFail($id);

        return response()->json([
            'url' => $image->url,
        ]);
    }
    

    public function upload(Request $request){
   
     if ($request->hasFile('image1')) {
        $image = new Banner();
        $file = $request->file('image1');

        //CHECK EKSTENSI GAMBAR
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
        $ext = $file->getClientOriginalExtension();
        if (!in_array($ext, $allowed_exts)) {
            return response()->json(['error' => 'File harus berupa gambar dengan format jpg, jpeg, png, atau svg']);
        }

        $path = $request->file('image1')->storeAs('Banner', $request->file('image1')->getClientOriginalName(),'public');
        $image->url = 'storage/' . $path;
        $image->save();
        $images[] = $image;
    }

    if ($request->hasFile('image2')) {
        $image = new Banner();
         $file = $request->file('image2');

        //CHECK EKSTENSI GAMBAR
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
        $ext = $file->getClientOriginalExtension();
        if (!in_array($ext, $allowed_exts)) {
            return response()->json(['error' => 'File harus berupa gambar dengan format jpg, jpeg, png, atau svg']);
        }

        $path = $request->file('image2')->storeAs('Banner', $request->file('image2')->getClientOriginalName(),'public');
        $image->url = 'storage/' . $path;
        $image->save();
        $images[] = $image;
    }

     if ($request->hasFile('image3')) {
        $image = new Banner();
        $file = $request->file('image3');

        //CHECK EKSTENSI GAMBAR
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
        $ext = $file->getClientOriginalExtension();
        if (!in_array($ext, $allowed_exts)) {
            return response()->json(['error' => 'File harus berupa gambar dengan format jpg, jpeg, png, atau svg']);
        }
        $path = $request->file('image3')->storeAs('Banner', $request->file('image3')->getClientOriginalName(),'public');
        $image->url = 'storage/' . $path;
        $image->save();
        $images[] = $image;
    }

    return response()->json(['result' => $images]);
    }
}
