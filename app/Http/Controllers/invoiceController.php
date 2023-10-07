<?php

namespace App\Http\Controllers;

use App\Models\BuyInvoice;
use App\Models\BuyOrder;
use App\Models\InvoiceDetail;
use App\Models\Medicine;
use App\Models\Offer;
use App\Models\OrderDetail;
use App\Models\Pharmacy;
use App\Models\PharmacyMedicine;
use App\Models\QrValidation;
use App\Models\SaleInvoice;
use App\Models\SaleInvoiceDetail;
use App\Models\Warehouse;
use App\Models\WarehouseDispenser;
use App\Models\WarehouseEmployee;
use App\Models\WarehouseMedicine;
use App\Models\Warehousemedicines_load;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class invoiceController extends Controller
{
    public function createOrder(Request $request)
    {
        $user = $request->user();
        request()->validate([
            'date' => 'required',
            'warehouse_id' => 'required|numeric|min:1|max:' . Warehouse::max('id'),
            'pharmacy_id' => 'required|numeric|min:1|max:' . Pharmacy::max('id'),
            'medicines' => 'array',
            'medicines.*' => 'array' ,
            'medicines.*.quantity' => 'required|numeric',
            'medicines.*.offer_id' => 'nullable|numeric|min:1|max:' . Offer::max('id'),
            'medicines.*.load_id' => 'nullable|numeric|min:1|max:' . Warehousemedicines_load::max('id'),

        ]);
        $request['warehousemedicines_ids'] = array_keys($request->medicines );
        $keys=array_keys($request->medicines );
        $after_checking=array_unique($keys);
        if(!(array_diff($keys, $after_checking) == [])){
            return  response()->json(['message' => 'you cant order the same medicine twice'], 400);

        }
        request()->validate([
            'warehousemedicines_ids' => 'exists:App\Models\WarehouseMedicine,id',
        ]);
        $pharmacy = Pharmacy::find($request->pharmacy_id);
        if ($pharmacy->user_id == $user->id) {

            $request['state'] = 0;
            $order = BuyOrder::create(request(['date', 'warehouse_id', 'pharmacy_id', 'state']));
            // $order=BuyOrder::with(['warehouse','pharmacy'])->find($order->id) ;

            foreach ($request->medicines as $key => $value) {
                $warehouseMedicine=WarehouseMedicine::find($key) ;
                if ($warehouseMedicine->warehouse_id!=$request['warehouse_id']){
                    $order->delete();
                    return  response()->json(['message' => 'medicine has to be exist in warehouse'], 400);
                }
                if ($warehouseMedicine->max_quantity< $value['quantity']){
                    $order->delete();
                    return  response()->json(['message' => 'quantity has to be smaller or equal to max quantity'], 400);
                }
                //$request['received_amounts'] = 0;
                OrderDetail::create(
                    [
                        'quantity' => $value['quantity'],
                        //'received_amounts' => $request->received_amounts,
                        'order_id' => $order->id,
                        'offer_id' => $value['offer_id'],
                        'load_id' => $value['load_id'],
                        'warehouseMedicine_id' => $key,
                    ]
                );
            }

            return  ['message' => 'your order has been created'];
        } else {
            return response()->json(['message' => 'unauthorized'], 400);

        }
    }
    /*
    public function createOrderDetail(Request $request)
    {
        $user = $request->user();
        request()->validate([
            'pharmacy_id' => 'required|numeric|min:1|max:' . Pharmacy::max('id'),
            'quantity' => 'required',
            'order_id' => 'required|numeric|min:1|max:' . BuyOrder::max('id'),
            'warehouseMedicine_id' => 'required|numeric|min:1|max:' . WarehouseMedicine::max('id'),
            'offer_id' => 'nullable|numeric|min:1|max:' . Offer::max('id'),
            'load_id' => 'nullable|numeric|min:1|max:' . Warehousemedicines_load::max('id'),
        ]);
        $pharmacy = Pharmacy::find($request->pharmacy_id);
        if ($pharmacy->user_id == $user->id) {
            $request['received_amounts'] = 0;
            OrderDetail::create(request(['quantity', 'received_amounts', 'order_id', 'warehouseMedicine_id', 'offer_id', 'load_id']));
            return  ['message' => 'your order detail has been added'];
        } else {
            return response()->json(['message' => 'unauthorized'], 400);
        }
    }*/
    public function showOrderDetail(Request $request)
    {
        $user = $request->user();
        request()->validate([
            'order_id' => ' required|numeric|exists:App\Models\BuyOrder,id',
        ]);

        $order = BuyOrder::find($request['order_id']);
        $warehouse = Warehouse::find($order->warehouse_id);
        $pharmacy = Pharmacy::find($order->pharmacy_id);
        if(($user->id == $warehouse->user_id) || ($user->id == $pharmacy->user_id) || ($user->admin_level==-1))
        {
            $orderDetails = OrderDetail::where('order_id', $request->order_id)->with(['warehouseMedicine.medicine.company', 'offer', 'loadQuantity.looad.medicine.company'])->get();
            return response()->json(['orderDetails' => $orderDetails], 200);
        }
        else {
            return response()->json(['message' => 'unauthorized'], 400);
        }
    }
    public function createInvoice (Request $request){
        $user = $request->user();
        request()->validate([
            'date_of_invoice' => 'required',
            'order_id' => 'required|numeric|min:1|max:' . BuyOrder::max('id'),
            'warehouseDispenser_id' => 'required|numeric|exists:App\Models\WarehouseDispenser,id',

            'invoice_medicines' => 'array',
            'invoice_medicines.*' => 'array' ,
            'invoice_medicines.*.quantity' => 'required|numeric',
            'invoice_medicines.*.offer_id' => 'nullable|numeric',
            'invoice_medicines.*.load_id' => 'nullable|numeric|min:1|max:' . Warehousemedicines_load::max('id'),
        ]);
        $request['invoice_medicines_ids'] = array_keys($request->invoice_medicines );
        request()->validate([
            'invoice_medicines_ids' => 'exists:App\Models\WarehouseMedicine,id',
        ]);
        $order=BuyOrder::find($request->order_id);
        if ($order->state!=0){
            return response()->json(['message' => 'the invoice of this order is created in the past'], 400);
        }
        $warehouse=Warehouse::find($order->warehouse_id);
        if (($user->admin_level==-1 )|| ($warehouse->user_id==$user->id)){
            $invoice=BuyInvoice::create([
                'total_price'=>0 ,
                'date_of_invoice'=>$request->date_of_invoice ,
                'state'=>false ,
                'warehouseDispenser_id'=>$request->warehouseDispenser_id ,
                'order_id'=>$request->order_id ,
            ]);
            $totalPrice=0;
            foreach ($request->invoice_medicines as $key => $value) {
               // $request['received_amounts'] = 0;
                $offer=Offer::find($value['offer_id']);
                $load=Warehousemedicines_load::find($value['load_id']);
                if ($offer){
                    $off=$offer->free_quantity ;
                    $demand=$offer->demand_quantity ;
                }else {
                    $off=0;
                    $demand= $value['quantity'];
                }
                if ($load){
                    $medicine=Medicine::whereRelation('warehousemedicine', 'id', '=',  $load->load_id)->first();
                    $price=$medicine['net_price']*$load->load_quantity ;
                    InvoiceDetail::create(
                        [
                            'price'=>$price ,
                            'quantity' => $load->load_quantity,
                            'additional' => 0,
                            'invoice_id' => $invoice->id,
                            'warehouseMedicine_id' => $load->load_id,
                        ]
                    );
                    $totalPrice+=$medicine['net_price']*$load->load_quantity ;
                }
                $mm=Medicine::whereRelation('warehousemedicine', 'id', '=',  $key)->first();
                InvoiceDetail::create(
                    [
                        'price'=>$mm['net_price']*$demand ,
                        'quantity' => $demand,
                        'additional' => $off,
                        'invoice_id' => $invoice->id,
                        'warehouseMedicine_id' => $key,
                    ]
                );
                $totalPrice+=$mm['net_price']*$demand ;
            }
            $invoice->total_price=$totalPrice;
            $invoice->save();
            $order->state=1;
            $order->save();
            return  ['message' => 'your invoice has been created'];
        }else {
            return response()->json(['message' => 'unauthorized'], 400);
        }
    }
    public function showDispenserInvoicesWhichDone (Request $request){
        $user = $request->user();
        if ($user->admin_level==-2){
            return response()->json(['Dispenserinvoices' => BuyInvoice::where('state',1)->whereRelation('WarehouseDispenser.user','id','=',$user->id)->with('BuyOrder.pharmacy')->get()],200);
            //whereRelation('WarehouseDispenser.user','id','=',$user->id)->with('InvoiceDetail','BuyOrder.pharmacy')->get()], 200);
        }else {
            return response()->json(['message' => 'unauthorized'], 400);
        }
    }
    public function showDispenserInvoices (Request $request){
        $user = $request->user();
        if ($user->admin_level==-2){
            return response()->json(['Dispenserinvoices' => BuyInvoice::where('state',0)->whereRelation('WarehouseDispenser.user','id','=',$user->id)->with('BuyOrder.pharmacy')->get()],200);
            //whereRelation('WarehouseDispenser.user','id','=',$user->id)->with('InvoiceDetail','BuyOrder.pharmacy')->get()], 200);
        }else {
            return response()->json(['message' => 'unauthorized'], 400);
        }
    }
    public function showDispenserInvoicesDetails (Request $request){
        $user = $request->user();
        request()->validate([
            'invoice_id' => 'required|numeric|min:1|max:' . BuyInvoice::max('id'),
        ]);
        if($user->admin_level==-2){
            return response()->json(['DispenserInvoicesDetails' => InvoiceDetail::where('invoice_id', $request->invoice_id)->with(['warehouseMedicine.medicine'])->get()],200);
        }else {
            return response()->json(['message' => 'unauthorized'], 400);
        }
    }
    public function ConfirmPaymentAndReceipt (Request $request){
        $user = $request->user();
        request()->validate([
            'qr_code' => 'required|numeric',
            'invoice_id'=> 'required|numeric|min:1|max:' . BuyInvoice::max('id'),
        ]);
        if($user->admin_level==-2){
        $invoice=BuyInvoice::find($request->invoice_id);
        if ($invoice->state){
            return response()->json(['message' => 'invoice is confirmed in the past'], 400);
        }
        $order=BuyOrder::find($invoice['order_id']);
        $pharmacy=Pharmacy::find($order->pharmacy_id);
        $rec = QrValidation::firstWhere('pharmacy_id', $pharmacy->id);
        if (!$rec){
            $res = ['message' => 'َQR code is Ungenerated'];
            $st = 400;
            return response()->json($res, $st);
        }
        $res = '';
        $st = 0;
        if ( !Hash::check($request->qr_code, $rec->qr_code)) {
            $res = ['message' => 'code is invalid'];
            $st = 400;
            $rec->delete();
        }  else{
            $res = ['message' => 'confirmed successfully'];
            $st = 200;
            $invoice->state=1;
            $invoice->save();
            $rec->delete();
            $details=InvoiceDetail::where('invoice_id',$invoice->id)->get();
            foreach ($details as $key => $value){
                $warehouseMedicine=WarehouseMedicine::find($value->warehouseMedicine_id);
                $pharmacyMedicine=PharmacyMedicine::where('pharmacy_id',$pharmacy->id)->where('medicine_id',$warehouseMedicine->medicine_id)->first();
                $pharmacyMedicine->quantity += $value->quantity +$value->additional ;
                $pharmacyMedicine->save();
            }
        }
        return response()->json($res, $st);
        } else {
                return response()->json(['message' => 'unauthorized'], 400);
            }
    }
    public function createSalesInvoice(Request $request){
        $user = $request->user();
        request()->validate([
            'date' => 'required',
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
            $saleInvoice=SaleInvoice::create([
                'date'=>$request->date,
                'pharmacy_id'=>$request->pharmacy_id,
                'total_price'=>0,
            ]);
            $temp=0;
            foreach ($request->medicines as $key => $value) {
                $PharmacyMedicine=PharmacyMedicine::find($key) ;
                if ($PharmacyMedicine->pharmacy_id!=$request['pharmacy_id']){
                    $saleInvoice->delete();
                    return  response()->json(['message' => 'medicine has to be exist in pharmacy'], 400);
                }
                if ($PharmacyMedicine->quantity< $value['quantity']){
                    $saleInvoice->delete();
                    return  response()->json(['message' => 'quantity has to be smaller or equal to max quantity'], 400);
                }
                //$request['received_amounts'] = 0;

                $medicine=Medicine::find($PharmacyMedicine->medicine_id);
                $price=($medicine->commercial_price)*($value['quantity']);
                SaleInvoiceDetail::create(
                    [
                        'quantity' => $value['quantity'],
                        'sale_invoice_id' => $saleInvoice->id,
                        'price_of_quantity' =>$price,
                        'pharmacyMedicine_id' => $key,
                    ]
                );
                $PharmacyMedicine->quantity-=$value['quantity'] ;
                $PharmacyMedicine->save();
                $temp+=$price;
            }
            $saleInvoice->total_price=$temp ;
            $saleInvoice->save();
            return  ['message' => 'your sale invoice has been created'];
        }
        else {
            return response()->json(['message' => 'unauthorized'], 400);
        }

    }
    public function readBarcode(Request $request){
        $user = $request->user();
        request()->validate([
            'barcode' => 'required',
            'pharmacy_id' => 'required|numeric|exists:App\Models\Pharmacy,id',
        ]);
        $pharmacy=Pharmacy::find($request->pharmacy_id);
        if ($pharmacy->user_id==$user->id){
            $pharmacyMedicine=PharmacyMedicine::where('quantity','>',0)->whereRelation('medicine','barcode','=',$request->barcode)->with('medicine.company')->get();
            if (count($pharmacyMedicine) == 0){
                return response()->json(['message' =>'The medicine is not exist' ], 400);
            }


            return response()->json(['pharmacyMedicine' =>$pharmacyMedicine ], 200);
        }
        else {
            return response()->json(['message' => 'unauthorized'], 400);
        }
    }
}
