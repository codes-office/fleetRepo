<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Model\Bookings;
use App\Model\CitiesModel;
use App\Model\DriverLogsModel;
use App\Model\DriverVehicleModel;
use App\Model\Expense;
use App\Model\FuelModel;
use App\Model\IncCats;
use App\Model\IncomeModel;
use App\Model\ServiceReminderModel;
use App\Model\User;
use App\Model\UserData;
use App\Model\VehicleGroupModel;
use App\Model\VehicleModel;
use App\Model\VehicleReviewModel;
use App\Model\VehicleTypeModel;
// use App\Model\InsuranceModel;
use App\Model\ExpCats;
use App\Model\ServiceItemsModel;
use App\Model\Vendor;
use App\Model\DriverPayments;
use App\Rules\UniqueMobile;
use Illuminate\Support\Facades\Auth as Login;
use Auth;
use DB;
use Hyvikk;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PDF;
//use Barryvdh\DomPDF\Facade as PDF;
use Validator;
class VendorApiController extends Controller
{
    public function get_vendor(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'user_id' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = "";
        } else {
            $number = $request->phone;
            $user = User::where('id', $request->user_id)->where('user_type', 'O')->first();
            if ($user) {
                $data['success'] = 1;
                $data['message'] = "You have Signed in Successfully!";
                $data['data'] = ['userinfo' => array("user_id" => $user->id,
                    "api_token" => $user->api_token,
                    "fcm_id" => $user->getMeta('fcm_id'),
                    "device_token" => $user->getMeta('device_token'),
                    // "socialmedia_uid" => $user->getMeta('socialmedia_uid'),
                    "user_name" => $user->name,
                    "user_type" => $user->user_type,
                    "mobno" => $user->phone,
                    // "phone_code" => $user->getMeta('phone_code'),
                    "emailid" => $user->email,
                    // "gender" => $user->getMeta('gender'),
                    "password" => $user->password,
                    "profile_pic" => (isset($user->profile_image)) ? asset('uploads/' . $user->profile_image) : null,
                    //"group" => ($user->group_id) ? $user->group->name : null,
                    "city" => ($user->city_id) ? $user->city->city : null,
                    "status" => $user->getMeta('login_status'),
                    "address" => $user->address,
                    "whatsapp" => $user->alt_mobile,
                    "timestamp" => date('Y-m-d H:i:s', strtotime($user->created_at))),
                ];
            } else {
                $data['success'] = 0;
                $data['message'] = "User not exists";
                $data['data'] = "";
            }
        }
        return $data;
    }
    public function types(Request $request)
    {
        $vehicle_types = VehicleTypeModel::where('isenable', 1);
        if (isset($request->timestamp)) {
            $time = date('Y-m-d H:i:s', strtotime($request->timestamp));
            $vehicle_types = $vehicle_types->where("updated_at", ">", $time);
        }
        $vehicle_types = $vehicle_types->withTrashed()->get();
        $vehicle_type_data = array();
        foreach ($vehicle_types as $vehicle_type) {
            if ($vehicle_type->icon != null) {
                $url = asset("uploads/" . $vehicle_type->icon);
            } else {
                $url = asset("assets/images/vehicle.jpeg");
            }
            $vehicle_type_data[] = array(
                'id' => $vehicle_type->id,
                'vehicletype' => $vehicle_type->vehicletype,
                'displayname' => $vehicle_type->displayname,
                'icon' => $url,
                'no_seats' => $vehicle_type->seats,
                'timestamp' => date('Y-m-d H:i:s', strtotime($vehicle_type->updated_at)),
                "delete_status" => (isset($vehicle_type->deleted_at)) ? 1 : 0,
            );
        }
        $data['success'] = "1";
        $data['message'] = "Data fetched!";
        $data['data'] = $vehicle_type_data;
        return $data;
    }
    public function cities(Request $request)
    {
        $cities = CitiesModel::withTrashed()->get();
        if (isset($request->timestamp)) {
            $time = date('Y-m-d H:i:s', strtotime($request->timestamp));
            $cities = CitiesModel::where("updated_at", ">", $time)->withTrashed()->get();
        }
        $details = array();
        foreach ($cities as $row) {
            $details[] = array(
                'id' => $row->id,
                'city' => $row->city,
                'timestamp' => date('Y-m-d H:i:s', strtotime($row->updated_at)),
                "delete_status" => (isset($row->deleted_at)) ? 1 : 0,
            );
        }
        $data['success'] = "1";
        $data['message'] = "Data fetched!";
        $data['data'] = $details;
        return $data;
    }
    public function income_chart(Request $request)
    {
        $validation = Validator::make($request->all(), [
            "user_id" => 'required|integer',
            "type" => "required|in:day,month,year",
            "year" => "required_if:type,month",
            "month" => "required_if:type,month",
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $yearly_income = null;
            $monthly_income = null;
            $daily_income = null;
            $total = 0;
            if ($request->type == "year") {
                $details = $this->yearly_income($request->year, $request->user_id);
                foreach ($details as $key => $val) {
                    $yearly_income[] = $val;
                }
            }
            if ($request->type == "month") {
                $details2 = $this->monthly_income($request->month, $request->year, $request->user_id);
                foreach ($details2 as $key => $val) {
                    $monthly_income[] = $val;
                }
            }
            $sunday = date('Y-m-d', strtotime('previous sunday'));
            $saturday = date('Y-m-d', strtotime('saturday this week'));
            $details3 = $this->daily_income($sunday, $saturday, $request->user_id);
            foreach ($details3 as $key => $val) {
                $daily_income[] = $val;
                $total = $total + $val;
            }
            // dd($sunday, $saturday);
            // dd($monthly_income);
            $data['success'] = "1";
            $data['message'] = "Data fetched!";
            $data['data'] = array(
                "yearly_income" => $yearly_income,
                "monthly_income" => $monthly_income,
                "daily_income" => $daily_income,
                "total" => round($total, 2),
            );
        }
        return $data;
    }
    private function daily_income($sunday, $saturday, $user_id)
    {
        $user = User::find($user_id);
        if ($user->group_id == null || $user->user_type == "S") {
            $all_vehicles = VehicleModel::get();
        } else {
            $all_vehicles = VehicleModel::where('user_id', $user_id)->where('group_id', $user->group_id)->get();
        }
        $vehicle_ids = array(0);
        foreach ($all_vehicles as $key) {
            $vehicle_ids[] = $key->id;
        }
        $incomes = DB::select('select date(date) as mnth,sum(amount) as tot from income where date(date) between ? and ? and  deleted_at is null and vehicle_id in (' . join(",", $vehicle_ids) . ') group by date(date)', [$sunday, $saturday]);
        // Start date
        $date = $sunday;
        // End date
        $end_date = $saturday;
        $week = array();
        $week[$date] = 0;
        for ($i = 0; $i < 6; $i++) {
            // echo "$date\n";
            $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
            $week[$date] = 0;
        }
        foreach ($incomes as $income) {
            $week[$income->mnth] = $income->tot;
        }
        // dd($income2, $dates);
        // // $yr = array_merge($dates, $income2);
        // dd($incomes, $week);
        // dd($yr);
        return $week;
        // return implode(",", $yr);
    }
    private function monthly_income($month, $year, $user_id)
    {
        $user = User::find($user_id);
        if ($user->group_id == null || $user->user_type == "S") {
            $all_vehicles = VehicleModel::get();
        } else {
            $all_vehicles = VehicleModel::where('user_id', $user_id)->where('group_id', $user->group_id)->get();
        }
        $vehicle_ids = array(0);
        foreach ($all_vehicles as $key) {
            $vehicle_ids[] = $key->id;
        }
        $incomes = DB::select('select day(date) as mnth,sum(amount) as tot from income where year(date)=? and month(date)=? and  deleted_at is null and vehicle_id in (' . join(",", $vehicle_ids) . ') group by date(date)', [$year, $month]);
        $end = date('t', strtotime($year . "-" . $month));
        $dates = array();
        for ($i = 1; $i <= $end; $i++) {
            $dates[$i] = 0;
        }
        $income2 = array();
        foreach ($incomes as $income) {
            $dates[$income->mnth] = $income->tot;
        }
        // dd($income2, $dates);
        // // $yr = array_merge($dates, $income2);
        // dd($yr);
        return $dates;
        // return implode(",", $yr);
    }
    private function yearly_income($year, $user_id)
    {
        $user = User::find($user_id);
        //dd($user);
        if ($user->group_id == null || $user->user_type == "S") {
            $all_vehicles = VehicleModel::get();
        } else {
            $all_vehicles = VehicleModel::where('user_id', $user_id)->where('group_id', $user->group_id)->get();
            //dd($all_vehicles);
        }
        $vehicle_ids = array(0);
        foreach ($all_vehicles as $key) {
            $vehicle_ids[] = $key->id;
        }
        $incomes = DB::select('select monthname(date) as mnth,sum(amount) as tot from income where year(date)=? and  deleted_at is null and vehicle_id in (' . join(",", $vehicle_ids) . ') group by month(date)', [$year]);
        $months = ["January" => 0, "February" => 0, "March" => 0, "April" => 0, "May" => 0, "June" => 0, "July" => 0, "August" => 0, "September" => 0, "October" => 0, "November" => 0, "December" => 0];
        $income2 = array();
        foreach ($incomes as $income) {
            $income2[$income->mnth] = $income->tot;
        }
        // dd($months, $income2);
        $yr = array_merge($months, $income2);
        return $yr;
        // return implode(",", $yr);
    }
    public function years()
    {
        $years = DB::select(DB::raw("select distinct year(date) as years from income  union select distinct year(date) as years from expense order by years desc"));
        $y = array();
        foreach ($years as $year) {
            $y[] = "" . $year->years;
        }
        if ($years == null) {
            $y[] = "" . date('Y');
        }
        $data['success'] = "1";
        $data['message'] = "Data fetched!";
        $data['data'] = $y;
        return $data;
    }
    public function counts_old(Request $request)//old count function returns all counts
    {
        $validation = Validator::make($request->all(), [
            "user_id" => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $user = User::find($request->user_id);
            if ($user->group_id == null) {
                $vehicle_ids = VehicleModel::pluck('id')->toArray();
            } else {
                // $vehicle_ids = VehicleModel::where('user_id', $user->id)->where('group_id', $user->group_id)->pluck('id')->toArray();
                $vehicle_ids = VehicleModel::where('group_id', $user->group_id)->pluck('id')->toArray();
            }          
            $index['income'] = IncomeModel::whereRaw('year(date) = ? and month(date)=?', [date("Y"), date("n")])->whereIn('vehicle_id', $vehicle_ids)->sum("amount");
            $index['expense'] = Expense::whereRaw('year(date) = ? and month(date)=?', [date("Y"), date("n")])->whereIn('vehicle_id', $vehicle_ids)->sum("amount");
            $index['vehicles'] = VehicleModel::whereIn('id', $vehicle_ids)->count();
            $index['users'] = User::whereUser_type("O")->get()->count();
            $index['drivers'] = User::whereUser_type("D")->get()->count();
            $index['customers'] = User::where('user_type', 'C')->get()->count();
           /* $vehicle_ids = VehicleModel::where('group_id', $user->group_id)->pluck('id')->toArray();
            $ids1 = Bookings::where('user_id', $request->user_id)->pluck('id')->toArray();
            $ids2 = Bookings::where('driver_id', null)->whereRaw('created_at >= now() - interval 5 minute')->pluck('id')->toArray(); 
            $index['bookings'] = Bookings::whereIn('id', array_merge($ids1, $ids2))->count(); */
            $vehicle_ids = array(0);
            if ($user->group_id == null || $user->user_type == "S") {
                $index['vehicles'] = VehicleModel::all()->count();
                $index['bookings'] = Bookings::all()->count();
                $vehicle_ids = VehicleModel::pluck('id')->toArray();
                if ($vehicle_ids == null) {
                    $vehicle_ids = array(0);
                }
            } else {
                $index['vehicles'] = VehicleModel::where('group_id', $user->group_id)->count();
                $vehicle_ids = VehicleModel::where('group_id', $user->group_id)->pluck('id')->toArray();
                if ($vehicle_ids == null) {
                    $vehicle_ids = array(0);
                }
                $index['bookings'] = Bookings::whereIn('vehicle_id', $vehicle_ids)->count();
            }
            $index['custom_requests'] = Bookings::meta()->where('bookings_meta.key', '=', 'booking_option')->where('bookings_meta.value', '=', 'offer request')->distinct('bookings.id')->count();
            $data['success'] = "1";
            $data['message'] = "Data fetched!";
            $data['data'] = $index;
        }
        return $data;
    }
    public function counts(Request $request)//new count function based on current and previous year/month/day added on 26-05-2023
    {
        $validation = Validator::make($request->all(), [
            "user_id" => 'required|integer',
            "type" => "required|in:day,month,year",
            "year" => "required_if:type,month",
            "month" => "required_if:type,month",
        ]);
        
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            if ($request->type == "year") 
            {
                $user = User::find($request->user_id);
                if ($user->group_id == null) {
                    $vehicle_ids = VehicleModel::pluck('id')->toArray();
                } else {
                    // $vehicle_ids = VehicleModel::where('user_id', $user->id)->where('group_id', $user->group_id)->pluck('id')->toArray();
                    $vehicle_ids = VehicleModel::where('group_id', $user->group_id)->pluck('id')->toArray();
                }          
                $curr_income = IncomeModel::whereYear('created_at', '=', $request->year)->whereIn('vehicle_id', $vehicle_ids)->sum("amount");
                $curr_expense = Expense::whereYear('created_at', '=', $request->year)->whereIn('vehicle_id', $vehicle_ids)->sum("amount");
                $curr_vehicles = VehicleModel::whereYear('created_at', '=', $request->year)->whereIn('id', $vehicle_ids)->count();
                $curr_users = User::whereYear('created_at', '=', $request->year)->whereUser_type("O")->get()->count();
                $curr_drivers = User::whereYear('created_at', '=', $request->year)->whereUser_type("D")->get()->count();
                $curr_customers = User::whereYear('created_at', '=', $request->year)->where('user_type', 'C')->get()->count();
              
                $prev_income = IncomeModel::whereYear('created_at', '=', $request->year-1)->whereIn('vehicle_id', $vehicle_ids)->sum("amount");
                $prev_expense = Expense::whereYear('created_at', '=', $request->year-1)->whereIn('vehicle_id', $vehicle_ids)->sum("amount");
                $prev_vehicles = VehicleModel::whereYear('created_at', '=', $request->year-1)->whereIn('id', $vehicle_ids)->count();
                $prev_users = User::whereYear('created_at', '=', $request->year-1)->whereUser_type("O")->get()->count();
                $prev_drivers = User::whereYear('created_at', '=', $request->year-1)->whereUser_type("D")->get()->count();
                $prev_customers = User::whereYear('created_at', '=', $request->year-1)->where('user_type', 'C')->get()->count();
              
                $vehicle_ids = array(0);
                if ($user->group_id == null || $user->user_type == "S") {
                    $curr_vehicles = VehicleModel::whereYear('created_at', '=', $request->year)->get()->count();
                    $curr_bookings = Bookings::whereYear('created_at', '=', $request->year)->get()->count();
                    $prev_vehicles = VehicleModel::whereYear('created_at', '=', $request->year-1)->get()->count();
                    $prev_bookings = Bookings::whereYear('created_at', '=', $request->year-1)->get()->count();
                    $vehicle_ids = VehicleModel::pluck('id')->toArray();
                    if ($vehicle_ids == null) {
                        $vehicle_ids = array(0);
                    }
                } else {
                    $curr_vehicles = VehicleModel::whereYear('created_at', '=', $request->year)->where('group_id', $user->group_id)->count();
                    $vehicle_ids = VehicleModel::where('group_id', $user->group_id)->pluck('id')->toArray();
                    if ($vehicle_ids == null) {
                        $vehicle_ids = array(0);
                    }
                    $curr_bookings = Bookings::whereYear('created_at', '=', $request->year)->whereIn('vehicle_id', $vehicle_ids)->count();

                    $prev_vehicles = VehicleModel::whereYear('created_at', '=', $request->year-1)->where('group_id', $user->group_id)->count();
                    $prev_bookings = Bookings::whereYear('created_at', '=', $request->year-1)->whereIn('vehicle_id', $vehicle_ids)->count();

                }
               // $index['custom_requests'] = Bookings::meta()->where('bookings_meta.key', '=', 'booking_option')->where('bookings_meta.value', '=', 'offer request')->distinct('bookings.id')->count();
                $index['curr_data']=array(
                    "income" => $curr_income,
                    "expense"  => $curr_expense,
                    "vehicles" => $curr_vehicles,
                    "users" => $curr_users,
                    "drivers"  => $curr_drivers,
                    "customers" => $curr_customers,
                    "bookings"  => $curr_bookings,
                );
                $index['prev_data']=array(
                    "income" => $prev_income,
                    "expense"  => $prev_expense,
                    "vehicles" => $prev_vehicles,
                    "users" => $prev_users,
                    "drivers"  => $prev_drivers,
                    "customers" => $prev_customers,
                    "bookings"  => $prev_bookings,
                    
                );
                $data['success'] = "1";
                $data['message'] = "Data fetched!";
                $data['data'] = $index;
            }
            if ($request->type == "month") 
            {
                $user = User::find($request->user_id);
                if ($user->group_id == null) {
                    $vehicle_ids = VehicleModel::pluck('id')->toArray();
                } else {
                    // $vehicle_ids = VehicleModel::where('user_id', $user->id)->where('group_id', $user->group_id)->pluck('id')->toArray();
                    $vehicle_ids = VehicleModel::where('group_id', $user->group_id)->pluck('id')->toArray();
                }          
                $curr_income = IncomeModel::whereMonth('created_at', '=', $request->month)->whereIn('vehicle_id', $vehicle_ids)->sum("amount");
                $curr_expense = Expense::whereMonth('created_at', '=', $request->month)->whereIn('vehicle_id', $vehicle_ids)->sum("amount");
                $curr_vehicles = VehicleModel::whereMonth('created_at', '=', $request->month)->whereIn('id', $vehicle_ids)->count();
                $curr_users = User::whereMonth('created_at', '=', $request->month)->whereUser_type("O")->get()->count();
                $curr_drivers = User::whereMonth('created_at', '=', $request->month)->whereUser_type("D")->get()->count();
                $curr_customers = User::whereMonth('created_at', '=', $request->month)->where('user_type', 'C')->get()->count();
              
                $prev_income = IncomeModel::whereMonth('created_at', '=', $request->month-1)->whereIn('vehicle_id', $vehicle_ids)->sum("amount");
                $prev_expense = Expense::whereMonth('created_at', '=', $request->month-1)->whereIn('vehicle_id', $vehicle_ids)->sum("amount");
                $prev_vehicles = VehicleModel::whereMonth('created_at', '=', $request->month-1)->whereIn('id', $vehicle_ids)->count();
                $prev_users = User::whereMonth('created_at', '=', $request->month-1)->whereUser_type("O")->get()->count();
                $prev_drivers = User::whereMonth('created_at', '=', $request->month-1)->whereUser_type("D")->get()->count();
                $prev_customers = User::whereMonth('created_at', '=', $request->month-1)->where('user_type', 'C')->get()->count();
              
                $vehicle_ids = array(0);
                if ($user->group_id == null || $user->user_type == "S") {
                    $curr_vehicles = VehicleModel::whereMonth('created_at', '=', $request->month)->get()->count();
                    $curr_bookings = Bookings::whereMonth('created_at', '=', $request->month)->get()->count();
                    $prev_vehicles = VehicleModel::whereMonth('created_at', '=', $request->month-1)->get()->count();
                    $prev_bookings = Bookings::whereMonth('created_at', '=', $request->month-1)->get()->count();
                    $vehicle_ids = VehicleModel::pluck('id')->toArray();
                    if ($vehicle_ids == null) {
                        $vehicle_ids = array(0);
                    }
                } else {
                    $curr_vehicles = VehicleModel::whereMonth('created_at', '=', $request->month)->where('group_id', $user->group_id)->count();
                    $vehicle_ids = VehicleModel::where('group_id', $user->group_id)->pluck('id')->toArray();
                    if ($vehicle_ids == null) {
                        $vehicle_ids = array(0);
                    }
                    $curr_bookings = Bookings::whereMonth('created_at', '=', $request->month)->whereIn('vehicle_id', $vehicle_ids)->count();

                    $prev_vehicles = VehicleModel::whereMonth('created_at', '=', $request->month-1)->where('group_id', $user->group_id)->count();
                    $prev_bookings = Bookings::whereMonth('created_at', '=', $request->month-1)->whereIn('vehicle_id', $vehicle_ids)->count();

                }
                //$index['custom_requests'] = Bookings::meta()->where('bookings_meta.key', '=', 'booking_option')->where('bookings_meta.value', '=', 'offer request')->distinct('bookings.id')->count();
                $index['curr_data']=array(
                    "income" => $curr_income,
                    "expense"  => $curr_expense,
                    "vehicles" => $curr_vehicles,
                    "users" => $curr_users,
                    "drivers"  => $curr_drivers,
                    "customers" => $curr_customers,
                    "bookings"  => $curr_bookings,
                );
                $index['prev_data']=array(
                    "income" => $prev_income,
                    "expense"  => $prev_expense,
                    "vehicles" => $prev_vehicles,
                    "users" => $prev_users,
                    "drivers"  => $prev_drivers,
                    "customers" => $prev_customers,
                    "bookings"  => $prev_bookings,
                    
                );
                $data['success'] = "1";
                $data['message'] = "Data fetched!";
                $data['data'] = $index;
            } 
            if($request->type == "day")
            {
                $user = User::find($request->user_id);
                if ($user->group_id == null) {
                    $vehicle_ids = VehicleModel::pluck('id')->toArray();
                } else {
                    // $vehicle_ids = VehicleModel::where('user_id', $user->id)->where('group_id', $user->group_id)->pluck('id')->toArray();
                    $vehicle_ids = VehicleModel::where('group_id', $user->group_id)->pluck('id')->toArray();
                }          
                $curr_income = IncomeModel::whereDay('created_at', '=', $request->day)->whereIn('vehicle_id', $vehicle_ids)->sum("amount");
                $curr_expense = Expense::whereDay('created_at', '=', $request->day)->whereIn('vehicle_id', $vehicle_ids)->sum("amount");
                $curr_vehicles = VehicleModel::whereDay('created_at', '=', $request->day)->whereIn('id', $vehicle_ids)->count();
                $curr_users = User::whereDay('created_at', '=', $request->day)->whereUser_type("O")->get()->count();
                $curr_drivers = User::whereDay('created_at', '=', $request->day)->whereUser_type("D")->get()->count();
                $curr_customers = User::whereDay('created_at', '=', $request->day)->where('user_type', 'C')->get()->count();
              
                $prev_income = IncomeModel::whereDay('created_at', '=', $request->day-1)->whereIn('vehicle_id', $vehicle_ids)->sum("amount");
                $prev_expense = Expense::whereDay('created_at', '=', $request->day-1)->whereIn('vehicle_id', $vehicle_ids)->sum("amount");
                $prev_vehicles = VehicleModel::whereDay('created_at', '=', $request->day-1)->whereIn('id', $vehicle_ids)->count();
                $prev_users = User::whereDay('created_at', '=', $request->day-1)->whereUser_type("O")->get()->count();
                $prev_drivers = User::whereDay('created_at', '=', $request->day-1)->whereUser_type("D")->get()->count();
                $prev_customers = User::whereDay('created_at', '=', $request->day-1)->where('user_type', 'C')->get()->count();
              
                $vehicle_ids = array(0);
                if ($user->group_id == null || $user->user_type == "S") {
                    $curr_vehicles = VehicleModel::whereDay('created_at', '=', $request->day)->get()->count();
                    $curr_bookings = Bookings::whereDay('created_at', '=', $request->day)->get()->count();
                    $prev_vehicles = VehicleModel::whereDay('created_at', '=', $request->day-1)->get()->count();
                    $prev_bookings = Bookings::whereDay('created_at', '=', $request->day-1)->get()->count();
                    $vehicle_ids = VehicleModel::pluck('id')->toArray();
                    if ($vehicle_ids == null) {
                        $vehicle_ids = array(0);
                    }
                } else {
                    $curr_vehicles = VehicleModel::whereDay('created_at', '=', $request->day)->where('group_id', $user->group_id)->count();
                    $vehicle_ids = VehicleModel::where('group_id', $user->group_id)->pluck('id')->toArray();
                    if ($vehicle_ids == null) {
                        $vehicle_ids = array(0);
                    }
                    $curr_bookings = Bookings::whereDay('created_at', '=', $request->day)->whereIn('vehicle_id', $vehicle_ids)->count();

                    $prev_vehicles = VehicleModel::whereDay('created_at', '=', $request->day-1)->where('group_id', $user->group_id)->count();
                    $prev_bookings = Bookings::whereDay('created_at', '=', $request->day-1)->whereIn('vehicle_id', $vehicle_ids)->count();

                }
               // $index['custom_requests'] = Bookings::meta()->where('bookings_meta.key', '=', 'booking_option')->where('bookings_meta.value', '=', 'offer request')->distinct('bookings.id')->count();
                $index['curr_data']=array(
                    "income" => $curr_income,
                    "expense"  => $curr_expense,
                    "vehicles" => $curr_vehicles,
                    "users" => $curr_users,
                    "drivers"  => $curr_drivers,
                    "customers" => $curr_customers,
                    "bookings"  => $curr_bookings,
                );
                $index['prev_data']=array(
                    "income" => $prev_income,
                    "expense"  => $prev_expense,
                    "vehicles" => $prev_vehicles,
                    "users" => $prev_users,
                    "drivers"  => $prev_drivers,
                    "customers" => $prev_customers,
                    "bookings"  => $prev_bookings,
                    
                );
                $data['success'] = "1";
                $data['message'] = "Data fetched!";
                $data['data'] = $index;
            }
            if($request->type == "")
            {
                $user = User::find($request->user_id);
                if ($user->group_id == null) {
                    $vehicle_ids = VehicleModel::pluck('id')->toArray();
                } else {
                    $vehicle_ids = VehicleModel::where('group_id', $user->group_id)->pluck('id')->toArray();
                }          
                $index['income'] = IncomeModel::whereRaw('year(date) = ? and month(date)=?', [date("Y"), date("n")])->whereIn('vehicle_id', $vehicle_ids)->sum("amount");
                $index['expense'] = Expense::whereRaw('year(date) = ? and month(date)=?', [date("Y"), date("n")])->whereIn('vehicle_id', $vehicle_ids)->sum("amount");
                $index['vehicles'] = VehicleModel::whereIn('id', $vehicle_ids)->count();
                $index['users'] = User::whereUser_type("O")->get()->count();
                $index['drivers'] = User::whereUser_type("D")->get()->count();
                $index['customers'] = User::where('user_type', 'C')->get()->count();
                $vehicle_ids = array(0);
                if ($user->group_id == null || $user->user_type == "S") {
                    $index['vehicles'] = VehicleModel::all()->count();
                    $index['bookings'] = Bookings::all()->count();
                    $vehicle_ids = VehicleModel::pluck('id')->toArray();
                    if ($vehicle_ids == null) {
                        $vehicle_ids = array(0);
                    }
                } else {
                    $index['vehicles'] = VehicleModel::where('group_id', $user->group_id)->count();
                    $vehicle_ids = VehicleModel::where('group_id', $user->group_id)->pluck('id')->toArray();
                    if ($vehicle_ids == null) {
                        $vehicle_ids = array(0);
                    }
                    $index['bookings'] = Bookings::whereIn('vehicle_id', $vehicle_ids)->count();
                }
                $index['custom_requests'] = Bookings::meta()->where('bookings_meta.key', '=', 'booking_option')->where('bookings_meta.value', '=', 'offer request')->distinct('bookings.id')->count();
                $data['success'] = "1";
                $data['message'] = "Data fetched!";
                $data['data'] = $index;
            }
        }
        return $data;
    }
    public function update_vendor_image(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'image' => 'required|mimes:jpg,png,jpeg,svg',
            "user_id" => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $user = User::find($request->user_id);
            if ($request->file('image') && $request->file('image')->isValid()) {
                $file = $request->file('image');
                $destinationPath = './uploads'; // upload path
                $extension = $file->getClientOriginalExtension();
                $fileName1 = Str::uuid() . '.' . $extension;
                $file->move($destinationPath, $fileName1);
                $user->profile_image = $fileName1;
                $user->save();
            }
            $data['success'] = 1;
            $data['message'] = "Profile updated successfully!";
            $data['data'] = ['userinfo' => array("user_id" => $user->id,
                "api_token" => $user->api_token,
                "fcm_id" => $user->getMeta('fcm_id'),
                "device_token" => $user->getMeta('device_token'),
                // "socialmedia_uid" => $user->getMeta('socialmedia_uid'),
                "user_name" => $user->name,
                "user_type" => $user->user_type,
                "mobno" => $user->phone,
                // "phone_code" => $user->getMeta('phone_code'),
                "emailid" => $user->email,
                // "gender" => $user->getMeta('gender'),
                "password" => $user->password,
                "profile_pic" => (isset($user->profile_image)) ? asset('uploads/' . $user->profile_image) : null,
                "group" => ($user->group_id) ? $user->group->name : null,
                "city" => ($user->city_id) ? $user->city->city : null,
                "status" => $user->getMeta('login_status'),
                "address" => $user->address,
                "whatsapp" => $user->alt_mobile,
                "timestamp" => date('Y-m-d H:i:s', strtotime($user->updated_at))),
            ];
        }
        return $data;
    }
    public function edit_profile(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'email' => 'required|email|unique:users,email,' . \Request::get("user_id"),
            'mobno' => 'required',
            "whatsapp" => 'required',
            "name" => 'required',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = "";
        } else {
            $user = User::find($request->user_id);
            $user->email = $request->email;
            $user->phone = $request->mobno;
            $user->alt_mobile = $request->whatsapp;
            $user->name = $request->name;
            $name = explode(" ", $request->name);
            $user->first_name = $name[0];
            $user->last_name = "";
            if (sizeof($name) > 1) {
                $user->last_name = $name[1];
            }
            $user->setMeta([
                $user->address = $request->address,
                $user->city = $request->city,
            ]); 
            $user->save();
            $data['success'] = 1;
            $data['message'] = "Profile updated successfully!";
            $data['data'] = ['userinfo' => array("user_id" => $user->id,
                "api_token" => $user->api_token,
                "fcm_id" => $user->getMeta('fcm_id'),
                "device_token" => $user->getMeta('device_token'),
                // "socialmedia_uid" => $user->getMeta('socialmedia_uid'),
                "user_name" => $user->name,
                "user_type" => $user->user_type,
                "mobno" => $user->phone,
                // "phone_code" => $user->getMeta('phone_code'),
                "emailid" => $user->email,
                // "gender" => $user->getMeta('gender'),
                "password" => $user->password,
                "profile_pic" => (isset($user->profile_image)) ? asset('uploads/' . $user->profile_image) : null,
                "group" => ($user->group_id) ? $user->group->name : null,

                //"city" => ($user->city_id) ? $user->city->city : null,
                "status" => $user->getMeta('login_status'),
                "address" => $user->getMeta('address'),
                "city" => $user->getMeta('city'),
                "whatsapp" => $user->alt_mobile,
                "timestamp" => date('Y-m-d H:i:s', strtotime($user->updated_at))),
            ];
        }
        return $data;
    }
    public function mobile_login(Request $request)
    {
        //dd($request->all());
        $validation = Validator::make($request->all(), [
            'email' => 'required|email',
           // 'fcm_id' => 'required',
           // 'auth_id' => 'required',
            'password' =>'required',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = "";
        } 
        else
        {
            //dd($request->all());
            //$data=[];
            $email = $request->email;
            $password=$request->password;
            $user = User::where('email', $email)->first();
            if (!$user) {
                $data['success'] = 0;
                $data['message'] = "User Not Found with this Email Id";
                $data['data'] = "";
            } else {
            // dd($password);
                if (Login::attempt(['email' => $email, 'password' => $password])) {
                $user=Auth::user();
                    if ($user) {
                        $user->fcm_id = $request->get('fcm_id');
                        // if ($request->otp) {
                        //     $user->otp = $request->otp;
                        // }
                        $user->login_status = 1;
                        $user->is_available = 1;
                        $user->firebaseauth_id = $request->auth_id;
                        $user->device_token = $request->get('device_token');
                        $user->save();
                        $data['success'] = 1;
                        $data['message'] = "You have Signed in Successfully!";
                        // dd($user->user_type);
                        if ($user->user_type == "D") {
                            if ($user->vehicle_id != null) {
                                $v = VehicleModel::find($user->vehicle_id);
                                $v_info = array(
                                    'make_id' => $v->maker->make,
                                    'model_id' => $v->vehiclemodel->model,
                                    'type_id' => $v->type_id,
                                    'license_plate' => $v->license_plate,
                                    'color_id' => $v->color_id,
                                    'vehicle_id' => $v->id,
                                    'color' => $v->vehiclecolor->color,
                                    'vehicletype' => $v->types->displayname,
                                );
                                $vehicle = $v->license_plate;
                            } else {
                                $vehicle = "";
                                $v_info = new \stdClass();
                            }
                            $data['data'] = ['userinfo' => array("user_id" => $user->id,
                                "api_token" => $user->api_token,
                                "fcm_id" => $user->getMeta('fcm_id'),
                                "device_token" => $user->getMeta('device_token'),
                                "socialmedia_uid" => "",
                                "user_name" => $user->name,
                                "user_type" => $user->user_type,
                                "mobno" => $user->getMeta('phone'),
                                "phone_code" => $user->getMeta('phone_code'),
                                "emailid" => $user->email,
                                "gender" => $user->getMeta('gender'),
                                "password" => $user->password,
                                "profile_pic" => $user->getMeta('driver_image'),
                                "address" => $user->getMeta('address'),
                                "city" => $user->getMeta('city'),
                                "id-proof" => $user->getMeta('license_image'),
                                "id-proof-type" => "License",
                                "vehicle-number" => $vehicle,
                                "availability" => $user->getMeta('is_available'),
                                "whatsapp" => $user->alt_mobile,
                                "status" => $user->getMeta('login_status'),
                                "timestamp" => date('Y-m-d H:i:s', strtotime($user->created_at)),
                                "is_verified" => $user->is_verified,
                            ),
                                "vehicle_info" => $v_info,
                            ];
                        }
                        if ($user->user_type == "O") {
                            $data['data'] = ['userinfo' => array("user_id" => $user->id,
                                "api_token" => $user->api_token,
                                "fcm_id" => $user->getMeta('fcm_id'),
                                "device_token" => $user->getMeta('device_token'),
                                // "socialmedia_uid" => $user->getMeta('socialmedia_uid'),
                                "user_name" => $user->name,
                                "user_type" => $user->user_type,
                                "mobno" => $user->phone,
                                // "phone_code" => $user->getMeta('phone_code'),
                                "emailid" => $user->email,
                                // "gender" => $user->getMeta('gender'),
                                "password" => $user->password,
                                "profile_pic" => (isset($user->profile_image)) ? asset('uploads/' . $user->profile_image) : null,
                                "group" => ($user->group_id) ? $user->group->name??'' : null,
                                //"city" => ($user->city_id) ? $user->city->city : null,
                                "status" => $user->getMeta('login_status'),
                                "city" => $user->getMeta('city'),
                                "address" => $user->address,
                                "whatsapp" => $user->alt_mobile,
                                "timestamp" => date('Y-m-d H:i:s', strtotime($user->created_at))),
                            ];
                        }
                    } else {
                        $data['success'] = 0;
                        $data['message'] = "User not verified";
                        $data['data'] = "";
                    }
                }
            }
        }
        //dd('test');
        return $data;
    }
    // public function mobile_login(Request $request)
    // {
    //     //dd($request->all());
    //     $validation = Validator::make($request->all(), [
    //         'email' => 'required|email',
    //         'fcm_id' => 'required',
    //         'auth_id' => 'required',
    //         'password' =>'required',
    //     ]);
    //     $errors = $validation->errors();
    //     if (count($errors) > 0) {
    //         $data['success'] = "0";
    //         $data['message'] = implode(", ", $errors->all());
    //         $data['data'] = "";
    //     } else {
    //         $email = $request->email;
    //         // dd($number);
    //         $user = User::where('email', $email)->where('user_type','=','O')->first();
    //         if ($user) {
    //             $user->fcm_id = $request->get('fcm_id');
    //             if ($request->otp) {
    //                 $user->otp = $request->otp;
    //             }
    //             $user->login_status = 1;
    //             $user->is_available = 1;
    //             $user->firebaseauth_id = $request->auth_id;
    //             $user->device_token = $request->get('device_token');
    //             $user->save();
    //             $data['success'] = 1;
    //             $data['message'] = "You have Signed in Successfully!";
    //             if ($user->user_type == "D") {
    //                 if ($user->vehicle_id != null) {
    //                     $v = VehicleModel::find($user->vehicle_id);
    //                     $v_info = array(
    //                         'make_id' => $v->maker->make,
    //                         'model_id' => $v->vehiclemodel->model,
    //                         'type_id' => $v->type_id,
    //                         'license_plate' => $v->license_plate,
    //                         'color_id' => $v->color_id,
    //                         'vehicle_id' => $v->id,
    //                         'color' => $v->vehiclecolor->color,
    //                         'vehicletype' => $v->types->displayname,
    //                     );
    //                     $vehicle = $v->license_plate;
    //                 } else {
    //                     $vehicle = "";
    //                     $v_info = new \stdClass();
    //                 }
    //                 $data['data'] = ['userinfo' => array("user_id" => $user->id,
    //                     "api_token" => $user->api_token,
    //                     "fcm_id" => $user->getMeta('fcm_id'),
    //                     "device_token" => $user->getMeta('device_token'),
    //                     "socialmedia_uid" => "",
    //                     "user_name" => $user->name,
    //                     "user_type" => $user->user_type,
    //                     "mobno" => $user->getMeta('phone'),
    //                     "phone_code" => $user->getMeta('phone_code'),
    //                     "emailid" => $user->email,
    //                     "gender" => $user->getMeta('gender'),
    //                     "password" => $user->password,
    //                     "profile_pic" => $user->getMeta('driver_image'),
    //                     "address" => $user->getMeta('address'),
    //                     "id-proof" => $user->getMeta('license_image'),
    //                     "id-proof-type" => "License",
    //                     "vehicle-number" => $vehicle,
    //                     "availability" => $user->getMeta('is_available'),
    //                     "whatsapp" => $user->alt_mobile,
    //                     "status" => $user->getMeta('login_status'),
    //                     "timestamp" => date('Y-m-d H:i:s', strtotime($user->created_at)),
    //                     "is_verified" => $user->is_verified,
    //                 ),
    //                     "vehicle_info" => $v_info,
    //                 ];
    //             }
    //             if ($user->user_type == "A") {
    //                 $data['data'] = ['userinfo' => array("user_id" => $user->id,
    //                     "api_token" => $user->api_token,
    //                     "fcm_id" => $user->getMeta('fcm_id'),
    //                     "device_token" => $user->getMeta('device_token'),
    //                     // "socialmedia_uid" => $user->getMeta('socialmedia_uid'),
    //                     "user_name" => $user->name,
    //                     "user_type" => $user->user_type,
    //                     "mobno" => $user->phone,
    //                     // "phone_code" => $user->getMeta('phone_code'),
    //                     "emailid" => $user->email,
    //                     // "gender" => $user->getMeta('gender'),
    //                     "password" => $user->password,
    //                     "profile_pic" => (isset($user->profile_image)) ? asset('uploads/' . $user->profile_image) : null,
    //                     "group" => ($user->group_id) ? $user->group->name : null,
    //                     "city" => ($user->city_id) ? $user->city->city : null,
    //                     "status" => $user->getMeta('login_status'),
    //                     "address" => $user->address,
    //                     "whatsapp" => $user->alt_mobile,
    //                     "timestamp" => date('Y-m-d H:i:s', strtotime($user->created_at))),
    //                 ];
    //             }
    //         } else {
    //             $data['success'] = 0;
    //             $data['message'] = "User not verified";
    //             $data['data'] = "";
    //         }
    //     }
    //     return $data;
    // }
    public function generate_pdf(Request $request)
    {
        // dd($request->all());
        $validation = Validator::make($request->all(), [
            'booking_id' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } 
        else {
            $id = $request->booking_id;
            $index['booking'] = Bookings::find($id);
            $filename = time() . '_receipt_' . $id . '.pdf';
            $pdf = PDF::loadView('bookings.pdf', $index);
            $pdf->save('assets/pdf/' . $filename);
           // $pdf->download($filename);
            $url = asset('assets/pdf/' . $filename);
            $data['success'] = "1";
            $data['message'] = "data fetched";
            $data['data'] = array('pdf' => $url, 'filename' => $filename);
        }
        return $data;
    }
    private function get_uploaded_doc($file)
    {
        $destinationPath = './uploads'; // upload path
        $extension = $file->getClientOriginalExtension();
        $fileName1 = Str::uuid() . '.' . $extension;
        $file->move($destinationPath, $fileName1);
        return $fileName1;
    }
    public function vehicle_document(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'vehicle_image' => 'nullable|mimes:jpg,png,jpeg,svg',
            'other_image' => 'nullable|mimes:jpg,png,jpeg,svg',
            'insurance' => 'nullable|mimes:jpg,png,jpeg,svg,pdf,docx,dox',
            'rc_book' => 'nullable|mimes:jpg,png,jpeg,svg,pdf,docx,dox',
            'permit' => 'nullable|mimes:jpg,png,jpeg,svg,pdf,docx,dox',
            'vehicle_fitness' => 'nullable|mimes:jpg,png,jpeg,svg,pdf,docx,dox',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $vehicle_image = null;
            $other_image = null;
            $insurance = null;
            $rc_book = null;
            $permit = null;
            $vehicle_fitness = null;
            if ($request->file('vehicle_image') && $request->file('vehicle_image')->isValid()) {
                $vehicle_image = $this->get_uploaded_doc($request->file('vehicle_image'));
            }
            if ($request->file('other_image') && $request->file('other_image')->isValid()) {
                $other_image = $this->get_uploaded_doc($request->file('other_image'));
            }
            if ($request->file('insurance') && $request->file('insurance')->isValid()) {
                $insurance = $this->get_uploaded_doc($request->file('insurance'));
            }
            if ($request->file('rc_book') && $request->file('rc_book')->isValid()) {
                $rc_book = $this->get_uploaded_doc($request->file('rc_book'));
            }
            if ($request->file('permit') && $request->file('permit')->isValid()) {
                $permit = $this->get_uploaded_doc($request->file('permit'));
            }
            if ($request->file('vehicle_fitness') && $request->file('vehicle_fitness')->isValid()) {
                $vehicle_fitness = $this->get_uploaded_doc($request->file('vehicle_fitness'));
            }
            $data['success'] = "1";
            $data['message'] = "Vehicle documents uploaded successfully!";
            $data['data'] = array(
                'vehicle_image' => $vehicle_image,
                'other_image' => $other_image,
                'insurance' => $insurance,
                'rc_book' => $rc_book,
                'permit' => $permit,
                'vehicle_fitness' => $vehicle_fitness,
            );
        }
        return $data;
    }
    public function edit_vehicle(Request $request)
    {
        //dd($request->all());
        $validation = Validator::make($request->all(), [
            'vehicle_id' => 'required|integer',
            'make_name' => 'required',
            'model_name' => 'required',
            'color_name' => 'required',
            'type_id' => 'required|integer',
            'vehicle_number' => 'required|unique:vehicles,license_plate,' . \Request::get("vehicle_id") . ',id,deleted_at,NULL',
            // 'images' => 'array|max:3',
            'group_id' => 'nullable|integer',
            'user_id' => 'required|integer',
            // 'vehicle_image' => 'nullable|mimes:jpg,png,jpeg,svg',
            'vehicle_image' => 'nullable',
            'other_image' => 'nullable',
            'insurance' => 'nullable',
            'rc_book' => 'nullable',
            'permit' => 'nullable',
            'vehicle_fitness' => 'nullable',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $user_id = $request->get('user_id');
            $vehicle = VehicleModel::find($request->vehicle_id);
            $vehicle->update([
                'make_name' => $request->make_name,
                'model_name' => $request->model_name,
                'color_name' => $request->color_name,
                'license_plate' => $request->get("vehicle_number"),
                'group_id' => $request->get('group_id'),
                'user_id' => $request->get('user_id'),
                'in_service' =>$request->get('in_service')?? 1,
                'type_id' => $request->get('type_id'),
                'engine_type' => $request->get("engine_type"),
                'int_mileage' =>$request->get('initial_mileage'),
               // 'average' => $request->get('average_km'),
            ]);
            $vehicle->average=$request->get('average_km');
            $vehicle->vehicle_image = ($request->vehicle_image) ? $request->vehicle_image : $vehicle->vehicle_image;
            $vehicle->other_image = ($request->other_image) ? $request->other_image : $vehicle->other_image;
            $vehicle->insurance = ($request->insurance) ? $request->insurance : $vehicle->insurance;
            $vehicle->rc_book = ($request->rc_book) ? $request->rc_book : $vehicle->rc_book;
            $vehicle->permit = ($request->permit) ? $request->permit : $vehicle->permit;
            $vehicle->vehicle_fitness = ($request->vehicle_fitness) ? $request->vehicle_fitness : $vehicle->vehicle_fitness;
           // dd($vehicle->getMeta('average'));
            $vehicle->save();
            // if ($request->file('vehicle_image') && $request->file('vehicle_image')->isValid()) {
            //     $this->upload_vehicle_file($request->file('vehicle_image'), "vehicle_image", $request->vehicle_id);
            // }
            // if ($request->file('insurance') && $request->file('insurance')->isValid()) {
            //     $this->upload_doc($request->file('insurance'), 'insurance', $request->vehicle_id);
            // }
            // if ($request->file('rc_book') && $request->file('rc_book')->isValid()) {
            //     $this->upload_doc($request->file('rc_book'), 'rc_book', $request->vehicle_id);
            // }
            // if ($request->file('permit') && $request->file('permit')->isValid()) {
            //     $this->upload_doc($request->file('permit'), 'permit', $request->vehicle_id);
            // }
            // if ($request->file('vehicle_fitness') && $request->file('vehicle_fitness')->isValid()) {
            //     $this->upload_doc($request->file('vehicle_fitness'), 'vehicle_fitness', $request->vehicle_id);
            // }
            // if ($request->images) {
            //     $all_files = array();
            //     foreach ($request->file('images') as $img) {
            //         $destinationPath = './uploads'; // upload path
            //         $extension = $img->getClientOriginalExtension();
            //         $fileName1 = Str::uuid() . '.' . $extension;
            //         $img->move($destinationPath, $fileName1);
            //         $all_files[] = $fileName1;
            //     }
            //     $vehicle = VehicleModel::find($request->vehicle_id);
            //     $vehicle->other_images = json_encode($all_files);
            //     $vehicle->save();
            // }
            $ins_image = ($vehicle->insurance) ? asset('uploads/' . $vehicle->insurance) : null;
            $rc_book = ($vehicle->rc_book) ? asset('uploads/' . $vehicle->rc_book) : null;
            $permit = ($vehicle->permit) ? asset('uploads/' . $vehicle->permit) : null;
            $fitness = ($vehicle->vehicle_fitness) ? asset('uploads/' . $vehicle->vehicle_fitness) : null;
            $other_img = ($vehicle->other_image) ? asset('uploads/' . $vehicle->other_image) : null;
            $image = asset("assets/images/vehicle.jpeg");
            if ($vehicle->vehicle_image != null) {
                $image = asset('uploads/' . $vehicle->vehicle_image);
            }
            $data['success'] = "1";
            $data['message'] = "Vehicle updated successfully!";
            $data['data'] = array(
                'vehicle_id' => $request->vehicle_id,
                'timestamp' => date('Y-m-d H:i:s', strtotime($vehicle->updated_at)),
                "vehicle_image" => $image,
                "ins_image" => $ins_image,
                "rc_book" => $rc_book,
                "permit" => $permit,
                "fitness" => $fitness,
                "other_images" => $other_img,
            );
        }
        return $data;
    }
    public function add_vehicle(Request $request)
    {
        //dd($request->all());
        $validation = Validator::make($request->all(), [
            'make_name' => 'required',
            'model_name' => 'required',
            'color_name' => 'required',
            'type_id' => 'required|integer',
            'vehicle_number' => 'required|unique:vehicles,license_plate,NULL,id,deleted_at,NULL',
            // 'images' => 'array|max:3',
            'group_id' => 'nullable|integer',
            'user_id' => 'required|integer',
            // 'vehicle_image' => 'nullable|mimes:jpg,png,jpeg,svg',
            'vehicle_image' => 'nullable',
            'other_image' => 'nullable',
            'insurance' => 'nullable',
            'rc_book' => 'nullable',
            'permit' => 'nullable',
            'vehicle_fitness' => 'nullable',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $user_id = $request->get('user_id');
            $vehicle = VehicleModel::create([
                'make_name' => $request->make_name,
                'model_name' => $request->model_name,
                'color_name' => $request->color_name,
                'license_plate' => $request->get("vehicle_number"),
                'group_id' => $request->get('group_id'),
                'user_id' => $request->get('user_id'),
                'in_service' => $request->get('in_service')??1,
                'type_id' => $request->get('type_id'),
                'int_mileage' =>$request->get('initial_mileage'),
                'engine_type' => $request->get("engine_type"),
            ])->id;
            $meta = VehicleModel::find($vehicle);
            $meta->setMeta([
                $meta->average = $request->average_km
            ]); 
            $meta->save();
            // if ($request->file('vehicle_image') && $request->file('vehicle_image')->isValid()) {
            //     $this->upload_vehicle_file($request->file('vehicle_image'), "vehicle_image", $vehicle);
            // }
            // if ($request->file('insurance') && $request->file('insurance')->isValid()) {
            //     $this->upload_doc($request->file('insurance'), 'insurance', $vehicle);
            // }
            // if ($request->file('rc_book') && $request->file('rc_book')->isValid()) {
            //     $this->upload_doc($request->file('rc_book'), 'rc_book', $vehicle);
            // }
            // if ($request->file('permit') && $request->file('permit')->isValid()) {
            //     $this->upload_doc($request->file('permit'), 'permit', $vehicle);
            // }
            // if ($request->file('vehicle_fitness') && $request->file('vehicle_fitness')->isValid()) {
            //     $this->upload_doc($request->file('vehicle_fitness'), 'vehicle_fitness', $vehicle);
            // }
            $vehicle = VehicleModel::find($vehicle);
            $vehicle->vehicle_image = $request->vehicle_image;
            $vehicle->other_image = $request->other_image;
            $vehicle->insurance = $request->insurance;
            $vehicle->rc_book = $request->rc_book;
            $vehicle->permit = $request->permit;
            $vehicle->vehicle_fitness = $request->vehicle_fitness;
            $vehicle->save();
            // if ($request->images) {
            //     $all_files = array();
            //     foreach ($request->file('images') as $img) {
            //         $destinationPath = './uploads'; // upload path
            //         $extension = $img->getClientOriginalExtension();
            //         $fileName1 = Str::uuid() . '.' . $extension;
            //         $img->move($destinationPath, $fileName1);
            //         $all_files[] = $fileName1;
            //     }
            //     $vehicle->other_images = json_encode($all_files);
            //     $vehicle->save();
            // }
            $ins_image = ($vehicle->insurance) ? asset('uploads/' . $vehicle->insurance) : null;
            $rc_book = ($vehicle->rc_book) ? asset('uploads/' . $vehicle->rc_book) : null;
            $permit = ($vehicle->permit) ? asset('uploads/' . $vehicle->permit) : null;
            $fitness = ($vehicle->vehicle_fitness) ? asset('uploads/' . $vehicle->vehicle_fitness) : null;
            $other_img = ($vehicle->other_image) ? asset('uploads/' . $vehicle->other_image) : null;
            $image = asset("assets/images/vehicle.jpeg");
            if ($vehicle->vehicle_image != null) {
                $image = asset('uploads/' . $vehicle->vehicle_image);
            }
            $data['success'] = "1";
            $data['message'] = "Vehicle added successfully!";
            $data['data'] = array(
                'vehicle_id' => $vehicle->id,
                'timestamp' => date('Y-m-d H:i:s', strtotime($vehicle->updated_at)),
                "vehicle_image" => $image,
                "ins_image" => $ins_image,
                "rc_book" => $rc_book,
                "permit" => $permit,
                "fitness" => $fitness,
                "other_images" => $other_img,
            );
        }
        return $data;
    }
    public function customers(Request $request)
    {
       // $customers = User::where('user_type', 'C')->where('user_id',$request->user_id);
        $customers = User::where('user_type', 'C');
        if (isset($request->timestamp)) {
            $time = date('Y-m-d H:i:s', strtotime($request->timestamp));
            $customers = $customers->where("updated_at", ">", $time);
        }
        $customers = $customers->withTrashed()->get();
        $records = array();
        foreach ($customers as $row) {
            $records[] = array('customer_id' => $row->id, 'phone' => $row->mobno, 'name' => $row->name,'timestamp' => date('Y-m-d H:i:s', strtotime($row->updated_at))
                , "delete_status" => (isset($row->deleted_at)) ? 1 : 0,
            );
        }
        $data['success'] = "1";
        $data['message'] = "Data fetched";
        $data['data'] = $records;
        return $data;
    }
    public function StoreCustomer(Request $request)
    {
        //dd($request->all());
        $validation = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'unique:users,email,' . \Request::get("id"),
            'phone' => 'required',
            'gender' => 'required',
            // 'address1' => 'required',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } 
        else
        {
            $id = User::create([
                "name" => $request->get("first_name") . " " . $request->get("last_name"),
                "email" => $request->get("email"),
                "password" => bcrypt("password"),
                "user_type" => "C",
                "api_token" => str_random(60),
            ])->id;
            $user = User::find($id);
            $user->user_id = $request->get("user_id");
            $user->first_name = $request->get("first_name");
            $user->last_name = $request->get("last_name");
            $user->address = $request->get("address");
            $user->mobno = $request->get("phone");
            $user->gender = $request->get('gender');
            $user->save();
            $customers = User::where('user_type', 'C')->where('user_id',$request->user_id)->withTrashed()->get();
            $records = array();
            foreach ($customers as $row) {
                $records[] = array('customer_id' => $row->id, 'phone' => $row->mobno, 'name' => $row->name,'timestamp' => date('Y-m-d H:i:s', strtotime($row->updated_at))
                    , "delete_status" => (isset($row->deleted_at)) ? 1 : 0,
                );
            }
            $data['success'] = "1";
            $data['message'] = "Data fetched";
            $data['data'] = $records;
        }
        return $data;
        //$user->givePermissionTo(['Bookings add', 'Bookings edit', 'Bookings list', 'Bookings delete']);
    }
    public function EditCustomer(Request $request)
    {
       // dd($request->all());
        // $validation = Validator::make($request->all(), [
        //     'first_name' => 'required',
        //     'last_name' => 'required',
        //     'email' => 'unique:users,email,' . \Request::get("id"),
        //     'phone' => 'required',
        //     'gender' => 'required',
        //     // 'address1' => 'required',
        // ]);
        //$errors = $validation->errors();
        // if (count($errors) > 0) 
        // {
        //     $data['success'] = "0";
        //     $data['message'] = implode(", ", $errors->all());
        //     $data['data'] = null;
        // } 
        //else
        //{
            //$index['data'] = User::whereId($request->id)->first();
            $customers = User::where('user_type', 'C')->where('user_id',$request->user_id)->where('id',$request->customer_id)->withTrashed()->get();
            $records = array();
            foreach ($customers as $row) {
                $records[] = array('customer_id' => $row->id, 'phone' => $row->mobno, 'name' => $row->name,'timestamp' => date('Y-m-d H:i:s', strtotime($row->updated_at))
                    , "delete_status" => (isset($row->deleted_at)) ? 1 : 0,
                );
            }
            $data['success'] = "1";
            $data['message'] = "Data fetched";
            $data['data'] = $records;
      //  }
        return $data;
    }
    public function DeleteCustomer(Request $request)
    {
        //dd($request->all());
        $validation = Validator::make($request->all(), [
            'customer_id' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = "Something went wrong, please try again later!";
            $data['data'] = null;
        } else {
            $c_id = User::find($request->customer_id);
            // dd($c_id);
            if($c_id)
            {
                //User::find($c_id)->user_data()->delete();
                $c_id->user_data()->delete();
                //dd($c_id->user_data()->delete());
                //$user = User::find($request->get('id'))->delete();
                $user = User::find($request->customer_id);
                $user->update([
                    'email' => time() . "_deleted" . $user->email,
                ]);
                $user->delete();
                //User::find($request->customer_id)->delete();
                $data['success'] = "1";
                $data['message'] = "Record deleted successfully!";
                $data['data'] = array('customer_id' => $request->customer_id, 'timestamp' => date('Y-m-d H:i:s'));
            }else
            {
                $data['success'] = "0";
                    $data['message'] = "This user is not exist!";
                    $data['data'] = null;
            }
        }
        return $data;
    }
    // public function DeleteCustomer(Request $request)
    // {
        // //dd($request->all());
        // $validation = Validator::make($request->all(), [
        //     'customer_id' => 'required|integer',
        // ]);
        // $errors = $validation->errors();
        // if (count($errors) > 0) {
        //     $data['success'] = "0";
        //     $data['message'] = "Something went wrong, please try again later!";
        //     $data['data'] = null;
        // } else {
        //     User::find($request->customer_id)->user_data()->delete();
        //     //$user = User::find($request->get('id'))->delete();
        //     $user = User::find($request->customer_id);
        //     $user->update([
        //         'email' => time() . "_deleted" . $user->email,
        //     ]);
        //     $user->delete();
        //     //User::find($request->customer_id)->delete();
        //     $data['success'] = "1";
        //     $data['message'] = "Record deleted successfully!";
        //     $data['data'] = array('customer_id' => $request->customer_id, 'timestamp' => date('Y-m-d H:i:s'));
        // }
        // return $data;
   // }
    public function edit_income(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'income_id' => 'required|integer',
            'user_id' => 'required|integer',
            'vehicle_id' => 'required|integer',
            'date' => 'required|date',
            'mileage' => 'required|integer',
            'tax_charges' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'type_id' => 'required|integer',
            'booking_id' => 'required',
            'driver_id' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $tax = 0;
            if (Hyvikk::get('tax_charge') != "null") {
                $taxes = json_decode(Hyvikk::get('tax_charge'), true);
                foreach ($taxes as $key => $val) {
                    $tax = $tax + $val;
                }
            }
            $income = IncomeModel::find($request->income_id);
            $income->update([
                "vehicle_id" => $request->get("vehicle_id"),
                "amount" => $request->get("total_amount"),
                "user_id" => $request->user_id,
                "date" => date('Y-m-d', strtotime($request->get('date'))),
                "mileage" => $request->get("mileage"),
                "income_cat" => $request->get("type_id"),
                "tax_percent" => $tax,
                "tax_charge_rs" => $request->tax_charges,
                "booking_id" => $request->booking_id,
                "driver_id" => $request->driver_id,
            ]);
            $v = VehicleModel::find($request->get("vehicle_id"));
            //dd($v);
            if(!$v)
            {
                $data['success'] = "0";
                $data['message'] = "vehicle not found";
                $data['data'] = null;
            }
            else
            {
                $v->mileage = $request->mileage;
                $v->save();
                $data['success'] = "1";
                $data['message'] = "Income record updated successfully.";
                $data['data'] = array('income_id' => $income->id, 'timestamp' => date('Y-m-d H:i:s', strtotime($income->updated_at)));
            }
        }
        return $data;
    }
    public function delete_income(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'income_id' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = "Something went wrong, please try again later!";
            $data['data'] = null;
        } else {
            $income= IncomeModel::find($request->income_id);
            if($income)
            {
                IncomeModel::find($request->income_id)->delete();
                $data['success'] = "1";
                $data['message'] = "Record deleted successfully!";
                $data['data'] = array('income_id' => $request->income_id, 'timestamp' => date('Y-m-d H:i:s'));
            }
            else{
                $data['success'] = "0";
                $data['message'] = "Record not found!";
                $data['data'] = null;
            }
        }
        return $data;
    }
    public function add_income(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'vehicle_id' => 'required|integer',
            'date' => 'required|date',
            'mileage' => 'required|integer',
            'tax_charges' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'type_id' => 'required|integer',
            'booking_id' => 'required',
            'driver_id' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $tax = 0;
            if (Hyvikk::get('tax_charge') != "null") {
                $taxes = json_decode(Hyvikk::get('tax_charge'), true);
                foreach ($taxes as $key => $val) {
                    $tax = $tax + $val;
                }
            }
            $income = IncomeModel::create([
                "vehicle_id" => $request->get("vehicle_id"),
                "amount" => $request->get("total_amount"),
                "user_id" => $request->user_id,
                "date" => date('Y-m-d', strtotime($request->get('date'))),
                "mileage" => $request->get("mileage"),
                "income_cat" => $request->get("type_id"),
                "tax_percent" => $tax,
                "tax_charge_rs" => $request->tax_charges,
                "booking_id" => $request->booking_id,
                "driver_id" => $request->driver_id,
            ]);
            // dd($request->get("vehicle_id"));
            $v = VehicleModel::find($request->get("vehicle_id"));
            if($v)
            {
                $v->mileage = $request->mileage;
                $v->save();
                $data['success'] = "1";
                $data['message'] = "Income record added successfully.";
                $data['data'] = array('income_id' => $income->id, 'timestamp' => date('Y-m-d H:i:s', strtotime($income->updated_at)));
            }
            else{
                $data['success'] = "0";
                $data['message'] = "This vehicle is not exist";
                $data['data'] = null;
            }
        }
        return $data;
    }
    public function income_types(Request $request)
    {
        $types = IncCats::withTrashed()->get();
        if (isset($request->timestamp)) {
            $time = date('Y-m-d H:i:s', strtotime($request->timestamp));
            $types = IncCats::where("updated_at", ">", $time)->withTrashed()->get();
        }
        $records = array();
        foreach ($types as $row) {
            $records[] = array(
                'id' => $row->id,
                'name' => $row->name,
                'timestamp' => date('Y-m-d H:i:s', strtotime($row->updated_at)),
                "delete_status" => (isset($row->deleted_at)) ? 1 : 0,
            );
        }
        $data['success'] = "1";
        $data['message'] = "Data fetched";
        $data['data'] = $records;
        return $data;
    }
    public function income_list(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'user_id' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $vehicles = VehicleModel::where('user_id', $request->user_id)->whereIn_service(1)->withTrashed()->get();//withTrahsed added on 15-02-23
            //dd($vehicles);
            $vehicle_ids = $vehicles->pluck('id')->toArray();
            $types = IncCats::get();
            // $income = IncomeModel::whereIn('vehicle_id', $vehicle_ids)->withTrashed();
            $income = IncomeModel::query();
            // where('user_id', $request->user_id)->
            // withTrashed();
            if (isset($request->timestamp)) {
                $time = date('Y-m-d H:i:s', strtotime($request->timestamp));
                $income = $income->where("updated_at", ">", $time);
            }
            $total = $income->sum('amount');
            $incomes = $income->where('user_id',$request->user_id)->orderBy('id','DESC')->withTrashed()->get();
            $records = array();
           // dd($incomes);
            foreach ($incomes as $row) {
                $records[] = array(
                    'id' => $row->id,
                    'make_model' => ($row->vehicle_id) ? $row->vehicle->make_name??'' . '-' . $row->vehicle->model_name??'': "",
                    'make' => ($row->vehicle_id) ? $row->vehicle->make_name??'' : "",
                    'model' => ($row->vehicle_id) ? $row->vehicle->model_name??'' : "",
                    'vehicle_number' => ($row->vehicle_id) ? $row->vehicle->license_plate??'' : "",
                    'income_type' => $row->category->name ??'',
                    'date' => date('Y-m-d', strtotime($row->date)),
                    // 'date' => date('d-m-Y', strtotime($row->date)),
                    'total_amount' => round($row->amount, 2),
                    'mileage' => $row->mileage,
                    'type_id' => $row->income_cat,
                    'vehicle_id' => $row->vehicle_id,
                    'tax_percent' => $row->tax_percent,
                    'tax_charges' => $row->tax_charge_rs,
                    'vendor_id' => $row->user_id,
                    'amount' => round($row->amount - $row->tax_charge_rs, 2),
                    'booking_id' => $row->booking_id,
                    'driver_id' => $row->driver_id,
                    'driver_name' => ($row->driver_id) ? $row->driver->name : "",
                    'timestamp' => date('Y-m-d H:i:s', strtotime($row->updated_at)),
                    "delete_status" => (isset($row->deleted_at)) ? 1 : 0,
                );
            }
            $data['success'] = "1";
            $data['message'] = "Data fetched.";
            $data['data'] = array(
                'records' => $records,
                'total' => $total,
            );
        }
        return $data;
    }
    private function upload_vehicle_file($file, $field, $id)
    {
        $destinationPath = './uploads'; // upload path
        $extension = $file->getClientOriginalExtension();
        $fileName1 = Str::uuid() . '.' . $extension;
        $file->move($destinationPath, $fileName1);
        $x = VehicleModel::find($id)->update([$field => $fileName1]);
    }
    private function upload_doc($file, $field, $id)
    {
        $destinationPath = './uploads'; // upload path
        $extension = $file->getClientOriginalExtension();
        $fileName1 = Str::uuid() . '.' . $extension;
        $file->move($destinationPath, $fileName1);
        $vehicle = VehicleModel::find($id);
        $vehicle->setMeta([$field => $fileName1]);
        $vehicle->save();
    }
    public function delete_vehicle(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = "Something went wrong, please try again later!";
            $data['data'] = null;
        } else {
            $vehicle = VehicleModel::find($request->get('id'));
            if (isset($vehicle->driver_id)) {
                $driver = User::find($vehicle->driver_id);
                if ($driver != null) {
                    $driver->vehicle_id = null;
                    $driver->save();
                }
            }
            DriverVehicleModel::where('vehicle_id', $request->id)->delete();
            VehicleModel::find($request->get('id'))->income()->delete();
            VehicleModel::find($request->get('id'))->expense()->delete();
            VehicleModel::find($request->get('id'))->delete();
            VehicleReviewModel::where('vehicle_id', $request->get('id'))->delete();
            ServiceReminderModel::where('vehicle_id', $request->get('id'))->delete();
            FuelModel::where('vehicle_id', $request->get('id'))->delete();
            $data['success'] = "1";
            $data['message'] = "Record deleted successfully!";
            $data['data'] = array('vehicle_id' => $request->get('id'), 'timestamp' => date('Y-m-d H:i:s'));
        }
        return $data;
    }
    public function vehicles(Request $request)
    {
       // dd($request->all());
        $user = User::find($request->id);
        if ($user->group_id == null) {
            $vehicles = VehicleModel::whereNotNull('id');
        } else {
            $vehicles = VehicleModel::whereNotNull('group_id');
            //where('user_id', $user->id)->where('group_id', $user->group_id);
        }
        if (isset($request->timestamp)) {
            $time = date('Y-m-d H:i:s', strtotime($request->timestamp));
            $vehicles = $vehicles->where("updated_at", ">", $time);
        }
        $vehicles = $vehicles
        ->withTrashed()
        ->get();
        $details = array();
        foreach ($vehicles as $row) {
            $ins_image = ($row->insurance) ? asset('uploads/' . $row->insurance) : null;
            $rc_book = ($row->rc_book) ? asset('uploads/' . $row->rc_book) : null;
            $permit = ($row->permit) ? asset('uploads/' . $row->permit) : null;
            $fitness = ($row->vehicle_fitness) ? asset('uploads/' . $row->vehicle_fitness) : null;
            $other_img = ($row->other_image) ? asset('uploads/' . $row->other_image) : null;
            // $imgs = (($row->other_images)) ? json_decode($row->other_images) : array();
            // $other_images = array();
            // foreach ($imgs as $img) {
            //     $other_images[] = asset('uploads/' . $img);
            // }
            $image = asset("assets/images/vehicle.jpeg");
            if ($row->vehicle_image != null) {
                $image = asset('uploads/' . $row->vehicle_image);
            }
            //dd($row);
            $details[] = array(
                "id" => $row->id,
                "make" => $row->make_name,
                "model" => $row->model_name,
                // "make" => ($row->maker()->exists()) ? $row->maker->make : "",
                // "model" => ($row->vehiclemodel()->exists()) ? $row->vehiclemodel->model : "",
                "vehicle_type" => ($row->type_id && $row->types()->exists()) ? $row->types->displayname : "",
                "type_id" => $row->type_id,
                // "color" => ($row->color_id && $row->vehiclecolor()->exists()) ? $row->vehiclecolor->color : "",
                "color" => $row->color_name,
                "vehicle_number" => $row->license_plate,
                "group_id" => $row->group_id,
                "group" => ($row->group_id) ? $row->group->name : "",
                "int_mileage" =>$row->int_mileage,
                "average" => $row->getMeta('average'),
                // "driver_id" => $row->driver_id,
                // "assigned_driver" => ($row->driver_id) ? (isset($row->driver->assigned_driver)) ? $row->driver->assigned_driver->name : "" : "",
                "vehicle_image" => $image,
                "ins_image" => $ins_image,
                'engine_type' =>$row->engine_type,
                "rc_book" => $rc_book,
                "permit" => $permit,
                "fitness" => $fitness,
                // "other_images" => $other_images,
                "other_images" => $other_img,
                "is_active" => ($row->in_service==1?'Active':'Inactive'),
                'timestamp' => date('Y-m-d H:i:s', strtotime($row->updated_at)),
                "delete_status" => (isset($row->deleted_at)) ? 1 : 0,
            );
        }
        $data['success'] = "1";
        $data['message'] = "Data fetched!";
        $data['data'] = $details;
        return $data;
    }
    public function delete_driver(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'driver_id' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = "Something went wrong, please try again later!";
            $data['data'] = null;
        } else {
            $driver = User::find($request->driver_id);
            if ($driver->vehicle_id) {
                $vehicle = VehicleModel::find($driver->vehicle_id);
                if ($vehicle != null) {
                    $vehicle->driver_id = null;
                    $vehicle->save();
                }
            }
            DriverVehicleModel::where('driver_id', $request->driver_id)->delete();
            User::find($request->get('driver_id'))->user_data()->delete();
            User::find($request->get('driver_id'))->delete();
            $data['success'] = "1";
            $data['message'] = "Record deleted successfully!";
            $data['data'] = array('driver_id' => $request->driver_id, 'timestamp' => date('Y-m-d H:i:s'));
        }
        return $data;
    }
    public function available_vehicles(Request $request)
    {
        if (isset($request->vehicle_id)) {
            $exclude = DriverVehicleModel::select('vehicle_id')->where('vehicle_id', '!=', $request->vehicle_id)->get('vehicle_id')->pluck('vehicle_id')->toArray();
        } else {
            $exclude = DriverVehicleModel::select('vehicle_id')->get('vehicle_id')->pluck('vehicle_id')->toArray();
            // dd($exclude);
        }
        $vehicles = VehicleModel::where('user_id', $request->id)->whereNotIn('id', $exclude)->get();
        $details = array();
        foreach ($vehicles as $row) {
            $details[] = array(
                'id' => $row->id,
                'vehicle' => $row->maker->make . " - " . $row->vehiclemodel->model . " - " . $row->license_plate,
                'timestamp' => date('Y-m-d H:i:s', strtotime($row->updated_at)),
            );
        }
        $data['success'] = "1";
        $data['message'] = "Data fetched!";
        $data['data'] = $details;
        return $data;
    }
    public function enable_disable_driver(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'is_active' => 'required|integer',
            'driver_id' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = "Something went wrong, please try again later!";
            // $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $driver = User::find($request->driver_id);
            $driver->is_active = $request->is_active;
            $driver->save();
            $data['success'] = "1";
            $data['message'] = "Driver's active status changed successfully!";
            $data['data'] = array(
                'driver_id' => $driver->id,
                'active' => $driver->is_active,
                "is_active" => ($driver->getMeta('is_active')) ? "YES" : "NO",
                'timestamp' => date('Y-m-d H:i:s', strtotime($driver->updated_at)),
            );
        }
        return $data;
    }
    public function drivers(Request $request)
    {
        $drivers = User::
        // where('user_id', $request->id)
        // ->
        whereUser_type("D");
        if (isset($request->timestamp)) {
            $time = date('Y-m-d H:i:s', strtotime($request->timestamp));
            $drivers = $drivers->where('updated_at', ">", $time);
        }
        $drivers = $drivers->orderBy('id', 'desc')
        ->withTrashed()
        ->get();
        $details = array();
        foreach ($drivers as $row) {
            //dump($row);
            $details[] = array(
                'driver_id' => $row->id,
                'name' => $row->name,
                'email' => $row->email,
                'phone' => $row->getMeta('phone'),
                'alt_mobile' => $row->alt_mobile,
                //'vehicle_id' => $row->vehicle_id,
                //'vehicle_id' => ($row->vehicle_id>1) ? $row->vehicle_id[0]:$row->vehicle_id,
                //'vehicle' => ($row->vehicle_id) ? ($row->driver_vehicle()->exists()) ? $row->driver_vehicle->vehicle->make_name . "-" . $row->driver_vehicle->vehicle->model_name . "-" . $row->driver_vehicle->vehicle->license_plate : "" : "",
                'address'=>$row->getMeta("address"),
                'city' => $row->getMeta("city"),
                'image' => ($row->driver_image) ? url('uploads/' . $row->driver_image) : url('assets/images/user-noimage.png'),
                'active' => ($row->is_active) ? 1 : 0,
                "is_active" => ($row->getMeta('is_active')) ? "YES" : "NO",
                'gender_text' => ($row->gender == 1) ? "Male" : "Female",
                'gender' => $row->gender,
                'driver_commision_type' => $row->getMeta('driver_commision_type'),
                'driver_commision' => $row->getMeta('driver_commision'),
                'license_image' => ($row->license_image) ? url('uploads/' . $row->license_image) : null,
                'timestamp' => date('Y-m-d H:i:s', strtotime($row->updated_at)),
                'uid' => $row->uid,
                'join_date' => date('d-m-Y', strtotime($row->created_at)),
                "delete_status" => (isset($row->deleted_at)) ? 1 : 0,
                'id_proof' => ($row->id_proof) ? url('uploads/' . $row->id_proof) : null,
                'password' => $row->password,
            );
        }
        $data['success'] = "1";
        $data['message'] = "Data fetched!";
        $data['data'] = $details;
        return $data;
    }
    public function add_driver(Request $request)
    {
        //dd($request->all());
        $validation = Validator::make($request->all(), [
            // 'emp_id' => ['required', new UniqueEId],
            // 'license_number' => ['required', new UniqueLicenceNumber],
            // 'contract_number' => ['required', new UniqueContractNumber],
            'name' => 'required',
            // 'last_name' => 'required',
            // 'address' => 'required',
            'phone' => ['required', new UniqueMobile],
            // 'phone' => 'required',
            'email' => 'required|email|unique:users,email,' . \Request::get("id"),
            // 'exp_date' => 'required|date|date_format:Y-m-d|after:tomorrow',
            // 'start_date' => 'date|date_format:Y-m-d',
            'id_proof' => 'nullable',
            'license_image' => 'nullable',
            // 'documents.*' => 'nullable|mimes:jpg,png,jpeg,pdf,doc,docx',
            'password' => 'required',
            'city' => 'required',
            'id' => 'required|integer',
            //'vehicle_id' => 'required|integer',
            'gender' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            // $data['message'] = "Something went wrong, please try again later!";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $last = User::where('user_type', 'D')->latest()->first();
            $uid = $last->uid + 1;
            $ifuid = User::meta()
                ->where(function ($query) use ($uid) {
                    $query->where('users_meta.key', '=', 'uid')
                        ->where('users_meta.value', '=', $uid)
                        ->where('users_meta.deleted_at', '=', null);
                })->exists();
            if ($ifuid) {
                $uid++;
            }
            $id = User::create([
                "name" => $request->name,
                "email" => $request->get("email"),
                "password" => bcrypt($request->get("password")),
                "user_type" => "D",
                'api_token' => str_random(60),
                'user_id' => $request->id,
            ])->id;
            $user = User::find($id);
            // if ($request->file('id_proof') && $request->file('id_proof')->isValid()) {
            //     $this->upload_file($request->file('id_proof'), "id_proof", $id);
            // }
            // if ($request->file('license_image') && $request->file('license_image')->isValid()) {
            //     $this->upload_file($request->file('license_image'), "license_image", $id);
            //     $user->id_proof_type = "License";
            //     $user->save();
            // }
            if (isset($request->id_proof)) {
                $user->id_proof = $request->id_proof;
            }
            if (isset($request->license_image)) {
                $user->license_image = $request->license_image;
            }
            $user->save();
            $form_data = $request->all();
            $user->setMeta([
                $user->driver_commision_type = $request->driver_commision_type,
                $user->driver_commision = $request->driver_commision,
                $user->phone = $request->phone,
                $user->city = $request->city,
                $user->address = $request->address,
                $user->gender = $request->gender,
            ]); 
            // unset($form_data['driver_image']);
            // unset($form_data['documents']);
            unset($form_data['license_image']);
            unset($form_data['id_proof']);
            //$user->setMeta($form_data);
            $user->uid = $uid;
            $user->save();
            // $vehicle = VehicleModel::find($request->get('vehicle_id'));
            // //dd($vehicle);
            // $vehicle->driver_id = $user->id;
            // $vehicle->save();
            // DriverLogsModel::create(['driver_id' => $user->id, 'vehicle_id' => $request->get('vehicle_id'), 'date' => date('Y-m-d H:i:s')]);
            // DriverVehicleModel::updateOrCreate(['vehicle_id' => $request->get('vehicle_id')], ['vehicle_id' => $request->get('vehicle_id'), 'driver_id' => $user->id]);
            $data['success'] = "1";
            $data['message'] = "Driver added successfully!";
            $data['data'] = array(
                'driver_id' => $user->id,
                'uid' => $user->uid,
                'join_date' => date('d-m-Y', strtotime($user->created_at)),
                'timestamp' => date('Y-m-d H:i:s', strtotime($user->updated_at)),
                'license_image' => ($user->license_image) ? url('uploads/' . $user->license_image) : null,
                'id_proof' => ($user->id_proof) ? url('uploads/' . $user->id_proof) : null,
            );
        }
        return $data;
    }
    public function edit_driver(Request $request)
    {     
        $validation = Validator::make($request->all(), [
            //'driver_id'=>'required|integer',
            'name' => 'required',
            // 'last_name' => 'required',
            // 'address' => 'required',
            'phone' => ['required', new UniqueMobile],
            'email' => 'required|email|unique:users,email,' . \Request::get("user_id"),
            // 'start_date' => 'date|date_format:Y-m-d',
            'id_proof' => 'nullable',
            'license_image' => 'nullable',
            // 'documents.*' => 'nullable|mimes:jpg,png,jpeg,pdf,doc,docx',
            'city' => 'required',
            'user_id' => 'required|integer',
            //'vehicle_id' => 'required|integer',
            'gender' => 'required|integer',
            'edit' => 'required|integer',
        ]);
        $errors = $validation->errors();
        //dd($errors->all());
        if (count($errors) > 0) {
            $data['success'] = "0";
            //$data['message'] = "Something went wrong, please try again later!";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $id = $request->get('user_id');
            $user = User::find($id);
            // if ($user->vehicle_id != $request->vehicle_id) {
            //     $old_vehicle = VehicleModel::find($user->vehicle_id);
            //     if ($old_vehicle) {
            //         $old_vehicle->driver_id = null;
            //         $old_vehicle->save();
            //     }
            //     $vehicle = VehicleModel::find($request->get('vehicle_id'));
            //     $vehicle->driver_id = $user->id;
            //     $vehicle->save();
            //     DriverLogsModel::create(['driver_id' => $user->id, 'vehicle_id' => $request->get('vehicle_id'), 'date' => date('Y-m-d H:i:s')]);
            //     DriverVehicleModel::updateOrCreate(['driver_id' => $user->id], ['vehicle_id' => $request->get('vehicle_id'), 'driver_id' => $user->id]);
            // }
            if (isset($request->id_proof)) {
                $user->id_proof = $request->id_proof;
            }
            if (isset($request->license_image)) {
                $user->license_image = $request->license_image;
            }
            $user->id_proof_type = "License";
            $user->save();
            // if ($request->file('id_proof') && $request->file('id_proof')->isValid()) {
            //     $this->upload_file($request->file('id_proof'), "id_proof", $id);
            // }
            // if ($request->file('license_image') && $request->file('license_image')->isValid()) {
            //     $this->upload_file($request->file('license_image'), "license_image", $id);
            //     $user->id_proof_type = "License";
            //     $user->save();
            // }
            // if ($request->file('documents')) {
            //     $this->upload_file($request->file('documents'), "documents", $id);
            // }
            if ($request->password != null) {
                $user->password = bcrypt($request->password);
            }
            $user->name = $request->name;
            $user->email = $request->get('email');
            $user->city = $request->city;
            $user->save();
            $form_data = $request->all();
            // unset($form_data['driver_image']);
            unset($form_data['id_proof']);
            unset($form_data['license_image']);
            unset($form_data['user_id']);
            $user->setMeta([
                $user->driver_commision_type = $request->driver_commision_type,
                $user->driver_commision = $request->driver_commision,
                $user->phone = $request->phone,
                $user->address = $request->address,
                $user->gender = $request->gender,
            ]);
            $user->save();
            $data['success'] = "1";
            $data['message'] = "Driver updated successfully!";
            $data['data'] = array(
                'driver_id' => $user->id,
                'uid' => $user->uid,
                'join_date' => date('d-m-Y', strtotime($user->created_at)),
                'timestamp' => date('Y-m-d H:i:s', strtotime($user->updated_at)),
                'license_image' => ($user->license_image) ? url('uploads/' . $user->license_image) : null,
                'id_proof' => ($user->id_proof) ? url('uploads/' . $user->id_proof) : null,
                'password' => $user->password,
            );
        }
        return $data;
    }
    public function driver_document(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'id_proof' => 'nullable|mimes:jpg,png,jpeg',
            'license_image' => 'nullable|mimes:jpg,png,jpeg',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $id_proof = null;
            $license_image = null;
            if ($request->file('id_proof') && $request->file('id_proof')->isValid()) {
                $id_proof = $this->get_uploaded_doc($request->file('id_proof'));
            }
            if ($request->file('license_image') && $request->file('license_image')->isValid()) {
                $license_image = $this->get_uploaded_doc($request->file('license_image'));
            }
            $data['success'] = "1";
            $data['message'] = "Driver documents uploaded successfully!";
            $data['data'] = array(
                'id_proof' => $id_proof,
                'license_image' => $license_image,
            );
        }
        return $data;
    }
    private function upload_file($file, $field, $id)
    {
        $destinationPath = './uploads'; // upload path
        $extension = $file->getClientOriginalExtension();
        $fileName1 = Str::uuid() . '.' . $extension;
        $file->move($destinationPath, $fileName1);
        $user = User::find($id);
        $user->setMeta([$field => $fileName1]);
        $user->save();
    }
    public function groups(Request $request)
    {
        $user = User::find($request->id);
        if ($user->group_id == null) {
            $groups = VehicleGroupModel::whereNotNull('id');
        } else {
            $groups = VehicleGroupModel::where('id', $user->group_id);
        }
        if (isset($request->timestamp)) {
            $time = date('Y-m-d H:i:s', strtotime($request->timestamp));
            $groups = $groups->where("updated_at", ">", $time);
        }
        $groups = $groups->withTrashed()->get();
        $details = array();
        foreach ($groups as $row) {
            $details[] = array(
                "id" => $row->id,
                "name" => $row->name,
                "description" => $row->description,
                "note" => $row->note,
                'timestamp' => date('Y-m-d H:i:s', strtotime($row->updated_at)),
                "delete_status" => (isset($row->deleted_at)) ? 1 : 0,
            );
        }
        $data['success'] = "1";
        $data['message'] = "Data fetched!";
        $data['data'] = $details;
        return $data;
    }
    public function expense_list(Request $request)
    {
        //dd($request->all());
        $validation = Validator::make($request->all(), [
            'user_id' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;           
        } else {
            $user=User::find($request->user_id);
           // $user = auth()->guard('api')->user();
            if ($user->user_type == "D") {
                // $v = DriverLogsModel::where('driver_id',Auth::user()->id)->get();
                // $vehicle_ids = $v->pluck('vehicle_id')->toArray();
                // $data['vehicels'] = VehicleModel::whereId($vehicle_ids)->whereIn_service(1)->get();
                $vehicle_ids = auth()->user()->vehicles()->pluck('vehicle_id')->toArray();
               $vehicles = auth()->user()->vehicles()->whereIn_service(1)->get();
            } else {
                if ($user->group_id == null || $user->user_type == "S") {
                   $vehicles = VehicleModel::whereIn_service(1)->get();
                    $vehicle_ids =$vehicles->pluck('id')->toArray();
                } else {
                   $vehicles = VehicleModel::whereIn_service(1)->where('group_id', $user->group_id)->get();
                   $vehicle_ids =$vehicles->pluck('id')->toArray();   
                }
            }
           // $data['types'] = ExpCats::get();
           // $data['service_items'] = ServiceItemsModel::get();
            $total = Expense::whereIn('vehicle_id', $vehicle_ids)->whereDate('date', DB::raw('CURDATE()'))->sum('amount');
           $expenses = Expense::whereIn('vehicle_id', $vehicle_ids);
           // $data['vendors'] = Vendor::get();
            // $data['date1'] = null;
            // $data['date2'] = null;
            if (isset($request->timestamp)) {
                $time = date('Y-m-d H:i:s', strtotime($request->timestamp));
                $expenses = $expenses->where('updated_at', ">", $time);
            }
            $expenses = $expenses->where('user_id',$request->user_id)->orderBy('id','DESC')->withTrashed()->get();
            $records = array();
            foreach ($expenses as $row) {
                //dd($row);
                $records[] = array(
                    'id' => $row->id,
                    'make_model' => ($row->vehicle_id) ? $row->vehicle->make_name??'' . '-' . $row->vehicle->model_name??'' : "",
                    'make' => ($row->vehicle_id) ? $row->vehicle->make_name??'' : "",
                    'model' => ($row->vehicle_id) ? $row->vehicle->model_name ??'': "",
                    'vehicle_number' => ($row->vehicle_id) ? $row->vehicle->license_plate??'' : "",
                    'expense_type' => $row->category->name ??'',
                    'booking_id' =>$row->booking_id,
                    'date' => date('Y-m-d', strtotime($row->date)),
                    // 'date' => date('d-m-Y', strtotime($row->date)),
                    'total_amount' => round($row->amount, 2),
                    'vehicle_id' => $row->vehicle_id,
                    'vendor_id' => $row->user_id,
                    'note'=>$row->comment,
                    //'amount' => round($row->amount - $row->tax_charge_rs, 2),
                    'booking_id' => $row->booking_id,
                    'driver_id' => $row->driver_id,
                    'driver_name' => ($row->driver_id) ? $row->driver->name : "",
                    'timestamp' => date('Y-m-d H:i:s', strtotime($row->updated_at)),
                    "delete_status" => (isset($row->deleted_at)) ? 1 : 0,
                );
            }
            $data['success'] = "1";
            $data['message'] = "Data fetched.";
            $data['data'] = array(
                'records' => $records,
                'total' => $total,
            );
        }
        return $data;   
    }
    public function add_expense(Request $request)
    {
       //dd($request->get('booking_id'));
        $validation = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'vehicle_id' => 'required',
            'expense_type' => 'required',
            'revenue' => 'required',
            'vendor_id'=>'required'
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $result = explode('_', $request->get("expense_type"));
            $expense=Expense::create([
                "vehicle_id" => $request->get("vehicle_id"),
                "amount" => $request->get("revenue"),
                "user_id" =>auth()->guard('api')->user()->id,
                "date" => $request->get('date'),
                "comment" => $request->get('note'),
                "expense_type" => $result[1],
                "type" => $result[0],
                "vendor_id" => $request->get('vendor_id'),
                "booking_id" => $request->get('booking_id'),
            ]);
            $data['success'] = "1";
            $data['message'] = "Expense record added successfully.";
            $data['data'] = array('expense_id' => $expense->id, 'timestamp' => date('Y-m-d H:i:s', strtotime($expense->updated_at)));
        }
        return $data;
    }
    public function delete_expense(Request $request)
    {
        //dd($request->all());
        $validation = Validator::make($request->all(), [
            'expense_id' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = "Something went wrong, please try again later!";
            $data['data'] = null;
        } else {
            Expense::find($request->expense_id)->delete();
            $user = auth()->guard('api')->user();
            if ($user->user_type == "D") {
                $v = DriverLogsModel::where('driver_id', $user->id)->get();
                $vehicle_ids = $v->pluck('vehicle_id')->toArray();
            } else {
                if ($user->group_id == null || $user->user_type == "S") {
                    $vehicle_ids = VehicleModel::whereIn_service(1)->pluck('id')->toArray();
                } else {
                    $vehicle_ids = VehicleModel::whereIn_service(1)->where('group_id', $user->group_id)->pluck('id')->toArray();
                }
            }
            // $data['today'] = Expense::whereIn('vehicle_id', $vehicle_ids)->whereDate('date', DB::raw('CURDATE()'))->get();
            // $data['total'] = Expense::whereIn('vehicle_id', $vehicle_ids)->whereDate('date', DB::raw('CURDATE()'))->sum('amount');
            $data['success'] = "1";
            $data['message'] = "Record deleted successfully!";
            $data['data'] = array('expense_id' => $request->expense_id, 'timestamp' => date('Y-m-d H:i:s'));
        }
        return $data;
    }
    public function expense_types(Request $request)
    {
       // $records = ExpCats::get();
        $types = ExpCats::withTrashed()->get();
        if (isset($request->timestamp)) {
            $time = date('Y-m-d H:i:s', strtotime($request->timestamp));
            $types = ExpCats::where("updated_at", ">", $time)->withTrashed()->get();
        }
        $records = array();
        foreach ($types as $row) {
            $records[] = array(
                'id' => $row->id,
                'name' => $row->name,
                'timestamp' => date('Y-m-d H:i:s', strtotime($row->updated_at)),
                "delete_status" => (isset($row->deleted_at)) ? 1 : 0,
            );
        }
        $data['success'] = "1";
        $data['message'] = "Data fetched";
        $data['data'] = $records;
        return $data;
    }
    public function update_expense(Request $request)//added on 13-03-23
    {
        //dd($request->all());
        $validation = Validator::make($request->all(), [
            'expense_id'=>'required|integer',
            'user_id' => 'required|integer',
            'vehicle_id' => 'required',
            'expense_type' => 'required',
            'revenue' => 'required'
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } 
        else 
        {   
            $result = explode('_', $request->get("expense_type"));
            $expense=Expense::find($request->expense_id);           
            if(!empty($expense))
            {
                $expense->update([
                    "vehicle_id" => $request->get("vehicle_id"),
                    "amount" => $request->get("revenue"),
                    "user_id" =>auth()->guard('api')->user()->id,
                    "date" => $request->get('date'),
                    "comment" => $request->get('note'),
                    "expense_type" => $result[1],
                    "type" => $result[0],
                    "vendor_id" => $request->get('vendor_id'),
                    "booking_id" => $request->get('booking_id'),
                ]);
                $expense->save();
                $data['success'] = "1";
                $data['message'] = "Expense updated successfully!";
                $data['data'] = array('expense_id' => $expense->id, 'timestamp' => date('Y-m-d H:i:s', strtotime($expense->updated_at)));
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
    public function getVendors(Request $request)
    {
        //dd($request->all());
        $validation = Validator::make($request->all(), [
           // 'user_id' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;           
        } else {
            //$vendors = Vendor::select('*');
           // $vendors=Vendor::where('user_id',$request->user_id)->get();
           $vendors = Vendor::get();
            //orderBy('id',$request->order[0]['dir']);
            //$date_format_setting = (Hyvikk::get('date_format')) ? Hyvikk::get('date_format') : 'd-m-Y';
            $records = array();
            if(count($vendors)>0)
            {
                foreach ($vendors as $vendor) {
                    //dd($row);
                    $src = ($vendor->photo != null) ? asset('uploads/' . $vendor->photo) : asset('assets/images/no-user.jpg');
                    $records[] = array(
                        'vendor_id'=> $vendor->id,
                        'image'=>$src,
                        'name'=>$vendor->name,
                        'type'=>$vendor->type,
                        'email'=>$vendor->email,
                        'phone'=>$vendor->phone,
                        'website'=>$vendor->address,
                        'timestamp' => date('Y-m-d H:i:s', strtotime($vendor->updated_at)),
                        "delete_status" => (isset($vendor->deleted_at)) ? 1 : 0,
                    );
                }
                $data['success'] = "1";
                $data['message'] = "Data fetched.";
                $data['data'] =$records;
            }
            else
            {
                $data['success'] = "0";
                $data['message'] = "No record found";
                $data['data'] = null;
            }
        }
        return $data;   
    }
    public function vehicle_model(Request $request)
    {
        $models=VehicleModel::groupBy('model_name')->get()->pluck('model_name')->toArray();
        if(count($models)>0)
        {
            $data['success'] = "1";
            $data['message'] = "Data fetched!";
            $data['data'] = $models;
        }
        else
        {
            $data['success'] = "0";
            $data['message'] = "No record found!";
            $data['data'] = null;
        }
        return $data;
    }
    public function vehicle_make(Request $request)
    {
        $makes=VehicleModel::groupBy('make_name')->get()->pluck('make_name')->toArray();
        if(count($makes)>0)
        {
            $data['success'] = "1";
            $data['message'] = "Data fetched!";
            $data['data'] = $makes;
        }
        else
        {
            $data['success'] = "0";
            $data['message'] = "No record found!";
            $data['data'] = null;
        }
        return $data;
    }
    public function vehicle_color(Request $request)
    {
        $colors=VehicleModel::groupBy('color_name')->get()->pluck('color_name')->toArray();
        if(count($colors)>0)
        {
            $data['success'] = "1";
            $data['message'] = "Data fetched!";
            $data['data'] = $colors;
        }
        else
        {
            $data['success'] = "0";
            $data['message'] = "No record found!";
            $data['data'] = null;
        }
        return $data;
    }
    public function tax_calculation(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'amount' => 'required|numeric',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = "";
        } else {
            $tax = 0;
            if (Hyvikk::get('tax_charge') != "null") {
                $taxes = json_decode(Hyvikk::get('tax_charge'), true);
                foreach ($taxes as $key => $val) {
                    $tax = $tax + $val;
                }
            }
            $total_amount = (($request->amount * $tax) / 100) + $request->amount;
            $total_tax_charge = (($request->amount * $tax) / 100);
            $data['success'] = "1";
            $data['message'] = "Data fetched!";
            $data['data'] = array(
                'ride_amount' => $request->amount,
                'total_amount' => $total_amount,
                'tax_charges' => $total_tax_charge,
                'tax' => $tax . "%",
            );
        }
        return $data;
    }
    public function driver_payment_list(Request $request)
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
            $bookings = Bookings::whereNotNull('driver_id')->orderBy('bookings.id', 'desc')
                ->meta()->where(function ($q) {
                $q->where('bookings_meta.key', 'receipt');
                $q->where('bookings_meta.value', 1);
            })->get();
            if(isset($request->timestamp))
            {
                $driver_payments = DriverPayments::where('updated_at','>=',$request->timestamp)->latest()->get()->toBase()->merge($bookings);
            }
            else{
                $driver_payments = DriverPayments::latest()->get()->toBase()->merge($bookings);
            }
            $drivers = User::where('user_type', 'D')->has('bookings')->orderBy('name')->pluck('name', 'id')->toArray();
            $driver_bookings = Bookings::whereIn('driver_id', array_keys($drivers))->get();
            $driver_remaining_amounts = User::where('user_type', 'D')->has('bookings')->get();
            $driver_amount = [];
            foreach ($driver_remaining_amounts as $dram) {
                $driver_amount[$dram->id]['data-remaining-amount'] = $dram->remaining_amount;
            }
            foreach ($driver_bookings as $am) {
                $amount = $am->driver_amount ?? $am->tax_total;
                if (!empty($driver_amount[$am->driver_id]['data-amount'])) {
                    $driver_amount[$am->driver_id]['data-amount'] = $driver_amount[$am->driver_id]['data-amount'] + $amount;
                } else {
                    $driver_amount[$am->driver_id]['data-amount'] = $amount;
                }
            }
            $driver_booking_amount = $driver_amount;
            $records=array();
            $currency = Hyvikk::get('currency') ;
            $date_format_setting = (Hyvikk::get('date_format')) ? Hyvikk::get('date_format') : 'd-m-Y'; 
            //dd($driver_payments);
            if(count($driver_payments)>0)
            {
                foreach($driver_payments as $key => $payment)
                {
                    $records[]= [
                        'payment_id'=>$payment->id,
                        'driver_id'=>$payment->driver_id ,
                        'driver_name'=>$payment->driver->name??'' ,
                        'description'=>(($payment instanceof Bookings) ? 'Booking Id:'.$payment->id : 'Payment'),
                        'amount'=> $currency.(($payment instanceof Bookings) ? $payment->driver_amount??$payment->total : $payment->amount),
                        'timestamp'=>date($date_format_setting.' h:i A',strtotime($payment->updated_at)) 
                    ];
                }
                    $data['success'] = "1";
                    $data['message'] = "Data fetched";
                    $data['data'] = $records;
            }
            else{
                $data['success'] = "0";
                $data['message'] = "No Records Found";
                $data['data'] = $null;
            }
        }
        return $data;
    }
    public function add_driver_payment_report(Request $request)
    {
        //dd($request->all());  
        $validation = Validator::make($request->all(), [
            'driver_id' => 'required|integer',
            'amount' => 'required',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        }
        else {
            $driver_payment=DriverPayments::create([
                'user_id' => auth()->id(),
                'driver_id' => $request->driver_id,
                'amount' => $request->amount,
                'notes' => $request->notes,
            ]);
            $driver = User::find($request->driver_id);
            $remainig_amount_after_saved = $request->remaining_amount_hidden - $request->amount;
            $driver->remaining_amount = $remainig_amount_after_saved;
            //dd($driver);
            $driver->save();
            $records = array();
            $records[] = array(
                "driver_payment_id"=>$driver_payment->id,
                // "driver_name"=>$driver->name,
                'description'=>(($driver_payment instanceof Bookings) ? 'Booking Id:'.$driver_payment->id : 'Payment'),
                // "amount"=>$driver->driver_amount,
                'timestamp' => date('Y-m-d H:i:s', strtotime($driver->updated_at)),
                "delete_status" => (isset($driver->deleted_at)) ? 1 : 0,
            );
            $data['success'] = "1";
            $data['message'] = "Record Added Succesfully";
            $data['data'] = $records;
        }
        return $data;
    }
    public function settings(Request $request)
    {
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
            $currency = Hyvikk::get('currency') ;
            $distance_format = Hyvikk::get("dis_format");
            $fuel_qty_unit=Hyvikk::get("fuel_unit");
            $records=array();
            $records[]= [
                'currency'=>$currency,
                'distance_format'=>$distance_format ,
                'qty_unit'=>$fuel_qty_unit                        
            ];
            $data['success'] = "1";
                    $data['message'] = "Data fetched";
                    $data['data'] = $records;
        }
        return $data;
    }
    public function enable_disable_vehicle(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'is_active' => 'required|integer',
            'vehicle_id' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = "Something went wrong, please try again later!";
            // $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $vehicle = VehicleModel::find($request->vehicle_id);
            $vehicle->in_service = $request->is_active;
            $vehicle->save();
            $data['success'] = "1";
            $data['message'] = "Vehicle's active status changed successfully!";
            $data['data'] = array(
                'vehicle_id' => $vehicle->id,
                'is_active' => ($vehicle->in_service==1?'Active':'Inactive'),
                'timestamp' => date('Y-m-d H:i:s', strtotime($vehicle->updated_at)),
            );
        }
        return $data;
    }
}
