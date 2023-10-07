<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\BuyOrder;
use App\Models\Location;
use App\Models\Medicine;
use App\Models\OrderDetail;
use App\Models\Pharmacy;
use App\Models\Pharmacy_admin;
use App\Models\PharmacyMedicine;
use App\Models\QrValidation;
use App\Models\Role;
use App\Models\User;
use DateTime;
use DateTimeZone;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PharmacyController extends Controller
{

    private function toRadians($degree)
{
    // cmath library in C++
    // defines the constant
    // M_PI as the value of
    // pi accurate to 1e-30
    $one_deg = pi() / 180;
    return ($one_deg * $degree);
}

private function distance($lat1, $long1,
                     $lat2, $long2)
{
    // Convert the latitudes
    // and longitudes
    // from degree to radians.
    $lat1 = $this->toRadians($lat1);
    $long1 = $this->toRadians($long1);
    $lat2 = $this->toRadians($lat2);
    $long2 = $this->toRadians($long2);

    // Haversine Formula
    $dlong = $long2 - $long1;
    $dlat = $lat2 - $lat1;

    $ans = pow(sin($dlat / 2), 2) +
                          cos($lat1) * cos($lat2) *
                          pow(sin($dlong / 2), 2);

    $ans = 2 * asin(sqrt($ans));

    // Radius of Earth in
    // Kilometers, R = 6371
    // Use R = 3956 for miles
    $R = 6371;

    // Calculate the result
    $ans = $ans * $R;

    return $ans;
}

    public function closest(Request $request)
    {
        $date = new DateTime();
        $dayName = date_format($date,"D");
        $timeNow = (idate('H'))*60 + idate('i');
        request()->validate([
            'start' => 'required',
            'medicine_id' => 'required|numeric|min:1|max:'. Medicine::max('id'),
        ]);
        $mn = 100000000;
        $selectedPhar = -1;
        $con = false;
        $pharmacies = Pharmacy::whereHas('medicines',
        fn ($query) => $query->where('medicine_id', $request['medicine_id'])
        )->where('to_min','>=',$timeNow)->where('from_min','<=',$timeNow)
        ->whereHas('holiday',
        fn ($query) => $query->where('name','!=', $dayName)
        )->get();
        $arr = explode(",",$request['start']);
        $lat = $arr[0];
        $lon = $arr[1];
        foreach($pharmacies as $pharmacy)
        {
            $dist = $this->distance($lat,$lon,$pharmacy->latitude,$pharmacy->longitude);
            if($dist<$mn)
            {
                $mn = $dist;
                $con = true;
                $selectedPhar = $pharmacy;
            }
        }
        if(!$con)
        return response()->json(['message'=>'there is no pharmacy'],200);

        $returnJson['pharmacy'] = $selectedPhar;
        $returnJson['coordinates'] = [];
        return $returnJson;
    }
    public function create(Request $request)
    {

        $user = $request->user();
        $request['user_id'] = $user->id;
        request()->validate([
            'pharmacyName' => 'required',
            'number' => 'required|unique:pharmacies,number',
            'longitude' => 'required',
            'latitude' => 'required',
            'img' => 'required|mimes:jpeg,jpg,png',
            'area_id' =>'required|numeric|min:1|max:'. Area::max('id'),
            'location_desc'=>'required',
            'holiday_id' => 'required|numeric|min:1|max:7',
            'from_min' => 'required|numeric|min:0|max:1439',
            'to_min' => 'required|numeric|min:'.$request['from_min'].'|max:1439',
            // 'location_id' =>'required',
        ]);
        $extension = $request->file('img')->getClientOriginalExtension();
        $id=Pharmacy::max('id');
        $imgpath = $request->file('img')->storeAs('pharmaciesPhotos',1+$id.'.'.$extension,'mypublic');
        $request['path_of_photo'] = $imgpath;
        //deActivate previous job whereEver it was
        // $tableName = ucfirst(Role::firstWhere('id',$user->role));
        // DB::table($tableName)->where('user_id', $user->id)->where('active',1)->update(['active'=>0]);
        $location = Location::create(request(['area_id','location_desc']));
        $request['location_id'] = $location->id;
        $pharmacy=Pharmacy::create(request(['user_id','pharmacyName', 'number','path_of_photo', 'longitude', 'latitude','location_id','holiday_id','from_min','to_min']));

        $medicines = Medicine::all();
        foreach ($medicines as $medicine){
            PharmacyMedicine::create([
                'quantity'=>0 ,
               // 'net_price'=> 0,
                //'commercial_price'=> 0,
                'pharmacy_id'=>$pharmacy->id,
                'medicine_id'=>$medicine->id,
                ]);
        }

        $user = User::with('pharmacies','warehouses')->find($user->id);
        return $user;
    }
    /*
    public function addMedicine(Request $request)
    {
        $user = $request->user();
        $request['user_id'] = $user->id;
        $pharmacy = Pharmacy::find($request->pharmacy_id);
        if ($pharmacy->user_id == $user->id) {
            request()->validate([
                'max_quantity' => 'required|numeric',
                'price' => 'required|numeric',
                'pharmacy_id' => 'required|numeric|min:1|max:' . Pharmacy::max('id'),
                'medicine_id' => 'required|numeric|min:1|max:' . Medicine::max('id'),
            ]);
            $pharmacyMedicine=PharmacyMedicine::create(request(['max_quantity', 'price', 'pharmacy_id', 'medicine_id']));
            $pharmacyMedicine=PharmacyMedicine::where('id',$pharmacyMedicine->id)->with(['medicine'])->get() ;
            return  ['message' => 'your medicine has been added', 'medicine' =>$pharmacyMedicine];
        } else {
            return response()->json(['message' => 'unauthorized'], 400);
        }
    }*/
    public function pharmacyOrders(Request $request,$pharmacy_id){
        $user = $request->user();
        $pharmacy = Pharmacy::find($pharmacy_id);
        if(!$pharmacy){
            return response()->json(['message' => 'not exist'], 400);
        }
        if ($pharmacy->user_id == $user->id) {
            $pharmacyOrders=BuyOrder::where('pharmacy_id',$pharmacy_id)->with(['warehouse'])->get();
          //  $pharmacyOrders=OrderDetail::with(['BuyOrder','BuyOrder.warehouse','warehouseMedicine','warehouseMedicine.medicine'])->whereRelation('BuyOrder', 'pharmacy_id', '=',$pharmacy_id )->get();
            return response()->json(['pharmacyOrders' => $pharmacyOrders], 200);
        } else {
            return response()->json(['message' => 'unauthorized'], 400);
        }
    }
    public function existMedicines($pharmacy_id){
    $pharmacy= Pharmacy::find($pharmacy_id);
    if (!$pharmacy){
        return response()->json(['message' => 'not exist'], 400);
    }
        $pharmacyMedicines=PharmacyMedicine::filter(request(['search']))->where('pharmacy_id',$pharmacy_id)->where('quantity','>',0)->with('medicine.company')->get();
        return   response()->json(['pharmacyMedicines' => $pharmacyMedicines], 200);
    }
    /*
    public function showMedicines($pharmacy_id){
        $pharmacyMedicines=PharmacyMedicine::where('pharmacy_id',$pharmacy_id)->where('quantity','>',0)->with('medicine')->get();
        return  $pharmacyMedicines;
    }*/
    public function showMedicines($pharmacy_id){
        $pharmacy=Pharmacy::find($pharmacy_id);
        if (!$pharmacy){
            return   response()->json(['message' => 'not exist'], 400);
        }
        $pharmacyMedicines=PharmacyMedicine::filter(request(['search']))->where('pharmacy_id',$pharmacy_id)->with('medicine.company')->get();
        return   response()->json(['pharmacyMedicines' => $pharmacyMedicines], 200);
    }
    public function editMedicine(Request $request)
    {
        $user = $request->user();
        request()->validate([

            'quantity' => 'required|numeric',
            //'net_price' => 'numeric',
            //'commercial_price' => 'numeric',

            'pharmacyMedicine_id' => 'required|numeric|min:1|max:' . PharmacyMedicine::max('id'),

        ]);

        $pharmacyMedicine = PharmacyMedicine::find($request->pharmacyMedicine_id);
        $pharmacy = Pharmacy::find($pharmacyMedicine->pharmacy_id);
        if ($pharmacy->user_id == $user->id) {
            $pharmacyMedicine->quantity=$request->quantity ;
            $pharmacyMedicine->save();


            return  ['message' => 'your medicine has been edited'];
        } else {
            return response()->json(['message' => 'unauthorized'], 400);
        }
    }
    public function generateQr(Request $request){
        $user = $request->user();
        request()->validate([
            'pharmacy_id' => 'required|numeric|min:1|max:' . Pharmacy::max('id'),
        ]);
        $pharmacy = Pharmacy::find($request->pharmacy_id);
        if ($pharmacy->user_id == $user->id) {
            $toBeSentString = '';
            for ($i = 0; $i < 6; $i++)
                $toBeSentString .= rand(0, 9);

                $newData = [
                    'pharmacy_id' => $request->pharmacy_id,
                    'qr_code' => $toBeSentString
                ];
                $rec = QrValidation::firstWhere('pharmacy_id', $request->pharmacy_id);
                if ($rec)
                    $rec->update($newData);
                else
                QrValidation::create($newData);
                return $toBeSentString;
        } else {
            return response()->json(['message' => 'unauthorized'], 400);
        }
    }
    public function getSimilar(Request $request,$pharmacymedicine_id){
        $user = $request->user();
        $PharmacyMedicine = PharmacyMedicine::find($pharmacymedicine_id);
        $medicine=Medicine::find($PharmacyMedicine->medicine_id);
        $pharmacy=Pharmacy::find($PharmacyMedicine->pharmacy_id);
        if ($pharmacy->user_id == $user->id) {
            $alternatives=PharmacyMedicine::where('id','!=',$PharmacyMedicine->id)->where('quantity','>','0')->where('pharmacy_id',$pharmacy->id)->whereRelation('medicine','description_ar','=',$medicine->description_ar)->with('medicine.company')->get();
            return response()->json(['alternatives' => $alternatives], 200);
        }
        else {
            return response()->json(['message' => 'unauthorized'], 400);
        }
    }
    public function destroyMedicines(Request $request){
            $user = $request->user();
            request()->validate([
                'pharmacy_id' => 'required|numeric|exists:App\Models\Pharmacy,id',
                'medicines' => 'array',
                'medicines.*' => 'array' ,
                'medicines.*.quantity' => 'required|numeric',
            ]);
            $pharmacy=Pharmacy::find($request->pharmacy_id);
            $request['pharmacymedicines_ids'] = array_keys($request->medicines );
            $keys=array_keys($request->medicines );
            $after_checking=array_unique($keys);
            if(!(array_diff($keys, $after_checking) == [])){
                return  response()->json(['message' => 'you cant put the same medicine twice'], 400);
            }
            request()->validate([
                'pharmacymedicines_ids' => 'exists:App\Models\PharmacyMedicine,id',
            ]);
            if ($pharmacy->user_id==$user->id){
                $temp=0;
                foreach ($request->medicines as $key => $value) {
                    $PharmacyMedicine=PharmacyMedicine::find($key) ;
                    if ($PharmacyMedicine->pharmacy_id!=$request['pharmacy_id']){
                        return  response()->json(['message' => 'medicine has to be exist in pharmacy'], 400);
                    }
                    if ($PharmacyMedicine->quantity< $value['quantity']){
                        return  response()->json(['message' => 'quantity has to be smaller or equal to max quantity'], 400);
                    }
                    $PharmacyMedicine->quantity-=$value['quantity'] ;
                    $PharmacyMedicine->save();
                }
                return  ['message' => 'your Medicines have been destroyed'];
            }
            else {
                return response()->json(['message' => 'unauthorized'], 400);
            }
    }

    public function NotExistMedicines($pharmacy_id){
        $pharmacy= Pharmacy::find($pharmacy_id);
        if (!$pharmacy){
            return response()->json(['message' => 'not exist'], 400);
        }
            $pharmacyMedicines=PharmacyMedicine::filter(request(['search']))->where('pharmacy_id',$pharmacy_id)->where('quantity','=',0)->with('medicine.company')->get();
            return   response()->json(['pharmacyMedicines' => $pharmacyMedicines], 200);
        }
}





