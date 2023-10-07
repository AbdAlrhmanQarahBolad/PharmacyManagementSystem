<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyInvoice extends Model
{
    use HasFactory;
    protected $guarded = [] ;
    public function InvoiceDetail()
    {
        return $this->hasMany(InvoiceDetail::class,'invoice_id');
    }
    public function WarehouseDispenser (){
        return $this->belongsTo(WarehouseDispenser::class,'warehouseDispenser_id');
    }
    public function  BuyOrder(){
        return $this->belongsTo(BuyOrder::class,'order_id');
    }
}
