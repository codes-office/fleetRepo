<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Expense;
use App\Model\FuelModel;
use App\Model\VehicleModel;
use App\Model\Vendor;
use Auth;
use Validator;
use Illuminate\Support\Str;
use App\Model\Hyvikk;
class FuelApiController extends Controller
{
    public function fuelList(Request $request)
    {
       // dd($request->all());  
        $validation = Validator::make($request->all(), [
            'user_id' => 'required|integer',

        ]);
        $errors = $validation->errors();

        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } 
        else {
           // dd(Auth::user());
           // dd(auth()->guard('api')->user());
            if (auth()->guard('api')->user()->user_type == "S" || auth()->guard('api')->user()->user_type != "D") {
                if (auth()->guard('api')->user()->group_id == null) {
                    $vehicle_ids = VehicleModel::pluck('id')->toArray();
                } else {
                    $vehicle_ids = VehicleModel::where('group_id', auth()->guard('api')->user()->group_id)->pluck('id')->toArray();
                }
            }
            if (auth()->guard('api')->user()->user_type == "D") {
                // $vehicle = DriverLogsModel::where('driver_id',Auth::user()->id)->get()->toArray();
                // $vehicle_ids = VehicleModel::where('id', $vehicle[0]['vehicle_id'])->pluck('id')->toArray();
                $vehicle_ids = auth()->user()->vehicles()->pluck('vehicles.id')->toArray();
            }    
            $records = array();
            $FuelList= FuelModel::whereIn('vehicle_id', $vehicle_ids);
            //dd($FuelList);
            if (isset($request->timestamp)) {
                $time = date('Y-m-d H:i:s', strtotime($request->timestamp));
                $FuelList = $FuelList->where('updated_at', ">", $time);
            }
            $Fuel_List = $FuelList->orderBy('id','DESC')->withTrashed()->get();
            $currency=Hyvikk::get('currency');

            if(count($Fuel_List)> 0)
            {
                foreach ($Fuel_List as $row) {
                    $total = $row->qty * $row->cost_per_unit;
                    $cost = $currency.' '.$total??'0';

                    $records[] = array(
                        'fuel_id'=>$row->id,
                        'image'=> asset('uploads/'.$row->vehicle_data['vehicle_image'])??asset(" assets/images/vehicle.jpeg"),
                        'vehicle_name'=>$row->vehicle_data->make_name.' - '.$row->vehicle_data->model_name,
                        'cost'=>$cost,
                        'license_plate'=>$row->vehicle_data->license_plate,
                        'fuel_from'=>$row->fuel_from,
                        'start_meter' => $row->start_meter,
                        'end_meter' => $row->end_meter,
                        'reference' => $row->reference, 
                        'province' => $row->province, 
                        'note' => $row->note, 
                        'qty' => $row->qty,
                        'date' => $row->date, 
                        'timestamp' => date('Y-m-d H:i:s', strtotime($row->updated_at)),
                        "delete_status" => (isset($row->deleted_at)) ? 1 : 0,
                    );
                }
                $data['success'] = "1";
                $data['message'] = "Data fetched";
                $data['data'] = $records;
            }
            else{
                $data['success'] = "0";
                $data['message'] = "No Records Found";
                //$data['data'] = $records;
            }
           
        }
        return $data;
    }
    public function fuelList123(Request $request)
    {
       // dd($request->all());  
        $validation = Validator::make($request->all(), [
            'user_id' => 'required|integer',

        ]);
        $errors = $validation->errors();

        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } 
        else {
           // dd(Auth::user());
           // dd(auth()->guard('api')->user());
            if (auth()->guard('api')->user()->user_type == "S" || auth()->guard('api')->user()->user_type != "D") {
                if (auth()->guard('api')->user()->group_id == null) {
                    $vehicle_ids = VehicleModel::pluck('id')->toArray();
                } else {
                    $vehicle_ids = VehicleModel::where('group_id', auth()->guard('api')->user()->group_id)->pluck('id')->toArray();
                }
            }
            if (auth()->guard('api')->user()->user_type == "D") {
                // $vehicle = DriverLogsModel::where('driver_id',Auth::user()->id)->get()->toArray();
                // $vehicle_ids = VehicleModel::where('id', $vehicle[0]['vehicle_id'])->pluck('id')->toArray();
                $vehicle_ids = auth()->user()->vehicles()->pluck('vehicles.id')->toArray();
            }    
            $records = array();
            $FuelList= FuelModel::where('vehicle_id', $vehicle_ids);
            //dd($FuelList);
            if (isset($request->timestamp)) {
                $time = date('Y-m-d H:i:s', strtotime($request->timestamp));
                $FuelList = $FuelList->where('updated_at', ">", $time);
            }
            $Fuel_List = $FuelList->orderBy('id','DESC')->withTrashed()->get();
            $currency=Hyvikk::get('currency');
            dd($Fuel_List);
            if(count($Fuel_List)> 0)
            {
                foreach ($Fuel_List as $row) {
                    $total = $row->qty * $row->cost_per_unit;
                    $cost = $currency.' '.$total??'0';

                    $records[] = array(
                        'fuel_id'=>$row->id,
                        'image'=> asset('uploads/'.$row->vehicle_data['vehicle_image'])??asset(" assets/images/vehicle.jpeg"),
                        'vehicle_name'=>$row->vehicle_data->make_name.' - '.$row->vehicle_data->model_name,
                        'cost'=>$cost,
                        'license_plate'=>$row->vehicle_data->license_plate,
                        'fuel_from'=>$row->fuel_from,
                        'start_meter' => $row->start_meter,
                        'end_meter' => $row->end_meter,
                        'reference' => $row->reference, 
                        'province' => $row->province, 
                        'note' => $row->note, 
                        'qty' => $row->qty,
                        'date' => $row->date, 
                        'timestamp' => date('Y-m-d H:i:s', strtotime($row->updated_at)),
                        "delete_status" => (isset($row->deleted_at)) ? 1 : 0,
                    );
                }
                $data['success'] = "1";
                $data['message'] = "Data fetched";
                $data['data'] = $records;
            }
            else{
                $data['success'] = "0";
                $data['message'] = "No Records Found";
                //$data['data'] = $records;
            }
           
        }
        return $data;
    }

    public function storeFuel(Request $request)
    {
       // dd($request->all());  
        $validation = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'vehicle_id' =>'required|integer'

        ]);
        $errors = $validation->errors();

        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        }
        else {

            $fuel = new FuelModel();
            $fuel->vehicle_id = $request->get('vehicle_id');
            $fuel->user_id = $request->get('user_id');
            $condition = FuelModel::orderBy('id', 'desc')->where('vehicle_id', $request->get('vehicle_id'))->first();
            // dd($condition->qty);
            if ($condition != null) {

                $fuel->start_meter = $request->get('start_meter');
                $fuel->end_meter = "0";
                $fuel->consumption = "0";
                $condition->end_meter = $end = $request->get('start_meter');
                // dd($condition->end_meter);
                // $fuel->start_meter = $start = $request->get('start_meter');
                // dd($condition->start_meter);
                // dd($end); //value fetched
                if ($request->get('qty') == 0) {
                    $condition->consumption = $con = 0;
                } else {
                    $condition->consumption = $con = ($end - $condition->start_meter) / $condition->qty;
                }
                // dd($con);
                $condition->save();

            } else {

                $fuel->start_meter = $request->get('start_meter');
                $fuel->end_meter = "0";
                $fuel->consumption = "0";

            }
            $fuel->reference = $request->get('reference');
            $fuel->province = $request->get('province');
            $fuel->note = $request->get('note');
            $fuel->qty = $request->get('qty');
            $fuel->fuel_from = $request->get('fuel_from');
            $fuel->vendor_name = $request->get('vendor_name');
            $fuel->cost_per_unit = $request->get('cost_per_unit');
            $fuel->date = $request->get('date');
            $fuel->complete = $request->get("complete");

            $file = $request->file('image');
            if ($file && $file->isValid()) {
                $destinationPath = './uploads'; // upload path
                $extension = $file->getClientOriginalExtension();

                $fileName1 = Str::uuid() . '.' . $extension;

                $file->move($destinationPath, $fileName1);

                $fuel->image = $fileName1;
            }

            $fuel->save();

            $expense = new Expense();
            $expense->vehicle_id = $request->get('vehicle_id');
            $expense->user_id = $request->get('user_id');
            $expense->expense_type = '8';
            $expense->comment = $request->get('note');
            $expense->date = $request->get('date');
            $amount = $request->get('qty') * $request->get('cost_per_unit');
            $expense->amount = $amount;
            $expense->exp_id = $fuel->id;
            $expense->save();
            VehicleModel::where('id', $request->vehicle_id)->update(['mileage' => $request->start_meter]);

            $records = array();
            $FuelList= FuelModel::orderBy('id','desc')->first();
            //dd($FuelList);
            $currency=Hyvikk::get('currency');
            $total = $FuelList->qty * $FuelList->cost_per_unit;
            $cost = $currency.' '.$total??'0';
            $records[] = array(
                'fuel_id' => $FuelList->id,
                'vehicle_id' => $FuelList->vehicle_id, 
                'start_meter' => $FuelList->start_meter,
                'end_meter' => $FuelList->end_meter,
                'reference' => $FuelList->reference, 
                'province' => $FuelList->province, 
                'note' => $FuelList->note, 
                'qty' => $FuelList->qty,
                'fuel_from'=>$FuelList->fuel_from,
                'cost'=>$cost,
                'date' => $FuelList->date, 
                'timestamp' => date('Y-m-d H:i:s', strtotime($FuelList->updated_at)),
                "delete_status" => (isset($FuelList->deleted_at)) ? 1 : 0,
            );
            $data['success'] = "1";
            $data['message'] = "Fuel Added Succesfully";
            $data['data'] = $records;
        }
        return $data;
    }

    public function deleteFuel(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'fuel_id' => 'required|integer',
        ]);
        $errors = $validation->errors();
       
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {

            $fuel = FuelModel::find($request->fuel_id);

            if (!is_null($fuel->image) && file_exists('uploads/' . $fuel->image)) {
                unlink('uploads/' . $fuel->image);
            }
    
            $fuel->delete();
    
            Expense::where('exp_id', $request->get('id'))->where('expense_type', 8)->delete();
            $data['success'] = "1";
            $data['message'] = "Fuel deleted successfully!";
            $data['data'] = array('id' => $request->fuel_id, 'timestamp' => date('Y-m-d H:i:s'));
        }
        return $data;
    }

    public function editFuel(Request $request)
    {
       // dd($request->all());
        $validation = Validator::make($request->all(), [
            'fuel_id'=>'required|integer'

        ]);
        $errors = $validation->errors();

        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {

            $data['data'] = $fuel = FuelModel::whereId($request->fuel_id)->first();
           // dd($data);
            $data['vehicle_id'] = $fuel->vehicle_id;
            $data['vendors'] = Vendor::where('type', 'fuel')->get();

            $records[] = array(
                'fuel_id' => $fuel->id,
                'vehicle_id' => $fuel->vehicle_id, 
                'start_meter' => $fuel->start_meter,
                'end_meter' => $fuel->end_meter,
                'reference' => $fuel->reference, 
                'province' => $fuel->province, 
                'note' => $fuel->note, 
                'qty' => $fuel->qty,
                'date' => $fuel->date, 
                'timestamp' => date('Y-m-d H:i:s', strtotime($fuel->updated_at)),
                "delete_status" => (isset($fuel->deleted_at)) ? 1 : 0,
            );

            $data['success'] = "1";
            $data['message'] = "Fuel fetched successfully!";
            $data['data'] = $records;
        }
        return $data;
    }

    public function updateFuel(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'vehicle_id' =>'required|integer'

        ]);
        $errors = $validation->errors();

        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {

            $fuel = FuelModel::find($request->fuel_id);
            // $form_data = $request->all();
            $old = FuelModel::where('vehicle_id', $fuel->vehicle_id)->where('end_meter', $fuel->start_meter)->first();
            if ($old != null) {
                $old->end_meter = $request->get('start_meter');
                $old->consumption = ($old->end_meter - $old->start_meter) / $old->qty;
                $old->save();
            }
           
            $fuel->start_meter = $request->get('start_meter');
            $fuel->reference = $request->get('reference');
            $fuel->province = $request->get('province');
            $fuel->note = $request->get('note');
            $fuel->qty = $request->get('qty');
            $fuel->fuel_from = $request->get('fuel_from');
            $fuel->vendor_name = $request->get('vendor_name');
            $fuel->cost_per_unit = $request->get('cost_per_unit');
            $fuel->date = $request->get('date');
            $fuel->complete = $request->get("complete");
            if ($fuel->end_meter != 0) {
                $fuel->consumption = ($fuel->end_meter - $request->get('start_meter')) / $request->get('qty');
            }
    
            $file = $request->file('image');
            if ($file && $file->isValid()) {
                $destinationPath = './uploads'; // upload path
                $extension = $file->getClientOriginalExtension();
    
                $fileName1 = Str::uuid() . '.' . $extension;
    
                $file->move($destinationPath, $fileName1);
    
                $fuel->image = $fileName1;
            }
    
            $fuel->save();
            $exp = Expense::where('exp_id', $request->get('id'))->where('expense_type', 8)->first();
            if ($exp != null) {
                $exp->amount = $request->get('qty') * $request->get('cost_per_unit');
                $exp->save();
            }
            VehicleModel::where('id', $request->vehicle_id)->update(['mileage' => $request->start_meter]);
            $currency=Hyvikk::get('currency');
            $total = $fuel->qty * $fuel->cost_per_unit;
            $cost = $currency.' '.$total??'0';
            $records[] = array(
                'fuel_id' => $fuel->id,
                'vehicle_id' => $fuel->vehicle_id, 
                'start_meter' => $fuel->start_meter,
                'end_meter' => $fuel->end_meter,
                'reference' => $fuel->reference, 
                'province' => $fuel->province, 
                'note' => $fuel->note, 
                'fuel_from'=>$fuel->fuel_from,
                'cost'=>$cost,
                'qty' => $fuel->qty,
                'date' => $fuel->date, 
                'timestamp' => date('Y-m-d H:i:s', strtotime($fuel->updated_at)),
                "delete_status" => (isset($fuel->deleted_at)) ? 1 : 0,
            );

            $data['success'] = "1";
            $data['message'] = "Fuel Updated successfully!";
            $data['data'] = $records;
        }
        return $data;
    }
}
