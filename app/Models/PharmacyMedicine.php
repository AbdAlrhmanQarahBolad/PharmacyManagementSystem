<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyMedicine extends Model
{
    use HasFactory;
    protected $guarded = [] ;
    public function scopeFilter($query, array $filters)
    {
        $query->when(
            $filters['search'] ?? false,
            fn ($query, $search) =>
            $query->whereHas(
                'medicine',
                fn ($query) => $query->where('trade_name_ar', 'like', '%' . $search . '%')
                ->orWhere('trade_name_en','like','%'.$search.'%')
            )
        );
    }
    public function medicine()
    {
        return $this->belongsTo(Medicine::class,'medicine_id');
    }
    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class,'pharmacy_id');
    }
    public function SaleInvoiceDetail(){
        return $this->hasMany(SaleInvoiceDetail::class);
    }
}
