<?php

namespace App\Http\Controllers;

use App\Events\NewWarehouse;
use App\Events\NewWarehouseMedicine;
use App\Models\Area;
use App\Models\BuyOrder;
use App\Models\Location;
use App\Models\Medicine;
use App\Models\Offer;
use App\Models\OrderDetail;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseDispenser;
use App\Models\WarehouseEmployee;
use App\Models\WarehouseMedicine;
use App\Models\Warehousemedicines_load;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function create(Request $request)
    {

        $user = $request->user();
        $request['user_id'] = $user->id;
        request()->validate([
            'warehouseName' => 'required|unique:warehouses,warehouseName',
            'number' => 'required|unique:pharmacies,number',
            'img' => 'required|mimes:jpeg,jpg,png',
            'area_id' => 'required|numeric|min:1|max:' . Area::max('id'),
            'location_desc' => 'required',
            // 'longitude' => 'required',
            // 'latitude' => 'required',
            // 'location_id' =>'required',
        ]);
        $extension = $request->file('img')->getClientOriginalExtension();
        $id = Warehouse::max('id');
        $imgpath = $request->file('img')->storeAs('WarehousesPhotos', 1 + $id . '.' . $extension, 'mypublic');
        $request['path_of_photo'] = $imgpath;
        //deActivate previous job whereEver it was
        // $tableName = ucfirst(Role::firstWhere('id',$user->role));
        // DB::table($tableName)->where('user_id', $user->id)->where('active',1)->update(['active'=>0]);
        $location = Location::create(request(['area_id', 'location_desc']));
        $request['location_id'] = $location->id;
        $warehouse=Warehouse::create(request(['user_id', 'warehouseName', 'path_of_photo', 'number', 'location_id']));
        event(new NewWarehouse($warehouse));
        $medicines = Medicine::all();
        foreach ($medicines as $medicine){
            WarehouseMedicine::create([
                'max_quantity'=>0 ,
                'warehouse_id'=>$warehouse->id,
                'medicine_id'=>$medicine->id,
                ]);
        }

        $user = User::with('pharmacies', 'warehouses')->find($user->id);
        return $user;
    }
/*
    public function addMedicine(Request $request)
    {
        $user = $request->user();
        $warehouse = Warehouse::find($request->warehouse_id);
        if ($warehouse->user_id == $user->id) {
            request()->validate([
                'medicines'=>'array',

                'max_quantity' => 'required|numeric',
                'price' => 'required|numeric',
                'warehouse_id' => 'required|numeric|min:1|max:' . Warehouse::max('id'),
                'medicine_id' => 'required|numeric|min:1|max:' . Medicine::max('id'),

            ]);
            foreach($request->medicines as $key =>$value){
            WarehouseMedicine::create([
                'max_quantity'=>$value['max_quantity'],
                'price'=>$value['price'],
                'warehouse_id'=>$value['warehouse_id'],
                'medicine_id'=>$value['medicine_id'],
                ]);
            }
            return  ['message' => 'your medicines have been added'];
        } else {
            return response()->json(['message' => 'unauthorized'], 400);
        }
    }*/
    public function existMedicines($warehouse_id){
        $warehouseMedicines=WarehouseMedicine::filter(request(['search']))->where('warehouse_id',$warehouse_id)->where('max_quantity','>',0)->with('medicine.company','Offer','warehousemedicinesload.looad.medicine.company')->get();
        return   response()->json(['warehouseMedicines' => $warehouseMedicines], 200);
    }
    public function showMedicines($warehouse_id){
        $warehouse=Warehouse::find($warehouse_id);
        if (!$warehouse){
            return   response()->json(['message' => 'not exist'], 400);
        }
            $warehouseMedicines=WarehouseMedicine::filter(request(['search']))->where('warehouse_id',$warehouse_id)->with('medicine.company','Offer','warehousemedicinesload.looad.medicine.company')->get();
            return  $warehouseMedicines;
        }
    public function warehouseOrders(Request $request,$warehouse_id){
        $user = $request->user();
        $warehouse = Warehouse::find($warehouse_id);
        if(!$warehouse){
            return response()->json(['message' => 'not exist'], 400);
        }
        if ($warehouse->user_id == $user->id || $user->admin_level==-1) {
            $warehouseOrders=BuyOrder::where('warehouse_id',$warehouse_id)->where('state',0)->with(['pharmacy'])->get();
         //   $warehouseOrders=OrderDetail::with(['BuyOrder','BuyOrder.pharmacy','warehouseMedicine','warehouseMedicine.medicine'])->whereRelation('BuyOrder', 'warehouse_id', '=',$warehouse_id )->get();
            return response()->json(['warehouseOrders' => $warehouseOrders], 200);
        } else {
            return response()->json(['message' => 'unauthorized'], 400);
        }
    }



    public function editMedicine(Request $request)
    {
        $user = $request->user();
        request()->validate([

            'max_quantity' => 'required|numeric',
            'warehouseMedicine_id' => 'required|numeric|min:1|max:' . WarehouseMedicine::max('id'),

        ]);

        $WarehouseMedicine = WarehouseMedicine::find($request->warehouseMedicine_id);
        $warehouse = Warehouse::find($WarehouseMedicine->warehouse_id);
        if ($warehouse->user_id == $user->id) {
            $WarehouseMedicine->max_quantity=$request->max_quantity ;
            $WarehouseMedicine->save();
            $WarehouseMedicine =  WarehouseMedicine::with(['warehouse','medicine'])->where('id',$WarehouseMedicine->id)->first();

        event(new NewWarehouseMedicine($WarehouseMedicine));


            return  ['message' => 'your medicine has been edited'];
        } else {
            return response()->json(['message' => 'unauthorized'], 400);
        }
    }
    public function createOffer (Request $request){
        $user = $request->user();
        request()->validate([
            'demand_quantity' => 'required|numeric',
            'free_quantity' => 'required|numeric',
            'warehouseMedicine_id' => 'required|numeric|min:1|max:' . WarehouseMedicine::max('id'),
        ]);
        $warehouseMedicine=WarehouseMedicine::find($request->warehouseMedicine_id);
        $warehouse=Warehouse::find($warehouseMedicine->warehouse_id);
        if ($warehouse->user_id == $user->id) {

            Offer::create([
                'demand_quantity'=>$request['demand_quantity'],
                'free_quantity'=>$request['free_quantity'],
                'warehouseMedicine_id'=>$request['warehouseMedicine_id'],
                ]);


            return  ['message' => 'your offer has been created'];
        } else {
            return response()->json(['message' => 'unauthorized'], 400);
        }
    }
    public function deleteOffer(Request $request){
        $user = $request->user();
        request()->validate([
            'offer_id' => 'required|numeric|exists:App\Models\Offer,id'
        ]);
        $offer=Offer::find($request->offer_id);
        $warehouseMedicine=WarehouseMedicine::find($offer->warehousemedicine_id);
        $warehouse=Warehouse::find($warehouseMedicine->warehouse_id);
        if ($warehouse->user_id == $user->id) {

            Offer::find($request->offer_id)->delete();
            return  ['message' => 'your offer has been deleted'];
        } else {
            return response()->json(['message' => 'unauthorized'], 400);
        }
    }
    public function createLoad (Request $request){
        $user = $request->user();
        request()->validate([
            'load_quantity' => 'required|numeric',
            'load_id' => 'required|numeric|min:1|max:' . WarehouseMedicine::max('id'),
            'warehouseMedicine_id' => 'required|numeric|min:1|max:' . WarehouseMedicine::max('id'),
        ]);
        $warehouseMedicine=WarehouseMedicine::find($request->warehouseMedicine_id);
        $warehouse=Warehouse::find($warehouseMedicine->warehouse_id);
        if ($warehouse->user_id == $user->id) {

            Warehousemedicines_load::create([
                'load_quantity'=>$request['load_quantity'],
                'load_id'=>$request['load_id'],
                'warehouseMedicine_id'=>$request['warehouseMedicine_id'],
                ]);


            return  ['message' => 'your Load has been created'];
        } else {
            return response()->json(['message' => 'unauthorized'], 400);
        }
    }
    public function deleteLoad(Request $request){
        $user = $request->user();
        request()->validate([
            'Warehousemedicines_load_id' => 'required|numeric|exists:App\Models\Warehousemedicines_load,id'
        ]);
        $Warehousemedicines_load=Warehousemedicines_load::find($request->Warehousemedicines_load_id);
        $warehouseMedicine=WarehouseMedicine::find($Warehousemedicines_load->warehousemedicine_id);
        $warehouse=Warehouse::find($warehouseMedicine->warehouse_id);
        if ($warehouse->user_id == $user->id) {

            Warehousemedicines_load::find($request->Warehousemedicines_load_id)->delete();
            return  ['message' => 'your Load has been deleted'];
        } else {
            return response()->json(['message' => 'unauthorized'], 400);
        }
    }
    public function showOffers ($warehouseMedicine_id){
        $offers=Offer::where('warehouseMedicine_id',$warehouseMedicine_id)->get();
        return $offers ;
    }
    public function showLoads ($warehouseMedicine_id){
        $loads=Warehousemedicines_load::where('warehouseMedicine_id',$warehouseMedicine_id)->with('looad.medicine')->get();
        return $loads ;
    }
    public function makeEmployee (Request $request){
        $user = $request->user();
        request()->validate([

            'warehouse_id' => 'required|numeric|min:1|max:' . Warehouse::max('id'),
            'employeeEmail' => 'required|exists:App\Models\User,email',
        ]);
        $warehouse=Warehouse::find($request->warehouse_id);
        if ($warehouse->user_id == $user->id) {
            $employee=User::where('email',$request->employeeEmail)->first();
            if (WarehouseEmployee::where('user_id',$employee->id)->exists()){
                return  response()->json(['message' => 'Employee should be work in one warehouse'], 400);
            }
            WarehouseEmployee::create([
                'warehouse_id'=>$request['warehouse_id'],
                'user_id'=>$employee->id,
            ]);
            $employee->admin_level=-1;
            $employee->save();
            return  ['message' => 'Employee has been created'];
        } else {
            return response()->json(['message' => 'unauthorized'], 400);
        }
    }
    public function makeDispenser (Request $request){
        $user = $request->user();
        request()->validate([
            'warehouse_id' => 'required|numeric|min:1|max:' . Warehouse::max('id'),
            'dispenserEmail' => 'required|exists:App\Models\User,email',
        ]);
        $warehouse=Warehouse::find($request->warehouse_id);
        if ($warehouse->user_id == $user->id  ) {
            $dispenser=User::where('email',$request->dispenserEmail)->first();
            if (WarehouseDispenser::where('user_id',$dispenser->id)->exists()){
                return  response()->json(['message' => 'dispenser should be work in one warehouse'], 400);
            }
            WarehouseDispenser::create([
                'warehouse_id'=>$request['warehouse_id'],
                'user_id'=>$dispenser->id,
            ]);
            $dispenser->admin_level=-2;
            $dispenser->save();
            return  ['message' => 'Dispenser has been created'];
        } else {
            return response()->json(['message' => 'unauthorized'], 400);
        }
    }
    public function jobWarehouses (Request $request){
        $user = $request->user();
        if ($user->admin_level==-1){
            return  response()->json(['jobwarehouses' => Warehouse::whereRelation('warehouseEmployee','user_id','=',$user->id)->get()], 200);

        }else if ($user->admin_level==-2){
            return response()->json(['jobwarehouses' =>  Warehouse::whereRelation('warehouseDispenser','user_id','=',$user->id)->get()], 200);
        }else {
            return response()->json(['message' => 'unauthorized'], 400);
        }
    }
    public function getDispensers (Request $request,$warehouse_id){
        $user = $request->user();
        $warehouse=Warehouse::find($warehouse_id);
        if(!$warehouse){
            return response()->json(['message' => 'not exist'], 400);
        }
        if (($user->admin_level==-1)  || ($warehouse->user_id==$user->id)){
            return response()->json(['dispensers' =>  WarehouseDispenser::where('warehouse_id',$warehouse_id)->with('user')->get()], 200);
        }else {
            return response()->json(['message' => 'unauthorized'], 400);
        }
    }
    public function wharehouseWhichHaveMedicine(){
        $wharehouseWhichHaveMedicine=WarehouseMedicine::filter(request(['search']))->where('max_quantity','>',0)->with('warehouse','medicine.company','Offer','warehousemedicinesload.looad.medicine.company')->get();
        return   response()->json(['warehouseMedicines' => $wharehouseWhichHaveMedicine], 200);
    }
}
