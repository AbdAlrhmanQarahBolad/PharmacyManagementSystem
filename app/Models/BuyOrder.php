<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyOrder extends Model
{
    use HasFactory;
    protected $guarded = [] ;
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class,'warehouse_id');
    }
    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class,'pharmacy_id');
    }
    public function OrderDetail(){
        return $this->hasMany(OrderDetail::class);
    }
    public function BuyInvoice(){
        return $this->hasMany(BuyInvoice::class);
    }
}
