<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\SuccessPaymentStoreJob;
use App\Services\Midtrans\CallbackService;
use App\Models\{StoreTransaction, Transaction};

class MidtransController extends Controller
{
    public function receive()
    {
        $callback = new CallbackService;

        if ($callback->isSignatureKeyVerified()) {
            $notification = $callback->getNotification();
            $transaction = $callback->getTransaction();

            if ($callback->isSuccess()) {
                $transaction = Transaction::where('code', $transaction->code)
                    ->with([
                        'store_transactions' => function ($query) {
                            $query->with(['store.user', 'items']);
                        }
                    ])
                    ->first();


                $transaction->update([
                    'payment_status' => 'dibayar',
                ]);

                StoreTransaction::where('transaction_id', $transaction->id)->update([
                    'status' => 'menunggu_konfirmasi'
                ]);

                dispatch(new SuccessPaymentStoreJob($transaction));
            }

            if ($callback->isExpire()) {
                Transaction::where('code', $transaction->code)->update([
                    'payment_status' => 'expired',
                ]);

                StoreTransaction::where('transaction_id', $transaction->id)->update([
                    'status' => 'expired'
                ]);
            }

            if ($callback->isCancelled()) {
                Transaction::where('code', $transaction->code)->update([
                    'payment_status' => 'dibatalkan',
                ]);

                StoreTransaction::where('transaction_id', $transaction->id)->update([
                    'status' => 'dibatalkan'
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
