<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class EmailValidation extends Model
{
    use HasFactory;
    protected $guarded = [] ;

    public function setCodeAttribute($code)
    {
        $this->attributes['code'] = Hash::make($code);
    }
}
