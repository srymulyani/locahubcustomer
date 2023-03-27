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
          
        $image = Banner::findOrFail($id)->get();

        return response()->json([
            'url' => $image->url,
        ]);
    }
    

    public function upload(Request $request){
    
     $images = [];
     if ($request->hasFile('image1')) {
        $image = new Banner();
        $file = $request->file('image1');

         // CHECK UKURAN GAMBAR
        $max_file_size = 5000000; // ukuran maksimal file dalam byte (5MB)
        if ($file->getSize() > $max_file_size) {
            return response()->json(['error' => 'Ukuran file terlalu besar. Maksimal ' . ($max_file_size/1000000) . ' MB']);
        }
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

    return response()->json(['result' => $images]);
    }

}
