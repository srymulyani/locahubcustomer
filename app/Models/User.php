<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Laravel\Jetstream\HasProfilePhoto;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\MailResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'phone_number',
        'ttl',
        'bio',
        'roles',
        'password',
        // 'isVerified',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function transactions ()
    {
        return $this->hasMany(Transaction::class,'user_id','id');
    }

    public function store()
    {
        return $this->hasOne(Store::class,'user_id','id');
    }

    public function address()
    {
        return $this->hasMany(Address::class, 'user_id', 'id');
    }

    public function products_rating()
    {
        return $this->hasMany(ProductRating::class,'user_id','id');
    }

    public function bank_account()
    {
        return $this->hasMany(BankAccount::class,'user_id','id');
    }
    // public function setPasswordAttribute($value){
    //     $this->attributes['password'] = Hash::make($value);
    // }
    public function sendPasswordResetNotification($token)
    {
        // $this->clientBaseUrl
        $url = 'https://spa.test/reset-password?token='.$token;

        $this->notify(new MailResetPasswordNotification($url));
    }

}
