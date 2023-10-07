<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleInvoiceDetail extends Model
{
    use HasFactory;
    protected $guarded = [] ;
    public function pharmacyMedicine()
    {
        return $this->belongsTo(PharmacyMedicine::class,'pharmacyMedicine_id');
    }
    public function SaleInvoice()
    {
        return $this->belongsTo(SaleInvoice::class,'sale_invoice_id');
    }
}
