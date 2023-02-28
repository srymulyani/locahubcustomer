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
            return generateTransactionCode();
        }

        return $transactionCode;
    }

    private static function _isTransactionCodeExist($transactionCode)
    {
        return Transaction::where('code', $transactionCode)->count() > 0;
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

    public function items(){
        return $this->hasMany(StoreTransactionItem::class); 
    }
}
