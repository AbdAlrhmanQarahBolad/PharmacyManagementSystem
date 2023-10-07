<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseMedicine extends Model
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
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class,'warehouse_id');
    }
    public function medicine()
    {
        return $this->belongsTo(Medicine::class,'medicine_id');
    }
    public function Offer()
    {
        return $this->hasMany(Offer::class,'warehousemedicine_id');
    }
    public function warehousemedicinesload()
    {
        return $this->hasMany(warehousemedicines_load::class,'warehousemedicine_id');
    }
    public function loadMedicine()
    {
        return $this->hasMany(warehousemedicines_load::class,'load_id');
    }
}
