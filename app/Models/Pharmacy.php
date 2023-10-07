<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pharmacy extends Model
{
    use HasFactory;
    protected $guarded = [] ;
    protected $with = ['holiday'] ;
    public function medicines()
    {
        return $this->hasMany(PharmacyMedicine::class,'pharmacy_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function holiday()
    {
        return $this->belongsTo(WeekDay::class,'holiday_id');
    }
    public function location()
    {
        return $this->belongsTo(location::class,'location_id');
    }
    public function buyorder(){
        return $this->hasMany(BuyOrder::class);
    }
    public function QrValidation(){
        return $this->hasOne(QrValidation::class);
    }
    public function SaleInvoice(){
        return $this->hasMany(SaleInvoice::class);
    }
}
