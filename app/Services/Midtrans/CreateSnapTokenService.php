<?php
 
namespace App\Services\Midtrans;
 
use Midtrans\Snap;
 
class CreateSnapTokenService extends Midtrans
{
    protected $transaction;
 
    public function __construct($transaction)
    {
        parent::__construct();
 
        $this->transaction = $transaction;
    }
 
    public function getSnapToken()
    {
        $params = [
            'transaction_details' => [
                'order_id' => $this->transaction->code,
                'gross_amount' => $this->transaction->grand_total,
            ],
            'item_details' => [
                [
                    'id' => $this->transaction->code,
                    'price' => $this->transaction->grand_total,
                    'quantity' => 1,
                    'name' => 'Order '.$this->transaction->code,
                ],
            ],
            'customer_details' => [
                'first_name' => $this->transaction->buyer->name,
                'email' => $this->transaction->buyer->email,
                'phone' => $this->transaction->buyer->phone_number,
            ]
        ];
 
        $snapToken = Snap::getSnapToken($params);
 
        return $snapToken;
    }
}