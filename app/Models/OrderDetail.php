<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    protected $guarded = [] ;
    public function BuyOrder()
    {
        return $this->belongsTo(BuyOrder::class,'order_id');
    }
    public function warehouseMedicine()
    {
        return $this->belongsTo(WarehouseMedicine::class,'warehouseMedicine_id');
    }
    public function offer()
    {
        return $this->belongsTo(Offer::class,'offer_id');
    }
    public function loadQuantity()
    {
        return $this->belongsTo(Warehousemedicines_load::class,'load_id');
    }
}
