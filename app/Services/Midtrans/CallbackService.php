<?php
 
namespace App\Services\Midtrans;
 
use App\Models\Transaction;
use App\Services\Midtrans\Midtrans;
use Midtrans\Notification;
 
class CallbackService extends Midtrans
{
    protected $notification;
    protected $transaction;
    protected $serverKey;
 
    public function __construct()
    {
        parent::__construct();
 
        $this->serverKey = config('midtrans.server_key');
        $this->_handleNotification();
    }
 
    public function isSignatureKeyVerified()
    {
        return ($this->_createLocalSignatureKey() == $this->notification->signature_key);
    }
 
    public function isSuccess()
    {
        $statusCode = $this->notification->status_code;
        $transactionStatus = $this->notification->transaction_status;
        $fraudStatus = !empty($this->notification->fraud_status) ? ($this->notification->fraud_status == 'accept') : true;
        return ($statusCode == 200 && $fraudStatus && ($transactionStatus == 'capture' || $transactionStatus == 'settlement'));
    }
 
    public function isExpire()
    {
        return ($this->notification->transaction_status == 'expire');
    }
 
    public function isCancelled()
    {
        return ($this->notification->transaction_status == 'cancel');
    }
 
    public function getNotification()
    {
        return $this->notification;
    }
 
    public function getTransaction()
    {
        return $this->transaction;
    }
 
    protected function _createLocalSignatureKey()
    {
        $transaction_code = $this->transaction->code;
        $statusCode = $this->notification->status_code;
        $grossAmount = $this->transaction->grand_total.'.00';
        $serverKey = $this->serverKey;
        $input = $transaction_code . $statusCode . $grossAmount . $serverKey;
        $signature = openssl_digest($input, 'sha512');
 
        return $signature;
    }
 
    protected function _handleNotification()
    {
        $notification = new Notification();
 
        $id = $notification->order_id;
        $transaction = Transaction::where('id', $id)->first();
 
        $this->notification = $notification;
        $this->transaction = $transaction;
    }
}