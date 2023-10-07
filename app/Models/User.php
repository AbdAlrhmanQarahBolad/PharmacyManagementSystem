<?php

namespace App\Models;

//use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Http\Controllers\SocialiteController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [] ;
    //protected $with = ['pharmacies','warehouses'];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function scopeFilter($query,$id)
    {
        $query->whereHas('sentMessages',
        fn($query)=>$query->where('from',$id)
        );
        //don't forget to order them!
    }
    public function sentMessages()
    {
      return $this->hasMany(Message::class, 'from');
    }

    public function receivedMessages()
    {
      return $this->hasMany(Message::class, 'to');
    }
    public function pharmacies()
    {
        return $this->hasMany(Pharmacy::class);
    }
    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }
    public function validateForPassportPasswordGrant(string $password): bool
    {
        $condition = false;
        if(request()->has('google_access_token'))
        $condition = app(SocialiteController::class)->check(request('google_access_token'));
        if (!$condition) {
            return Hash::check($password, $this->password);
        } else {
            return true;
        }
    }
    public function warehouseDispenser()
    {
        return $this->hasOne(WarehouseDispenser::class);
    }
}
