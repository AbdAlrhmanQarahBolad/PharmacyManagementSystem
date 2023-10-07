<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    use HasFactory;
    protected $guarded = [] ;
    public function BuyInvoice (){
        return $this->belongsTo(BuyInvoice::class,'invoice_id');
    }
    public function warehouseMedicine (){
        return $this->belongsTo(WarehouseMedicine::class,'warehouseMedicine_id');
    }
}
