<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\{Transaction,StoreTransaction, StoreTransactionItem, Store, Address,User};
use Barryvdh\DomPDF;
use Illuminate\Support\Facades\Response;


class InvoiceController extends Controller
{
       public function printInvoice($id)
    {
        // $transaction = Transaction::find($id);

       $transaction = Transaction::with('buyer')
            ->with('address')
            ->with('store_transactions.store')
            ->with('store_transactions.items.product')
            ->where('id', $id)
            ->first();
        $data = [
            'title' => 'Digital Invoice',
            'date' => date('m/d/Y'),
            'transaction' => $transaction,

        ];
        $pdf = app()->make('dompdf.wrapper');
        $pdf->loadView('invoice', $data);
        $pdf->setPaper('a4', 'potrait');

        return $pdf->download('invoice.pdf');

    }
}
