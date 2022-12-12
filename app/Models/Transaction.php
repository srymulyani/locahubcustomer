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
        'buyer_id',
        'address_id',
        'code',
        'note',
        'grand_total',
        'payment_status',
        'snap_token',
    ];

    public const TRANSACTIONCODE ='INV';
    
    public const PAID = 'paid';
    public const UNPAID ='unpaid';

    public const CONFIRMED ='confirmed';
    public const PACKED ='packed';
    public const DELIVERED ='delivered';
    public const COMPLETED ='completed';
    public const CANCELLED ='cancelled';
    public const RETURNED ='returned';
    public const EXPIRED ='expired';

    public const STATUSES =[
        self::UNPAID =>'Unpaid',
        self::PAID => 'Paid',
        self::CONFIRMED =>'Confirmed',
        self::PACKED =>'Packed',
        self::DELIVERED =>'Delivered',
        self::COMPLETED =>'Completed',
        self::CANCELLED =>'Cancelled',
        self::RETURNED => 'Returned',
        self::EXPIRED => 'Expired',

    ];

    public static function generateCode()
    {
        $dateCode = self::TRANSACTIONCODE. '/'. date('Ymd'). '/'. General::integerToRoman(date('m')). '/'. General::integerToRoman(date('d')). '/';

        $lastTransaction = self::select([\DB::raw('MAX(transaction.code) AS last_code')])
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
            return generateTransactionCode();
        }

        return $transactionCode;
    }

    private static function _isTransactionCodeExist($transactionCode)
    {
        return Transaction::where('code', $transactionCode)->count() > 0;
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

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function store_transactions()
    {
        return $this->hasMany(StoreTransaction::class);
    }
}
