<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    public function get()
    {
        return Medicine::filter(request(['search']))->get();
    }
    /*
    public function getMedicineInfo($medicineName){
        $medicine=Medicine::where('trade_name_ar',$medicineName)->orWhere('trade_name_en',$medicineName)->get();
        if(!$medicine){
            return  response()->json(['message' => 'there isnt medicine like this'], 400);
        }
        return  response()->json(['medicine' => $medicine], 200);
    }*/
}
