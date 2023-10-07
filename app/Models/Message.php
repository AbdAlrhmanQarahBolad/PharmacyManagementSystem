<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Message extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function scopeFilter($query,$id1,$id2)
    {
        $query->where(
            fn($query)=>
            $query->where(
                fn($query)=>
                $query->where('from',$id1)
                ->where('to',$id2)
            )
            ->orWhere(
                fn($query)=>
                $query->where('from',$id2)
                ->where('to',$id1)
            )
            );
    }
    public function from()
    {
        return $this->belongsTo(User::class,'from');
    }
    public function to()
    {
        return $this->belongsTo(User::class,'to');
    }
}
