<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ServiceReminder;
use App\Model\ServiceItemsModel;
use App\Model\ServiceReminderModel;
use App\Model\User;
use App\Model\VehicleModel;
use Hyvikk;
use Validator;
class ServiceReminderApiController extends Controller
{
    public function serviceReminderList(Request $request)
    {
       // dd($request->user_id);
        $validation = Validator::make($request->all(), [
            'user_id' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } 
        else
        {
            $user = User::find($request->user_id);
          //  dd($user);
            if ($user->group_id == null ||$user->user_type == "S") {
                $vehicle_ids = VehicleModel::pluck('id')->toArray();
            } else {
                $vehicle_ids = VehicleModel::where('group_id', $user->group_id)->pluck('id')->toArray();
            }
            $service_reminders = ServiceReminderModel::whereIn('vehicle_id', $vehicle_ids);  
            if (isset($request->timestamp)) {
                $time = date('Y-m-d H:i:s', strtotime($request->timestamp));
                $service_reminders = $service_reminders->where('updated_at', ">", $time);
            }
            $service_reminders = $service_reminders->orderBy('id','DESC')->withTrashed()->get();
            $date_format_setting=(Hyvikk::get('date_format'))?Hyvikk::get('date_format'):'d-m-Y';
            if(count($service_reminders)>0)
            {
                $records = array();
                foreach ($service_reminders as $service_reminder) {
                    //dd($row);
                    $interval = substr($service_reminder->services->overdue_unit,0,-3);
                    if($service_reminder->services->overdue_time != null)
                    {
                        $int = $service_reminder->services->overdue_time.$interval;
                    }
                    else
                    {
                        $int = Hyvikk::get('time_interval')."day";
                    }
                    if($service_reminder->last_date != 'N/D')
                    {
                        $date = date('Y-m-d', strtotime($int, strtotime($service_reminder->last_date)));
                    }
                   else
                    {
                        $date = date('Y-m-d', strtotime($int, strtotime(date('Y-m-d'))));
                    }
                    if($service_reminder->services->overdue_meter != null)
                    {
                        if($service_reminder->last_meter == 0)
                        {
                            $next_due_meter=$service_reminder->vehicle->int_mileage + $service_reminder->services->overdue_meter;
                        }
                        else
                        {
                            $next_due_meter=$service_reminder->last_meter + $service_reminder->services->overdue_meter;
                        } 
                    }
                    else
                    {
                        $next_due_meter=0;
                    }
                    $to = \Carbon\Carbon::now();
                    $from = \Carbon\Carbon::createFromFormat('Y-m-d', $date);
                    $diff_in_days = $to->diffInDays($from);
                    $records[] = array(
                        'id'=>$service_reminder->id,
                        'image'=>$service_reminder->vehicle['vehicle_image'] ? asset('uploads/'.$service_reminder->vehicle['vehicle_image']) : asset("assets/images/vehicle.jpeg") ,
                        'service_name'=>$service_reminder->services['description'],
                        'overdue_time'=>$service_reminder->services->overdue_time,
                        'overdue_unit'=>$service_reminder->services->overdue_unit,
                        'overdue_meter'=>$service_reminder->services->overdue_meter??'',
                        'make_model' => ($service_reminder->vehicle_id) ? $service_reminder->vehicle->make_name??'' . '-' . $service_reminder->vehicle->model_name??'' : "",
                        'make' => ($service_reminder->vehicle_id) ? $service_reminder->vehicle->make_name??'' : "",
                        'model' => ($service_reminder->vehicle_id) ? $service_reminder->vehicle->model_name ??'': "",
                        'vehicle_number' => ($service_reminder->vehicle_id) ? $service_reminder->vehicle->license_plate??'' : "",
                        'start_date'=>$service_reminder->last_date,
                        'last_meter'=>$service_reminder->last_meter,
                        'next_due'=>date($date_format_setting,strtotime($date)),
                        'diff_in_days'=>$diff_in_days,
                        'next_due_meter'=>$next_due_meter,
                        'timestamp' => date('Y-m-d H:i:s', strtotime($service_reminder->updated_at)),
                        "delete_status" => (isset($service_reminder->deleted_at)) ? 1 : 0,
                    );
                }
                $data['success'] = "1";
                $data['message'] = "Data fetched.";
                $data['data'] = $records;
            }
            else{        
                $data['success'] = "0";
                $data['message'] = "No record found";
                $data['data'] = null;
            }
        }
        return $data;
    }
    public function deleteServiceReminder(Request $request)
    {
        //dd($request->service_reminder_id);
        $validation = Validator::make($request->all(), [
            'service_reminder_id' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = "Something went wrong, please try again later!";
            $data['data'] = null;
        } else {           
           $service_reminder=ServiceReminderModel::find($request->get('service_reminder_id'));
            if(!empty($service_reminder))
            {
                $service_reminder->delete();
                $data['success'] = "1";
                $data['message'] = "Record deleted successfully!";
                $data['data'] = array('service_reminder_id' => $request->service_reminder_id, 'timestamp' => date('Y-m-d H:i:s'));
            }
            else
            {
                $data['success'] = "0";
                $data['message'] = "This id does not exist";
                $data['data'] = null;
            }
        }
        return $data;
    }
    public function storeServiceReminder(Request $request)
    {
        //dd($request->all());
        $validation = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'vehicle_id' => 'required',
            'services' => 'required'
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {           
            $users = User::where('user_type', 'S')->get();
            $services=explode(",",$request->get('services'));
            foreach ($services as $item) {
                $history = ServiceReminderModel::whereVehicleId($request->get('vehicle_id'))->where('service_id', $item)->orderBy('id', 'desc')->first();
                if ($history == null) {
                    $last_date = "N/D";
                    $last_meter = "0";
                } else {
                    $interval = substr($history->services->overdue_unit, 0, -3);
                    $int = $history->services->overdue_time . $interval;
                    $date = date('Y-m-d', strtotime($int, strtotime(date('Y-m-d'))));
                    $last_date = $date;
                    if ($history->last_meter == 0) {
                        $total = $history->vehicle->int_mileage;
                    } else {
                        $total = $history->last_meter;
                    }
                    $last_meter = $total + $history->services->overdue_meter;
                }
                $reminder = new ServiceReminderModel();
                $reminder->vehicle_id = $request->get('vehicle_id');
                $reminder->service_id = $item;
                $reminder->last_date = $request->start_date;
                $reminder->last_meter = $last_meter;
                $reminder->user_id = $request->user_id;
                $reminder->save();
            }
            $data['success'] = "1";
            $data['message'] = "Service reminder added successfully.";
            $data['data'] = array('reminder_id' => $reminder->id,'vehicle_id' => $reminder->vehicle_id,'service_id' => $reminder->service_id, 'timestamp' => date('Y-m-d H:i:s', strtotime($reminder->updated_at)));
        }
        return $data;
    }
    public function updateServiceReminder(Request $request) //added on 13-03-23
    {
        //dd($request->all());
          $validation = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'ser_reminder_id'=>'required|integer',
            'vehicle_id' => 'required',
            'services' => 'required'
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } 
        else 
        {   
            $user_id = $request->get('user_id');
            $services=explode(",",$request->get('services'));
            $ser_reminder = ServiceReminderModel::find($request->ser_reminder_id);
            if(!empty($ser_reminder))
            {
                foreach ($services as $item) {
                    $history = ServiceReminderModel::whereVehicleId($request->get('vehicle_id'))->where('service_id', $item)->orderBy('id', 'desc')->first();
                    if ($history == null) {
                        $last_date = "N/D";
                        $last_meter = "0";
                    } else {
                        $interval = substr($history->services->overdue_unit, 0, -3);
                        $int = $history->services->overdue_time . $interval;
                        $date = date('Y-m-d', strtotime($int, strtotime(date('Y-m-d'))));
                        $last_date = $date;
                        if ($history->last_meter == 0) {
                            $total = $history->vehicle->int_mileage;
                        } else {
                            $total = $history->last_meter;
                        }
                        $last_meter = $total + $history->services->overdue_meter;
                    }
                    $ser_reminder->update([
                        'vehicle_id'=>$request->get('vehicle_id'),
                        'service_id'=> $item,
                        'last_date'=>$request->start_date,
                        'last_meter' => $last_meter,
                        'user_id'=>$request->user_id,
                    ]);
                    $ser_reminder->save();
                }       
                $data['success'] = "1";
                $data['message'] = "Service reminder updated successfully!";
                $data['data'] = array(
                    'ser_reminder_id' => $ser_reminder->id,
                    'timestamp' => date('Y-m-d H:i:s', strtotime($ser_reminder->updated_at)),
                );
            }
            else
            {
                $data['success'] = "0";
                $data['message'] = "This record doesnot exist!";
                $data['data'] = null;
            }
        }
        return $data;
    }
    public function serviceItemList()//added on 04-03-23
    {    
        $service_items= ServiceItemsModel::orderBy('id', 'desc')->get();
        if(count($service_items)>0)
        {
            $records = array();
            foreach ($service_items as $service_item) {
                //dd($row);
                $records[] = array(
                    'id'=>$service_item->id,
                    'description'=>$service_item->description,
                    'overdue_time'=>$service_item->overdue_time,
                    'overdue_unit'=>$service_item->overdue_unit,
                    'overdue_meter'=>$service_item->overdue_meter,
                    'duesoon_time'=>$service_item->duesoon_time,
                    'duesoon_unit'=>$service_item->duesoon_unit,
                    'timestamp' => date('Y-m-d H:i:s', strtotime($service_item->updated_at)),
                    "delete_status" => (isset($service_item->deleted_at)) ? 1 : 0,
                );
            }
            $data['success'] = "1";
            $data['message'] = "Data fetched.";
            $data['data'] = $records;
        }
        else
        {
            $data['success'] = "0";
            $data['message'] = "No record found";
            $data['data'] = null;
        }
        return $data;
    }
}
