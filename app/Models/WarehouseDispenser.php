<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseDispenser extends Model
{
    use HasFactory;
    protected $guarded = [] ;
    public function warehouse (){
        return $this->belongsTo(Warehouse::class,'warehouse_id');
    }
    public function user (){
        return $this->belongsTo(User::class,'user_id');
    }
    public function BuyInvoice(){
        return $this->hasMany(BuyInvoice::class);
    }
}
