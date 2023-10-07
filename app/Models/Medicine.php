<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;
    protected $guarded = [] ;
    public function scopeFilter($query, array $filters)
    {
        $query->when(
            $filters['search'] ?? false,
            fn ($query, $search) =>
            $query->where('trade_name_ar', 'like', '%' . $search . '%')->orWhere('trade_name_en','like','%'.$search.'%')
        );
    }
    public function warehouse()
    {
        return $this->hasMany(Warehouse::class);
    }
    public function company (){
        return $this->belongsTo(Company::class,'company_id');
    }
    public function warehousemedicine()
    {
        return $this->hasMany(WarehouseMedicine::class);
    }
}
