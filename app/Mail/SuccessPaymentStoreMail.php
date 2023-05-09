<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SuccessPaymentStoreMail extends Mailable
{
    use Queueable, SerializesModels;

    public $storeTransaction, $code;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($storeTransaction, $code)
    {
        $this->storeTransaction = $storeTransaction;
        $this->code = $code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS'))
            ->view('emails.success-payment-store', [
                'data' => $this->storeTransaction,
                'code' => $this->code,
            ]);
    }
}
