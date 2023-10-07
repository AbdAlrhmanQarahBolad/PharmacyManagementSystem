<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    protected $guarded = [] ;
    protected $with = ['area'];
    public function pharmacy()
    {
        return $this->hasMany(Pharmacy::class);
    }
    public function warehouse()
    {
        return $this->hasMany(Warehouse::class);
    }
    public function area()
    {
        return $this->belongsTo(Area::class,'area_id');
    }
}

