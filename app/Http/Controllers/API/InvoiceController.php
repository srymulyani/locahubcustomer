<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\{Transaction,StoreTransaction, StoreTransactionItem, Store, Address};
use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\Response;


class InvoiceController extends Controller
{
       public function printInvoice($id)
    {

        $transaction = Transaction::find($id);
        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $html = '<html><body>';
        $html .= '<h1>Invoice</h1>';

        $html .= '<div style="width: 50%; float: left;">';
        $html .= '<p>Waktu Pembayaran: ' . $transaction->created_at . '</p>';
        $html .= '<p>Toko: ' . $transaction->created_at . '</p>';
        $html .= '</div>';

        $html .= '<div style="width: 50%; float: right;">';
        $html .= '<p>Transaction Total: ' . $transaction->grand_total . '</p>';
        $html .= '<p>Transaction Code: ' . $transaction->code . '</p>';
        $html .= '</div>';

        $html .= '<h1> Rincian Pesanan <h1>';
        $html .= '<p>' . $transaction->created_at . '</p>';

        $html .= '<p>Total Belanja: ' . $transaction->grand_total . '</p>';
        $html .= '<p>Diskon Belanja:  Rp. 0 </p>';
        $html .= '<p>Diskon Ongkos Kirim:  Rp.0 </p>';
        $html .= '<p>Total Pembayaran: ' . $transaction->grand_total . '</p>';
        $html .= '</body></html>';

        $pdf = app()->make('dompdf.wrapper');
        $pdf->loadHTML($html);
        $pdf->setPaper('a4', 'potrait');

        return $pdf->download('invoice.pdf');
    }
}
