<?php

namespace App\Http\Controllers;

use App\Imports\MedicineImport;
use App\Models\ActiveMat;
use App\Models\Company;
use App\Models\Medicine;
use App\Models\MedMat;
use App\Models\Pharmacy;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public function makeAdmin(Request $request)
    {
        request()->validate([
            'email' => 'required',
        ]);
        $user = $request->user();
        if ($user->admin_level !== 2)
            return response()->json(['message' => 'You are not authorized'], 403);
        $user = User::firstWhere('email', request('email'));
        if (!$user)
            return response()->json(['message' => 'invalid email'], 400);
        if ($user->admin_level)
            return response()->json(['message' => 'user is already an admin'], 200);
        $user->update(['admin_level' => 1]);
        return $user;
    }
    public function validatePharmacy(Request $request)
    {
        $validator = Validator::make($request->only('id', 'owner_of_Permission_name'), [
            'id' => ['required'],
            'owner_of_Permission_name' => ['required', 'string'],
        ]);
        if ($validator->fails())
            return response()->json($validator->errors(), 422);

        $pharmacy = Pharmacy::find($request->id);
        $pharmacy->update([
            'owner_of_Permission_name' => $request->owner_of_Permission_name,
            'validated' => true
        ]);
        return $pharmacy;
    }
    public function validateWarehouse(Request $request)
    {
        $validator = Validator::make($request->only('id', 'owner_of_Permission_name'), [
            'id' => ['required'],
            'owner_of_Permission_name' => ['required', 'string'],
        ]);
        if ($validator->fails())
            return response()->json($validator->errors(), 422);

        $warehouse = Warehouse::find($request->id);
        $warehouse->update([
            'owner_of_Permission_name' => $request->owner_of_Permission_name,
            'validated' => true
        ]);
        return $warehouse;
    }
    public function deletePharmacy(Request $request)
    {
        $validator = Validator::make($request->only('id'), [
            'id' => ['required']
        ]);
        if ($validator->fails())
            return response()->json($validator->errors(), 422);

        Pharmacy::find($request->id)->delete();
        return  ['message' => 'pharmacy has been deleted'];
    }
    public function deleteWarehouse(Request $request)
    {
        $validator = Validator::make($request->only('id'), [
            'id' => ['required']
        ]);
        if ($validator->fails())
            return response()->json($validator->errors(), 422);

        Warehouse::find($request->id)->delete();
        return  ['message' => 'warehouse has been deleted'];
    }
    public function showNotValidatedPharmacies(Request $request)
    {
        $pharmacies = Pharmacy::with(['location', 'location.area', 'location.area.city'])->where('validated', false)->get();

        return response()->json([
            'pharmacies' => $pharmacies,
        ], 200);
    }
    public function showNotValidatedWarehouses(Request $request)
    {
        $warehouses = Warehouse::with(['location', 'location.area', 'location.area.city'])->where('validated', false)->get();

        return response()->json([
            'warehouses' => $warehouses,
        ], 200);
    }
    public function createActiveMat(Request $request)
    {
        $user = $request->user();
        $validator = Validator::make($request->only('name_ar','name_en'), [
            'name_ar' => ['required', 'string'],
            'name_en' => ['required', 'string'],
        ]);
        if ($validator->fails())
            return response()->json($validator->errors(), 422);
        if ($user->admin_level == 2 || $user->admin_level == 1) {
            ActiveMat::create(request(['name_ar','name_en']));
            return  ['message' => 'active material has been created'];
        } else {
            return response()->json(['message' => 'unauthorized'], 400);
        }
    }
    public function createCompany(Request $request)
    {
        $user = $request->user();
        $validator = Validator::make($request->only('name_en', 'name_ar'), [
            'name_ar' => ['required', 'string'],
            'name_en' => ['required', 'string'],
        ]);
        if ($validator->fails())
            return response()->json($validator->errors(), 422);
        if ($user->admin_level == 2 || $user->admin_level == 1) {

            Company::create(request(['name_ar', 'name_en']));
            return  ['message' => 'Company has been created'];
        } else {
            return response()->json(['message' => 'unauthorized'], 400);
        }
    }
    public function createMedicine(Request $request)
    {
        $user = $request->user();
        request()->validate([
            'barcode'=>'nullable|string',
            'trade_name_ar' => 'required|string',
            'trade_name_en' => 'required|string',
            'description_en' => 'required|string',
            'description_ar' =>  'required|string',
            'medicine_form_ar' => 'required|string',
            'medicine_form_en' =>  'required|string',
            'commercial_price' => 'required|numeric',
            'net_price' =>  'required|numeric',
            'size' =>  'required',
            'parts' =>  'nullable|numeric',
            'company_id' => 'exists:App\Models\Company,id',
            'img' => 'nullable|mimes:jpeg,jpg,png',
            'active_mats'=>'array' ,
            'active_mats.*'=>'array',
            'active_mats.*.active_mat_en' => 'required|string',
            'active_mats.*.active_mat_ar' =>  'required|string',
            'active_mats.*.concentration' =>  'required|numeric',
        ]);

        if ($user->admin_level == 2 || $user->admin_level == 1) {
            $request['medicine_photo_path']=null ;
            if ($request->img){
            $extension = $request->file('img')->getClientOriginalExtension();
            $id=Medicine::max('id');
            $imgpath = $request->file('img')->storeAs('medicinesPhotos',1+$id.'.'.$extension,'mypublic');
            $request['medicine_photo_path'] = $imgpath;}
            $med = Medicine::create([
                'barcode'=>$request->barcode,
                'trade_name_ar' => $request->trade_name_ar,
                'trade_name_en' => $request->trade_name_en,
                'description_en' => $request->description_en,
                'description_ar' => $request->description_ar,
                'medicine_form_ar' =>  $request->meicine_form_ar,
                'medicine_form_en' =>   $request->meicine_form_en,
                'commercial_price' => $request->commercial_price,
                'medicine_photo_path'=>$request->medicine_photo_path,
                'size' => $request->size,
                'parts' => $request->parts,
                'net_price' => $request->net_price,
                'company_id' => $request->company_id,
            ]);
            foreach($request->active_mats as $key =>$value){
                $activeMat = ActiveMat::firstOrCreate(
                    ['name_en' => $value['active_mat_en']],
                    ['name_ar' => $value['active_mat_ar']]
                );
                MedMat::firstOrCreate([
                    'concentration' => $value['concentration'],
                    'med_id' => $med->id,
                    'active_id' => $activeMat->id,
                ]);
            }

            return  ['message' => 'Medicine has been created'];
        } else {
            return response()->json(['message' => 'unauthorized'], 400);
        }
    }
    public function updateAllMedicine(Request $request){
        $user = $request->user();

        request()->validate([
            'file' => 'required|mimes:csv,xlsx',
        ]);

        if ($user->admin_level == 2 || $user->admin_level == 1) {

            DB::statement("SET foreign_key_checks=0");
            Medicine::truncate();
            DB::statement("SET foreign_key_checks=1");
            Excel::import(new MedicineImport,$request->file);
            return  ['message' => 'medicines now in database'];

        }
        else{
            return response()->json(['message' => 'unauthorized'], 400);
        }

    }
}
