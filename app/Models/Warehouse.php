<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;
    protected $guarded = [] ;
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function location()
    {
        return $this->belongsTo(location::class,'location_id');
    }
    public function buyorder(){
        return $this->hasMany(BuyOrder::class);
    }
    public function warehouseEmployee(){
        return $this->hasMany(WarehouseEmployee::class);
    }
    public function warehouseDispenser(){
        return $this->hasMany(WarehouseDispenser::class);
    }
}
