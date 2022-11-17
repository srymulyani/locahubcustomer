<?php

namespace App\Models;

use App\Helpers\General;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    
        /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $softDelete = true;
    protected $table ="transaction";
    protected $fillable = [
        'user_id',
        'code',
        'store_id',
        'address_id',
        'pay_method_id',
        'payment_url',
        'payment_token',
        'payment_status',
        'payment_due',
        'no_resi',
        'jasa_antar',
        'cancelation_note',
        'cancelled_by',
        'approved_at',
        'approved_by',
        'total_shop',
        'disc_total',
        'shipping_total',
        'shipping_disc',
        'price_total',
        'status',
        'note',
        'invoice',
    ];

    public const TRANSACTIONCODE ='INV';
    
    public const PAID = 'paid';
    public const UNPAID ='unpaid';

    public const CREATED ='created';
    public const CONFIRMED ='confirmed';
    public const PACKED ='packed';
    public const DELIVERED ='delivered';
    public const COMPLETED ='completed';
    public const CANCELLED ='cancelled';
    public const RETURNED ='returned';

    public const STATUSES =[
        self::CREATED =>'Created',
        self::CONFIRMED =>'Confirmed',
        self::PACKED =>'Packed',
        self::DELIVERED =>'Delivered',
        self::COMPLETED =>'Completed',
        self::CANCELLED =>'Cancelled',
        self::RETURNED => 'Returned',

    ];

    public static function generateCode()
    {
        $dateCode = self::TRANSACTIONCODE. '/'. date('Ymd'). '/'. General::integerToRoman(date('m')). '/'. General::integerToRoman(date('d')). '/';

        $lastTransaction = self::select([\DB::raw('MAX(transactions.code) AS last_code')])
            ->where('code', 'like', $dateCode. '%')
            ->first();

        $lastTransactionCode = !empty($lastTransaction) ? $lastTransaction['last_code'] : null;

        $transactionCode = $dateCode.'00001';
        if ($lastTransactionCode) {
            $lastTransactionNumber = str_replace ($dateCode, '', $lastTransactionCode);
            $nextTransactionNumber = sprintf('%05d', (int) $lastTransactionNumber + 1);

            $transactionCode = $dateCode. $nextTransactionNumber;
        }
        if (self::_isTransactionCodeExist($transactionCode)){
            // return generateTransactionCode();
        }

        return $transactionCode;
    }

    private static function _isTransactionCodeExist($transactionCode)
    {
        return Transaction::where('code', '=', $transactionCode)->exist();
    }

    public function isPaid()
    {
        return $this->status == self::PAID;
    }

    public function isCreated()
    {
        return $this->status ==self::CREATED;
    }

    public function isConfirmed()
    {
        return $this->status == self::CONFIRMED;
    }
    public function isPacked(){
        return $this->status == self::PACKED;
    }

    public function isDelivered()
    {
        return $this->status ==self::DELIVERED;
    }

    public function isCancelled()
    {
        return $this->status ==self::CANCELLED;
    }

    public function isReturned()
    {
        return $this->status ==self::RETURNED;
    }
    
    public function  user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function payment()
    {
        return $this->belongsTo(PaymentMethod::class,'pay_method_id','id');
    }
    public function details()
    {
        return $this->hasMany(TransactionDetail::class,'transaction_id', 'id');
    } 
    public function store()
    {
        return $this->belongsTo(Store::class,'store_id','id');
    }

}
