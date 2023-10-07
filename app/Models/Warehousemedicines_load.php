<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehousemedicines_load extends Model
{
    use HasFactory;
    protected $guarded = [] ;
    public function WarehouseMedicine()
    {
        return $this->belongsTo(WarehouseMedicine::class,'warehousemedicine_id');
    }
    public function looad()
    {
        return $this->belongsTo(WarehouseMedicine::class,'load_id');
    }
    public function OrderDetail()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
