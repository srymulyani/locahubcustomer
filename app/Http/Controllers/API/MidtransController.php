<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\{Transaction};
use Illuminate\Http\Request;
use App\Services\Midtrans\CallbackService;

class MidtransController extends Controller
{
    public function receive()
    {
        $callback = new CallbackService;
 
        if ($callback->isSignatureKeyVerified()) {
            $notification = $callback->getNotification();
            $transaction = $callback->getTransaction();
 
            if ($callback->isSuccess()) {
                Transaction::where('id', $transaction->id)->update([
                    'payment_status' => 'dibayar',
                ]);
                StoreTransaction::where('transaction_id', $transaction->id)->update([
                    'status' => 'menunggu konfirmasi'
                ]);
            }
 
            if ($callback->isExpire()) {
                Transaction::where('id', $transaction->id)->update([
                    'payment_status' => 'expired',
                ]);
            }
 
            if ($callback->isCancelled()) {
                Transaction::where('id', $transaction->id)->update([
                    'payment_status' => 'dibatalkan',
                ]);
            }
 
            return response()
                ->json([
                    'success' => true,
                    'message' => 'Notifikasi berhasil diproses',
                ], 200);
        } else {
            return response()
                ->json([
                    'error' => true,
                    'message' => 'Signature key tidak terverifikasi',
                ], 403);
        }
    }
}
