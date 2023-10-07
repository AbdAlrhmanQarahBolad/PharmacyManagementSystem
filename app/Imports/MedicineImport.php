<?php

namespace App\Imports;

use App\Models\Medicine;
use Maatwebsite\Excel\Concerns\ToModel;

class MedicineImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Medicine([
            "barcode" => $row[0],
            "trade_name_en" => $row[1],
            "medicine_form_ar" => $row[2],
            "size" => $row[4],
            "company_id"=>$row[5],
            "commercial_price" => $row[6],
            "net_price" => $row[7],
            "trade_name_ar" => $row[8],
            "medicine_form_en" => $row[9],
        ]);
    }
}
