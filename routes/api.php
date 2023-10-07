
<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\invoiceController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\SocialiteController;
use App\Models\City;
use App\Models\EmailValidation;
use App\Models\Pharmacy;
use App\Models\Pharmacy_admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageController;

use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseDispenser;
use App\Models\WarehouseEmployee;
use App\Models\WarehouseMedicine;
use App\Models\WeekDay;
use Symfony\Component\HttpFoundation\Response;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/






//to finish user authenication :
//make api for logout
//make api for refresh token
//return user info with : login,register,

Route::prefix('medicine')->group(function () {
    Route::get('/getMedicines',[MedicineController::class, 'get'])->middleware('auth:api');
   // Route::get('/getMedicineInfo/{medicineName}',[MedicineController::class, 'getMedicineInfo']);
});


Route::prefix('user')->group(function () {
    // don't forget to run the schedule in the command line
    Route::get('/get', function (Request $request) {
        $user = $request->user();
        $user = User::with('pharmacies','warehouses')->find($user->id);
        return $user;
    })->middleware('auth:api');
    Route::post('/checkCode', [UserController::class, 'checkCode'])->middleware('guest:api');
    Route::patch('/forgotPass', [UserController::class, 'forgotPass'])->middleware('guest:api');
    // Route::post('/resetPass', [UserController::class, 'resetPass'])->middleware('auth:api');
    Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:api');
    Route::post('/refresh', [UserController::class, 'refresh'])->middleware('guest:api');
    Route::post('/login', [UserController::class, 'login'])->middleware('guest:api');
    Route::post('/register', [UserController::class, 'create'])->middleware('guest:api');
    Route::post('/sendMeEmail', [UserController::class, 'sendEmail'])->middleware('guest:api');
    //Route::post('/validatePharmacy', [UserController::class, 'validatePharmacy']);

});
Route::prefix('pharmacy')->group(function () {

    Route::post('/closest', [PharmacyController::class, 'closest'])->middleware('auth:api');
    Route::post('/register', [PharmacyController::class, 'create'])->middleware('auth:api');
    Route::get('/orders/{pharmacy_id}',[PharmacyController::class, 'pharmacyOrders'])->middleware('auth:api');
    Route::get('/getSimilar/{pharmacymedicine_id}',[PharmacyController::class, 'getSimilar'])->middleware('auth:api');
    Route::get('/showMedicines/{pharmacy_id}',[PharmacyController::class, 'showMedicines']);
    Route::get('/show',function () {
        $pharmacies=Pharmacy::with('user')->get();
        return  response()->json(['pharmacies' => $pharmacies], 200);
    });
    Route::post('/editMedicine',[PharmacyController::class, 'editMedicine'])->middleware('auth:api');
    Route::post('/destroyMedicines',[PharmacyController::class, 'destroyMedicines'])->middleware('auth:api');
    Route::get('/existMedicines/{pharmacy_id}',[PharmacyController::class, 'existMedicines'])->middleware('auth:api');
    Route::get('/NotExistMedicines/{pharmacy_id}',[PharmacyController::class, 'NotExistMedicines'])->middleware('auth:api');
    //Route::get('/showMedicines/{pharmacy_id}',[PharmacyController::class, 'showMedicines']);
    Route::post('/generateQr',[PharmacyController::class, 'generateQr'])->middleware('auth:api');
});
Route::prefix('warehouse')->group(function () {
    Route::post('/register', [WarehouseController::class, 'create'])->middleware('auth:api');
    Route::get('/show',function (Request $request) {
        return Warehouse::with('user')->get();
    });
    Route::post('/editMedicine',[WarehouseController::class, 'editMedicine'])->middleware('auth:api');
    Route::get('/showMedicines/{warehouse_id}',[WarehouseController::class, 'showMedicines']);
    Route::get('/existMedicines/{warehouse_id}',[WarehouseController::class, 'existMedicines']);
    Route::get('/wharehouseWhichHaveMedicine',[WarehouseController::class, 'wharehouseWhichHaveMedicine'])->middleware('auth:api');
    Route::get('/orders/{warehouse_id}',[WarehouseController::class, 'warehouseOrders'])->middleware('auth:api');
    Route::post('/createOffer', [WarehouseController::class, 'createOffer'])->middleware('auth:api');
    Route::delete('/deleteOffer', [WarehouseController::class, 'deleteOffer'])->middleware('auth:api');
    Route::post('/createLoad', [WarehouseController::class, 'createLoad'])->middleware('auth:api');
    Route::delete('/deleteLoad', [WarehouseController::class, 'deleteLoad'])->middleware('auth:api');
    Route::get('/showOffers/{warehouseMedicine_id}', [WarehouseController::class, 'showOffers']);
    Route::get('/showLoads/{warehouseMedicine_id}', [WarehouseController::class, 'showLoads']);

    //  Route::get('/showMedicines/{warehouse_id}',[WarehouseController::class, 'showMedicines']);
    Route::post('/makeEmployee',[WarehouseController::class, 'makeEmployee'])->middleware('auth:api');
    Route::post('/makeDispenser',[WarehouseController::class, 'makeDispenser'])->middleware('auth:api');

    Route::get('/jobWarehouses', [WarehouseController::class, 'jobWarehouses'])->middleware('auth:api');
    Route::get('/getDispensers/{warehouse_id}', [WarehouseController::class, 'getDispensers'])->middleware('auth:api');
});

Route::prefix('order')->group(function () {
    Route::post('/register', [invoiceController::class, 'createOrder'])->middleware('auth:api');
    Route::post('/createInvoice', [invoiceController::class, 'createInvoice'])->middleware('auth:api');
    Route::post('/createDetail', [invoiceController::class, 'createOrderDetail'])->middleware('auth:api');
    Route::post('/showDetails', [invoiceController::class, 'showOrderDetail'])->middleware('auth:api');
    Route::get('/showDispenserInvoicesWhichDone', [invoiceController::class, 'showDispenserInvoicesWhichDone'])->middleware('auth:api');
    Route::get('/showDispenserInvoices', [invoiceController::class, 'showDispenserInvoices'])->middleware('auth:api');
    Route::post('/showDispenserInvoicesDetails', [invoiceController::class, 'showDispenserInvoicesDetails'])->middleware('auth:api');
    Route::post('/ConfirmPaymentAndReceipt', [invoiceController::class, 'ConfirmPaymentAndReceipt'])->middleware('auth:api');
    Route::post('/createSalesInvoice', [invoiceController::class, 'createSalesInvoice'])->middleware('auth:api');
    Route::post('/readBarcode', [invoiceController::class, 'readBarcode'])->middleware('auth:api');

});



//api for processing google access token and check it
Route::post('/googlelogin/callback', [SocialiteController::class, 'handleGoogleCallback']);

Route::prefix('admin')->middleware(['auth:api', 'admin'])->group(function () {
    Route::post('/validatePharmacy', [AdminController::class, 'validatePharmacy']);
    Route::post('/validateWarehouse', [AdminController::class, 'validateWarehouse']);
    Route::delete('/deletePharmacy', [AdminController::class, 'deletePharmacy']);
    Route::delete('/deleteWarehouse', [AdminController::class, 'deleteWarehouse']);
    Route::get('/showNotValidatedPharmacies', [AdminController::class, 'showNotValidatedPharmacies']);
    Route::get('/showNotValidatedWarehouses', [AdminController::class, 'showNotValidatedWarehouses']);
    Route::post('/createActiveMat', [AdminController::class, 'createActiveMat'])->middleware('auth:api');
    Route::post('/createCompany', [AdminController::class, 'createCompany'])->middleware('auth:api');
    Route::post('/createMedicine', [AdminController::class, 'createMedicine'])->middleware('auth:api');
    Route::post('/makeAdmin', [AdminController::class, 'makeAdmin']);
    Route::post('/updateAllMedicine', [AdminController::class, 'updateAllMedicine'])->middleware('auth:api');
 });

 Route::get('/places',function (Request $request) {
   return City::with('areas')->get();
 });
 Route::get('/days',function (Request $request) {
   return WeekDay::all();
 });

//messages:
Route::post('/send/from/{u1}/to/{u2}',[MessageController::class,'create'])->middleware('auth:api');
Route::get('messages/of/{u1}/with/{u2}',[MessageController::class,'get'])->middleware('auth:api');
Route::get('contacts/of/{u1}',[UserController::class,'getContacts'])->middleware('auth:api');


// api for testing:
    Route::post('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:api');







