<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
     public function all(Request $request)
     {
        $id = $request->input('id');
        $limit = $request->input('limit');
        $status = $request->input('status');

     }
}
