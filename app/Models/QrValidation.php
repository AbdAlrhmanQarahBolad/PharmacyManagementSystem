<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class QrValidation extends Model
{
    use HasFactory;
    protected $guarded = [] ;
    public function setQrCodeAttribute($qr_code)
    {
        $this->attributes['qr_code'] = Hash::make($qr_code);
    }
    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class,'pharmacy_id');
    }
}
