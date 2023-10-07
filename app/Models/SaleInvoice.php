<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleInvoice extends Model
{
    use HasFactory;
    protected $guarded = [] ;
    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class,'pharmacy_id');
    }
    public function SaleInvoiceDetail(){
        return $this->hasMany(SaleInvoiceDetail::class,'sale_invoice_id');
    }
}
